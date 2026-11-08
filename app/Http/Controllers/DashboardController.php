<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller fino: apenas valida/normaliza o filtro de período e
 * repassa ao DashboardService para montagem dos indicadores.
 */
class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService,
    ) {
    }

    public function index(): View
    {
        $anoAtual = (int) now()->format('Y');
        $mesAtual = (int) now()->format('n');

        return view('dashboard.index', [
            'mesInicioDefault' => 1,
            'anoInicioDefault' => $anoAtual,
            'mesFimDefault' => $mesAtual,
            'anoFimDefault' => $anoAtual,
            'meses' => $this->listaMeses(),
            'anos' => range($anoAtual - 5, $anoAtual + 1),
            'classificacoes' => $this->dashboardService->listarClassificacoes(),
            'pessoas' => $this->dashboardService->listarPessoas(),
        ]);
    }

    public function dados(Request $request): JsonResponse
    {
        [$mesInicio, $anoInicio, $mesFim, $anoFim] = $this->normalizarPeriodo($request);
        $classificacao = $request->input('classificacao') ?: null;
        $pessoa = $request->input('pessoa') ?: null;

        return response()->json([
            'resumo' => $this->dashboardService->resumo($mesInicio, $anoInicio, $mesFim, $anoFim, $classificacao, $pessoa),
            'evolucao' => $this->dashboardService->evolucaoMensal($mesInicio, $anoInicio, $mesFim, $anoFim, $classificacao, $pessoa)->values(),
        ]);
    }

    public function itensDoMes(Request $request): JsonResponse
    {
        $mes = max(1, min(12, (int) $request->input('mes')));
        $ano = (int) $request->input('ano');
        $classificacao = $request->input('classificacao') ?: null;
        $pessoa = $request->input('pessoa') ?: null;

        return response()->json([
            'itens' => $this->dashboardService->itensDoMes($mes, $ano, $classificacao, $pessoa),
        ]);
    }

    /** @return array{0: int, 1: int, 2: int, 3: int} */
    private function normalizarPeriodo(Request $request): array
    {
        $anoAtual = (int) now()->format('Y');
        $mesAtual = (int) now()->format('n');

        $mesInicio = max(1, min(12, (int) $request->input('mes_inicio', 1)));
        $anoInicio = (int) $request->input('ano_inicio', $anoAtual);
        $mesFim = max(1, min(12, (int) $request->input('mes_fim', $mesAtual)));
        $anoFim = (int) $request->input('ano_fim', $anoAtual);

        if ($anoInicio * 100 + $mesInicio > $anoFim * 100 + $mesFim) {
            return [$mesFim, $anoFim, $mesInicio, $anoInicio];
        }

        return [$mesInicio, $anoInicio, $mesFim, $anoFim];
    }

    /** @return array<int, string> */
    private function listaMeses(): array
    {
        return [
            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março',
            4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
            7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro',
            10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro',
        ];
    }
}
