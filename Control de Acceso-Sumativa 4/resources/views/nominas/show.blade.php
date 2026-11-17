@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="bg-white p-6 rounded-xl border border-slate-200 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-bold text-slate-900">Nómina: {{ $nomina->codigo }}</h1>
                <span class="bg-indigo-100 text-indigo-800 text-xs font-bold px-2.5 py-0.5 rounded-full uppercase">{{ $nomina->tipo }}</span>
            </div>
            <p class="text-sm text-slate-500 font-mono mt-1">Período: {{ $nomina->fecha_inicio }} al {{ $nomina->fecha_fin }}</p>
        </div>
        <div class="flex gap-4">
            <div class="text-right">
                <p class="text-xs text-slate-400 font-semibold uppercase">Total Neto Liquidado</p>
                <p class="text-2xl font-extrabold text-emerald-700 font-mono">{{ number_format($nomina->total_neto, 2) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-slate-600 uppercase text-xs font-semibold">
                <tr>
                    <th class="px-4 py-3 text-left">Trabajador</th>
                    <th class="px-4 py-3 text-center">Días Trab.</th>
                    <th class="px-4 py-3 text-right">Salario Dev.</th>
                    <th class="px-4 py-3 text-right">Cestaticket</th>
                    <th class="px-4 py-3 text-right">IVSS (4%)</th>
                    <th class="px-4 py-3 text-right">RPE (0.5%)</th>
                    <th class="px-4 py-3 text-right">FAOV (1%)</th>
                    <th class="px-4 py-3 text-right">Neto a Cobrar</th>
                    <th class="px-4 py-3 text-center">Recibo</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($nomina->detalles as $det)
                <tr class="hover:bg-slate-50 transition font-mono text-xs">
                    <td class="px-4 py-3 font-sans font-medium text-slate-800 text-sm">
                        {{ $det->trabajador->nombre_completo }}
                        <span class="block text-xs font-mono text-slate-400">{{ $det->trabajador->cedula }}</span>
                    </td>
                    <td class="px-4 py-3 text-center font-bold">{{ $det->dias_trabajados }}</td>
                    <td class="px-4 py-3 text-right text-slate-700">{{ number_format($det->salario_devengado, 2) }}</td>
                    <td class="px-4 py-3 text-right text-slate-700">{{ number_format($det->cestaticket_devengado, 2) }}</td>
                    <td class="px-4 py-3 text-right text-rose-600">-{{ number_format($det->deduccion_ivss, 2) }}</td>
                    <td class="px-4 py-3 text-right text-rose-600">-{{ number_format($det->deduccion_rpe, 2) }}</td>
                    <td class="px-4 py-3 text-right text-rose-600">-{{ number_format($det->deduccion_faov, 2) }}</td>
                    <td class="px-4 py-3 text-right font-bold text-emerald-700 text-sm">{{ number_format($det->neto_a_pagar, 2) }}</td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('nominas.recibo', $det->id) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-2.5 py-1 rounded font-sans font-bold">
                            Imprimir 🖨️
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
