<?php

namespace App\Services;

use App\Enums\TipoLancamento;
use App\Repositories\ItemDocumentoRepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Agrega os dados de itens para a tela de dashboard financeiro.
 */
class DashboardService
{
    public function __construct(
        private readonly ItemDocumentoRepositoryInterface $repository,
    ) {
    }

    /**
     * @return array{totalEntradas: float, totalSaidas: float, saldo: float, quantidadeItens: int}
     */
    public function resumo(int $mesInicio, int $anoInicio, int $mesFim, int $anoFim, ?string $classificacao = null, ?string $pessoa = null): array
    {
        $dados = $this->repository->resumoPorPeriodo($mesInicio, $anoInicio, $mesFim, $anoFim, $classificacao, $pessoa);

        return [
            ...$dados,
            'saldo' => $dados['totalEntradas'] - $dados['totalSaidas'],
        ];
    }

    /**
     * Retorna colunas mensais para o gráfico/tabela de evolução.
     *
     * @return Collection<int, array{rotulo: string, mes: int, ano: int, totalEntradas: float, totalSaidas: float, saldo: float}>
     */
    public function evolucaoMensal(int $mesInicio, int $anoInicio, int $mesFim, int $anoFim, ?string $classificacao = null, ?string $pessoa = null): Collection
    {
        $linhas = $this->repository->itensPorMesNoPeriodo($mesInicio, $anoInicio, $mesFim, $anoFim, $classificacao, $pessoa);

        // Indexa por "ano-mes" para combinar entradas e saídas
        $meses = collect();
        foreach ($linhas as $linha) {
            $chave = sprintf('%04d-%02d', $linha->ano, $linha->mes);

            if (! $meses->has($chave)) {
                $meses->put($chave, [
                    'rotulo' => $this->rotuloMes((int) $linha->mes, (int) $linha->ano),
                    'mes' => (int) $linha->mes,
                    'ano' => (int) $linha->ano,
                    'totalEntradas' => 0.0,
                    'totalSaidas' => 0.0,
                    'saldo' => 0.0,
                ]);
            }

            $atual = $meses->get($chave);
            $tipo = $linha->tipo instanceof TipoLancamento ? $linha->tipo->value : $linha->tipo;

            if ($tipo === 'entrada') {
                $atual['totalEntradas'] = (float) $linha->total;
            } else {
                $atual['totalSaidas'] = (float) $linha->total;
            }

            $atual['saldo'] = $atual['totalEntradas'] - $atual['totalSaidas'];
            $meses->put($chave, $atual);
        }

        return $meses->sortKeys()->values();
    }

    /**
     * Itens de um mês específico, para validação/detalhe ao expandir a linha.
     *
     * @return Collection<int, array{id: int, item: string, valor: float, tipo: string, classificacao: ?string, documentoId: int, documentoTitulo: string}>
     */
    public function itensDoMes(int $mes, int $ano, ?string $classificacao = null, ?string $pessoa = null): Collection
    {
        return $this->repository->itensDoMes($mes, $ano, $classificacao, $pessoa)->map(fn ($item) => [
            'id' => $item->id,
            'item' => $item->item,
            'valor' => (float) $item->valor,
            'tipo' => $item->tipo->value,
            'classificacao' => $item->classificacao,
            'documentoId' => $item->documento_id,
            'documentoTitulo' => $item->documento?->titulo ?: $item->documento?->arquivo_nome_original ?: 'Comprovante #'.$item->documento_id,
        ])->values();
    }

    /**
     * @return Collection<int, string>
     */
    public function listarClassificacoes(): Collection
    {
        return $this->repository->listarClassificacoes();
    }

    /**
     * @return Collection<int, string>
     */
    public function listarPessoas(): Collection
    {
        return $this->repository->listarPessoas();
    }

    private function rotuloMes(int $mes, int $ano): string
    {
        $nomes = [
            1 => 'Jan', 2 => 'Fev', 3 => 'Mar', 4 => 'Abr',
            5 => 'Mai', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
            9 => 'Set', 10 => 'Out', 11 => 'Nov', 12 => 'Dez',
        ];

        return ($nomes[$mes] ?? $mes).'/'.$ano;
    }
}
