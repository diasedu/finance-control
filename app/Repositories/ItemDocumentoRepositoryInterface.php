<?php

namespace App\Repositories;

use App\Models\ItemDocumento;
use Illuminate\Support\Collection;

/**
 * Contrato de acesso a dados de ItemDocumento.
 */
interface ItemDocumentoRepositoryInterface
{
    public function paraDocumento(int $documentoId): Collection;

    public function findOrFail(int $id): ItemDocumento;

    public function create(array $atributos): ItemDocumento;

    /**
     * @param  array<int, array<string, mixed>>  $listaAtributos
     * @return Collection<int, ItemDocumento>
     */
    public function createMany(int $documentoId, array $listaAtributos): Collection;

    public function update(ItemDocumento $item, array $atributos): ItemDocumento;

    public function delete(ItemDocumento $item): void;

    public function deleteParaDocumento(int $documentoId): void;

    /**
     * Soma entradas e saídas dos itens dentro de um intervalo de mês/ano.
     *
     * @return array{totalEntradas: float, totalSaidas: float, quantidadeItens: int}
     */
    public function resumoPorPeriodo(int $mesInicio, int $anoInicio, int $mesFim, int $anoFim, ?string $classificacao = null, ?string $pessoa = null): array;

    /**
     * Retorna os itens agrupados por mês/ano dentro do intervalo.
     *
     * @return Collection<int, object{mes: int, ano: int, totalEntradas: float, totalSaidas: float}>
     */
    public function itensPorMesNoPeriodo(int $mesInicio, int $anoInicio, int $mesFim, int $anoFim, ?string $classificacao = null, ?string $pessoa = null): Collection;

    /**
     * Lista as classificações distintas já cadastradas (para popular o filtro).
     *
     * @return Collection<int, string>
     */
    public function listarClassificacoes(): Collection;

    /**
     * Lista as pessoas distintas já cadastradas nos comprovantes (para popular o filtro).
     *
     * @return Collection<int, string>
     */
    public function listarPessoas(): Collection;

    /**
     * Lista os itens (com o documento de origem) de um mês/ano específico.
     *
     * @return Collection<int, ItemDocumento>
     */
    public function itensDoMes(int $mes, int $ano, ?string $classificacao = null, ?string $pessoa = null): Collection;
}
