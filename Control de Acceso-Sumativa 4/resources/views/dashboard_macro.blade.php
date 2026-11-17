@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Encabezado Principal del Panel Macro (SuperAdmin) -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white p-6 rounded-2xl shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border border-indigo-900/50">
        <div>
            <div class="flex items-center space-x-2">
                <span class="bg-amber-500 text-slate-950 text-xs font-black px-2.5 py-0.5 rounded-full uppercase tracking-wider">SuperAdmin</span>
                <h1 class="text-2xl font-black tracking-wide text-white">Panel Macro — Operador Central SaaS</h1>
            </div>
            <p class="text-xs text-indigo-200 mt-1.5">
                Administración B2B: Gestión de clientes, asignación de cuotas (trabajadores y sedes), control de suspensión por mora y normativas legales.
            </p>
        </div>
        <div class="flex flex-wrap gap-2.5">
            <a href="{{ route('empresas.create') }}" class="bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-black px-4 py-2.5 rounded-xl shadow transition flex items-center space-x-1.5">
                <span>➕ Registrar Nueva Empresa</span>
            </a>
            <a href="{{ route('leyes.index') }}" class="bg-indigo-800/80 hover:bg-indigo-700 text-white border border-indigo-600/50 text-xs font-bold px-4 py-2.5 rounded-xl shadow transition flex items-center space-x-1.5">
                <span>⚖️ Actualizar Leyes Laborales</span>
            </a>
        </div>
    </div>

    <!-- 4 Métricas Clave del SaaS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- 1. Empresas Clientes -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-center text-xs font-bold text-slate-400 uppercase">
                    <span>Empresas Clientes</span>
                    <span class="text-xl">🏢</span>
                </div>
                <p class="text-3xl font-black text-slate-900 mt-2 font-mono">{{ $totalEmpresas }}</p>
            </div>
            <div class="pt-3 border-t border-slate-100 mt-3 text-xxs flex justify-between">
                <span class="text-emerald-700 font-bold">🟢 {{ $empresasActivas }} Activas</span>
                <span class="text-rose-600 font-bold">🔴 {{ $empresasSuspendidas }} Suspendidas</span>
            </div>
        </div>

        <!-- 2. Personal Global Bajo Plataforma -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-center text-xs font-bold text-slate-400 uppercase">
                    <span>Trabajadores Totales</span>
                    <span class="text-xl">👥</span>
                </div>
                <p class="text-3xl font-black text-indigo-900 mt-2 font-mono">{{ $totalTrabajadores }}</p>
            </div>
            <div class="pt-3 border-t border-slate-100 mt-3 text-xxs text-slate-500">
                Empleados gestionados en todos los clientes
            </div>
        </div>

        <!-- 3. Sedes Georeferenciadas -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-center text-xs font-bold text-slate-400 uppercase">
                    <span>Sedes & Sucursales</span>
                    <span class="text-xl">📍</span>
                </div>
                <p class="text-3xl font-black text-emerald-800 mt-2 font-mono">{{ $totalSedes }}</p>
            </div>
            <div class="pt-3 border-t border-slate-100 mt-3 text-xxs text-slate-500">
                Puntos de control GPS satelital activos
            </div>
        </div>

        <!-- 4. Normativas y Leyes Laborales -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-center text-xs font-bold text-slate-400 uppercase">
                    <span>Parámetros Legales</span>
                    <span class="text-xl">⚖️</span>
                </div>
                <p class="text-3xl font-black text-amber-700 mt-2 font-mono">{{ $totalParametrosLegales }}</p>
            </div>
            <div class="pt-3 border-t border-slate-100 mt-3 text-xxs text-slate-500">
                <a href="{{ route('leyes.index') }}" class="text-indigo-600 font-bold hover:underline">Gestionar Leyes LOTTT &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Gestión de Empresas y Control de Pagos / Suspensión -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-900">Empresas Clientes en la Plataforma</h2>
                <p class="text-xs text-slate-500">Configure cupos de personal y sedes, habilite o suspenda acceso por falta de pago e inspeccione el panel de cada cliente.</p>
            </div>
            <a href="{{ route('empresas.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                Ver Listado Completo &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-xs">
                <thead class="bg-slate-50 text-slate-600 font-semibold uppercase">
                    <tr>
                        <th class="px-5 py-3 text-left">Empresa / RIF</th>
                        <th class="px-5 py-3 text-left">Cuotas SaaS (Trabajadores & Sedes)</th>
                        <th class="px-5 py-3 text-center">Estado de Servicio</th>
                        <th class="px-5 py-3 text-left">Teléfono Alertas</th>
                        <th class="px-5 py-3 text-center">Acciones SuperAdmin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($empresas as $emp)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-4">
                            <p class="font-bold text-slate-900 text-sm">{{ $emp->nombre_comercial }}</p>
                            <p class="text-xxs font-mono text-slate-400">{{ $emp->rif }} &bull; {{ $emp->razon_social }}</p>
                            @if($emp->isSuspendida() && $emp->motivo_suspension)
                                <p class="text-xxs font-bold text-rose-600 mt-0.5">⚠️ Motivo: {{ $emp->motivo_suspension }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="space-y-1">
                                <p class="font-mono text-slate-700">
                                    <strong>Trabajadores:</strong> {{ $emp->trabajadores_count }} / <span class="text-indigo-700 font-bold">{{ $emp->limite_trabajadores }}</span>
                                </p>
                                <p class="font-mono text-slate-500 text-xxs">
                                    <strong>Sedes:</strong> {{ $emp->sedes_count }} / {{ $emp->limite_sedes ?? 3 }} permitidas
                                </p>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($emp->isActiva())
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xxs font-black bg-emerald-100 text-emerald-800">
                                ● ACTIVA (Al Día)
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xxs font-black bg-rose-100 text-rose-800">
                                ✕ SUSPENDIDA (Mora)
                            </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 font-mono text-emerald-700 font-bold">
                            📲 {{ $emp->telefono_notificaciones }}
                        </td>
                        <td class="px-5 py-4 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <!-- Botón Entrar al Panel de la Empresa (Modo Auditor) -->
                                <form method="POST" action="{{ route('empresas.impersonar', $emp->id) }}">
                                    @csrf
                                    <button type="submit" title="Entrar al panel como auditor" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 text-xxs font-bold px-2.5 py-1.5 rounded-lg transition">
                                        👁️ Entrar al Panel
                                    </button>
                                </form>

                                <!-- Botón de Suspender o Habilitar -->
                                @if($emp->isActiva())
                                <button type="button" onclick="abrirModalSuspension({{ $emp->id }}, '{{ addslashes($emp->nombre_comercial) }}')" class="bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xxs font-bold px-2.5 py-1.5 rounded-lg transition">
                                    🚫 Suspender
                                </button>
                                @else
                                <form method="POST" action="{{ route('empresas.toggle_estado', $emp->id) }}" onsubmit="return confirm('¿Desea restablecer el acceso para esta empresa?');">
                                    @csrf
                                    <button type="submit" class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-xxs font-bold px-2.5 py-1.5 rounded-lg transition">
                                        ✓ Habilitar
                                    </button>
                                </form>
                                @endif

                                <a href="{{ route('empresas.show', $emp->id) }}" class="text-slate-500 hover:text-slate-800 text-xxs font-bold px-2 py-1.5 rounded bg-slate-100 hover:bg-slate-200 transition">
                                    Ficha &rarr;
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-slate-400">No hay empresas registradas en la plataforma.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para Suspender Empresa por Falta de Pago -->
<div id="modalSuspender" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="text-sm font-bold text-slate-900">Suspender Servicio a Empresa</h3>
            <button type="button" onclick="document.getElementById('modalSuspender').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
        </div>

        <p class="text-xs text-slate-500">
            Al suspender a <strong id="nombreEmpresaSuspender" class="text-slate-800"></strong>, todos sus usuarios y trabajadores tendrán el acceso bloqueado de inmediato hasta que se regularice su situación.
        </p>

        <form method="POST" id="formSuspension" action="" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Motivo de la Suspensión *</label>
                <select name="motivo_suspension" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-xs">
                    <option value="Mora en el pago de suscripción mensual SaaS">Mora en el pago de suscripción mensual SaaS</option>
                    <option value="Incumplimiento de términos y condiciones de servicio">Incumplimiento de términos y condiciones de servicio</option>
                    <option value="Suspensión temporal a solicitud del cliente">Suspensión temporal a solicitud del cliente</option>
                    <option value="Vencimiento del período de prueba">Vencimiento del período de prueba</option>
                </select>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('modalSuspender').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg">Cancelar</button>
                <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 text-xs font-bold rounded-lg shadow">Confirmar Suspensión</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalSuspension(id, nombre) {
    document.getElementById('nombreEmpresaSuspender').innerText = nombre;
    document.getElementById('formSuspension').action = '/empresas/' + id + '/toggle-estado';
    document.getElementById('modalSuspender').classList.remove('hidden');
}
</script>
@endsection
