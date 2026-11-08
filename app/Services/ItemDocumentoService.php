<?php

namespace App\Services;

use App\Models\ItemDocumento;
use App\Repositories\ItemDocumentoRepositoryInterface;
use Illuminate\Support\Collection;

/**
 * CRUD manual dos itens/lançamentos de um documento (independente da
 * extração automática via LLM, que fica em DocumentoService).
 */
class ItemDocumentoService
{
    public function __construct(
        private readonly ItemDocumentoRepositoryInterface $repository,
    ) {
    }

    public function listarPorDocumento(int $documentoId): Collection
    {
        return $this->repository->paraDocumento($documentoId);
    }

    public function buscar(int $id): ItemDocumento
    {
        return $this->repository->findOrFail($id);
    }

    public function adicionar(int $documentoId, array $dados): ItemDocumento
    {
        return $this->repository->create([
            'documento_id' => $documentoId,
            'mes' => $dados['mes'],
            'ano' => $dados['ano'],
            'item' => $dados['item'],
            'valor' => $dados['valor'],
            'classificacao' => $dados['classificacao'] ?? null,
            'tipo' => $dados['tipo'],
        ]);
    }

    public function atualizar(int $id, array $dados): ItemDocumento
    {
        $item = $this->repository->findOrFail($id);

        return $this->repository->update($item, [
            'mes' => $dados['mes'],
            'ano' => $dados['ano'],
            'item' => $dados['item'],
            'valor' => $dados['valor'],
            'classificacao' => $dados['classificacao'] ?? null,
            'tipo' => $dados['tipo'],
        ]);
    }

    public function excluir(int $id): void
    {
        $this->repository->delete($this->repository->findOrFail($id));
    }
}
