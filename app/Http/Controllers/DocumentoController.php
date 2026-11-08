<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentoRequest;
use App\Http\Requests\UpdateDocumentoRequest;
use App\Services\DocumentoService;
use App\Services\ItemDocumentoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Controller intencionalmente fino: apenas traduz a requisição HTTP em
 * chamadas aos Services e devolve a view/redirect. Nenhuma query ou
 * regra de negócio aqui.
 */
class DocumentoController extends Controller
{
    public function __construct(
        private readonly DocumentoService $documentoService,
        private readonly ItemDocumentoService $itemDocumentoService,
    ) {
    }

    public function index(): View
    {
        return view('documentos.index', [
            'documentos' => $this->documentoService->listarPaginado(),
        ]);
    }

    public function create(): View
    {
        return view('documentos.create');
    }

    public function store(StoreDocumentoRequest $request): RedirectResponse
    {
        $documento = $this->documentoService->criarAPartirDoUpload(
            $request->only(['titulo', 'tipo', 'pessoa', 'observacoes']),
            $request->file('arquivo'),
        );

        return redirect()
            ->route('documentos.show', $documento)
            ->with('success', 'Comprovante enviado! A extração dos metadados e itens está em processamento.');
    }

    public function show(int $documento): View
    {
        return view('documentos.show', [
            'documento' => $this->documentoService->buscar($documento),
            'itens' => $this->itemDocumentoService->listarPorDocumento($documento),
        ]);
    }

    public function edit(int $documento): View
    {
        return view('documentos.edit', [
            'documento' => $this->documentoService->buscar($documento),
        ]);
    }

    public function update(UpdateDocumentoRequest $request, int $documento): RedirectResponse
    {
        $this->documentoService->atualizar(
            $documento,
            $request->only(['titulo', 'tipo', 'valor', 'data_documento', 'fornecedor', 'pessoa', 'observacoes']),
            $request->file('arquivo'),
        );

        return redirect()
            ->route('documentos.show', $documento)
            ->with('success', 'Comprovante atualizado com sucesso.');
    }

    public function reprocessar(int $documento): RedirectResponse
    {
        $this->documentoService->reprocessarOcr($documento);

        return redirect()
            ->route('documentos.show', $documento)
            ->with('success', 'Reprocessamento enviado para a fila.');
    }

    public function destroy(int $documento): RedirectResponse
    {
        $this->documentoService->excluir($documento);

        return redirect()
            ->route('documentos.index')
            ->with('success', 'Comprovante excluído.');
    }

    public function arquivo(int $documento): StreamedResponse
    {
        return $this->documentoService->baixarArquivo($documento);
    }
}
