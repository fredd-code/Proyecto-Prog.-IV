@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Editar Permisos: {{ $usuario->name }}</h1>
            <p class="text-sm text-slate-500">Configure los accesos por módulo y estado de la cuenta para {{ $empresa->nombre_comercial }}.</p>
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

    <form method="POST" action="{{ route('usuarios_empresa.update', $usuario->id) }}" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-6">
        @csrf
        @method('PUT')

        <!-- Datos Básicos -->
        <div class="border-b border-slate-100 pb-5">
            <h2 class="text-xs font-bold uppercase tracking-wider text-indigo-900 mb-3">1. Datos de Identificación y Estado</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nombre y Apellido *</label>
                    <input type="text" name="name" value="{{ old('name', $usuario->name) }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Cargo Interno *</label>
                    <input type="text" name="cargo_interno" value="{{ old('cargo_interno', $usuario->cargo_interno) }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Correo Electrónico *</label>
                    <input type="email" name="email" value="{{ old('email', $usuario->email) }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Estado de la Cuenta *</label>
                    <select name="estado_usuario" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
                        <option value="activo" {{ $usuario->estado_usuario === 'activo' ? 'selected' : '' }}>Activo (Permite Login)</option>
                        <option value="inactivo" {{ $usuario->estado_usuario === 'inactivo' ? 'selected' : '' }}>Inactivo (Acceso Bloqueado)</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Cambiar Contraseña (Opcional)</label>
                    <input type="password" name="password" placeholder="Dejar en blanco para mantener la clave actual" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <!-- Matriz de Permisos Granulares por Módulo -->
        <div class="space-y-3">
            <h2 class="text-xs font-bold uppercase tracking-wider text-indigo-900">2. Permisos Especiales por Módulo</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1">
                <label class="flex items-start p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-indigo-50/50 cursor-pointer transition select-none">
                    <input type="checkbox" name="puede_gestionar_prestamos" value="1" {{ old('puede_gestionar_prestamos', $usuario->puede_gestionar_prestamos) ? 'checked' : '' }} class="mt-0.5 rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                    <div class="ml-3">
                        <span class="text-xs font-bold text-slate-900 block">💳 Otorgar Préstamos y Deducciones</span>
                        <span class="text-xxs text-slate-500">Permite registrar anticipos y deducciones salariales.</span>
                    </div>
                </label>

                <label class="flex items-start p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-indigo-50/50 cursor-pointer transition select-none">
                    <input type="checkbox" name="puede_aprobar_retiros" value="1" {{ old('puede_aprobar_retiros', $usuario->puede_aprobar_retiros) ? 'checked' : '' }} class="mt-0.5 rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                    <div class="ml-3">
                        <span class="text-xs font-bold text-slate-900 block">⚠️ Aprobar Permisos y Retiros</span>
                        <span class="text-xxs text-slate-500">Permite justificar retiros anticipados y ausencias.</span>
                    </div>
                </label>

                <label class="flex items-start p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-indigo-50/50 cursor-pointer transition select-none">
                    <input type="checkbox" name="puede_ver_expedientes" value="1" {{ old('puede_ver_expedientes', $usuario->puede_ver_expedientes) ? 'checked' : '' }} class="mt-0.5 rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                    <div class="ml-3">
                        <span class="text-xs font-bold text-slate-900 block">📁 Ver Expedientes Digitales</span>
                        <span class="text-xxs text-slate-500">Acceso a consultar documentos y soportes médicos.</span>
                    </div>
                </label>

                <label class="flex items-start p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-indigo-50/50 cursor-pointer transition select-none">
                    <input type="checkbox" name="puede_modificar_trabajadores" value="1" {{ old('puede_modificar_trabajadores', $usuario->puede_modificar_trabajadores) ? 'checked' : '' }} class="mt-0.5 rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                    <div class="ml-3">
                        <span class="text-xs font-bold text-slate-900 block">👥 Modificar y Cargar Personal</span>
                        <span class="text-xxs text-slate-500">Alta y edición de fichas, sedes y horarios de trabajadores.</span>
                    </div>
                </label>

                <label class="flex items-start p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-indigo-50/50 cursor-pointer transition select-none">
                    <input type="checkbox" name="puede_procesar_nominas" value="1" {{ old('puede_procesar_nominas', $usuario->puede_procesar_nominas) ? 'checked' : '' }} class="mt-0.5 rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                    <div class="ml-3">
                        <span class="text-xs font-bold text-slate-900 block">📑 Procesar Nóminas e Imprimir Recibos</span>
                        <span class="text-xxs text-slate-500">Cierre de nómina quincenal/semanal y emisión de recibos.</span>
                    </div>
                </label>

                <label class="flex items-start p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-indigo-50/50 cursor-pointer transition select-none">
                    <input type="checkbox" name="puede_ver_asistencias" value="1" {{ old('puede_ver_asistencias', $usuario->puede_ver_asistencias) ? 'checked' : '' }} class="mt-0.5 rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                    <div class="ml-3">
                        <span class="text-xs font-bold text-slate-900 block">⏱️ Auditar Asistencias y Retardos</span>
                        <span class="text-xxs text-slate-500">Control de marcajes GPS y alertas satelitales.</span>
                    </div>
                </label>
            </div>
        </div>

        <div class="pt-4 flex justify-end space-x-3">
            <a href="{{ route('usuarios_empresa.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-xs font-bold text-slate-700 hover:bg-slate-50">Cancelar</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-xs font-bold shadow">
                Actualizar Permisos
            </button>
        </div>
    </form>
</div>
@endsection
