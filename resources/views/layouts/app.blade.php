<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo', 'Comprovantes') · Finance Control</title>

    {{-- Aplica o tema antes do primeiro paint, evitando flash de cor errada --}}
    <script>
        (function () {
            try {
                var tema = localStorage.getItem('tema');
                var escuro = tema ? tema === 'escuro' : window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.classList.toggle('dark', escuro);
            } catch {}
        }());
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased dark:bg-gray-950 dark:text-gray-100">
    <header class="border-b border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-6 py-4">
            <a href="{{ route('documentos.index') }}" class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                Finance Control
            </a>
            <nav class="flex items-center gap-3 text-sm">
                <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 {{ request()->routeIs('dashboard') ? 'font-semibold text-gray-900 dark:text-gray-100' : '' }}">
                    Dashboard
                </a>
                <a href="{{ route('documentos.index') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 {{ request()->routeIs('documentos.*') ? 'font-semibold text-gray-900 dark:text-gray-100' : '' }}">
                    Comprovantes
                </a>
                <a
                    href="{{ route('documentos.create') }}"
                    class="inline-flex items-center rounded-md bg-gray-900 px-3 py-2 font-medium text-white hover:bg-gray-700 dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-gray-300"
                >
                    Novo comprovante
                </a>

                <button
                    type="button"
                    id="botao-tema"
                    aria-label="Alternar tema claro/escuro"
                    class="ml-1 inline-flex h-9 w-9 items-center justify-center rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-100"
                >
                    <svg id="icone-sol" class="hidden h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 15a5 5 0 100-10 5 5 0 000 10zM10 0a1 1 0 011 1v1a1 1 0 11-2 0V1a1 1 0 011-1zm0 16a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM3.515 3.515a1 1 0 011.414 0l.707.707A1 1 0 014.222 5.64l-.707-.707a1 1 0 010-1.414zm11.85 11.85a1 1 0 011.414 0l.707.707a1 1 0 11-1.414 1.414l-.707-.707a1 1 0 010-1.414zM0 10a1 1 0 011-1h1a1 1 0 110 2H1a1 1 0 01-1-1zm16 0a1 1 0 011-1h1a1 1 0 110 2h-1a1 1 0 01-1-1zM3.515 16.485a1 1 0 010-1.414l.707-.707a1 1 0 111.414 1.414l-.707.707a1 1 0 01-1.414 0zM14.657 5.64a1 1 0 010-1.415l.707-.707a1 1 0 111.414 1.414l-.707.708a1 1 0 01-1.414 0z"/>
                    </svg>
                    <svg id="icone-lua" class="hidden h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                    </svg>
                </button>
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-6 py-8">
        @include('components.flash-messages')

        @yield('conteudo')
    </main>

    @stack('scripts')

    <script>
        (function () {
            const botao = document.getElementById('botao-tema');
            const iconeSol = document.getElementById('icone-sol');
            const iconeLua = document.getElementById('icone-lua');

            function sincronizarIcone() {
                const escuro = document.documentElement.classList.contains('dark');
                iconeSol.classList.toggle('hidden', !escuro);
                iconeLua.classList.toggle('hidden', escuro);
            }

            botao.addEventListener('click', function () {
                const escuro = document.documentElement.classList.toggle('dark');
                try { localStorage.setItem('tema', escuro ? 'escuro' : 'claro'); } catch {}
                sincronizarIcone();
            });

            sincronizarIcone();
        }());
    </script>
</body>
</html>
