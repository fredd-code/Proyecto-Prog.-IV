@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Registrar Ajuste Salarial o Préstamo</h1>
            <p class="text-sm text-slate-500">Configure deducciones extraordinarias o asignaciones extras con control de visibilidad en recibo.</p>
        </div>
        <a href="{{ route('ajustes.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
            &larr; Volver
        </a>
    </div>

    <form method="POST" action="{{ route('ajustes.store') }}" class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 space-y-5">
        @csrf

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Trabajador Beneficiario / Sujeto *</label>
            <select name="trabajador_id" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">Seleccione un trabajador...</option>
                @foreach($trabajadores as $t)
                <option value="{{ $t->id }}">{{ $t->nombre_completo }} ({{ $t->cedula }}) &bull; {{ $t->cargo }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Naturaleza del Ajuste *</label>
                <select name="tipo" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="deduccion">Deducción / Descuento (Préstamo, Anticipo, Sanción)</option>
                    <option value="asignacion">Asignación / Abono Extra (Bono especial, Hora extra)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Concepto Descriptivo *</label>
                <input type="text" name="concepto" placeholder="Ej: Préstamo para gastos médicos" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Monto Total (Bs.) *</label>
                <input type="number" step="0.01" name="monto_total" placeholder="5000.00" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Número de Cuotas *</label>
                <input type="number" name="cuotas_totales" value="2" min="1" max="48" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-indigo-500">
                <span class="text-xxs text-slate-400">Quincenas o semanas en que se dividirá.</span>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Fecha de Inicio *</label>
                <input type="date" name="fecha_inicio" value="{{ date('Y-m-d') }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <!-- Switch del Patrono para Visibilidad en Recibo -->
        <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100 flex items-start space-x-3">
            <input type="checkbox" name="reflejar_en_recibo" value="1" id="switchRecibo" checked class="mt-1 h-4 w-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
            <div>
                <label for="switchRecibo" class="text-sm font-bold text-indigo-950 cursor-pointer">
                    ¿Reflejar este concepto en el Recibo de Pago?
                </label>
                <p class="text-xs text-indigo-700 mt-0.5">
                    Si está activado, aparecerá desglosado en la boleta individual de pago del empleado. Si se desactiva, el patrono aplicará el descuento/abono en el balance líquido de forma confidencial sin imprimirlo en el recibo.
                </p>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Observaciones o Justificación Interna</label>
            <textarea name="observaciones" rows="2" placeholder="Detalles aprobados por gerencia..." class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm"></textarea>
        </div>

        <div class="pt-3 flex justify-end space-x-3">
            <a href="{{ route('ajustes.index') }}" class="px-4 py-2 border border-slate-300 text-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-50">Cancelar</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg text-sm font-bold shadow transition">
                Guardar Ajuste Salarial
            </button>
        </div>
    </form>
</div>
@endsection
