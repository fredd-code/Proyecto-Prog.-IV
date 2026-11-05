@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Listado de Trabajadores</h1>
            <p class="text-sm text-slate-500">Nómina activa y expedientes de personal.</p>
        </div>
        <a href="{{ route('trabajadores.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg shadow transition">
            + Nuevo Trabajador
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-xl border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-slate-600 font-semibold uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 text-left">Cédula</th>
                    <th class="px-6 py-3 text-left">Nombres y Apellidos</th>
                    <th class="px-6 py-3 text-left">Cargo / Dpto</th>
                    <th class="px-6 py-3 text-left">Modalidad</th>
                    <th class="px-6 py-3 text-right">Salario Base</th>
                    <th class="px-6 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($trabajadores as $t)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-4 font-mono font-medium text-slate-900">{{ $t->cedula }}</td>
                    <td class="px-6 py-4 font-semibold text-slate-800">{{ $t->nombre_completo }}</td>
                    <td class="px-6 py-4 text-slate-500">{{ $t->cargo }} <span class="text-xs text-slate-400">({{ $t->departamento }})</span></td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $t->frecuencia_pago === 'quincenal' ? 'bg-indigo-100 text-indigo-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ ucfirst($t->frecuencia_pago) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right font-mono font-bold text-slate-800">{{ number_format($t->salario_base, 2) }}</td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('trabajadores.show', $t->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-xs bg-indigo-50 px-3 py-1.5 rounded-md hover:bg-indigo-100 transition">
                            Expediente &rarr;
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-slate-400 text-sm">No hay trabajadores registrados aún.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
