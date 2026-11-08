@extends('layouts.app')

@section('titulo', 'Novo comprovante')

@section('conteudo')
    <div class="mx-auto max-w-2xl">
        <h1 class="mb-1 text-xl font-semibold text-gray-900 dark:text-gray-100">Novo comprovante</h1>
        <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">
            Envie o PDF do comprovante. Ao salvar, os metadados (tipo, valor, data, fornecedor)
            são extraídos automaticamente por uma LLM a partir do conteúdo do arquivo.
        </p>

        <form
            action="{{ route('documentos.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-5 rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900"
        >
            @csrf

            <div>
                <label for="arquivo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Arquivo do comprovante (PDF) <span class="text-rose-600 dark:text-rose-400">*</span>
                </label>
                <input
                    type="file"
                    name="arquivo"
                    id="arquivo"
                    accept="application/pdf"
                    required
                    class="mt-1.5 block w-full cursor-pointer rounded-md border border-gray-300 text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-gray-900 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-gray-700 dark:border-gray-700 dark:text-gray-300 dark:file:bg-gray-100 dark:file:text-gray-900 dark:hover:file:bg-gray-300"
                >
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Apenas PDF, até 10MB.</p>
            </div>

            <div>
                <label for="titulo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Título (opcional)</label>
                <input
                    type="text"
                    name="titulo"
                    id="titulo"
                    value="{{ old('titulo') }}"
                    placeholder="Ex: Nota fiscal — material de escritório"
                    class="mt-1.5 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:placeholder:text-gray-500 dark:focus:border-gray-500 dark:focus:ring-gray-500"
                >
            </div>

            <div>
                <label for="tipo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipo (opcional)</label>
                <input
                    type="text"
                    name="tipo"
                    id="tipo"
                    value="{{ old('tipo') }}"
                    placeholder="Deixe em branco para a LLM identificar automaticamente"
                    class="mt-1.5 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:placeholder:text-gray-500 dark:focus:border-gray-500 dark:focus:ring-gray-500"
                >
            </div>

            <div>
                <label for="pessoa" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pessoa (opcional)</label>
                <input
                    type="text"
                    name="pessoa"
                    id="pessoa"
                    value="{{ old('pessoa') }}"
                    placeholder="A quem este comprovante pertence"
                    class="mt-1.5 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:placeholder:text-gray-500 dark:focus:border-gray-500 dark:focus:ring-gray-500"
                >
            </div>

            <div>
                <label for="observacoes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Observações (opcional)</label>
                <textarea
                    name="observacoes"
                    id="observacoes"
                    rows="3"
                    class="mt-1.5 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:focus:border-gray-500 dark:focus:ring-gray-500"
                >{{ old('observacoes') }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('documentos.index') }}" class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                    Cancelar
                </a>
                <button
                    type="submit"
                    class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-gray-300"
                >
                    Enviar comprovante
                </button>
            </div>
        </form>
    </div>
@endsection
