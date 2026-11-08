@extends('layouts.app')

@section('titulo', 'Dashboard')

@section('conteudo')

{{-- Cabeçalho + filtro --}}
<div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <p class="text-xs font-medium uppercase tracking-widest text-gray-400 dark:text-gray-500">Finance Control</p>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Dashboard</h1>
    </div>

    <form id="form-filtro" class="flex flex-wrap items-end gap-3">
        <div class="flex flex-col gap-1">
            <span class="text-xs font-medium text-gray-400 dark:text-gray-500">De</span>
            <div class="flex gap-1.5">
                <div class="relative">
                    <select name="mes_inicio" class="h-9 appearance-none rounded-lg bg-gray-100 pl-3 pr-8 text-sm text-gray-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-gray-900 dark:bg-gray-800 dark:text-gray-200 dark:focus:bg-gray-800 dark:focus:ring-gray-100">
                        @foreach ($meses as $num => $nome)
                            <option value="{{ $num }}" @selected($num === $mesInicioDefault)>{{ $nome }}</option>
                        @endforeach
                    </select>
                    <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-gray-500 dark:text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
                </div>
                <div class="relative">
                    <select name="ano_inicio" class="h-9 appearance-none rounded-lg bg-gray-100 pl-3 pr-8 text-sm text-gray-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-gray-900 dark:bg-gray-800 dark:text-gray-200 dark:focus:bg-gray-800 dark:focus:ring-gray-100">
                        @foreach ($anos as $ano)
                            <option value="{{ $ano }}" @selected($ano === $anoInicioDefault)>{{ $ano }}</option>
                        @endforeach
                    </select>
                    <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-gray-500 dark:text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
                </div>
            </div>
        </div>
        <div class="flex flex-col gap-1">
            <span class="text-xs font-medium text-gray-400 dark:text-gray-500">Até</span>
            <div class="flex gap-1.5">
                <div class="relative">
                    <select name="mes_fim" class="h-9 appearance-none rounded-lg bg-gray-100 pl-3 pr-8 text-sm text-gray-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-gray-900 dark:bg-gray-800 dark:text-gray-200 dark:focus:bg-gray-800 dark:focus:ring-gray-100">
                        @foreach ($meses as $num => $nome)
                            <option value="{{ $num }}" @selected($num === $mesFimDefault)>{{ $nome }}</option>
                        @endforeach
                    </select>
                    <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-gray-500 dark:text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
                </div>
                <div class="relative">
                    <select name="ano_fim" class="h-9 appearance-none rounded-lg bg-gray-100 pl-3 pr-8 text-sm text-gray-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-gray-900 dark:bg-gray-800 dark:text-gray-200 dark:focus:bg-gray-800 dark:focus:ring-gray-100">
                        @foreach ($anos as $ano)
                            <option value="{{ $ano }}" @selected($ano === $anoFimDefault)>{{ $ano }}</option>
                        @endforeach
                    </select>
                    <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-gray-500 dark:text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
                </div>
            </div>
        </div>
        <div class="flex flex-col gap-1">
            <span class="text-xs font-medium text-gray-400 dark:text-gray-500">Tipo de gasto</span>
            <div class="relative">
                <select name="classificacao" class="h-9 w-44 appearance-none rounded-lg bg-gray-100 pl-3 pr-8 text-sm text-gray-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-gray-900 dark:bg-gray-800 dark:text-gray-200 dark:focus:bg-gray-800 dark:focus:ring-gray-100">
                    <option value="">Todos</option>
                    @foreach ($classificacoes as $classificacao)
                        <option value="{{ $classificacao }}">{{ $classificacao }}</option>
                    @endforeach
                </select>
                <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-gray-500 dark:text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
            </div>
        </div>
        <div class="flex flex-col gap-1">
            <span class="text-xs font-medium text-gray-400 dark:text-gray-500">Pessoa</span>
            <div class="relative">
                <select name="pessoa" class="h-9 w-44 appearance-none rounded-lg bg-gray-100 pl-3 pr-8 text-sm text-gray-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-gray-900 dark:bg-gray-800 dark:text-gray-200 dark:focus:bg-gray-800 dark:focus:ring-gray-100">
                    <option value="">Todas</option>
                    @foreach ($pessoas as $pessoa)
                        <option value="{{ $pessoa }}">{{ $pessoa }}</option>
                    @endforeach
                </select>
                <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-gray-500 dark:text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
            </div>
        </div>
        <button id="btn-filtrar" type="submit"
            class="h-9 rounded-lg bg-gray-900 px-5 text-sm font-medium text-white hover:bg-gray-700 disabled:opacity-40 dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-gray-300">
            Aplicar
        </button>
    </form>
</div>

{{-- KPIs --}}
<div id="kpis" class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-3"></div>

{{-- Gráfico --}}
<div id="chart-wrapper" class="mb-8 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100 dark:bg-gray-900 dark:ring-gray-800">
    <div class="mb-4 flex items-center justify-between">
        <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">Evolução mensal</p>
        <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
            <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>Entradas</span>
            <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-rose-500"></span>Saídas</span>
        </div>
    </div>
    <div class="relative h-64">
        <canvas id="grafico"></canvas>
        <div id="chart-skeleton" class="absolute inset-0 flex flex-col justify-end gap-2 pb-6">
            <div class="flex items-end justify-around gap-2 h-full px-4">
                @foreach(range(1,6) as $i)
                <div class="flex flex-1 flex-col items-center gap-1">
                    <div class="animate-pulse w-full rounded-t bg-gray-200 dark:bg-gray-700" style="height: {{ rand(30,90) }}%"></div>
                    <div class="animate-pulse h-2 w-8 rounded bg-gray-200 dark:bg-gray-700"></div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Tabela detalhada --}}
<div id="tabela-container"></div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js" integrity="sha512-CQBWl4fJHWbryGE+Pc7UAxWMUMNMWzWxF4SQo9CgkJIN1kx6djDQZjh3Y8SZ1d+6I+1zze6Z7kHXO7q3UyZAWw==" crossorigin="anonymous"></script>
<script>
(function () {
    const form       = document.getElementById('form-filtro');
    const btnFiltrar = document.getElementById('btn-filtrar');
    const kpisEl     = document.getElementById('kpis');
    const tabelaEl   = document.getElementById('tabela-container');
    const chartSkel  = document.getElementById('chart-skeleton');
    const baseUrl    = '{{ route('dashboard.dados') }}';
    const itensUrl   = '{{ route('dashboard.itens') }}';
    let chart        = null;
    let ultimaEvolucao = [];
    const itensCache = new Map();

    function temaEscuro() {
        return document.documentElement.classList.contains('dark');
    }

    /* ── helpers ── */
    function brl(v) {
        return 'R$ ' + Number(v).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function skeletonLine(w) {
        return `<div class="animate-pulse h-3 rounded bg-gray-200 dark:bg-gray-700 ${w}"></div>`;
    }

    const iconeEntrada = `
        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a.75.75 0 01-.75-.75V4.56L5.03 8.78a.75.75 0 01-1.06-1.06l5.5-5.5a.75.75 0 011.06 0l5.5 5.5a.75.75 0 01-1.06 1.06l-4.22-4.22V17.25A.75.75 0 0110 18z" clip-rule="evenodd"/></svg>`;
    const iconeSaida = `
        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 2a.75.75 0 01.75.75v12.69l4.22-4.22a.75.75 0 111.06 1.06l-5.5 5.5a.75.75 0 01-1.06 0l-5.5-5.5a.75.75 0 111.06-1.06l4.22 4.22V2.75A.75.75 0 0110 2z" clip-rule="evenodd"/></svg>`;

    /* ── skeleton ── */
    function renderSkeleton() {
        const card = `
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100 dark:bg-gray-900 dark:ring-gray-800 space-y-3">
                ${skeletonLine('w-20')}
                <div class="animate-pulse h-8 rounded bg-gray-200 dark:bg-gray-700 w-36"></div>
                ${skeletonLine('w-16')}
            </div>`;
        kpisEl.innerHTML = card + card + card;

        chartSkel.classList.remove('hidden');

        const row = `
            <tr>
                <td class="px-5 py-3">${skeletonLine('w-16')}</td>
                <td class="px-5 py-3"><div class="ml-auto ${skeletonLine('w-24')}"></div></td>
                <td class="px-5 py-3"><div class="ml-auto ${skeletonLine('w-24')}"></div></td>
                <td class="px-5 py-3"><div class="ml-auto ${skeletonLine('w-20')}"></div></td>
            </tr>`;
        tabelaEl.innerHTML = `
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-100 dark:bg-gray-900 dark:ring-gray-800">
                <div class="border-b border-gray-50 px-5 py-4 dark:border-gray-800">
                    <div class="animate-pulse h-3 rounded bg-gray-200 dark:bg-gray-700 w-32"></div>
                </div>
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-400 dark:bg-gray-800/60 dark:text-gray-500">
                        <tr>
                            <th class="px-5 py-3">Mês</th>
                            <th class="px-5 py-3 text-right">Entradas</th>
                            <th class="px-5 py-3 text-right">Saídas</th>
                            <th class="px-5 py-3 text-right">Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">${row.repeat(4)}</tbody>
                </table>
            </div>`;
    }

    /* ── KPI cards ── */
    function renderKpis(resumo) {
        const saldoPos = resumo.saldo >= 0;
        kpisEl.innerHTML = `
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100 dark:bg-gray-900 dark:ring-gray-800">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-400">${iconeEntrada}</span>
                    <p class="text-xs font-semibold uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Entradas</p>
                </div>
                <p class="mt-3 text-3xl font-bold tracking-tight text-emerald-600 dark:text-emerald-400">${brl(resumo.totalEntradas)}</p>
                <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">${resumo.quantidadeItens} lançamento${resumo.quantidadeItens !== 1 ? 's' : ''} no período</p>
            </div>
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100 dark:bg-gray-900 dark:ring-gray-800">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-rose-100 text-rose-600 dark:bg-rose-500/15 dark:text-rose-400">${iconeSaida}</span>
                    <p class="text-xs font-semibold uppercase tracking-widest text-rose-600 dark:text-rose-400">Saídas</p>
                </div>
                <p class="mt-3 text-3xl font-bold tracking-tight text-rose-600 dark:text-rose-400">${brl(resumo.totalSaidas)}</p>
            </div>
            <div class="rounded-2xl p-6 shadow-sm ring-1 ${saldoPos ? 'bg-gray-900 ring-gray-800 dark:bg-gray-800 dark:ring-gray-700' : 'bg-rose-700 ring-rose-600 dark:bg-rose-800 dark:ring-rose-700'}">
                <p class="text-xs font-semibold uppercase tracking-widest ${saldoPos ? 'text-gray-400' : 'text-rose-200'}">Saldo</p>
                <p class="mt-2 text-3xl font-bold tracking-tight text-white">${(saldoPos ? '' : '−') + brl(Math.abs(resumo.saldo))}</p>
            </div>`;
    }

    /* ── gráfico ── */
    function renderChart(evolucao) {
        chartSkel.classList.add('hidden');

        const labels  = evolucao.map(l => l.rotulo);
        const entradas = evolucao.map(l => l.totalEntradas);
        const saidas   = evolucao.map(l => l.totalSaidas);
        const escuro   = temaEscuro();

        if (chart) { chart.destroy(); }

        chart = new Chart(document.getElementById('grafico'), {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    { label: 'Entradas', data: entradas, backgroundColor: '#10b981', borderRadius: 6, borderSkipped: false },
                    { label: 'Saídas',   data: saidas,   backgroundColor: '#f43f5e', borderRadius: 6, borderSkipped: false },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' ' + brl(ctx.parsed.y),
                        },
                    },
                },
                scales: {
                    x: { grid: { display: false }, border: { display: false }, ticks: { color: escuro ? '#6b7280' : '#9ca3af' } },
                    y: {
                        grid: { color: escuro ? '#1f2937' : '#f3f4f6' },
                        border: { display: false, dash: [4, 4] },
                        ticks: { color: escuro ? '#6b7280' : '#9ca3af', callback: v => 'R$ ' + Number(v).toLocaleString('pt-BR') },
                    },
                },
            },
        });
    }

    /* ── tabela ── */
    function renderTabela(evolucao) {
        itensCache.clear();

        if (evolucao.length === 0) {
            tabelaEl.innerHTML = `
                <div class="rounded-2xl bg-white px-5 py-12 text-center text-sm text-gray-400 shadow-sm ring-1 ring-gray-100 dark:bg-gray-900 dark:text-gray-500 dark:ring-gray-800">
                    Nenhum lançamento encontrado para o período selecionado.
                </div>`;
            return;
        }

        const linhas = evolucao.map(l => {
            const pos   = l.saldo >= 0;
            const cor   = pos ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400';
            const fmt   = (pos ? '' : '−') + brl(Math.abs(l.saldo));
            const chave = `${l.ano}-${l.mes}`;
            return `
                <tr class="linha-mes cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/60" data-chave="${chave}" data-mes="${l.mes}" data-ano="${l.ano}">
                    <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">
                        <span class="inline-flex items-center gap-2">
                            <svg class="chevron h-3.5 w-3.5 text-gray-400 transition-transform dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/></svg>
                            ${l.rotulo}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right tabular-nums font-medium text-emerald-600 dark:text-emerald-400">+ ${brl(l.totalEntradas)}</td>
                    <td class="px-5 py-3 text-right tabular-nums font-medium text-rose-600 dark:text-rose-400">− ${brl(l.totalSaidas)}</td>
                    <td class="px-5 py-3 text-right tabular-nums font-semibold ${cor}">${fmt}</td>
                </tr>
                <tr class="linha-detalhe hidden" data-chave="${chave}">
                    <td colspan="4" class="bg-gray-50/70 px-5 py-4 dark:bg-gray-800/40"></td>
                </tr>`;
        }).join('');

        tabelaEl.innerHTML = `
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-100 dark:bg-gray-900 dark:ring-gray-800">
                <div class="border-b border-gray-50 px-5 py-4 dark:border-gray-800">
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">Detalhamento mensal</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">Clique em um mês para ver os lançamentos individuais</p>
                </div>
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase tracking-widest text-gray-400 dark:bg-gray-800/60 dark:text-gray-500">
                        <tr>
                            <th class="px-5 py-3 font-medium">Mês</th>
                            <th class="px-5 py-3 text-right font-medium text-emerald-600 dark:text-emerald-400">Entradas</th>
                            <th class="px-5 py-3 text-right font-medium text-rose-600 dark:text-rose-400">Saídas</th>
                            <th class="px-5 py-3 text-right font-medium">Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">${linhas}</tbody>
                </table>
            </div>`;

        tabelaEl.querySelectorAll('.linha-mes').forEach(tr => {
            tr.addEventListener('click', () => toggleMes(tr));
        });
    }

    function itemLinhaHtml(item) {
        const entrada = item.tipo === 'entrada';
        const cor = entrada ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400';
        const badge = entrada
            ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-400'
            : 'bg-rose-100 text-rose-600 dark:bg-rose-500/15 dark:text-rose-400';
        const icone = entrada ? iconeEntrada : iconeSaida;
        return `
            <div class="flex items-center gap-3 py-2 text-sm">
                <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full ${badge}">${icone}</span>
                <div class="min-w-0 flex-1">
                    <p class="truncate font-medium text-gray-800 dark:text-gray-200">${item.item}</p>
                    <p class="truncate text-xs text-gray-400 dark:text-gray-500">
                        ${item.classificacao ? item.classificacao + ' · ' : ''}${item.documentoTitulo}
                    </p>
                </div>
                <span class="shrink-0 tabular-nums font-semibold ${cor}">
                    ${entrada ? '+' : '−'} ${brl(item.valor)}
                </span>
            </div>`;
    }

    async function toggleMes(trMes) {
        const chave = trMes.dataset.chave;
        const trDetalhe = tabelaEl.querySelector(`.linha-detalhe[data-chave="${chave}"]`);
        const celula = trDetalhe.querySelector('td');
        const chevron = trMes.querySelector('.chevron');
        const abrindo = trDetalhe.classList.contains('hidden');

        // fecha qualquer outra linha aberta
        tabelaEl.querySelectorAll('.linha-detalhe').forEach(tr => tr.classList.add('hidden'));
        tabelaEl.querySelectorAll('.chevron').forEach(c => c.classList.remove('rotate-90'));

        if (!abrindo) { return; }

        trDetalhe.classList.remove('hidden');
        chevron.classList.add('rotate-90');

        if (itensCache.has(chave)) {
            celula.innerHTML = itensCache.get(chave);
            return;
        }

        celula.innerHTML = `
            <div class="space-y-2">
                ${Array(3).fill(`<div class="animate-pulse h-8 rounded bg-gray-200 dark:bg-gray-700"></div>`).join('')}
            </div>`;

        try {
            const dadosForm = new FormData(form);
            const params = new URLSearchParams({
                mes: trMes.dataset.mes,
                ano: trMes.dataset.ano,
                classificacao: dadosForm.get('classificacao') || '',
                pessoa: dadosForm.get('pessoa') || '',
            });
            const res = await fetch(itensUrl + '?' + params, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) { throw new Error(); }
            const { itens } = await res.json();

            const html = itens.length
                ? `<div class="divide-y divide-gray-100 dark:divide-gray-800">${itens.map(itemLinhaHtml).join('')}</div>`
                : `<p class="py-2 text-sm text-gray-400 dark:text-gray-500">Nenhum lançamento encontrado para este mês.</p>`;

            itensCache.set(chave, html);
            celula.innerHTML = html;
        } catch {
            celula.innerHTML = `<p class="py-2 text-sm text-rose-600 dark:text-rose-400">Não foi possível carregar os lançamentos.</p>`;
        }
    }

    /* ── fetch ── */
    async function carregar() {
        btnFiltrar.disabled = true;
        renderSkeleton();

        try {
            const params = new URLSearchParams(new FormData(form));
            const res = await fetch(baseUrl + '?' + params, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) { throw new Error(); }
            const { resumo, evolucao } = await res.json();
            ultimaEvolucao = evolucao;
            renderKpis(resumo);
            renderChart(evolucao);
            renderTabela(evolucao);
        } catch {
            kpisEl.innerHTML = `
                <div class="col-span-3 rounded-2xl bg-rose-50 px-5 py-4 text-sm text-rose-700 ring-1 ring-rose-200 dark:bg-rose-500/10 dark:text-rose-400 dark:ring-rose-500/30">
                    Não foi possível carregar os dados. Tente novamente.
                </div>`;
            chartSkel.classList.add('hidden');
            tabelaEl.innerHTML = '';
        } finally {
            btnFiltrar.disabled = false;
        }
    }

    form.addEventListener('submit', e => { e.preventDefault(); carregar(); });

    // Reconstrói o gráfico com as cores corretas ao alternar o tema
    // (setTimeout garante que a classe "dark" já foi alternada pelo listener do layout)
    document.getElementById('botao-tema')?.addEventListener('click', () => {
        if (chart) { setTimeout(() => renderChart(ultimaEvolucao), 0); }
    });

    carregar();
}());
</script>
@endpush
