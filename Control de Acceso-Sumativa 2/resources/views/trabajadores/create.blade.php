@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-sm border border-slate-200">
    <div class="border-b border-slate-100 pb-4 mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Registrar Nuevo Trabajador</h1>
        <p class="text-sm text-slate-500">Ingrese la información personal y los parámetros de asignación salarial.</p>
    </div>

    <form method="POST" action="{{ route('trabajadores.store') }}" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Cédula de Identidad *</label>
                <input type="text" name="cedula" value="{{ old('cedula') }}" placeholder="V-21202137" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Nombres *</label>
                <input type="text" name="nombres" value="{{ old('nombres') }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Apellidos *</label>
                <input type="text" name="apellidos" value="{{ old('apellidos') }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Cargo *</label>
                <input type="text" name="cargo" value="{{ old('cargo') }}" placeholder="Desarrollador Web" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Departamento *</label>
                <input type="text" name="departamento" value="{{ old('departamento') }}" placeholder="Tecnología" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Fecha de Ingreso *</label>
                <input type="date" name="fecha_ingreso" value="{{ old('fecha_ingreso', date('Y-m-d')) }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div class="border-t border-slate-100 pt-6">
            <h2 class="text-sm font-bold uppercase text-indigo-700 mb-4">Parámetros de Compensación y Pago</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Frecuencia de Pago *</label>
                    <select name="frecuencia_pago" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="quincenal">Quincenal (15 días)</option>
                        <option value="semanal">Semanal (7 días)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Salario Base Mensual *</label>
                    <input type="number" step="0.01" name="salario_base" value="{{ old('salario_base', '150.00') }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Cestaticket Mensual *</label>
                    <input type="number" step="0.01" name="bono_alimentacion" value="{{ old('bono_alimentacion', '40.00') }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Bono Asistencia/Extra</label>
                    <input type="number" step="0.01" name="bono_asistencia" value="{{ old('bono_asistencia', '0.00') }}" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3 pt-6 border-t border-slate-100">
            <a href="{{ route('trabajadores.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-medium px-4 py-2 rounded-lg transition">Cancelar</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-6 py-2 rounded-lg shadow transition">Guardar Trabajador</button>
        </div>
    </form>
</div>
@endsection
