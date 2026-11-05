@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Ficha del Trabajador -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-bold text-slate-900">{{ $trabajador->nombre_completo }}</h1>
                <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded-full uppercase">{{ $trabajador->estado }}</span>
            </div>
            <p class="text-sm text-slate-500 font-mono mt-1">C.I: {{ $trabajador->cedula }} | Cargo: {{ $trabajador->cargo }} ({{ $trabajador->departamento }})</p>
        </div>
        <div class="text-right">
            <p class="text-xs text-slate-400 uppercase font-semibold">Salario Base Mensual</p>
            <p class="text-2xl font-extrabold text-slate-900 font-mono">{{ number_format($trabajador->salario_base, 2) }}</p>
            <span class="text-xs text-indigo-600 font-bold">Frecuencia: {{ ucfirst($trabajador->frecuencia_pago) }}</span>
        </div>
    </div>

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
