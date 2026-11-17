@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Encabezado Principal del SaaS -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <h1 class="text-2xl font-black text-slate-900">Panel de Control SaaS y Nómina Avanzada</h1>
                <span class="bg-indigo-100 text-indigo-800 text-xs font-bold px-2.5 py-0.5 rounded-full">Fase IV Final</span>
            </div>
            <p class="text-sm text-slate-500 mt-1">Gestión B2B Multi-Empresa, Marcaje Anti-Fraude Geofencing, Préstamos y Deducciones LOTTT.</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('empresas.create') }}" class="bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold px-3.5 py-2 rounded-lg shadow transition">
                + Nueva Empresa Cliente
            </a>
            <a href="{{ route('nominas.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold px-3.5 py-2 rounded-lg shadow transition">
                + Procesar Nómina
            </a>
        </div>
    </div>

    <!-- Tarjetas de Módulos Operativos -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- 1. Empresas Clientes -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <span class="text-2xl">🏢</span>
                <span class="text-xs font-bold uppercase tracking-wider text-amber-600 bg-amber-50 px-2 py-0.5 rounded">Comercial</span>
            </div>
            <h2 class="text-lg font-bold text-slate-900 mt-3">Empresas Clientes</h2>
            <p class="text-xs text-slate-500 mt-1">Panel de control para registrar clientes, sedes GPS y teléfonos de alertas.</p>
            <a href="{{ route('empresas.index') }}" class="inline-block mt-4 text-xs font-bold text-indigo-600 hover:text-indigo-800">Gestionar Clientes &rarr;</a>
        </div>

        <!-- 2. Personal y Fichas -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <span class="text-2xl">👥</span>
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">RRHH</span>
            </div>
            <h2 class="text-lg font-bold text-slate-900 mt-3">Trabajadores</h2>
            <p class="text-xs text-slate-500 mt-1">Fichas con teléfono obligatorio, PIN de acceso seguro y expedientes digitales.</p>
            <a href="{{ route('trabajadores.index') }}" class="inline-block mt-4 text-xs font-bold text-indigo-600 hover:text-indigo-800">Ver Fichas de Empleados &rarr;</a>
        </div>

        <!-- 3. Asistencia Anti-Fraude & Kiosco -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <span class="text-2xl">🛰️</span>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">Anti-Fraude</span>
            </div>
            <h2 class="text-lg font-bold text-slate-900 mt-3">Asistencia & Geocerca</h2>
            <p class="text-xs text-slate-500 mt-1">Detección automática de entrada/salida, Geofencing GPS y modo Kiosco.</p>
            <a href="{{ route('asistencias.index') }}" class="inline-block mt-4 text-xs font-bold text-indigo-600 hover:text-indigo-800">Auditar Asistencias &rarr;</a>
        </div>

        <!-- 4. Préstamos y Ajustes Extras -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <span class="text-2xl">💳</span>
                <span class="text-xs font-bold uppercase tracking-wider text-rose-600 bg-rose-50 px-2 py-0.5 rounded">Finanzas</span>
            </div>
            <h2 class="text-lg font-bold text-slate-900 mt-3">Préstamos y Ajustes</h2>
            <p class="text-xs text-slate-500 mt-1">Descuentos o abonos extras, cuotas y switch de visibilidad en recibos.</p>
            <a href="{{ route('ajustes.index') }}" class="inline-block mt-4 text-xs font-bold text-indigo-600 hover:text-indigo-800">Registrar Ajuste &rarr;</a>
        </div>

    </div>

    <!-- Acceso Directo al Modo Kiosco de Recepción -->
    <div class="bg-gradient-to-r from-indigo-900 to-indigo-950 text-white p-6 rounded-xl shadow-md flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h3 class="text-lg font-bold flex items-center space-x-2">
                <span>📱 Modo Kiosco / Terminal Presencial de Sede</span>
                <span class="bg-emerald-500 text-slate-950 text-xs px-2 py-0.5 rounded font-black">Activo</span>
            </h3>
            <p class="text-xs text-indigo-200 mt-1">
                ¿A un empleado se le dañó el teléfono o se quedó sin batería? Use esta terminal táctil en la recepción para que marque con su Cédula y PIN.
            </p>
        </div>
        <a href="{{ route('asistencias.kiosco') }}" target="_blank" class="bg-white text-indigo-900 hover:bg-indigo-50 font-bold text-xs px-4 py-2.5 rounded-lg shadow whitespace-nowrap transition">
            Abrir Terminal Kiosco &rarr;
        </a>
    </div>
</div>
@endsection
