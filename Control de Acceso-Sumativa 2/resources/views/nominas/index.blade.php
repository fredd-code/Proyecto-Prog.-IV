@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Historial de Nóminas Procesadas</h1>
            <p class="text-sm text-slate-500">Cálculo de retenciones LOTTT y pagos por período.</p>
        </div>
        <a href="{{ route('nominas.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm px-4 py-2 rounded-lg shadow transition">
            + Generar Nueva Nómina
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-slate-600 uppercase text-xs font-semibold">
                <tr>
                    <th class="px-6 py-3 text-left">Código</th>
                    <th class="px-6 py-3 text-left">Tipo</th>
                    <th class="px-6 py-3 text-left">Período</th>
                    <th class="px-6 py-3 text-right">Asignaciones</th>
                    <th class="px-6 py-3 text-right">Deducciones (Ley)</th>
                    <th class="px-6 py-3 text-right">Neto Pagado</th>
                    <th class="px-6 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($nominas as $n)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-4 font-mono font-bold text-indigo-700">{{ $n->codigo }}</td>
                    <td class="px-6 py-4 uppercase font-semibold text-xs">{{ $n->tipo }}</td>
                    <td class="px-6 py-4 text-slate-600">{{ $n->fecha_inicio }} al {{ $n->fecha_fin }}</td>
                    <td class="px-6 py-4 text-right font-mono">{{ number_format($n->total_asignaciones, 2) }}</td>
                    <td class="px-6 py-4 text-right font-mono text-rose-600">-{{ number_format($n->total_deducciones, 2) }}</td>
                    <td class="px-6 py-4 text-right font-mono font-bold text-emerald-700">{{ number_format($n->total_neto, 2) }}</td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('nominas.show', $n->id) }}" class="text-indigo-600 hover:text-indigo-900 text-xs font-bold bg-indigo-50 px-3 py-1.5 rounded">
                            Ver Detalle &rarr;
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-slate-400">No se han procesado nóminas todavía.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
