<?php

namespace App\Repositories;

use App\Models\Documento;
use App\Models\ItemDocumento;
use Illuminate\Support\Collection;

/**
 * Implementação Eloquent do repositório de ItemDocumento.
 * Única classe autorizada a montar queries sobre itens_documento.
 */
class EloquentItemDocumentoRepository implements ItemDocumentoRepositoryInterface
{
    public function paraDocumento(int $documentoId): Collection
    {
        return ItemDocumento::query()
            ->where('documento_id', $documentoId)
            ->orderBy('ano')
            ->orderBy('mes')
            ->orderBy('id')
            ->get();
    }

    public function findOrFail(int $id): ItemDocumento
    {
        return ItemDocumento::query()->findOrFail($id);
    }

    public function create(array $atributos): ItemDocumento
    {
        return ItemDocumento::query()->create($atributos);
    }

    public function createMany(int $documentoId, array $listaAtributos): Collection
    {
        $criados = collect();

        foreach ($listaAtributos as $atributos) {
            $criados->push(
                ItemDocumento::query()->create([...$atributos, 'documento_id' => $documentoId])
            );
        }

        return $criados;
    }

    public function update(ItemDocumento $item, array $atributos): ItemDocumento
    {
        $item->update($atributos);

        return $item;
    }

    public function delete(ItemDocumento $item): void
    {
        $item->delete();
    }

    public function deleteParaDocumento(int $documentoId): void
    {
        ItemDocumento::query()->where('documento_id', $documentoId)->delete();
    }

    public function resumoPorPeriodo(int $mesInicio, int $anoInicio, int $mesFim, int $anoFim, ?string $classificacao = null, ?string $pessoa = null): array
    {
        $query = ItemDocumento::query()
            ->whereRaw('(ano * 100 + mes) >= ?', [$anoInicio * 100 + $mesInicio])
            ->whereRaw('(ano * 100 + mes) <= ?', [$anoFim * 100 + $mesFim])
            ->when($classificacao, fn ($q) => $q->where('classificacao', $classificacao))
            ->when($pessoa, fn ($q) => $q->whereHas('documento', fn ($d) => $d->where('pessoa', $pessoa)));

        $totalEntradas = (clone $query)->where('tipo', 'entrada')->sum('valor');
        $totalSaidas = (clone $query)->where('tipo', 'saida')->sum('valor');
        $quantidadeItens = $query->count();

        return [
            'totalEntradas' => (float) $totalEntradas,
            'totalSaidas' => (float) $totalSaidas,
            'quantidadeItens' => $quantidadeItens,
        ];
    }

    public function itensPorMesNoPeriodo(int $mesInicio, int $anoInicio, int $mesFim, int $anoFim, ?string $classificacao = null, ?string $pessoa = null): Collection
    {
        return ItemDocumento::query()
            ->selectRaw('mes, ano, tipo, SUM(valor) as total')
            ->whereRaw('(ano * 100 + mes) >= ?', [$anoInicio * 100 + $mesInicio])
            ->whereRaw('(ano * 100 + mes) <= ?', [$anoFim * 100 + $mesFim])
            ->when($classificacao, fn ($q) => $q->where('classificacao', $classificacao))
            ->when($pessoa, fn ($q) => $q->whereHas('documento', fn ($d) => $d->where('pessoa', $pessoa)))
            ->groupBy('ano', 'mes', 'tipo')
            ->orderBy('ano')
            ->orderBy('mes')
            ->get();
    }

    public function listarClassificacoes(): Collection
    {
        return ItemDocumento::query()
            ->whereNotNull('classificacao')
            ->where('classificacao', '!=', '')
            ->distinct()
            ->orderBy('classificacao')
            ->pluck('classificacao');
    }

    public function listarPessoas(): Collection
    {
        return Documento::query()
            ->whereNotNull('pessoa')
            ->where('pessoa', '!=', '')
            ->distinct()
            ->orderBy('pessoa')
            ->pluck('pessoa');
    }

    public function itensDoMes(int $mes, int $ano, ?string $classificacao = null, ?string $pessoa = null): Collection
    {
        return ItemDocumento::query()
            ->with('documento')
            ->where('mes', $mes)
            ->where('ano', $ano)
            ->when($classificacao, fn ($q) => $q->where('classificacao', $classificacao))
            ->when($pessoa, fn ($q) => $q->whereHas('documento', fn ($d) => $d->where('pessoa', $pessoa)))
            ->orderBy('tipo')
            ->orderByDesc('valor')
            ->get();
    }
}
