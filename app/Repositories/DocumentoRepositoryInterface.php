<?php

namespace App\Repositories;

use App\Models\Documento;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Contrato de acesso a dados de Documento.
 *
 * Toda query relacionada a Documento deve passar por aqui — controllers
 * e services nunca falam diretamente com o Eloquent/DB.
 */
interface DocumentoRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function findOrFail(int $id): Documento;

    public function create(array $attributes): Documento;

    public function update(Documento $documento, array $attributes): Documento;

    public function delete(Documento $documento): void;
}
