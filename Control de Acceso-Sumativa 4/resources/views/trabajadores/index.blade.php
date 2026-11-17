@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Listado General de Trabajadores</h1>
            <p class="text-sm text-slate-500">Gestión de personal: Sedes asignadas, jornadas individuales, recesos de ley y control biométrico/PIN.</p>
        </div>
        <a href="{{ route('trabajadores.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-lg shadow transition">
            + Nuevo Trabajador
        </a>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-xl text-sm font-medium">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-xl text-sm font-medium">
        {{ session('error') }}
    </div>
    @endif

    <div class="bg-white shadow-sm rounded-xl border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-slate-600 font-semibold uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 text-left">Cédula / Nombre</th>
                    <th class="px-6 py-3 text-left">Teléfono & PIN</th>
                    <th class="px-6 py-3 text-left">Empresa & Sede GPS</th>
                    <th class="px-6 py-3 text-center">Jornada & Receso LOTTT</th>
                    <th class="px-6 py-3 text-left">Cargo / Dpto</th>
                    <th class="px-6 py-3 text-right">Salario Base</th>
                    <th class="px-6 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($trabajadores as $t)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-4">
                        <p class="font-bold text-slate-900">{{ $t->nombre_completo }}</p>
                        <p class="text-xs font-mono text-slate-400">{{ $t->cedula }}</p>
                    </td>
                    <td class="px-6 py-4 font-mono text-xs">
                        <p class="font-bold text-emerald-700">📞 {{ $t->telefono ?? 'Sin teléfono' }}</p>
                        <p class="text-slate-400 text-xxs">PIN: <span class="bg-slate-100 px-1.5 py-0.5 rounded font-bold">{{ $t->pin_acceso ?? '1234' }}</span></p>
                    </td>
                    <td class="px-6 py-4 text-xs">
                        <p class="font-bold text-indigo-950">{{ $t->empresa->nombre_comercial ?? 'Sin Empresa' }}</p>
                        @if($t->sedes && $t->sedes->count() > 1)
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xxs font-bold bg-indigo-50 text-indigo-700" title="{{ $t->sedes->pluck('nombre')->join(', ') }}">
                            🏢 {{ $t->sedes->count() }} sedes autorizadas
                        </span>
                        @else
                        <p class="text-slate-500 text-xxs">🏢 {{ $t->sedes->first()->nombre ?? ($t->sede->nombre ?? 'Sede Principal') }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center text-xs font-mono">
                        <span class="font-bold text-slate-800">
                            {{ $t->hora_entrada ? substr($t->hora_entrada, 0, 5) : '08:00' }} - {{ $t->hora_salida ? substr($t->hora_salida, 0, 5) : '17:00' }}
                        </span>
                        @if($t->aplica_almuerzo)
                        <span class="block text-xxs text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded mt-0.5">
                            🍽️ Almuerzo: {{ substr($t->inicio_almuerzo, 0, 5) }} a {{ substr($t->fin_almuerzo, 0, 5) }}
                        </span>
                        @else
                        <span class="block text-xxs text-slate-400 mt-0.5">Sin receso</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-500">
                        {{ $t->cargo }} <span class="text-slate-400">({{ $t->departamento }})</span>
                    </td>
                    <td class="px-6 py-4 text-right font-mono font-bold text-slate-800">
                        Bs. {{ number_format($t->salario_base, 2) }}
                    </td>
                    <td class="px-6 py-4 text-center space-x-1">
                        <a href="{{ route('trabajadores.edit', $t->id) }}" class="text-slate-600 hover:text-slate-900 font-semibold text-xs bg-slate-100 px-2.5 py-1 rounded hover:bg-slate-200 transition">
                            Editar
                        </a>
                        <a href="{{ route('trabajadores.show', $t->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold text-xs bg-indigo-50 px-2.5 py-1 rounded hover:bg-indigo-100 transition">
                            Expediente &rarr;
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-slate-400 text-sm">No hay trabajadores registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
