@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="bg-indigo-100 text-indigo-900 text-xs font-black px-2.5 py-0.5 rounded-full uppercase">Seguridad & RBAC</span>
                <h1 class="text-2xl font-black text-slate-900">Usuarios y Permisos de {{ $empresa->nombre_comercial }}</h1>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Defina qué usuarios de su empresa tienen acceso a funciones sensibles: otorgar préstamos, ver expedientes, procesar nóminas o modificar personal.
            </p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('usuarios_empresa.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow transition">
                + Nuevo Usuario del Equipo
            </a>
            <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 self-center">
                &larr; Volver al panel
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-xl text-xs font-bold">
        ✓ {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Matriz de Acceso y Funciones por Módulo</h2>
            <span class="text-xxs text-slate-400 font-mono">{{ $usuarios->count() }} usuarios registrados</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-xs">
                <thead class="bg-slate-50 text-slate-600 font-semibold uppercase">
                    <tr>
                        <th class="px-5 py-3 text-left">Usuario / Cargo</th>
                        <th class="px-5 py-3 text-left">Rol de Cuenta</th>
                        <th class="px-5 py-3 text-left">Módulos y Permisos Concedidos</th>
                        <th class="px-5 py-3 text-center">Estado</th>
                        <th class="px-5 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($usuarios as $u)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-4">
                            <p class="font-bold text-slate-900 text-sm">{{ $u->name }}</p>
                            <p class="text-xxs font-mono text-slate-400">{{ $u->email }}</p>
                            <p class="text-xxs text-indigo-700 font-semibold mt-0.5">{{ $u->cargo_interno ?? 'Personal Administrativo' }}</p>
                        </td>
                        <td class="px-5 py-4">
                            @if($u->isAdminEmpresa())
                            <span class="bg-indigo-900 text-white font-mono text-xxs font-black px-2 py-0.5 rounded">
                                Administrador Principal
                            </span>
                            @else
                            <span class="bg-slate-100 text-slate-700 font-mono text-xxs font-bold px-2 py-0.5 rounded">
                                {{ ucfirst($u->rol) }}
                            </span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @if($u->isAdminEmpresa())
                            <span class="text-emerald-700 font-bold text-xxs bg-emerald-50 px-2 py-1 rounded border border-emerald-200">
                                ★ Todos los permisos y funciones habilitadas
                            </span>
                            @else
                            <div class="flex flex-wrap gap-1.5 max-w-md">
                                @if($u->puede_gestionar_prestamos)
                                <span class="bg-amber-50 text-amber-800 border border-amber-200 px-1.5 py-0.5 rounded text-xxs font-bold">💳 Préstamos / Deducciones</span>
                                @endif
                                @if($u->puede_aprobar_retiros)
                                <span class="bg-rose-50 text-rose-800 border border-rose-200 px-1.5 py-0.5 rounded text-xxs font-bold">⚠️ Permisos y Retiros</span>
                                @endif
                                @if($u->puede_ver_expedientes)
                                <span class="bg-blue-50 text-blue-800 border border-blue-200 px-1.5 py-0.5 rounded text-xxs font-bold">📁 Ver Expedientes</span>
                                @endif
                                @if($u->puede_modificar_trabajadores)
                                <span class="bg-purple-50 text-purple-800 border border-purple-200 px-1.5 py-0.5 rounded text-xxs font-bold">👥 Modificar Trabajadores</span>
                                @endif
                                @if($u->puede_procesar_nominas)
                                <span class="bg-emerald-50 text-emerald-800 border border-emerald-200 px-1.5 py-0.5 rounded text-xxs font-bold">📑 Procesar Nómina & Recibos</span>
                                @endif
                                @if($u->puede_ver_asistencias)
                                <span class="bg-teal-50 text-teal-800 border border-teal-200 px-1.5 py-0.5 rounded text-xxs font-bold">⏱️ Auditar Asistencias</span>
                                @endif

                                @if(!$u->puede_gestionar_prestamos && !$u->puede_aprobar_retiros && !$u->puede_ver_expedientes && !$u->puede_modificar_trabajadores && !$u->puede_procesar_nominas && !$u->puede_ver_asistencias)
                                <span class="text-slate-400 text-xxs">Solo Lectura General</span>
                                @endif
                            </div>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($u->estado_usuario === 'activo')
                            <span class="text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full font-bold text-xxs">Activo</span>
                            @else
                            <span class="text-rose-700 bg-rose-50 px-2 py-0.5 rounded-full font-bold text-xxs">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center space-x-1">
                            @if(!$u->isAdminEmpresa() || auth()->user()->isSuperAdmin())
                            <a href="{{ route('usuarios_empresa.edit', $u->id) }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xxs font-bold px-2.5 py-1.5 rounded-lg transition">
                                Editar Permisos
                            </a>
                            @if($u->id !== auth()->id())
                            <form method="POST" action="{{ route('usuarios_empresa.destroy', $u->id) }}" class="inline" onsubmit="return confirm('¿Seguro de revocar el acceso a este usuario?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-800 text-xxs font-bold px-2 py-1">Eliminar</button>
                            </form>
                            @endif
                            @else
                            <span class="text-slate-400 text-xxs font-semibold">Titular</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-slate-400">No hay usuarios adicionales registrados para esta empresa.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
