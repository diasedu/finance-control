@extends('layouts.app')

@section('titulo', 'Editar comprovante')

@section('conteudo')
    <div class="mx-auto max-w-2xl">
        <h1 class="mb-6 text-xl font-semibold text-gray-900 dark:text-gray-100">Editar comprovante</h1>

        <form
            action="{{ route('documentos.update', $documento) }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-5 rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900"
        >
            @csrf
            @method('PUT')

            <div>
                <label for="titulo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Título</label>
                <input
                    type="text"
                    name="titulo"
                    id="titulo"
                    value="{{ old('titulo', $documento->titulo) }}"
                    class="mt-1.5 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:focus:border-gray-500 dark:focus:ring-gray-500"
                >
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="tipo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipo</label>
                    <input
                        type="text"
                        name="tipo"
                        id="tipo"
                        value="{{ old('tipo', $documento->tipo) }}"
                        class="mt-1.5 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:focus:border-gray-500 dark:focus:ring-gray-500"
                    >
                </div>
                <div>
                    <label for="fornecedor" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fornecedor</label>
                    <input
                        type="text"
                        name="fornecedor"
                        id="fornecedor"
                        value="{{ old('fornecedor', $documento->fornecedor) }}"
                        class="mt-1.5 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:focus:border-gray-500 dark:focus:ring-gray-500"
                    >
                </div>
            </div>

            <div>
                <label for="pessoa" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pessoa</label>
                <input
                    type="text"
                    name="pessoa"
                    id="pessoa"
                    value="{{ old('pessoa', $documento->pessoa) }}"
                    placeholder="A quem este comprovante pertence"
                    class="mt-1.5 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:placeholder:text-gray-500 dark:focus:border-gray-500 dark:focus:ring-gray-500"
                >
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="valor" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Valor (R$)</label>
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        name="valor"
                        id="valor"
                        value="{{ old('valor', $documento->valor) }}"
                        class="mt-1.5 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:focus:border-gray-500 dark:focus:ring-gray-500"
                    >
                </div>
                <div>
                    <label for="data_documento" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data do documento</label>
                    <input
                        type="date"
                        name="data_documento"
                        id="data_documento"
                        value="{{ old('data_documento', $documento->data_documento?->format('Y-m-d')) }}"
                        class="mt-1.5 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:focus:border-gray-500 dark:focus:ring-gray-500 dark:[color-scheme:dark]"
                    >
                </div>
            </div>

            <div>
                <label for="observacoes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Observações</label>
                <textarea
                    name="observacoes"
                    id="observacoes"
                    rows="3"
                    class="mt-1.5 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:focus:border-gray-500 dark:focus:ring-gray-500"
                >{{ old('observacoes', $documento->observacoes) }}</textarea>
            </div>

            <div class="border-t border-gray-200 pt-5 dark:border-gray-800">
                <label for="arquivo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Substituir arquivo PDF (opcional)</label>
                <input
                    type="file"
                    name="arquivo"
                    id="arquivo"
                    accept="application/pdf"
                    class="mt-1.5 block w-full cursor-pointer rounded-md border border-gray-300 text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-gray-900 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-gray-700 dark:border-gray-700 dark:text-gray-300 dark:file:bg-gray-100 dark:file:text-gray-900 dark:hover:file:bg-gray-300"
                >
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Arquivo atual: {{ $documento->arquivo_nome_original }}. Enviar um novo PDF refaz a extração automática.
                </p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('documentos.show', $documento) }}" class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                    Cancelar
                </a>
                <button
                    type="submit"
                    class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-gray-300"
                >
                    Salvar alterações
                </button>
            </div>
        </form>
    </div>
@endsection
