@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Encabezado de la Empresa Cliente -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded-full uppercase">Empresa Cliente</span>
                <h1 class="text-2xl font-black text-slate-900">{{ $empresa->nombre_comercial }}</h1>
            </div>
            <p class="text-xs text-slate-500 font-mono mt-1">RIF: {{ $empresa->rif }} &bull; Razón Social: {{ $empresa->razon_social }}</p>
        </div>
        
        <div class="flex flex-wrap gap-2">
            @if(auth()->user()->tienePermiso('puede_modificar_trabajadores'))
            <a href="{{ route('trabajadores.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-3.5 py-2 rounded-xl shadow transition">
                + Nuevo Trabajador
            </a>
            @endif
            @if(auth()->user()->tienePermiso('puede_procesar_nominas'))
            <a href="{{ route('nominas.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-3.5 py-2 rounded-xl shadow transition">
                + Procesar Nómina
            </a>
            @endif
            @if(auth()->user()->isAdminEmpresa())
            <a href="{{ route('usuarios_empresa.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-3.5 py-2 rounded-xl transition">
                🔐 Equipo & Permisos
            </a>
            @endif
        </div>
    </div>

    <!-- 4 Tarjetas de Métricas de la Empresa -->
    @php
        $limiteTrab = $empresa->limite_trabajadores ?? 15;
        $porcentajeTrab = min(100, round(($totalTrabajadores / max(1, $limiteTrab)) * 100));
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- 1. Personal y Cupo -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex justify-between items-center text-xs font-bold text-slate-400 uppercase">
                <span>Personal Activo</span>
                <span>👥</span>
            </div>
            <p class="text-2xl font-black text-slate-900 mt-2 font-mono">
                {{ $totalTrabajadores }} <span class="text-xs font-normal text-slate-400">/ {{ $limiteTrab }}</span>
            </p>
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2 overflow-hidden">
                <div class="h-1.5 rounded-full {{ $totalTrabajadores >= $limiteTrab ? 'bg-rose-500' : 'bg-indigo-600' }}" style="width: {{ $porcentajeTrab }}%"></div>
            </div>
            <p class="text-xxs text-slate-400 mt-1.5">{{ $porcentajeTrab }}% del cupo contratado</p>
        </div>

        <!-- 2. Asistencias Hoy -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex justify-between items-center text-xs font-bold text-slate-400 uppercase">
                <span>Marcajes de Hoy</span>
                <span>⏱️</span>
            </div>
            <p class="text-2xl font-black text-emerald-700 mt-2 font-mono">
                {{ $asistenciasHoy }}
            </p>
            <div class="mt-2 text-xxs flex justify-between">
                <span class="text-slate-500 font-medium">Llegadas registradas</span>
                <span class="{{ $retardosHoy > 0 ? 'text-rose-600 font-bold' : 'text-slate-400' }}">{{ $retardosHoy }} retardos</span>
            </div>
        </div>

        <!-- 3. Préstamos Activos -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex justify-between items-center text-xs font-bold text-slate-400 uppercase">
                <span>Préstamos / Deducciones</span>
                <span>💳</span>
            </div>
            <p class="text-2xl font-black text-amber-700 mt-2 font-mono">
                {{ $prestamosPendientes }}
            </p>
            <p class="text-xxs text-slate-500 mt-2">Ajustes pendientes por descontar en nómina</p>
        </div>

        <!-- 4. Sedes Autorizadas -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex justify-between items-center text-xs font-bold text-slate-400 uppercase">
                <span>Sedes Activas</span>
                <span>📍</span>
            </div>
            <p class="text-2xl font-black text-indigo-950 mt-2 font-mono">
                {{ $empresa->sedes->count() }} <span class="text-xs font-normal text-slate-400">/ {{ $empresa->limite_sedes ?? 3 }}</span>
            </p>
            <p class="text-xxs text-slate-500 mt-2">Centros con geocerca GPS anti-fraude</p>
        </div>
    </div>

    <!-- Cuadrícula de Contenido: Personal Reciente y Últimos Marcajes -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Fichas de Empleados Recientes -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 space-y-3">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2.5">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Personal Registrado Recientemente</h3>
                <a href="{{ route('trabajadores.index') }}" class="text-xxs font-bold text-indigo-600 hover:underline">Ver Todos &rarr;</a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($trabajadoresRecientes as $tr)
                <div class="py-2.5 flex justify-between items-center text-xs">
                    <div>
                        <p class="font-bold text-slate-900">{{ $tr->nombre_completo }}</p>
                        <p class="text-xxs font-mono text-slate-400">{{ $tr->cedula }} &bull; {{ $tr->cargo }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xxs font-mono font-bold text-slate-700">Bs. {{ number_format($tr->salario_base, 2) }}</span>
                        <a href="{{ route('trabajadores.show', $tr->id) }}" class="block text-xxs font-bold text-indigo-600 hover:underline">Expediente</a>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400 py-4 text-center">No hay trabajadores registrados en esta empresa.</p>
                @endforelse
            </div>
        </div>

        <!-- Marcaciones de Asistencia de Hoy -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 space-y-3">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2.5">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Últimos Marcajes Registrados</h3>
                <a href="{{ route('asistencias.index') }}" class="text-xxs font-bold text-indigo-600 hover:underline">Auditoría GPS &rarr;</a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($asistenciasRecientes as $as)
                <div class="py-2.5 flex justify-between items-center text-xs">
                    <div>
                        <p class="font-bold text-slate-800">{{ $as->trabajador->nombre_completo ?? 'N/A' }}</p>
                        <span class="text-xxs text-slate-400 font-mono">{{ $as->sede->nombre ?? 'Sede' }} &bull; Entrada: {{ substr($as->hora_entrada, 0, 5) }}</span>
                    </div>
                    <div>
                        @if($as->es_retardo)
                            <span class="bg-rose-100 text-rose-700 text-xxs font-bold px-2 py-0.5 rounded">Retardo</span>
                        @else
                            <span class="bg-emerald-100 text-emerald-800 text-xxs font-bold px-2 py-0.5 rounded">Puntual</span>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400 py-4 text-center">No se han registrado asistencias el día de hoy.</p>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
