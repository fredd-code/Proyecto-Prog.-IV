@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Control de Asistencia Anti-Fraude (Multi-Sede GPS)</h1>
            <p class="text-sm text-slate-500">Marcaje inteligente automático por Cédula y PIN: Entrada, Salida Almuerzo, Regreso Almuerzo y Salida Final.</p>
        </div>
        <a href="{{ route('asistencias.kiosco') }}" target="_blank" class="bg-indigo-900 hover:bg-indigo-800 text-white text-xs font-bold px-3 py-2 rounded-lg shadow transition flex items-center space-x-1">
            <span>📱 Abrir Kiosco Sede</span>
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

    <!-- Módulo de Marcaje Inteligente Anti-Fraude -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Formulario de Marcaje Inteligente -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 h-fit lg:col-span-1 space-y-4">
            <div class="flex justify-between items-center">
                <h2 class="text-base font-bold text-slate-900">Marcaje Móvil / Web</h2>
                <span class="text-xs font-bold bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full">GPS Anti-Fraude</span>
            </div>
            
            <div class="bg-indigo-50 p-3 rounded-lg text-xxs text-indigo-900 space-y-1">
                <p class="font-bold">Secuencia Automática de la Jornada:</p>
                <p>1º Entrada &rarr; 2º Salida Almuerzo &rarr; 3º Regreso Almuerzo &rarr; 4º Salida Final.</p>
                <p class="text-slate-500">Detecta automáticamente el paso correspondiente según su horario asignado.</p>
            </div>

            <form method="POST" action="{{ route('asistencias.registrar') }}" id="formMarcaje" class="space-y-4">
                @csrf
                <!-- Coordenadas GPS Ocultas -->
                <input type="hidden" name="latitud" id="latitud">
                <input type="hidden" name="longitud" id="longitud">

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Cédula del Trabajador *</label>
                    <input type="text" name="cedula" id="cedula" placeholder="Ej: V-21202137" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">PIN Secreto de Acceso *</label>
                    <input type="password" name="pin_acceso" id="pin_acceso" maxlength="6" placeholder="****" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono text-center tracking-widest focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Estado del Sensor GPS -->
                <div id="gpsStatus" class="p-2.5 rounded-lg bg-slate-50 border border-slate-200 text-xxs text-slate-500 flex items-center space-x-2">
                    <span id="gpsDot" class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                    <span id="gpsText">Obteniendo coordenadas satelitales GPS...</span>
                </div>

                <!-- Botón Inteligente de Marcaje -->
                <button type="submit" id="btnMarcar" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-lg text-sm shadow transition flex justify-center items-center space-x-2">
                    <span>⚡ REGISTRAR ASISTENCIA (AUTOMÁTICO)</span>
                </button>
            </form>

            <!-- Botón Especial de Retiro por Imprevisto -->
            <div class="pt-4 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalRetiro').classList.remove('hidden')" class="w-full bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 font-bold py-2 rounded-lg text-xs transition flex justify-center items-center space-x-1">
                    <span>⚠️ Retiro por Imprevisto / Permiso Justificado</span>
                </button>
                <p class="text-xxs text-slate-400 text-center mt-1">Notifica de inmediato al teléfono de la empresa.</p>
            </div>
        </div>

        <!-- Tabla de Asistencias y Notificaciones -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Bitácora de Asistencias Diarias -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h2 class="text-base font-bold text-slate-900 mb-4">Registro Diario de Marcaciones</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-xs">
                        <thead class="bg-slate-50 text-slate-600 font-semibold uppercase">
                            <tr>
                                <th class="px-3 py-2.5 text-left">Trabajador & Sede</th>
                                <th class="px-2 py-2.5 text-center">Entrada</th>
                                <th class="px-2 py-2.5 text-center">Salida Almuerzo</th>
                                <th class="px-2 py-2.5 text-center">Regreso Almuerzo</th>
                                <th class="px-2 py-2.5 text-center">Salida Final</th>
                                <th class="px-3 py-2.5 text-center">Geocerca</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($asistencias as $asist)
                            <tr class="hover:bg-slate-50">
                                <td class="px-3 py-3">
                                    <p class="font-bold text-slate-800">{{ $asist->trabajador->nombre_completo ?? 'N/A' }}</p>
                                    <p class="text-xxs text-slate-400 font-mono">
                                        {{ $asist->trabajador->cedula ?? '' }} &bull; {{ $asist->fecha->format('d/m/Y') }}
                                    </p>
                                    <span class="inline-block text-xxs font-semibold text-indigo-700 bg-indigo-50 px-1.5 py-0.5 rounded mt-0.5">
                                        🏢 {{ $asist->sede->nombre ?? ($asist->trabajador->sede->nombre ?? 'Sede Principal') }}
                                    </span>
                                </td>
                                <!-- 1. Entrada -->
                                <td class="px-2 py-3 text-center font-mono font-bold text-slate-800">
                                    {{ $asist->hora_entrada ? substr($asist->hora_entrada, 0, 5) : '--' }}
                                    @if($asist->es_retardo)
                                        <span class="block text-xxs font-bold text-rose-600">RETARDO</span>
                                    @endif
                                </td>
                                <!-- 2. Salida Almuerzo -->
                                <td class="px-2 py-3 text-center font-mono text-slate-700">
                                    {{ $asist->hora_salida_almuerzo ? substr($asist->hora_salida_almuerzo, 0, 5) : '--' }}
                                </td>
                                <!-- 3. Regreso Almuerzo -->
                                <td class="px-2 py-3 text-center font-mono text-slate-700">
                                    {{ $asist->hora_entrada_almuerzo ? substr($asist->hora_entrada_almuerzo, 0, 5) : '--' }}
                                </td>
                                <!-- 4. Salida Final -->
                                <td class="px-2 py-3 text-center font-mono font-bold text-slate-800">
                                    {{ $asist->hora_salida ? substr($asist->hora_salida, 0, 5) : '--' }}
                                    @if($asist->tipo_marcaje === 'retiro_imprevisto')
                                        <span class="block text-xxs font-bold text-amber-600">RETIRO</span>
                                    @endif
                                </td>
                                <!-- Geocerca -->
                                <td class="px-3 py-3 text-center">
                                    @if($asist->metodo_marcaje === 'kiosco_sede')
                                        <span class="px-2 py-0.5 rounded text-xxs font-bold bg-indigo-100 text-indigo-800">Kiosco Sede</span>
                                    @elseif($asist->dentro_de_geocerca)
                                        <span class="px-2 py-0.5 rounded text-xxs font-bold bg-emerald-100 text-emerald-800">En Rango ({{ $asist->distancia_sede_metros ?? 0 }}m)</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-xxs font-bold bg-rose-100 text-rose-800">Fuera ({{ $asist->distancia_sede_metros }}m)</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-slate-400 text-xs">No hay asistencias registradas hoy.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Registro de Notificaciones Enviadas al Patrono -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center space-x-2">
                    <span>📲 Alertas Despachadas al Teléfono de la Empresa</span>
                </h3>
                <div class="divide-y divide-slate-100">
                    @forelse($notificaciones as $n)
                    <div class="py-2.5 flex justify-between items-center text-xs">
                        <div>
                            <span class="font-bold text-slate-800">Destino: {{ $n->telefono_destino }}</span> &bull;
                            <span class="text-slate-600">{{ $n->mensaje }}</span>
                        </div>
                        <span class="text-xxs text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded">{{ $n->created_at->format('h:i A') }}</span>
                    </div>
                    @empty
                    <p class="text-xxs text-slate-400 py-3 text-center">No hay alertas recientes.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
</div>

<!-- Modal de Retiro por Imprevisto -->
<div id="modalRetiro" class="hidden fixed inset-0 bg-slate-900 bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 space-y-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-2">
            <h3 class="text-base font-bold text-slate-900">Reportar Retiro por Imprevisto</h3>
            <button onclick="document.getElementById('modalRetiro').classList.add('hidden')" class="text-slate-400 hover:text-slate-700">&times;</button>
        </div>
        <form method="POST" action="{{ route('asistencias.retiro') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Cédula del Trabajador *</label>
                <input type="text" name="cedula" placeholder="Ej: V-21202137" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">PIN Secreto *</label>
                <input type="password" name="pin_acceso" maxlength="6" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono text-center">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Motivo del Retiro / Ausencia *</label>
                <select name="motivo_retiro" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
                    <option value="Emergencia médica / Salud">Emergencia médica / Salud</option>
                    <option value="Diligencia legal / Trámite urgente">Diligencia legal / Trámite urgente</option>
                    <option value="Causa de fuerza mayor / Familiar">Causa de fuerza mayor / Familiar</option>
                    <option value="Permiso previamente acordado">Permiso previamente acordado</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Observaciones Detalladas</label>
                <textarea name="observacion" rows="2" placeholder="Explique brevemente el motivo..." class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm"></textarea>
            </div>
            <div class="pt-2 flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('modalRetiro').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-lg">Cancelar</button>
                <button type="submit" class="px-4 py-2 text-xs font-bold bg-amber-600 hover:bg-amber-700 text-white rounded-lg shadow">Registrar Retiro y Alertar</button>
            </div>
        </form>
    </div>
</div>

<script>
// Captura de Geoubicación GPS para Marcaje Anti-Fraude
document.addEventListener('DOMContentLoaded', function () {
    const latInput = document.getElementById('latitud');
    const lonInput = document.getElementById('longitud');
    const gpsDot = document.getElementById('gpsDot');
    const gpsText = document.getElementById('gpsText');

    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(
            function (position) {
                latInput.value = position.coords.latitude;
                lonInput.value = position.coords.longitude;
                gpsDot.className = "w-2.5 h-2.5 rounded-full bg-emerald-500";
                gpsText.innerText = "GPS Satelital Verificado (" + position.coords.latitude.toFixed(4) + ", " + position.coords.longitude.toFixed(4) + ")";
            },
            function (error) {
                gpsDot.className = "w-2.5 h-2.5 rounded-full bg-rose-500";
                gpsText.innerText = "Acceso GPS no autorizado o indisponible. Se alertará como posible marcaje fuera de sede.";
            },
            { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
        );
    } else {
        gpsDot.className = "w-2.5 h-2.5 rounded-full bg-rose-500";
        gpsText.innerText = "Dispositivo sin soporte de geolocalización GPS.";
    }
});
</script>
@endsection
