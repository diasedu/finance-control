<?php

namespace App\Services;

use App\Enums\DocumentoStatus;
use App\Jobs\ProcessarOcrDocumentoJob;
use App\Models\Documento;
use App\Repositories\DocumentoRepositoryInterface;
use App\Repositories\ItemDocumentoRepositoryInterface;
use App\Services\Ocr\OcrServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

/**
 * Orquestra o caso de uso "Documentos/Comprovantes":
 * - guarda o PDF enviado
 * - manda pra fila a extração de metadados e itens via LLM (OCR)
 * - persiste tudo via repositório
 *
 * Controllers (e o Job da fila) chamam apenas métodos deste serviço;
 * nenhuma query ou regra de negócio fica fora dele.
 */
class DocumentoService
{
    private const DISCO = 'local';

    private const DIRETORIO = 'comprovantes';

    public function __construct(
        private readonly DocumentoRepositoryInterface $repository,
        private readonly ItemDocumentoRepositoryInterface $itemRepository,
        private readonly OcrServiceInterface $ocrService,
    ) {
    }

    public function listarPaginado(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function buscar(int $id): Documento
    {
        return $this->repository->findOrFail($id);
    }

    /**
     * Cria um novo documento a partir do upload e enfileira a extração
     * de metadados/itens via LLM. Retorna na hora, com status Pendente
     * — a requisição HTTP não fica esperando a LLM responder.
     *
     * @param  array{titulo?: ?string, tipo?: ?string, pessoa?: ?string, observacoes?: ?string}  $dados
     */
    public function criarAPartirDoUpload(array $dados, UploadedFile $arquivo): Documento
    {
        $caminho = $arquivo->store(self::DIRETORIO, self::DISCO);

        $documento = $this->repository->create([
            'titulo' => $dados['titulo'] ?? null,
            'tipo' => $dados['tipo'] ?? null,
            'pessoa' => $dados['pessoa'] ?? null,
            'observacoes' => $dados['observacoes'] ?? null,
            'arquivo_path' => $caminho,
            'arquivo_nome_original' => $arquivo->getClientOriginalName(),
            'arquivo_mime' => $arquivo->getClientMimeType(),
            'arquivo_tamanho' => $arquivo->getSize(),
            'status' => DocumentoStatus::Pendente,
        ]);

        ProcessarOcrDocumentoJob::dispatch($documento->id);

        return $documento;
    }

    /**
     * Atualiza os metadados de um documento. Se um novo arquivo for
     * enviado, o anterior é substituído e a extração via LLM é
     * reenfileirada (não roda na própria requisição).
     */
    public function atualizar(int $id, array $dados, ?UploadedFile $novoArquivo = null): Documento
    {
        $documento = $this->repository->findOrFail($id);

        $atributos = array_filter([
            'titulo' => $dados['titulo'] ?? null,
            'tipo' => $dados['tipo'] ?? null,
            'valor' => $dados['valor'] ?? null,
            'data_documento' => $dados['data_documento'] ?? null,
            'fornecedor' => $dados['fornecedor'] ?? null,
            'pessoa' => $dados['pessoa'] ?? null,
            'observacoes' => $dados['observacoes'] ?? null,
        ], static fn ($valor) => $valor !== null);

        $novaExtracaoNecessaria = $novoArquivo !== null;

        if ($novoArquivo !== null) {
            Storage::disk(self::DISCO)->delete($documento->arquivo_path);

            $caminho = $novoArquivo->store(self::DIRETORIO, self::DISCO);

            $atributos = array_merge($atributos, [
                'arquivo_path' => $caminho,
                'arquivo_nome_original' => $novoArquivo->getClientOriginalName(),
                'arquivo_mime' => $novoArquivo->getClientMimeType(),
                'arquivo_tamanho' => $novoArquivo->getSize(),
                'status' => DocumentoStatus::Pendente,
                'dados_extraidos' => null,
                'erro_processamento' => null,
            ]);
        }

        $documento = $this->repository->update($documento, $atributos);

        if ($novaExtracaoNecessaria) {
            ProcessarOcrDocumentoJob::dispatch($documento->id);
        }

        return $documento;
    }

    /**
     * Reenfileira a extração via LLM para um documento já existente,
     * sem precisar reenviar o arquivo (ex.: depois de corrigir uma
     * configuração que causou falha na extração).
     */
    public function reprocessarOcr(int $id): Documento
    {
        $documento = $this->repository->update(
            $this->repository->findOrFail($id),
            ['status' => DocumentoStatus::Pendente, 'erro_processamento' => null],
        );

        ProcessarOcrDocumentoJob::dispatch($documento->id);

        return $documento;
    }

    public function excluir(int $id): void
    {
        $documento = $this->repository->findOrFail($id);

        Storage::disk(self::DISCO)->delete($documento->arquivo_path);

        $this->repository->delete($documento);
    }

    public function baixarArquivo(int $id): StreamedResponse
    {
        $documento = $this->repository->findOrFail($id);

        return Storage::disk(self::DISCO)->download(
            $documento->arquivo_path,
            $documento->arquivo_nome_original,
        );
    }

    /**
     * Chama a LLM para extrair os metadados e os itens do PDF, e
     * atualiza o documento com o resultado (ou com o erro, se a
     * chamada falhar). Roda dentro de ProcessarOcrDocumentoJob, fora
     * da requisição HTTP que fez o upload.
     */
    public function executarExtracaoOcr(int $documentoId): Documento
    {
        $documento = $this->repository->update(
            $this->repository->findOrFail($documentoId),
            ['status' => DocumentoStatus::Processando],
        );

        $caminhoAbsoluto = Storage::disk(self::DISCO)->path($documento->arquivo_path);

        try {
            $resultado = $this->ocrService->extrairMetadados($caminhoAbsoluto);

            $documento = $this->repository->update($documento, array_filter([
                'tipo' => $documento->tipo ?? $resultado->tipo,
                'valor' => $resultado->valor,
                'data_documento' => $resultado->dataDocumento,
                'fornecedor' => $resultado->fornecedor,
                'observacoes' => $documento->observacoes ?? $resultado->descricao,
                'dados_extraidos' => $resultado->bruto,
                'status' => DocumentoStatus::Processado,
                'erro_processamento' => null,
            ], static fn ($valor) => $valor !== null));

            $this->sincronizarItensExtraidos($documento, $resultado->itens);

            return $documento;
        } catch (Throwable $exception) {
            Log::warning('Falha ao processar OCR do documento #'.$documento->id, [
                'mensagem' => $exception->getMessage(),
            ]);

            return $this->repository->update($documento, [
                'status' => DocumentoStatus::Erro,
                'erro_processamento' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * Substitui os itens do documento pelos itens que a LLM conseguiu
     * extrair com confiança. Itens incompletos são descartados: é
     * melhor o usuário completar manualmente do que gravar lixo.
     *
     * @param  array<int, \App\Services\Ocr\ItemExtraido>  $itensExtraidos
     */
    private function sincronizarItensExtraidos(Documento $documento, array $itensExtraidos): void
    {
        $this->itemRepository->deleteParaDocumento($documento->id);

        $linhas = [];

        foreach ($itensExtraidos as $itemExtraido) {
            if (! $itemExtraido->completo()) {
                continue;
            }

            $linhas[] = [
                'mes' => $itemExtraido->mes,
                'ano' => $itemExtraido->ano,
                'item' => $itemExtraido->item,
                'valor' => $itemExtraido->valor,
                'classificacao' => $itemExtraido->classificacao,
                'tipo' => $itemExtraido->tipo,
            ];
        }

        if ($linhas !== []) {
            $this->itemRepository->createMany($documento->id, $linhas);
        }
    }
}
