@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Registrar Usuario del Equipo</h1>
            <p class="text-sm text-slate-500">Cree un acceso para un supervisor, contador o analista de {{ $empresa->nombre_comercial }} y defina sus permisos.</p>
        </div>
        <a href="{{ route('usuarios_empresa.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
            &larr; Volver
        </a>
    </div>

    @if($errors->any())
    <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-xl text-xs font-medium">
        <ul class="list-disc pl-5 space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('usuarios_empresa.store') }}" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-6">
        @csrf

        <!-- Datos Básicos -->
        <div class="border-b border-slate-100 pb-5">
            <h2 class="text-xs font-bold uppercase tracking-wider text-indigo-900 mb-3">1. Datos de Identificación y Acceso</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nombre y Apellido *</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Ej: Lic. Mariana Gómez" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Cargo Interno en la Empresa *</label>
                    <input type="text" name="cargo_interno" value="{{ old('cargo_interno') }}" placeholder="Ej: Analista de Nómina / Jefe de Almacén" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Correo Electrónico (Usuario de Login) *</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="usuario@empresa.com" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Contraseña de Acceso *</label>
                    <input type="password" name="password" placeholder="Mínimo 6 caracteres" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <!-- Matriz de Permisos Granulares por Módulo -->
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <h2 class="text-xs font-bold uppercase tracking-wider text-indigo-900">2. Asignación de Permisos Especiales por Módulo</h2>
                <span class="text-xxs text-slate-400">Marque solo las funciones autorizadas para este usuario</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1">
                <!-- Permiso 1: Préstamos y Deducciones -->
                <label class="flex items-start p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-indigo-50/50 cursor-pointer transition select-none">
                    <input type="checkbox" name="puede_gestionar_prestamos" value="1" {{ old('puede_gestionar_prestamos') ? 'checked' : '' }} class="mt-0.5 rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                    <div class="ml-3">
                        <span class="text-xs font-bold text-slate-900 block">💳 Otorgar Préstamos y Deducciones</span>
                        <span class="text-xxs text-slate-500">Permite cargar anticipos, préstamos y decidir si se reflejan en el recibo.</span>
                    </div>
                </label>

                <!-- Permiso 2: Permisos y Retiros -->
                <label class="flex items-start p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-indigo-50/50 cursor-pointer transition select-none">
                    <input type="checkbox" name="puede_aprobar_retiros" value="1" {{ old('puede_aprobar_retiros') ? 'checked' : '' }} class="mt-0.5 rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                    <div class="ml-3">
                        <span class="text-xs font-bold text-slate-900 block">⚠️ Aprobar Permisos y Retiros</span>
                        <span class="text-xxs text-slate-500">Permite autorizar salidas anticipadas por imprevisto o ausencias justificadas.</span>
                    </div>
                </label>

                <!-- Permiso 3: Ver Expediente del Trabajador -->
                <label class="flex items-start p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-indigo-50/50 cursor-pointer transition select-none">
                    <input type="checkbox" name="puede_ver_expedientes" value="1" {{ old('puede_ver_expedientes') ? 'checked' : '' }} class="mt-0.5 rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                    <div class="ml-3">
                        <span class="text-xs font-bold text-slate-900 block">📁 Ver Expedientes Digitales</span>
                        <span class="text-xxs text-slate-500">Acceso a consultar y descargar archivos, cédulas y constancias médicas.</span>
                    </div>
                </label>

                <!-- Permiso 4: Modificar, Eliminar y Cargar Trabajadores -->
                <label class="flex items-start p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-indigo-50/50 cursor-pointer transition select-none">
                    <input type="checkbox" name="puede_modificar_trabajadores" value="1" {{ old('puede_modificar_trabajadores') ? 'checked' : '' }} class="mt-0.5 rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                    <div class="ml-3">
                        <span class="text-xs font-bold text-slate-900 block">👥 Modificar y Cargar Personal</span>
                        <span class="text-xxs text-slate-500">Dar de alta trabajadores, editar salarios, cambiar sedes y actualizar PIN.</span>
                    </div>
                </label>

                <!-- Permiso 5: Procesar Nómina e Imprimir Recibos -->
                <label class="flex items-start p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-indigo-50/50 cursor-pointer transition select-none">
                    <input type="checkbox" name="puede_procesar_nominas" value="1" {{ old('puede_procesar_nominas') ? 'checked' : '' }} class="mt-0.5 rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                    <div class="ml-3">
                        <span class="text-xs font-bold text-slate-900 block">📑 Procesar Nóminas e Imprimir Recibos</span>
                        <span class="text-xxs text-slate-500">Calcular nóminas quincenales/semanales y emitir recibos oficiales LOTTT.</span>
                    </div>
                </label>

                <!-- Permiso 6: Auditar Asistencias GPS y Retardos -->
                <label class="flex items-start p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-indigo-50/50 cursor-pointer transition select-none">
                    <input type="checkbox" name="puede_ver_asistencias" value="1" {{ old('puede_ver_asistencias') ? 'checked' : '' }} class="mt-0.5 rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                    <div class="ml-3">
                        <span class="text-xs font-bold text-slate-900 block">⏱️ Auditar Asistencias y Retardos</span>
                        <span class="text-xxs text-slate-500">Supervisar marcajes GPS, cumplimiento de geocercas y alertas.</span>
                    </div>
                </label>
            </div>
        </div>

        <div class="pt-4 flex justify-end space-x-3">
            <a href="{{ route('usuarios_empresa.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-xs font-bold text-slate-700 hover:bg-slate-50">Cancelar</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-xs font-bold shadow">
                Guardar Usuario y Permisos
            </button>
        </div>
    </form>
</div>
@endsection
