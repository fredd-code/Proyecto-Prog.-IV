@extends('layouts.app')

@section('content')
<div class="space-y-6">
    {{-- Encabezado Principal y Acciones de Operador Macro --}}
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <div class="flex items-center space-x-3">
                <span class="text-3xl">🏢</span>
                <div>
                    <h1 class="text-2xl font-black text-slate-900">{{ $empresa->nombre_comercial }}</h1>
                    <p class="text-xs text-slate-500 font-mono mt-0.5">
                        RIF: <strong class="text-slate-700">{{ $empresa->rif }}</strong> &bull; Razón Social: {{ $empresa->razon_social }}
                    </p>
                </div>
                <span class="px-3 py-1 text-xs font-black rounded-full uppercase tracking-wider {{ $empresa->isActiva() ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-rose-100 text-rose-800 border border-rose-300' }}">
                    {{ $empresa->estado }}
                </span>
            </div>
            @if($empresa->isSuspendida() && $empresa->motivo_suspension)
                <div class="mt-2 text-xs text-rose-700 bg-rose-50 border border-rose-200 px-3 py-1.5 rounded-lg flex items-center space-x-2">
                    <span>⚠️ <strong>Causa de Suspensión:</strong> {{ $empresa->motivo_suspension }}</span>
                </div>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if(auth()->user()->isSuperAdmin())
                {{-- Botón Entrar al Panel de la Empresa (Modo Auditor) --}}
                <form method="POST" action="{{ route('empresas.impersonar', $empresa->id) }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow transition flex items-center space-x-1.5">
                        <span>👁️ Entrar al Panel de la Empresa</span>
                    </button>
                </form>

                {{-- Botón Habilitar / Suspender por Pago --}}
                @if($empresa->isActiva())
                    <button type="button" onclick="document.getElementById('modalSuspenderShow').classList.remove('hidden')" class="bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 text-xs font-bold px-3.5 py-2.5 rounded-xl transition">
                        ⛔ Suspender por Mora
                    </button>
                @else
                    <form method="POST" action="{{ route('empresas.toggle_estado', $empresa->id) }}" class="inline" onsubmit="return confirm('¿Restablecer el acceso al servicio para esta empresa?');">
                        @csrf
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-3.5 py-2.5 rounded-xl shadow transition">
                            ✓ Reactivar Acceso
                        </button>
                    </form>
                @endif

                <a href="{{ route('empresas.edit', $empresa->id) }}" class="bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold px-3.5 py-2.5 rounded-xl transition">
                    ✏️ Configurar Cuotas
                </a>
            @endif

            <a href="{{ route('empresas.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 px-3 py-2">
                &larr; Volver
            </a>
        </div>
    </div>

    {{-- Tarjeta de Credenciales de Acceso Nuevas (Recién Creada) --}}
    @if(session('credenciales_nuevas'))
    <div class="bg-gradient-to-r from-amber-500 via-amber-600 to-amber-700 text-white p-6 rounded-2xl shadow-xl space-y-4">
        <div class="flex items-start justify-between">
            <div>
                <span class="bg-amber-900/60 text-amber-200 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                    🔐 Cuenta Administrador Generada para el Cliente
                </span>
                <h3 class="text-xl font-black mt-2">Credenciales de Acceso Oficiales para {{ session('credenciales_nuevas.empresa') }}</h3>
                <p class="text-xs text-amber-100 mt-1">
                    Haga entrega de estas credenciales al representante o dueño de la empresa para que pueda iniciar sesión en el sistema.
                </p>
            </div>
            <button onclick="copiarCredenciales()" class="bg-slate-900 hover:bg-black text-amber-300 font-bold text-xs px-4 py-2 rounded-xl transition shadow flex items-center space-x-1.5">
                <span id="btnCopiarTexto">📋 Copiar Credenciales</span>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-slate-950/40 p-4 rounded-xl border border-amber-400/30 font-mono text-sm">
            <div>
                <span class="text-xs text-amber-200 uppercase font-sans font-semibold block">Nombre del Administrador</span>
                <span class="font-bold text-white">{{ session('credenciales_nuevas.nombre') }}</span>
            </div>
            <div>
                <span class="text-xs text-amber-200 uppercase font-sans font-semibold block">Usuario / Correo Electrónico</span>
                <span class="font-bold text-amber-300" id="credEmail">{{ session('credenciales_nuevas.email') }}</span>
            </div>
            <div>
                <span class="text-xs text-amber-200 uppercase font-sans font-semibold block">Contraseña Temporal</span>
                <span class="font-bold text-emerald-300 text-base" id="credPassword">{{ session('credenciales_nuevas.password') }}</span>
            </div>
        </div>

        <p class="text-[11px] text-amber-200">
            ℹ️ El usuario administrador de la empresa podrá ingresar inmediatamente, registrar sedes, crear trabajadores y delegar permisos a su equipo.
        </p>
    </div>
    @endif

    <!-- Tarjetas de Parámetros de Operación y Cuotas SaaS -->
    @php
        $limiteTrab = $empresa->limite_trabajadores ?? 15;
        $usadosTrab = $empresa->trabajadores->count();
        $disponiblesTrab = max(0, $limiteTrab - $usadosTrab);
        $porcentajeTrab = min(100, round(($usadosTrab / max(1, $limiteTrab)) * 100));

        $limiteSedes = $empresa->limite_sedes ?? 3;
        $usadasSedes = $empresa->sedes->count();
        $disponiblesSedes = max(0, $limiteSedes - $usadasSedes);
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Cupo de Trabajadores -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex justify-between items-center">
                <p class="text-xs font-bold text-slate-400 uppercase">Cupo de Trabajadores (SaaS)</p>
                <span class="text-xs font-bold px-2 py-0.5 rounded {{ $usadosTrab >= $limiteTrab ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}">
                    {{ $disponiblesTrab }} cupos libres
                </span>
            </div>
            <p class="text-2xl font-bold font-mono mt-2 {{ $usadosTrab >= $limiteTrab ? 'text-rose-600' : 'text-slate-800' }}">
                {{ $usadosTrab }} <span class="text-sm font-normal text-slate-400">/ {{ $limiteTrab }} personal</span>
            </p>
            <div class="w-full bg-slate-100 rounded-full h-2 mt-3 overflow-hidden">
                <div class="h-2 rounded-full {{ $usadosTrab >= $limiteTrab ? 'bg-rose-500' : 'bg-emerald-500' }}" style="width: {{ $porcentajeTrab }}%"></div>
            </div>
            <p class="text-[11px] text-slate-400 mt-2">Capacidad ocupada al {{ $porcentajeTrab }}%. Límite administrado por el Operador Macro.</p>
        </div>

        <!-- Límite de Sedes Autorizadas -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex justify-between items-center">
                <p class="text-xs font-bold text-slate-400 uppercase">Límite de Sedes Físicas</p>
                <span class="text-xs font-bold px-2 py-0.5 rounded {{ $usadasSedes >= $limiteSedes ? 'bg-amber-100 text-amber-800' : 'bg-indigo-100 text-indigo-800' }}">
                    {{ $disponiblesSedes }} disponibles
                </span>
            </div>
            <p class="text-2xl font-bold font-mono mt-2 text-indigo-900">
                {{ $usadasSedes }} <span class="text-sm font-normal text-slate-400">/ {{ $limiteSedes }} sedes</span>
            </p>
            <p class="text-xs text-slate-500 mt-2">Cantidad máxima de sucursales o centros de trabajo geocercados contratados.</p>
        </div>

        <!-- Alertas Móviles -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-xs font-bold text-slate-400 uppercase">Centro de Alertas Móvil</p>
            <p class="text-lg font-bold text-emerald-700 font-mono mt-2">📲 {{ $empresa->telefono_notificaciones }}</p>
            <p class="text-xs text-slate-500 mt-2">Canal receptor de notificaciones automáticas por retardos, ausencias injustificadas y violaciones satelitales.</p>
        </div>
    </div>

    <!-- Módulo Multi-Sede: Listado de Sedes y Sucursales -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-900 flex items-center space-x-2">
                    <span>📍 Sedes y Sucursales Georeferenciadas</span>
                </h2>
                <p class="text-xs text-slate-500">Centros de trabajo de la empresa. Cada trabajador puede vincularse a una o múltiples sedes.</p>
            </div>
            @if($empresa->haAlcanzadoLimiteSedes())
                <span class="text-xs font-bold text-amber-700 bg-amber-50 border border-amber-200 px-3 py-1.5 rounded-lg">
                    ⚠️ Límite de {{ $limiteSedes }} Sedes Alcanzado
                </span>
            @else
                <button type="button" onclick="document.getElementById('modalNuevaSede').classList.remove('hidden')" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2 rounded-xl shadow transition flex items-center space-x-1.5">
                    <span>➕ Añadir Sede</span>
                </button>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-xs">
                <thead class="bg-slate-50 text-slate-600 font-semibold uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Nombre de Sede / Código</th>
                        <th class="px-4 py-3 text-left">Dirección &amp; Teléfono</th>
                        <th class="px-4 py-3 text-center">Coordenadas GPS</th>
                        <th class="px-4 py-3 text-center">Radio Geocerca</th>
                        <th class="px-4 py-3 text-center">Trabajadores</th>
                        <th class="px-4 py-3 text-center">Tipo</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($empresa->sedes as $sede)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-semibold text-slate-900">
                            {{ $sede->nombre }}
                            @if($sede->codigo)
                            <span class="block text-[10px] font-mono text-slate-400">Cód: {{ $sede->codigo }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-600">
                            <p>{{ $sede->direccion ?? 'Sin dirección especificada' }}</p>
                            @if($sede->telefono_contacto)
                            <p class="text-[10px] text-emerald-700 font-mono">📞 {{ $sede->telefono_contacto }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center font-mono text-slate-700">
                            {{ round($sede->latitud, 5) }}, {{ round($sede->longitud, 5) }}
                        </td>
                        <td class="px-4 py-3 text-center font-mono font-bold text-indigo-700">
                            {{ $sede->radio_geocerca_metros }} m
                        </td>
                        <td class="px-4 py-3 text-center font-bold text-slate-800">
                            {{ $sede->trabajadores_count ?? $sede->trabajadores->count() }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($sede->es_principal)
                            <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Sede Principal</span>
                            @else
                            <span class="bg-slate-100 text-slate-600 text-[10px] font-bold px-2 py-0.5 rounded-full">Sucursal</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center space-x-2">
                            @if(!$sede->es_principal && ($sede->trabajadores_count ?? $sede->trabajadores->count()) == 0)
                            <form method="POST" action="{{ route('sedes.destroy', $sede->id) }}" class="inline" onsubmit="return confirm('¿Seguro de eliminar esta sede?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-800 text-xs font-bold">Eliminar</button>
                            </form>
                            @else
                            <span class="text-[10px] text-slate-400">Protegida</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-slate-400">No hay sedes registradas para esta empresa.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Módulo de Usuarios y Equipo de la Empresa (RBAC) -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-4">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-base font-bold text-slate-900 flex items-center space-x-2">
                    <span>🔐 Cuentas de Acceso y Permisos de la Empresa</span>
                </h2>
                <p class="text-xs text-slate-500">Usuarios asignados al portal de esta empresa y matriz de autorizaciones RBAC.</p>
            </div>
            @if(auth()->user()->isSuperAdmin())
                <form method="POST" action="{{ route('empresas.impersonar', $empresa->id) }}">
                    @csrf
                    <button type="submit" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                        Gestionar Equipo &rarr;
                    </button>
                </form>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-xs">
                <thead class="bg-slate-50 text-slate-600 font-semibold uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Usuario / Correo</th>
                        <th class="px-4 py-3 text-left">Rol &amp; Cargo</th>
                        <th class="px-4 py-3 text-center">Permisos Asignados</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($empresa->usuarios as $u)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <p class="font-bold text-slate-900">{{ $u->name }}</p>
                            <p class="font-mono text-[11px] text-slate-400">{{ $u->email }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $u->isAdminEmpresa() ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700' }}">
                                {{ $u->rol === 'admin_empresa' ? 'Administrador Empresa' : 'Operador Delegado' }}
                            </span>
                            <p class="text-slate-500 text-[11px] mt-0.5">{{ $u->cargo_interno ?? 'Sin cargo definido' }}</p>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($u->isAdminEmpresa())
                                <span class="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                    ★ Todos los Permisos
                                </span>
                            @else
                                <div class="flex flex-wrap gap-1 justify-center max-w-xs mx-auto">
                                    @if($u->puede_gestionar_prestamos)
                                        <span class="bg-indigo-50 text-indigo-700 px-1.5 py-0.5 rounded text-[9px] font-semibold">Préstamos</span>
                                    @endif
                                    @if($u->puede_ver_expedientes)
                                        <span class="bg-indigo-50 text-indigo-700 px-1.5 py-0.5 rounded text-[9px] font-semibold">Expedientes</span>
                                    @endif
                                    @if($u->puede_modificar_trabajadores)
                                        <span class="bg-indigo-50 text-indigo-700 px-1.5 py-0.5 rounded text-[9px] font-semibold">Modif. Personal</span>
                                    @endif
                                    @if($u->puede_procesar_nominas)
                                        <span class="bg-indigo-50 text-indigo-700 px-1.5 py-0.5 rounded text-[9px] font-semibold">Nóminas</span>
                                    @endif
                                    @if($u->puede_ver_asistencias)
                                        <span class="bg-indigo-50 text-indigo-700 px-1.5 py-0.5 rounded text-[9px] font-semibold">Asistencias</span>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $u->estado_usuario === 'activo' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ ucfirst($u->estado_usuario) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-slate-400">No hay usuarios dados de alta en esta empresa aún.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bitácora de Alertas y Notificaciones Móviles -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-base font-bold text-slate-900 mb-4 flex items-center space-x-2">
            <span>📲 Bitácora de Notificaciones Enviadas a {{ $empresa->telefono_notificaciones }}</span>
        </h2>
        <div class="divide-y divide-slate-100">
            @forelse($empresa->notificaciones as $notif)
            <div class="py-3 flex justify-between items-center text-xs">
                <div>
                    <span class="font-bold px-2 py-0.5 rounded uppercase font-mono {{ $notif->tipo_evento === 'fuera_geocerca' ? 'bg-rose-100 text-rose-800' : ($notif->tipo_evento === 'retardo' ? 'bg-amber-100 text-amber-800' : 'bg-indigo-100 text-indigo-800') }}">
                        {{ str_replace('_', ' ', $notif->tipo_evento) }}
                    </span>
                    <span class="text-slate-800 ml-2 font-medium">{{ $notif->mensaje }}</span>
                </div>
                <div class="text-slate-400 font-mono">
                    {{ $notif->created_at->format('d/m/Y h:i A') }} &bull; <span class="text-emerald-600 font-bold">Despacho Móvil OK</span>
                </div>
            </div>
            @empty
            <p class="text-xs text-slate-400 py-4 text-center">No se han emitido alertas para esta empresa.</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal para Registrar Nueva Sede -->
<div id="modalNuevaSede" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="text-base font-bold text-slate-900">Agregar Nueva Sede / Sucursal</h3>
            <button type="button" onclick="document.getElementById('modalNuevaSede').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
        </div>

        <form method="POST" action="{{ route('sedes.store', $empresa->id) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nombre de la Sede *</label>
                <input type="text" name="nombre" placeholder="Ej: Sucursal Valencia / Almacén Guatire" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Código de Sede</label>
                    <input type="text" name="codigo" placeholder="Ej: SEDE-02" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Teléfono Sede</label>
                    <input type="text" name="telefono_contacto" placeholder="Ej: 0241-1234567" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Dirección Física</label>
                <input type="text" name="direccion" placeholder="Calle, Centro Comercial, Local" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Latitud GPS *</label>
                    <input type="number" step="any" name="latitud" value="10.246900" required class="w-full border border-slate-300 rounded-lg px-2.5 py-2 text-xs font-mono focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Longitud GPS *</label>
                    <input type="number" step="any" name="longitud" value="-67.595800" required class="w-full border border-slate-300 rounded-lg px-2.5 py-2 text-xs font-mono focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Radio (Metros) *</label>
                    <input type="number" name="radio_geocerca_metros" value="200" min="20" max="5000" required class="w-full border border-slate-300 rounded-lg px-2.5 py-2 text-xs font-mono focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('modalNuevaSede').classList.add('hidden')" class="px-4 py-2 border border-slate-300 rounded-lg text-xs font-semibold text-slate-700 hover:bg-slate-50">Cancelar</button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-xs font-bold shadow">Guardar Sede</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para Suspender Empresa -->
<div id="modalSuspenderShow" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="text-base font-bold text-rose-700">⛔ Suspender Servicio por Mora</h3>
            <button type="button" onclick="document.getElementById('modalSuspenderShow').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
        </div>

        <p class="text-xs text-slate-600">
            Al suspender a <strong>{{ $empresa->nombre_comercial }}</strong>, ningún usuario de esta empresa podrá acceder ni registrar asistencias.
        </p>

        <form method="POST" action="{{ route('empresas.toggle_estado', $empresa->id) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Motivo de Suspensión *</label>
                <textarea name="motivo_suspension" rows="3" required class="w-full border border-slate-300 rounded-lg p-2.5 text-xs focus:ring-2 focus:ring-rose-500">Falta de pago del canon mensual del servicio de nómina SaaS.</textarea>
            </div>

            <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalSuspenderShow').classList.add('hidden')" class="px-4 py-2 border border-slate-300 rounded-lg text-xs font-semibold text-slate-700 hover:bg-slate-50">
                    Cancelar
                </button>
                <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-lg text-xs font-bold shadow">
                    Confirmar Suspensión
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function copiarCredenciales() {
        const email = document.getElementById('credEmail').innerText;
        const password = document.getElementById('credPassword').innerText;
        const texto = `Credenciales SIGEN SaaS:\nUsuario: ${email}\nClave: ${password}\nAcceso: ${window.location.origin}/login`;

        navigator.clipboard.writeText(texto).then(() => {
            const btn = document.getElementById('btnCopiarTexto');
            btn.innerText = '✓ ¡Copiado al Portapapeles!';
            setTimeout(() => {
                btn.innerText = '📋 Copiar Credenciales';
            }, 3000);
        });
    }
</script>
@endsection
