@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Módulo de Préstamos, Anticipos y Ajustes Extras</h1>
            <p class="text-sm text-slate-500">Gestión de deducciones o asignaciones salariales extraordinarias con control de recibo.</p>
        </div>
        <a href="{{ route('ajustes.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-lg shadow transition">
            + Nuevo Préstamo / Ajuste
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-xl border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-slate-600 font-semibold uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 text-left">Trabajador</th>
                    <th class="px-6 py-3 text-left">Tipo</th>
                    <th class="px-6 py-3 text-left">Concepto</th>
                    <th class="px-6 py-3 text-right">Monto Total</th>
                    <th class="px-6 py-3 text-right">Cuota Quincenal</th>
                    <th class="px-6 py-3 text-center">Progreso</th>
                    <th class="px-6 py-3 text-center">En Recibo</th>
                    <th class="px-6 py-3 text-center">Estado</th>
                    <th class="px-6 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($ajustes as $aj)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-4">
                        <p class="font-bold text-slate-900">{{ $aj->trabajador->nombre_completo ?? 'N/A' }}</p>
                        <p class="text-xs font-mono text-slate-400">{{ $aj->trabajador->cedula ?? '' }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-full {{ $aj->tipo === 'deduccion' ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}">
                            {{ $aj->tipo === 'deduccion' ? 'Descuento (Préstamo)' : 'Abono Extra' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 font-medium text-slate-800">
                        {{ $aj->concepto }}
                    </td>
                    <td class="px-6 py-4 text-right font-mono font-bold text-slate-900">
                        Bs. {{ number_format($aj->monto_total, 2) }}
                    </td>
                    <td class="px-6 py-4 text-right font-mono font-semibold text-slate-700">
                        Bs. {{ number_format($aj->monto_cuota, 2) }}
                    </td>
                    <td class="px-6 py-4 text-center font-mono text-xs">
                        {{ $aj->cuotas_cobradas }} / {{ $aj->cuotas_totales }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($aj->reflejar_en_recibo)
                            <span class="px-2 py-0.5 rounded text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">Visible en Recibo</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-500">Oculto (Privado)</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2 py-0.5 text-xs font-bold rounded-full {{ $aj->estado === 'activo' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                            {{ ucfirst($aj->estado) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <form method="POST" action="{{ route('ajustes.destroy', $aj->id) }}" onsubmit="return confirm('¿Desea cancelar este ajuste salarial?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-600 hover:text-rose-800 text-xs font-bold">Cancelar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-8 text-center text-slate-400 text-sm">No hay préstamos ni ajustes salariales registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
