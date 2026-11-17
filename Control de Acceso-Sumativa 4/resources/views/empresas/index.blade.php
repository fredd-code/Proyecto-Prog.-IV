@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center space-x-2">
                <span>🏢 Gestión de Empresas Clientes (Panel Macro)</span>
            </h1>
            <p class="text-sm text-slate-500">
                Administración SaaS: Control de cuotas de personal, límites de sedes, credenciales y suspensión por mora.
            </p>
        </div>
        <a href="{{ route('empresas.create') }}" class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-sm px-4 py-2.5 rounded-xl shadow transition flex items-center space-x-2">
            <span>➕ Dar de Alta Nueva Empresa</span>
        </a>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-xl text-sm font-medium shadow-sm">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-900 text-slate-200 font-semibold uppercase text-xs">
                    <tr>
                        <th class="px-5 py-3 text-left">Empresa / RIF</th>
                        <th class="px-4 py-3 text-left">Contacto &amp; Alertas</th>
                        <th class="px-4 py-3 text-center">Cuota Sedes</th>
                        <th class="px-4 py-3 text-left">Cupo Trabajadores</th>
                        <th class="px-4 py-3 text-center">Estado Acceso</th>
                        <th class="px-5 py-3 text-right">Acciones de Operador</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($empresas as $emp)
                    @php
                        $limiteTrab = $emp->limite_trabajadores ?? 15;
                        $usadosTrab = $emp->trabajadores_count;
                        $porcTrab = min(100, round(($usadosTrab / max(1, $limiteTrab)) * 100));

                        $limiteSedes = $emp->limite_sedes ?? 3;
                        $usadasSedes = $emp->sedes_count;
                    @endphp
                    <tr class="hover:bg-slate-50 transition {{ $emp->isSuspendida() ? 'bg-rose-50/40' : '' }}">
                        <td class="px-5 py-4">
                            <div class="flex items-center space-x-2">
                                <span class="text-xl">🏢</span>
                                <div>
                                    <a href="{{ route('empresas.show', $emp->id) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition block">
                                        {{ $emp->nombre_comercial }}
                                    </a>
                                    <span class="text-xs font-mono text-slate-400">RIF: {{ $emp->rif }} &bull; {{ Str::limit($emp->razon_social, 28) }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-xs">
                            <p class="font-mono font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded inline-block">
                                📲 {{ $emp->telefono_notificaciones }}
                            </p>
                            <p class="text-slate-400 mt-1">{{ $emp->email_contacto ?? 'Sin correo' }}</p>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $usadasSedes >= $limiteSedes ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700' }}">
                                📍 {{ $usadasSedes }} / {{ $limiteSedes }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center justify-between text-xs font-mono mb-1">
                                <span class="font-bold {{ $usadosTrab >= $limiteTrab ? 'text-rose-600' : 'text-slate-700' }}">
                                    👥 {{ $usadosTrab }} / {{ $limiteTrab }} cupos
                                </span>
                                <span class="text-slate-400 font-sans">{{ $porcTrab }}%</span>
                            </div>
                            <div class="w-36 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="h-1.5 rounded-full {{ $usadosTrab >= $limiteTrab ? 'bg-rose-500' : ($porcTrab > 80 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ $porcTrab }}%"></div>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-center">
                            @if($emp->isActiva())
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    ✓ Activa
                                </span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-rose-100 text-rose-800 border border-rose-200" title="{{ $emp->motivo_suspension }}">
                                    ⛔ Suspendida
                                </span>
                                @if($emp->motivo_suspension)
                                    <span class="block text-[10px] text-rose-600 mt-1 max-w-[120px] truncate mx-auto">
                                        {{ $emp->motivo_suspension }}
                                    </span>
                                @endif
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right space-x-1.5 whitespace-nowrap">
                            {{-- Botón Entrar al Panel de la Empresa (Modo Auditor) --}}
                            <form method="POST" action="{{ route('empresas.impersonar', $emp->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-sm transition" title="Entrar al portal de esta empresa para auditar">
                                    👁️ Entrar al Panel
                                </button>
                            </form>

                            {{-- Botón Habilitar / Suspender --}}
                            @if($emp->isActiva())
                                <button type="button" onclick="abrirModalSuspender({{ $emp->id }}, '{{ addslashes($emp->nombre_comercial) }}')" class="bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold px-2.5 py-1.5 rounded-lg transition" title="Suspender acceso por falta de pago">
                                    ⛔ Suspender
                                </button>
                            @else
                                <form method="POST" action="{{ route('empresas.toggle_estado', $emp->id) }}" class="inline" onsubmit="return confirm('¿Restablecer el acceso al sistema para {{ addslashes($emp->nombre_comercial) }}?');">
                                    @csrf
                                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-2.5 py-1.5 rounded-lg transition shadow-sm">
                                        ✓ Habilitar
                                    </button>
                                </form>
                            @endif

                            {{-- Ver Ficha Detallada --}}
                            <a href="{{ route('empresas.show', $emp->id) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 px-2 py-1.5">
                                Ficha &rarr;
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                            <p class="text-base font-semibold text-slate-600">No hay empresas registradas aún en el sistema SaaS.</p>
                            <p class="text-xs mt-1">Utilice el botón superior para dar de alta la primera empresa cliente.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para Suspender Empresa -->
<div id="modalSuspender" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="text-base font-bold text-rose-700 flex items-center space-x-2">
                <span>⛔ Suspender Acceso a Empresa</span>
            </h3>
            <button type="button" onclick="cerrarModalSuspender()" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
        </div>

        <p class="text-xs text-slate-600">
            Al suspender a <strong id="nombreEmpresaSuspender"></strong>, ningún usuario de esta empresa podrá iniciar sesión ni registrar asistencias hasta que su cuenta sea reactivada.
        </p>

        <form id="formSuspender" method="POST" action="" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Motivo de Suspensión *</label>
                <textarea name="motivo_suspension" rows="3" required class="w-full border border-slate-300 rounded-lg p-2.5 text-xs focus:ring-2 focus:ring-rose-500 focus:border-rose-500">Mora o falta de pago del canon mensual del servicio de nómina SaaS.</textarea>
            </div>

            <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="cerrarModalSuspender()" class="px-4 py-2 border border-slate-300 rounded-lg text-xs font-semibold text-slate-700 hover:bg-slate-50">
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
    function abrirModalSuspender(id, nombre) {
        document.getElementById('nombreEmpresaSuspender').innerText = nombre;
        document.getElementById('formSuspender').action = '/empresas/' + id + '/toggle-estado';
        document.getElementById('modalSuspender').classList.remove('hidden');
    }

    function cerrarModalSuspender() {
        document.getElementById('modalSuspender').classList.add('hidden');
    }
</script>
@endsection
