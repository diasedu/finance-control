<?php

namespace App\Repositories;

use App\Models\Documento;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Implementação Eloquent do repositório de Documento.
 *
 * Única classe da aplicação autorizada a montar queries sobre a tabela
 * documentos.
 */
class EloquentDocumentoRepository implements DocumentoRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Documento::query()
            ->latest('id')
            ->paginate($perPage);
    }

    public function findOrFail(int $id): Documento
    {
        return Documento::query()->findOrFail($id);
    }

    public function create(array $attributes): Documento
    {
        return Documento::query()->create($attributes);
    }

    public function update(Documento $documento, array $attributes): Documento
    {
        $documento->update($attributes);

        return $documento;
    }

    public function delete(Documento $documento): void
    {
        $documento->delete();
    }
}
