<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Control Financiero')</title>

    <!-- Cambiamos al CDN de la versión 3 (Estable y compatible con class) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Configuración nativa de Tailwind v3 para modo oscuro por clase -->
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>

    <!-- Script de arranque para leer la memoria del navegador (Se queda igual) -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 dark:bg-gray-900 dark:text-gray-100 font-sans transition-colors duration-200">

    <!-- Barra de Navegación -->
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 p-4 sticky top-0 z-50 transition-colors duration-200">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <a href="{{ route('movimientos.index') }}" class="text-xl font-bold tracking-wider text-emerald-600 dark:text-emerald-400">
                ⚡ CONTROL YZAC ($BCV)
            </a>
            <div class="flex items-center space-x-6 font-medium">
                <a href="{{ route('movimientos.index') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition">Tablero</a>
                <a href="{{ route('prestamos.index') }}" class="hover:text-amber-600 dark:hover:text-amber-400 transition">Préstamos</a>

                <!-- Botón Interruptor de Tema -->
                <button id="theme-toggle" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition cursor-pointer text-lg" title="Cambiar tema">
                    <span id="theme-toggle-icon">🌓</span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Contenedor Principal Dinámico (Aquí entran tus otras vistas) -->
    <main class="max-w-6xl mx-auto p-4 sm:p-6 mt-4">
        @if(session('success'))
            <div class="bg-emerald-100 dark:bg-emerald-950/50 border border-emerald-400 dark:border-emerald-500 text-emerald-800 dark:text-emerald-200 p-4 rounded-lg mb-6 shadow-md">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Script de control interactivo -->
    <script>
        const toggleBtn = document.getElementById('theme-toggle');

        toggleBtn.addEventListener('click', () => {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                document.documentElement.style.colorScheme = 'light';
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                document.documentElement.style.colorScheme = 'dark';
                localStorage.setItem('theme', 'dark');
            }
        });
    </script>
</body>
</html>
