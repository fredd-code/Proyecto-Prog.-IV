@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Editar Configuración: {{ $empresa->nombre_comercial }}</h1>
            <p class="text-sm text-slate-500">Actualice datos fiscales, cupo máximo de trabajadores o teléfono de alertas de la empresa.</p>
        </div>
        <a href="{{ route('empresas.show', $empresa->id) }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
            &larr; Volver a la ficha
        </a>
    </div>

    @if($errors->any())
    <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-xl text-sm">
        <ul class="list-disc pl-5 space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('empresas.update', $empresa->id) }}" class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nombre Comercial *</label>
                <input type="text" name="nombre_comercial" value="{{ old('nombre_comercial', $empresa->nombre_comercial) }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Razón Social *</label>
                <input type="text" name="razon_social" value="{{ old('razon_social', $empresa->razon_social) }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-amber-900 uppercase mb-1">Límite Máximo de Trabajadores (Cupo SaaS) *</label>
                <input type="number" name="limite_trabajadores" value="{{ old('limite_trabajadores', $empresa->limite_trabajadores ?? 15) }}" min="1" max="5000" required class="w-full border-2 border-amber-400 bg-amber-50 rounded-lg px-3 py-2 text-sm font-mono font-bold text-amber-900 focus:ring-2 focus:ring-amber-500">
                <span class="text-xxs text-amber-700">Trabajadores actuales registrados: <strong>{{ $empresa->trabajadores()->count() }}</strong></span>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Teléfono Notificaciones *</label>
                <input type="text" name="telefono_notificaciones" value="{{ old('telefono_notificaciones', $empresa->telefono_notificaciones) }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Correo Electrónico de Contacto</label>
                <input type="email" name="email_contacto" value="{{ old('email_contacto', $empresa->email_contacto) }}" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Estado del Servicio *</label>
                <select name="estado" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="activa" {{ $empresa->estado === 'activa' ? 'selected' : '' }}>Activa (Servicio Operativo)</option>
                    <option value="suspendida" {{ $empresa->estado === 'suspendida' ? 'selected' : '' }}>Suspendida</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Dirección Fiscal</label>
                <input type="text" name="direccion" value="{{ old('direccion', $empresa->direccion) }}" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Latitud GPS Sede Central *</label>
                <input type="number" step="any" name="latitud_sede" value="{{ old('latitud_sede', $empresa->latitud_sede) }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Longitud GPS Sede Central *</label>
                <input type="number" step="any" name="longitud_sede" value="{{ old('longitud_sede', $empresa->longitud_sede) }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Radio Geocerca (Metros) *</label>
                <input type="number" name="radio_geocerca_metros" value="{{ old('radio_geocerca_metros', $empresa->radio_geocerca_metros) }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div class="pt-4 flex justify-end space-x-3">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg text-sm font-bold shadow transition">
                Actualizar Cambios
            </button>
        </div>
    </form>
</div>
@endsection
