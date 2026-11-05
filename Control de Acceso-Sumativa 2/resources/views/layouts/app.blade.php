<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SIGEN - Sistema de Gestión de Nómina' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col">
    
    <!-- Barra de Navegación Principal -->
    <header class="bg-indigo-900 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('dashboard') }}" class="text-2xl font-black tracking-wider text-indigo-400">SIGEN</a>
                    <span class="text-xs uppercase bg-indigo-800 text-indigo-200 px-2 py-0.5 rounded font-semibold">UBA Nómina</span>
                </div>

                @auth
                <nav class="hidden md:flex space-x-6 text-sm font-medium">
                    <a href="{{ route('dashboard') }}" class="hover:text-indigo-300 transition">Dashboard</a>
                    <a href="{{ route('trabajadores.index') }}" class="hover:text-indigo-300 transition">Trabajadores</a>
                    <a href="{{ route('asistencias.index') }}" class="hover:text-indigo-300 transition">Control Asistencia</a>
                    <a href="{{ route('nominas.index') }}" class="hover:text-indigo-300 transition">Nóminas</a>
                </nav>

                <div class="flex items-center space-x-4">
                    <span class="text-xs text-indigo-200">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-indigo-800 hover:bg-rose-700 text-white text-xs px-3 py-1.5 rounded transition">Salir</button>
                    </form>
                </div>
                @endauth
            </div>
        </div>
    </header>

    <!-- Notificaciones Flotantes Flash -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded shadow-sm text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-800 p-4 rounded shadow-sm text-sm">
                {{ session('error') }}
            </div>
        @endif
    </div>

    <!-- Contenido Principal -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

    <!-- Footer Institucional -->
    <footer class="bg-white border-t border-slate-200 text-center py-4 text-xs text-slate-500">
        Programación IV - Universidad Bicentenaria de Aragua &copy; {{ date('Y') }} | Desarrollado por Fredderick Moreno &amp; Iván Zambrano
    </footer>

</body>
</html>
