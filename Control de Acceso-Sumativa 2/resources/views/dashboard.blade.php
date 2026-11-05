@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Panel de Control de Nómina y RRHH</h1>
            <p class="text-sm text-slate-500">Bienvenido al sistema automatizado de pagos, deducciones LOTTT y expedientes digitales de la UBA.</p>
        </div>
        <a href="{{ route('nominas.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-lg shadow transition">
            + Procesar Nueva Nómina
        </a>
    </div>

    <!-- Tarjetas de Acceso Rápido -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition">
            <h2 class="text-slate-400 text-xs uppercase font-bold tracking-wider">Módulo de Personal</h2>
            <p class="text-2xl font-extrabold text-slate-800 mt-2">Trabajadores</p>
            <p class="text-xs text-slate-500 mt-1">Gestión de expedientes digitales y salarios base.</p>
            <a href="{{ route('trabajadores.index') }}" class="inline-block mt-4 text-sm font-medium text-indigo-600 hover:text-indigo-800">Ver empleados &rarr;</a>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition">
            <h2 class="text-slate-400 text-xs uppercase font-bold tracking-wider">Control Operativo</h2>
            <p class="text-2xl font-extrabold text-slate-800 mt-2">Asistencias y GPS</p>
            <p class="text-xs text-slate-500 mt-1">Cálculo de días trabajados con marcaje remoto.</p>
            <a href="{{ route('asistencias.index') }}" class="inline-block mt-4 text-sm font-medium text-indigo-600 hover:text-indigo-800">Ir a marcaje &rarr;</a>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition">
            <h2 class="text-slate-400 text-xs uppercase font-bold tracking-wider">Finanzas Laborales</h2>
            <p class="text-2xl font-extrabold text-slate-800 mt-2">Nóminas y Recibos</p>
            <p class="text-xs text-slate-500 mt-1">Cálculo quincenal/semanal con retenciones de ley.</p>
            <a href="{{ route('nominas.index') }}" class="inline-block mt-4 text-sm font-medium text-indigo-600 hover:text-indigo-800">Historial nóminas &rarr;</a>
        </div>
    </div>
</div>
@endsection
