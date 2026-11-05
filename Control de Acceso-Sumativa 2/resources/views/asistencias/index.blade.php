@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-xl border border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Control de Asistencia y Marcaje Remoto</h1>
            <p class="text-sm text-slate-500">Alimentación automática de días trabajados con validación geográfica.</p>
        </div>

        <!-- Formulario Rápido de Marcaje Remoto con Geolocalización -->
        <form method="POST" action="{{ route('asistencias.registrar') }}" id="formMarcaje" class="flex flex-wrap items-center gap-3">
            @csrf
            <input type="hidden" name="latitud" id="latitud">
            <input type="hidden" name="longitud" id="longitud">

            <select name="trabajador_id" required class="border border-slate-300 rounded-lg px-3 py-2 text-sm font-medium">
                <option value="">Seleccione Empleado...</option>
                @foreach($trabajadores as $tr)
                    <option value="{{ $tr->id }}">{{ $tr->cedula }} - {{ $tr->nombre_completo }}</option>
                @endforeach
            </select>

            <select name="estado" class="border border-slate-300 rounded-lg px-3 py-2 text-sm font-medium">
                <option value="presente">Presente (Asistió)</option>
                <option value="falta_injustificada">Falta Injustificada</option>
                <option value="falta_justificada">Falta Justificada</option>
                <option value="reposo">Reposo Médico</option>
            </select>

            <button type="button" onclick="marcarConGPS()" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm px-4 py-2 rounded-lg shadow transition">
                📍 Marcar con GPS
            </button>
        </form>
    </div>

    <!-- Tabla del Día -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
            <h2 class="font-bold text-slate-800 text-sm uppercase">Asistencias del Día: {{ $fecha }}</h2>
        </div>
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-slate-600 uppercase text-xs font-semibold">
                <tr>
                    <th class="px-6 py-3 text-left">Cédula</th>
                    <th class="px-6 py-3 text-left">Trabajador</th>
                    <th class="px-6 py-3 text-center">Hora Entrada</th>
                    <th class="px-6 py-3 text-center">Estado</th>
                    <th class="px-6 py-3 text-center">Ubicación GPS</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($trabajadores as $emp)
                @php $asist = $asistencias[$emp->id] ?? null; @endphp
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-4 font-mono font-medium">{{ $emp->cedula }}</td>
                    <td class="px-6 py-4 font-semibold text-slate-800">{{ $emp->nombre_completo }}</td>
                    <td class="px-6 py-4 text-center font-mono text-slate-600">{{ $asist ? $asist->hora_entrada : '--:--:--' }}</td>
                    <td class="px-6 py-4 text-center">
                        @if($asist)
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $asist->estado === 'presente' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ ucfirst(str_replace('_', ' ', $asist->estado)) }}
                            </span>
                        @else
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-slate-100 text-slate-500">Sin Marcaje</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center text-xs font-mono text-slate-400">
                        @if($asist && $asist->latitud)
                            <a href="https://maps.google.com/?q={{ $asist->latitud }},{{ $asist->longitud }}" target="_blank" class="text-indigo-600 underline">
                                {{ round($asist->latitud, 4) }}, {{ round($asist->longitud, 4) }}
                            </a>
                        @else
                            No registrada
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
function marcarConGPS() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('latitud').value = position.coords.latitude;
                document.getElementById('longitud').value = position.coords.longitude;
                document.getElementById('formMarcaje').submit();
            },
            function(error) {
                alert('No se pudo obtener GPS, marcando sin coordenadas.');
                document.getElementById('formMarcaje').submit();
            }
        );
    } else {
        document.getElementById('formMarcaje').submit();
    }
}
</script>
@endsection
