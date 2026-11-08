@extends('layouts.app')

@section('titulo', 'Comprovantes')

@section('conteudo')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Comprovantes</h1>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500 dark:bg-gray-800/60 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3">Título / Arquivo</th>
                    <th class="px-4 py-3">Tipo</th>
                    <th class="px-4 py-3">Fornecedor</th>
                    <th class="px-4 py-3">Pessoa</th>
                    <th class="px-4 py-3">Data</th>
                    <th class="px-4 py-3">Valor</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3"><span class="sr-only">Ações</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($documentos as $documento)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40">
                        <td class="px-4 py-3">
                            <a href="{{ route('documentos.show', $documento) }}" class="font-medium text-gray-900 hover:underline dark:text-gray-100">
                                {{ $documento->titulo ?: $documento->arquivo_nome_original }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $documento->tipo ?: '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $documento->fornecedor ?: '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $documento->pessoa ?: '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            {{ $documento->data_documento?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            {{ $documento->valor !== null ? 'R$ '.number_format($documento->valor, 2, ',', '.') : '—' }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $documento->status->badgeClasses() }}">
                                {{ $documento->status->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-3 text-sm">
                                <a href="{{ route('documentos.show', $documento) }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                                    Ver
                                </a>
                                <a href="{{ route('documentos.edit', $documento) }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                                    Editar
                                </a>
                                <form action="{{ route('documentos.destroy', $documento) }}" method="POST" onsubmit="return confirm('Excluir este comprovante?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-800 dark:text-rose-400 dark:hover:text-rose-300">
                                        Excluir
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">
                            Nenhum comprovante cadastrado ainda.
                            <a href="{{ route('documentos.create') }}" class="font-medium text-gray-900 hover:underline dark:text-gray-100">
                                Enviar o primeiro
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $documentos->links() }}
    </div>
@endsection
