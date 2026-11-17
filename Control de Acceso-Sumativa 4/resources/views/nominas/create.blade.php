@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Procesar Período de Nómina</h1>
            <p class="text-sm text-slate-500">Liquidación periódica con cruce de asistencias, deducciones LOTTT y préstamos.</p>
        </div>
        <a href="{{ route('nominas.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
            &larr; Volver
        </a>
    </div>

    <form method="POST" action="{{ route('nominas.store') }}" class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 space-y-5">
        @csrf

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Empresa a Liquidar *</label>
            <select name="empresa_id" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 bg-slate-50">
                @foreach($empresas as $emp)
                <option value="{{ $emp->id }}">{{ $emp->nombre_comercial }} ({{ $emp->rif }})</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Frecuencia de Liquidación *</label>
            <select name="tipo" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="quincenal">Nómina Quincenal (15 días base)</option>
                <option value="semanal">Nómina Semanal (7 días base)</option>
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Fecha de Inicio *</label>
                <input type="date" name="fecha_inicio" value="{{ date('Y-m-01') }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Fecha de Fin *</label>
                <input type="date" name="fecha_fin" value="{{ date('Y-m-15') }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div class="p-4 bg-slate-50 rounded-xl text-xs text-slate-600 border border-slate-200">
            <span class="font-bold text-slate-800">Automatizaciones que ejecutará el sistema:</span>
            <ul class="list-disc list-inside mt-1 space-y-0.5 text-slate-500">
                <li>Cruza marcajes de asistencia y geocerca para calcular días laborados efectivos.</li>
                <li>Calcula Cestaticket y retenciones LOTTT (4% IVSS, 0.5% RPE, 1% FAOV).</li>
                <li>Aplica cuotas de préstamos y anticipos según la decisión del patrono.</li>
            </ul>
        </div>

        <div class="pt-3 flex justify-end space-x-3">
            <a href="{{ route('nominas.index') }}" class="px-4 py-2 border border-slate-300 text-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-50">Cancelar</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg text-sm font-bold shadow transition">
                Calcular y Emitir Nómina
            </button>
        </div>
    </form>
</div>
@endsection
