@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Ficha del Trabajador -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 space-y-4">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center space-x-3">
                    <h1 class="text-2xl font-bold text-slate-900">{{ $trabajador->nombre_completo }}</h1>
                    <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded-full uppercase">{{ $trabajador->estado }}</span>
                </div>
                <p class="text-sm text-slate-500 font-mono mt-1">C.I: {{ $trabajador->cedula }} &bull; Cargo: {{ $trabajador->cargo }} ({{ $trabajador->departamento }})</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('trabajadores.edit', $trabajador->id) }}" class="bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold px-3 py-2 rounded-lg transition">
                    ✏️ Editar Ficha / Horario
                </a>
                <a href="{{ route('trabajadores.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                    &larr; Volver
                </a>
            </div>
        </div>

        <!-- Parámetros Clave: Teléfono, Sede, Horario y Descanso LOTTT -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-3 border-t border-slate-100 text-xs">
            <div class="bg-slate-50 p-3 rounded-lg border border-slate-200">
                <span class="text-slate-400 uppercase font-bold block text-xxs">Teléfono & PIN</span>
                <span class="text-emerald-700 font-bold font-mono text-sm block mt-0.5">📞 {{ $trabajador->telefono ?? 'Sin teléfono' }}</span>
                <span class="text-slate-500 text-xxs">PIN: <strong class="text-indigo-900 font-mono">{{ $trabajador->pin_acceso ?? '1234' }}</strong></span>
            </div>

            <div class="bg-slate-50 p-3 rounded-lg border border-slate-200">
                <span class="text-slate-400 uppercase font-bold block text-xxs">Sedes Autorizadas (GPS)</span>
                @if($trabajador->sedes && $trabajador->sedes->count() > 0)
                <span class="text-indigo-950 font-bold block mt-0.5 truncate" title="{{ $trabajador->sedes->pluck('nombre')->join(', ') }}">
                    🏢 {{ $trabajador->sedes->count() }} {{ $trabajador->sedes->count() == 1 ? 'Sede' : 'Sedes' }}: {{ $trabajador->sedes->first()->nombre }}
                </span>
                <span class="text-slate-500 text-xxs truncate block">{{ $trabajador->sedes->pluck('nombre')->join(', ') }}</span>
                @else
                <span class="text-indigo-950 font-bold block mt-0.5 truncate">{{ $trabajador->sede->nombre ?? 'Sede Principal' }}</span>
                <span class="text-slate-500 text-xxs truncate block">{{ $trabajador->empresa->nombre_comercial ?? 'Empresa' }}</span>
                @endif
            </div>

            <div class="bg-slate-50 p-3 rounded-lg border border-slate-200">
                <span class="text-slate-400 uppercase font-bold block text-xxs">Jornada Laboral</span>
                <span class="text-slate-900 font-bold font-mono text-sm block mt-0.5">
                    {{ $trabajador->hora_entrada ? substr($trabajador->hora_entrada, 0, 5) : '08:00' }} - {{ $trabajador->hora_salida ? substr($trabajador->hora_salida, 0, 5) : '17:00' }}
                </span>
                <span class="text-slate-500 text-xxs">Tol. gracia: {{ $trabajador->tolerancia_minutos ?? 15 }} min</span>
            </div>

            <div class="bg-slate-50 p-3 rounded-lg border border-slate-200">
                <span class="text-slate-400 uppercase font-bold block text-xxs">Descanso Legal (LOTTT 168)</span>
                @if($trabajador->aplica_almuerzo)
                <span class="text-amber-800 font-bold font-mono text-xs block mt-0.5">
                    🍽️ {{ substr($trabajador->inicio_almuerzo, 0, 5) }} a {{ substr($trabajador->fin_almuerzo, 0, 5) }}
                </span>
                <span class="text-amber-700 text-xxs">Pausa legal activa</span>
                @else
                <span class="text-slate-400 font-medium block mt-0.5">Sin receso legal</span>
                <span class="text-slate-400 text-xxs">Jornada continua</span>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-xl text-sm font-medium">
        {{ session('success') }}
    </div>
    @endif

    <!-- Módulo de Expediente Digital -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Subir Documento -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 h-fit">
            <h2 class="text-lg font-bold text-slate-900 mb-2">Cargar al Expediente</h2>
            <p class="text-xs text-slate-500 mb-4">Anexe documentos de identidad, contratos o soportes médicos.</p>

            <form method="POST" action="{{ route('expedientes.store', $trabajador->id) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Título del Documento *</label>
                    <input type="text" name="titulo" placeholder="Ej: Copia Cédula Ampliada" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tipo de Archivo *</label>
                    <select name="tipo_documento" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="cedula">Cédula de Identidad</option>
                        <option value="rif">RIF Vigente</option>
                        <option value="contrato">Contrato Laboral</option>
                        <option value="constancia_medica">Constancia Médica</option>
                        <option value="sintesis_curricular">Síntesis Curricular</option>
                        <option value="otro">Otro Documento</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Seleccionar Archivo (PDF/Imagen) *</label>
                    <input type="file" name="archivo" required class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 rounded-lg text-sm transition">
                    + Adjuntar al Expediente
                </button>
            </form>
        </div>

        <!-- Archivos en el Expediente -->
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Documentos en el Expediente Digital ({{ $trabajador->expedientes->count() }})</h2>

            <div class="divide-y divide-slate-100">
                @forelse($trabajador->expedientes as $doc)
                <div class="py-3 flex justify-between items-center hover:bg-slate-50 px-2 rounded-lg transition">
                    <div class="flex items-center space-x-3">
                        <div class="bg-indigo-100 text-indigo-700 p-2 rounded-lg">
                            📁
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800">{{ $doc->titulo }}</p>
                            <p class="text-xs text-slate-400 uppercase font-semibold">{{ $doc->tipo_documento }} &bull; {{ $doc->peso_kb }} KB &bull; {{ $doc->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ asset('storage/' . $doc->ruta_archivo) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 text-xs font-bold bg-indigo-50 px-3 py-1.5 rounded">Ver / Descargar</a>
                        <form method="POST" action="{{ route('expedientes.destroy', $doc->id) }}" onsubmit="return confirm('¿Desea eliminar este documento?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-600 hover:text-rose-800 text-xs font-bold px-2 py-1">Eliminar</button>
                        </form>
                    </div>
                </div>
                @empty
                <p class="text-sm text-slate-400 py-6 text-center">No hay documentos cargados en el expediente de este empleado.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
