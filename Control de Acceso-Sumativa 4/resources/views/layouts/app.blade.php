<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SIGEN v4 — Plataforma Multi-Empresa de Nómina y Control de Acceso' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col">

    @auth
        @php
            $user = auth()->user();
            $enModoAuditor = $user->isSuperAdmin() && session()->has('empresa_activa_id');
            $empresaAuditoria = $enModoAuditor ? \App\Models\Empresa::find(session('empresa_activa_id')) : null;
            $esMacro = $user->isSuperAdmin() && !$enModoAuditor;
        @endphp

        {{-- Banner Flotante de Modo Auditor para SuperAdmin --}}
        @if($enModoAuditor && $empresaAuditoria)
            <div class="bg-amber-500 text-slate-950 px-4 py-2 font-semibold text-xs sm:text-sm flex flex-wrap items-center justify-between gap-2 shadow-md border-b border-amber-600">
                <div class="flex items-center space-x-2">
                    <span class="text-base">👁️</span>
                    <span><strong>MODO AUDITOR ACTIVO:</strong> Inspeccionando empresa: <strong class="underline">{{ $empresaAuditoria->nombre_comercial }}</strong> (RIF: {{ $empresaAuditoria->rif }})</span>
                </div>
                <form method="POST" action="{{ route('empresas.salir_impersonacion') }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-slate-900 hover:bg-black text-amber-300 text-xs px-3 py-1.5 rounded-lg font-bold transition flex items-center space-x-1 shadow">
                        <span>⬅️ Volver al Panel Macro</span>
                    </button>
                </form>
            </div>
        @endif

        <!-- Barra de Navegación Principal -->
        <header class="{{ $esMacro ? 'bg-slate-950 border-b border-slate-800' : 'bg-indigo-950 border-b border-indigo-900' }} text-white shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    
                    {{-- Logo y Rol Contextual --}}
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('dashboard') }}" class="text-2xl font-black tracking-wider {{ $esMacro ? 'text-amber-400' : 'text-indigo-400' }}">
                            SIGEN <span class="text-xs bg-indigo-700 text-white px-2 py-0.5 rounded-full font-bold">Fase IV</span>
                        </a>
                        @if($esMacro)
                            <span class="hidden sm:inline text-xs uppercase bg-amber-950 text-amber-300 px-2.5 py-0.5 rounded-full font-bold border border-amber-700">
                                👑 Panel Macro SaaS
                            </span>
                        @elseif($enModoAuditor)
                            <span class="hidden sm:inline text-xs uppercase bg-amber-500 text-slate-950 px-2.5 py-0.5 rounded font-bold">
                                Auditor: {{ Str::limit($empresaAuditoria->nombre_comercial ?? '', 18) }}
                            </span>
                        @else
                            <span class="hidden sm:inline text-xs uppercase bg-indigo-900 text-indigo-200 px-2.5 py-0.5 rounded font-semibold border border-indigo-700">
                                {{ Str::limit($user->empresa->nombre_comercial ?? 'Empresa', 24) }}
                            </span>
                        @endif
                    </div>

                    {{-- Menú de Enlaces Condicional --}}
                    <nav class="hidden lg:flex space-x-5 text-sm font-medium items-center">
                        @if($esMacro)
                            {{-- ============================================== --}}
                            {{-- 1. MENÚ EXCLUSIVO DEL OPERADOR MACRO (SUPERADMIN) --}}
                            {{-- ============================================== --}}
                            <a href="{{ route('dashboard') }}" class="hover:text-amber-300 transition {{ request()->routeIs('dashboard') ? 'text-amber-400 font-bold' : '' }}">
                                📊 Dashboard Macro
                            </a>
                            <a href="{{ route('empresas.index') }}" class="hover:text-amber-300 transition {{ request()->routeIs('empresas.index') ? 'text-amber-400 font-bold' : 'text-amber-200' }}">
                                🏢 Empresas Clientes
                            </a>
                            <a href="{{ route('empresas.create') }}" class="hover:text-amber-300 transition {{ request()->routeIs('empresas.create') ? 'text-amber-400 font-bold' : '' }}">
                                ➕ Registrar Empresa
                            </a>
                            <a href="{{ route('leyes.index') }}" class="hover:text-amber-300 transition {{ request()->routeIs('leyes.*') ? 'text-amber-400 font-bold' : '' }}">
                                ⚖️ Leyes &amp; LOTTT
                            </a>
                        @else
                            {{-- ============================================== --}}
                            {{-- 2. MENÚ DEL PORTAL DE EMPRESA (CLIENTE TENANT) --}}
                            {{-- ============================================== --}}
                            <a href="{{ route('dashboard') }}" class="hover:text-indigo-300 transition {{ request()->routeIs('dashboard') ? 'text-indigo-300 font-bold' : '' }}">
                                Dashboard
                            </a>

                            @if($user->tienePermiso('ver_expedientes') || $user->tienePermiso('modificar_trabajadores'))
                                <a href="{{ route('trabajadores.index') }}" class="hover:text-indigo-300 transition {{ request()->routeIs('trabajadores.*') ? 'text-indigo-300 font-bold' : '' }}">
                                    👥 Personal
                                </a>
                            @endif

                            @if($user->tienePermiso('ver_asistencias'))
                                <a href="{{ route('asistencias.index') }}" class="hover:text-indigo-300 transition {{ request()->routeIs('asistencias.*') && !request()->routeIs('asistencias.kiosco') ? 'text-indigo-300 font-bold' : '' }}">
                                    📍 Asistencias GPS
                                </a>
                            @endif

                            @if($user->tienePermiso('gestionar_prestamos'))
                                <a href="{{ route('ajustes.index') }}" class="hover:text-indigo-300 transition {{ request()->routeIs('ajustes.*') ? 'text-indigo-300 font-bold' : '' }}">
                                    💰 Préstamos &amp; Ajustes
                                </a>
                            @endif

                            @if($user->tienePermiso('procesar_nominas'))
                                <a href="{{ route('nominas.index') }}" class="hover:text-indigo-300 transition {{ request()->routeIs('nominas.*') ? 'text-indigo-300 font-bold' : '' }}">
                                    📋 Nóminas LOTTT
                                </a>
                            @endif

                            @if($user->isAdminEmpresa() || $user->isSuperAdmin())
                                <a href="{{ route('usuarios_empresa.index') }}" class="hover:text-indigo-300 transition text-amber-300 font-semibold {{ request()->routeIs('usuarios_empresa.*') ? 'underline' : '' }}">
                                    🔐 Equipo &amp; Permisos
                                </a>
                            @endif
                        @endif
                    </nav>

                    {{-- Acciones de Usuario y Terminal Kiosco --}}
                    <div class="flex items-center space-x-3">
                        @if(!$esMacro)
                            <a href="{{ route('asistencias.kiosco') }}" target="_blank" class="bg-indigo-800 hover:bg-indigo-700 text-indigo-100 text-xs px-2.5 py-1.5 rounded-lg border border-indigo-600 font-semibold flex items-center space-x-1">
                                <span>📱 Kiosco Sede</span>
                            </a>
                        @endif

                        <div class="text-right hidden sm:block">
                            <span class="text-xs text-indigo-200 font-medium block">{{ $user->name }}</span>
                            <span class="text-[10px] {{ $esMacro ? 'text-amber-400 font-bold' : 'text-indigo-300' }} block">
                                {{ $user->cargo_interno ?? ($user->isSuperAdmin() ? 'Operador Macro' : 'Usuario Empresa') }}
                            </span>
                        </div>

                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-rose-900 hover:bg-rose-700 text-white text-xs px-3 py-1.5 rounded-lg font-semibold transition">
                                Salir
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>
    @endauth

    <!-- Notificaciones Flotantes Flash -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-lg shadow-sm text-sm flex items-center justify-between">
                <div>
                    <span class="font-bold">✓ Éxito: </span>{{ session('success') }}
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-800 p-4 rounded-lg shadow-sm text-sm flex items-center justify-between">
                <div>
                    <span class="font-bold">⚠️ Atención: </span>{{ session('error') }}
                </div>
            </div>
        @endif
    </div>

    <!-- Contenido Principal -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

    <!-- Footer Institucional -->
    <footer class="bg-white border-t border-slate-200 text-center py-4 text-xs text-slate-500">
        SIGEN v4.0 — Universidad Bicentenaria de Aragua &copy; {{ date('Y') }} | Desarrollado por Fredderick Moreno &amp; Iván Zambrano
    </footer>

</body>
</html>
