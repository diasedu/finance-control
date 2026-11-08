@extends('layouts.app')

@section('titulo', $documento->titulo ?: $documento->arquivo_nome_original)

@section('conteudo')
    <div class="mx-auto max-w-3xl">
        <div class="mb-6 flex items-start justify-between">
            <div>
                <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                    {{ $documento->titulo ?: $documento->arquivo_nome_original }}
                </h1>
                <span class="mt-2 inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $documento->status->badgeClasses() }}">
                    {{ $documento->status->label() }}
                </span>
            </div>
            <div class="flex items-center gap-3 text-sm">
                <a href="{{ route('documentos.arquivo', $documento) }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                    Baixar PDF
                </a>
                <a href="{{ route('documentos.edit', $documento) }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                    Editar
                </a>
                <form action="{{ route('documentos.reprocessar', $documento) }}" method="POST">
                    @csrf
                    <button type="submit" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                        Reprocessar extração
                    </button>
                </form>
            </div>
        </div>

        @if ($documento->status === \App\Enums\DocumentoStatus::Erro && $documento->erro_processamento)
            <div class="mb-6 rounded-md border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-400">
                <p class="font-medium">A extração automática falhou:</p>
                <p class="mt-1">{{ $documento->erro_processamento }}</p>
            </div>
        @endif

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <dl class="grid grid-cols-2 gap-6 text-sm">
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Tipo</dt>
                    <dd class="mt-1 font-medium text-gray-900 dark:text-gray-100">{{ $documento->tipo ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Fornecedor</dt>
                    <dd class="mt-1 font-medium text-gray-900 dark:text-gray-100">{{ $documento->fornecedor ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Pessoa</dt>
                    <dd class="mt-1 font-medium text-gray-900 dark:text-gray-100">{{ $documento->pessoa ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Data do documento</dt>
                    <dd class="mt-1 font-medium text-gray-900 dark:text-gray-100">
                        {{ $documento->data_documento?->format('d/m/Y') ?? '—' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Valor total</dt>
                    <dd class="mt-1 font-medium text-gray-900 dark:text-gray-100">
                        {{ $documento->valor !== null ? 'R$ '.number_format($documento->valor, 2, ',', '.') : '—' }}
                    </dd>
                </div>
                <div class="col-span-2">
                    <dt class="text-gray-500 dark:text-gray-400">Observações</dt>
                    <dd class="mt-1 font-medium text-gray-900 dark:text-gray-100">{{ $documento->observacoes ?: '—' }}</dd>
                </div>
                <div class="col-span-2">
                    <dt class="text-gray-500 dark:text-gray-400">Arquivo original</dt>
                    <dd class="mt-1 font-medium text-gray-900 dark:text-gray-100">{{ $documento->arquivo_nome_original }}</dd>
                </div>
            </dl>
        </div>

        {{-- Itens do comprovante: toda a adição/edição/exclusão abaixo acontece via AJAX,
             sem recarregar a página (ver script no fim da view). --}}
        <div class="mt-8">
            <h2 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Itens do comprovante</h2>

            <div id="itens-erros" class="mb-3 hidden rounded-md border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-400"></div>

            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500 dark:bg-gray-800/60 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3">Mês/Ano</th>
                            <th class="px-4 py-3">Item</th>
                            <th class="px-4 py-3">Tipo</th>
                            <th class="px-4 py-3">Classificação</th>
                            <th class="px-4 py-3">Valor</th>
                            <th class="px-4 py-3"><span class="sr-only">Ações</span></th>
                        </tr>
                    </thead>
                    <tbody id="itens-tbody" class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($itens as $item)
                            @include('documentos.itens._linha', ['item' => $item])
                        @empty
                        @endforelse
                    </tbody>
                </table>

                <p id="itens-vazio" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400 {{ $itens->isNotEmpty() ? 'hidden' : '' }}">
                    Nenhum item cadastrado ainda.
                </p>
            </div>

            <form
                id="form-item"
                data-documento-id="{{ $documento->id }}"
                class="mt-4 grid grid-cols-6 gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900"
            >
                <input type="hidden" id="item_id" value="">

                <div class="col-span-1">
                    <label for="item_mes" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Mês</label>
                    <input type="number" min="1" max="12" id="item_mes" required
                        class="mt-1 block w-full rounded-md border border-gray-300 px-2 py-1.5 text-sm focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                </div>
                <div class="col-span-1">
                    <label for="item_ano" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Ano</label>
                    <input type="number" min="2000" max="2100" id="item_ano" required
                        class="mt-1 block w-full rounded-md border border-gray-300 px-2 py-1.5 text-sm focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                </div>
                <div class="col-span-2">
                    <label for="item_descricao" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Item</label>
                    <input type="text" id="item_descricao" required
                        class="mt-1 block w-full rounded-md border border-gray-300 px-2 py-1.5 text-sm focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                </div>
                <div class="col-span-1">
                    <label for="item_valor" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Valor</label>
                    <input type="number" step="0.01" min="0" id="item_valor" required
                        class="mt-1 block w-full rounded-md border border-gray-300 px-2 py-1.5 text-sm focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                </div>
                <div class="col-span-1">
                    <label for="item_tipo" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Tipo</label>
                    <select id="item_tipo" required
                        class="mt-1 block w-full appearance-none rounded-md border border-gray-300 bg-white px-2 py-1.5 text-sm focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                        <option value="entrada">Entrada</option>
                        <option value="saida" selected>Saída</option>
                    </select>
                </div>

                <div class="col-span-4">
                    <label for="item_classificacao" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Classificação</label>
                    <input type="text" id="item_classificacao" placeholder="Ex: Alimentação, Transporte, Salário..."
                        class="mt-1 block w-full rounded-md border border-gray-300 px-2 py-1.5 text-sm focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:placeholder:text-gray-500">
                </div>

                <div class="col-span-2 flex items-end justify-end gap-2">
                    <button type="button" id="item_cancelar" class="hidden rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                        Cancelar
                    </button>
                    <button type="submit" id="item_submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-gray-300">
                        Adicionar item
                    </button>
                </div>
            </form>
        </div>

        @if ($documento->dados_extraidos)
            <div class="mt-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <h2 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Dados brutos extraídos pela LLM</h2>
                <pre class="overflow-x-auto rounded-md bg-gray-50 p-4 text-xs text-gray-700 dark:bg-gray-800/60 dark:text-gray-300">{{ json_encode($documento->dados_extraidos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        @endif

        <div class="mt-6">
            <form action="{{ route('documentos.destroy', $documento) }}" method="POST" onsubmit="return confirm('Excluir este comprovante?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm text-rose-600 hover:text-rose-800 dark:text-rose-400 dark:hover:text-rose-300">
                    Excluir comprovante
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const documentoId = document.getElementById('form-item').dataset.documentoId;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            const tbody = document.getElementById('itens-tbody');
            const vazio = document.getElementById('itens-vazio');
            const erros = document.getElementById('itens-erros');
            const form = document.getElementById('form-item');
            const inputId = document.getElementById('item_id');
            const inputMes = document.getElementById('item_mes');
            const inputAno = document.getElementById('item_ano');
            const inputDescricao = document.getElementById('item_descricao');
            const inputValor = document.getElementById('item_valor');
            const inputTipo = document.getElementById('item_tipo');
            const inputClassificacao = document.getElementById('item_classificacao');
            const btnSubmit = document.getElementById('item_submit');
            const btnCancelar = document.getElementById('item_cancelar');

            function mostrarErros(mensagens) {
                const lista = Array.isArray(mensagens)
                    ? mensagens
                    : Object.values(mensagens || {}).flat();

                erros.innerHTML = lista.map((m) => `<p>${m}</p>`).join('');
                erros.classList.remove('hidden');
            }

            function limparErros() {
                erros.classList.add('hidden');
                erros.innerHTML = '';
            }

            function atualizarEstadoVazio() {
                vazio.classList.toggle('hidden', tbody.children.length > 0);
            }

            function resetarFormulario() {
                form.reset();
                inputId.value = '';
                inputTipo.value = 'saida';
                btnSubmit.textContent = 'Adicionar item';
                btnCancelar.classList.add('hidden');
            }

            function preencherParaEdicao(linha) {
                inputId.value = linha.dataset.itemId;
                inputMes.value = linha.dataset.mes;
                inputAno.value = linha.dataset.ano;
                inputDescricao.value = linha.dataset.itemNome;
                inputValor.value = linha.dataset.valor;
                inputTipo.value = linha.dataset.tipo;
                inputClassificacao.value = linha.dataset.classificacao || '';
                btnSubmit.textContent = 'Salvar alterações';
                btnCancelar.classList.remove('hidden');
                form.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            async function excluirLinha(linha) {
                if (!confirm('Excluir este item?')) {
                    return;
                }

                const id = linha.dataset.itemId;

                const resposta = await fetch(`/itens/${id}`, {
                    method: 'DELETE',
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });

                if (resposta.ok) {
                    linha.remove();
                    atualizarEstadoVazio();
                }
            }

            tbody.addEventListener('click', (evento) => {
                const linha = evento.target.closest('tr');
                if (!linha) return;

                if (evento.target.closest('.editar-item')) {
                    preencherParaEdicao(linha);
                }

                if (evento.target.closest('.excluir-item')) {
                    excluirLinha(linha);
                }
            });

            btnCancelar.addEventListener('click', resetarFormulario);

            form.addEventListener('submit', async (evento) => {
                evento.preventDefault();
                limparErros();

                const payload = {
                    mes: inputMes.value,
                    ano: inputAno.value,
                    item: inputDescricao.value,
                    valor: inputValor.value,
                    classificacao: inputClassificacao.value,
                    tipo: inputTipo.value,
                };

                const editando = Boolean(inputId.value);
                const url = editando ? `/itens/${inputId.value}` : `/documentos/${documentoId}/itens`;
                const method = editando ? 'PUT' : 'POST';

                btnSubmit.disabled = true;

                try {
                    const resposta = await fetch(url, {
                        method,
                        headers: {
                            'Content-Type': 'application/json',
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify(payload),
                    });

                    const dados = await resposta.json();

                    if (!resposta.ok) {
                        mostrarErros(dados.errors || [dados.message]);
                        return;
                    }

                    if (editando) {
                        const linhaAtual = tbody.querySelector(`[data-item-id="${inputId.value}"]`);
                        linhaAtual.outerHTML = dados.html;
                    } else {
                        tbody.insertAdjacentHTML('beforeend', dados.html);
                    }

                    atualizarEstadoVazio();
                    resetarFormulario();
                } finally {
                    btnSubmit.disabled = false;
                }
            });
        })();
    </script>
@endpush
