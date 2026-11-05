@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto bg-white p-8 rounded-xl shadow-sm border border-slate-200">
    <div class="border-b border-slate-100 pb-4 mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Procesar Nueva Nómina</h1>
        <p class="text-sm text-slate-500">Seleccione el tipo de nómina y el rango de fechas a liquidar.</p>
    </div>

    <form method="POST" action="{{ route('nominas.store') }}" class="space-y-6">
        @csrf

        <div>
            <label class="block text-xs font-bold uppercase text-slate-700 mb-2">Frecuencia / Modalidad *</label>
            <select name="tipo" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="quincenal">Nómina Quincenal (15 días base)</option>
                <option value="semanal">Nómina Semanal (7 días base)</option>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-2">Fecha Inicio *</label>
                <input type="date" name="fecha_inicio" value="{{ date('Y-m-01') }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-2">Fecha Fin *</label>
                <input type="date" name="fecha_fin" value="{{ date('Y-m-15') }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div class="bg-indigo-50 p-4 rounded-lg text-xs text-indigo-800 space-y-1">
            <p class="font-bold">El motor automático ejecutará:</p>
            <p>&bull; Conteo de días trabajados desde el módulo de asistencias.</p>
            <p>&bull; Cálculo proporcional del Salario Base y Cestaticket.</p>
            <p>&bull; Retención del 4% IVSS, 0.5% Paro Forzoso y 1% FAOV (LOTTT).</p>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
            <a href="{{ route('nominas.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-medium px-4 py-2 rounded-lg transition">Cancelar</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm px-6 py-2 rounded-lg shadow transition">
                Calcular y Guardar Nómina
            </button>
        </div>
    </form>
</div>
@endsection
