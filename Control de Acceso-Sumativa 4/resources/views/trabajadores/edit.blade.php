@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Editar Ficha: {{ $trabajador->nombre_completo }}</h1>
            <p class="text-sm text-slate-500">Actualización de sedes autorizadas (una o varias), jornada individual y parámetros laborales.</p>
        </div>
        <a href="{{ route('trabajadores.show', $trabajador->id) }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
            &larr; Volver al expediente
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

    <form method="POST" action="{{ route('trabajadores.update', $trabajador->id) }}" id="formTrabajador" class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 space-y-6">
        @csrf
        @method('PUT')

        <!-- 1. Empresa -->
        <div class="border-b border-slate-100 pb-5">
            <h2 class="text-sm font-bold uppercase tracking-wider text-indigo-900 mb-3">1. Empresa Empleadora</h2>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Empresa *</label>
                <select name="empresa_id" id="selectEmpresa" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 bg-slate-50">
                    @foreach($empresas as $emp)
                    <option value="{{ $emp->id }}" data-sedes='@json($emp->sedes)' {{ $trabajador->empresa_id == $emp->id ? 'selected' : '' }}>
                        {{ $emp->nombre_comercial }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- 2. Asignación a UNA o VARIAS Sedes Identificadas -->
        @php
            $sedesAsignadasIds = $trabajador->sedes->pluck('id')->toArray();
            if (empty($sedesAsignadasIds) && $trabajador->sede_id) {
                $sedesAsignadasIds = [$trabajador->sede_id];
            }
        @endphp
        <div class="border-b border-slate-100 pb-5 space-y-3">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-2">
                <div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-indigo-900">2. Sedes Autorizadas para el Trabajador (Una o Varias)</h2>
                    <p class="text-xs text-slate-500">Marque las sedes donde el empleado tiene permitido laborar y registrar su asistencia GPS.</p>
                </div>
                <div class="flex items-center space-x-2 text-xs">
                    <button type="button" onclick="marcarTodasSedes(true)" class="text-indigo-600 font-bold hover:underline">Marcar Todas</button>
                    <span class="text-slate-300">&bull;</span>
                    <button type="button" onclick="marcarTodasSedes(false)" class="text-slate-500 hover:underline">Desmarcar</button>
                </div>
            </div>

            <!-- Contenedor de Sedes -->
            <div id="contenedorSedesTrabajador" class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1">
                @if($trabajador->empresa && $trabajador->empresa->sedes)
                    @foreach($trabajador->empresa->sedes as $s)
                    <label class="relative flex items-start p-3.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-indigo-50/50 hover:border-indigo-300 cursor-pointer transition select-none">
                        <div class="flex items-center h-5 mr-3">
                            <input type="checkbox" name="sedes[]" value="{{ $s->id }}" class="check-sede rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4" {{ in_array($s->id, $sedesAsignadasIds) ? 'checked' : '' }}>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2">
                                <span class="bg-indigo-100 text-indigo-800 font-mono text-xxs font-bold px-1.5 py-0.5 rounded">{{ $s->codigo ?? 'SEDE' }}</span>
                                <span class="text-xs font-bold text-slate-900 truncate">{{ $s->nombre }}</span>
                                @if($s->es_principal)
                                <span class="bg-amber-100 text-amber-800 text-xxs font-bold px-1.5 py-0.2 rounded-full">Principal</span>
                                @endif
                            </div>
                            <p class="text-xxs text-slate-500 mt-1 truncate">{{ $s->direccion ?? 'Sin dirección' }}</p>
                            <div class="mt-1 text-xxs font-mono text-slate-400 flex items-center space-x-2">
                                <span>📍 GPS: {{ round($s->latitud, 4) }}, {{ round($s->longitud, 4) }}</span>
                                <span>&bull;</span>
                                <span class="text-indigo-600 font-bold">Radio: {{ $s->radio_geocerca_metros }}m</span>
                            </div>
                        </div>
                    </label>
                    @endforeach
                @endif
            </div>
            <p id="errorSedes" class="hidden text-xs text-rose-600 font-bold">Debe seleccionar al menos una sede autorizada para el trabajador.</p>
        </div>

        <!-- 3. Datos Personales y Contacto -->
        <div class="border-b border-slate-100 pb-5">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 mb-3">3. Datos Personales y Seguridad</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Cédula *</label>
                    <input type="text" name="cedula" value="{{ old('cedula', $trabajador->cedula) }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nombres *</label>
                    <input type="text" name="nombres" value="{{ old('nombres', $trabajador->nombres) }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Apellidos *</label>
                    <input type="text" name="apellidos" value="{{ old('apellidos', $trabajador->apellidos) }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-emerald-900 uppercase mb-1">Teléfono (Obligatorio) *</label>
                    <input type="text" name="telefono" value="{{ old('telefono', $trabajador->telefono) }}" required class="w-full border-2 border-emerald-400 bg-emerald-50 rounded-lg px-3 py-2 text-sm font-mono font-bold text-emerald-900 focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Correo Electrónico</label>
                    <input type="email" name="correo" value="{{ old('correo', $trabajador->correo) }}" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-indigo-900 uppercase mb-1">PIN Secreto *</label>
                    <input type="text" name="pin_acceso" value="{{ old('pin_acceso', $trabajador->pin_acceso) }}" maxlength="6" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono text-center tracking-widest bg-indigo-50 text-indigo-900 font-bold focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <!-- 4. Jornada Individual y Descanso Legal (LOTTT) -->
        <div class="border-b border-slate-100 pb-5">
            <h2 class="text-sm font-bold uppercase tracking-wider text-indigo-900 mb-3">4. Horario Laboral y Descanso Legal (LOTTT)</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Hora de Entrada *</label>
                    <input type="time" name="hora_entrada" value="{{ old('hora_entrada', substr($trabajador->hora_entrada, 0, 5)) }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Hora de Salida *</label>
                    <input type="time" name="hora_salida" value="{{ old('hora_salida', substr($trabajador->hora_salida, 0, 5)) }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tolerancia de Retardo (Min) *</label>
                    <input type="number" name="tolerancia_minutos" value="{{ old('tolerancia_minutos', $trabajador->tolerancia_minutos ?? 15) }}" min="0" max="60" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Receso Almuerzo -->
            <div class="mt-4 p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                <div class="flex items-center space-x-2">
                    <input type="checkbox" name="aplica_almuerzo" id="chkAlmuerzo" value="1" {{ old('aplica_almuerzo', $trabajador->aplica_almuerzo) ? 'checked' : '' }} onchange="toggleAlmuerzo(this.checked)" class="rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                    <label for="chkAlmuerzo" class="text-xs font-bold text-slate-800 cursor-pointer">
                        ¿Aplica período de descanso legal y alimentación? (LOTTT Art. 168)
                    </label>
                </div>
                <div id="seccionAlmuerzo" class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 {{ $trabajador->aplica_almuerzo ? '' : 'hidden' }}">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Inicio de Almuerzo</label>
                        <input type="time" name="inicio_almuerzo" id="inicio_almuerzo" value="{{ old('inicio_almuerzo', $trabajador->inicio_almuerzo ? substr($trabajador->inicio_almuerzo, 0, 5) : '12:00') }}" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Fin de Almuerzo</label>
                        <input type="time" name="fin_almuerzo" id="fin_almuerzo" value="{{ old('fin_almuerzo', $trabajador->fin_almuerzo ? substr($trabajador->fin_almuerzo, 0, 5) : '13:00') }}" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono bg-white">
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Parámetros Laborales y Estado -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Cargo *</label>
                <input type="text" name="cargo" value="{{ old('cargo', $trabajador->cargo) }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Departamento *</label>
                <input type="text" name="departamento" value="{{ old('departamento', $trabajador->departamento) }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Fecha Ingreso *</label>
                <input type="date" name="fecha_ingreso" value="{{ old('fecha_ingreso', $trabajador->fecha_ingreso->format('Y-m-d')) }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Frecuencia Pago *</label>
                <select name="frecuencia_pago" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
                    <option value="quincenal" {{ $trabajador->frecuencia_pago === 'quincenal' ? 'selected' : '' }}>Quincenal</option>
                    <option value="semanal" {{ $trabajador->frecuencia_pago === 'semanal' ? 'selected' : '' }}>Semanal</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Salario Base (Bs.) *</label>
                <input type="number" step="0.01" name="salario_base" value="{{ old('salario_base', $trabajador->salario_base) }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Cestaticket (Bs.) *</label>
                <input type="number" step="0.01" name="bono_alimentacion" value="{{ old('bono_alimentacion', $trabajador->bono_alimentacion) }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Bono Asistencia (Bs.)</label>
                <input type="number" step="0.01" name="bono_asistencia" value="{{ old('bono_asistencia', $trabajador->bono_asistencia) }}" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Estado *</label>
                <select name="estado" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-bold">
                    <option value="activo" {{ $trabajador->estado === 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="inactivo" {{ $trabajador->estado === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    <option value="vacaciones" {{ $trabajador->estado === 'vacaciones' ? 'selected' : '' }}>Vacaciones</option>
                </select>
            </div>
        </div>

        <div class="pt-4 flex justify-end space-x-3">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow transition">
                Actualizar Ficha y Sedes
            </button>
        </div>
    </form>
</div>

<script>
function toggleAlmuerzo(activo) {
    const sec = document.getElementById('seccionAlmuerzo');
    if (activo) {
        sec.classList.remove('hidden');
    } else {
        sec.classList.add('hidden');
    }
}

function marcarTodasSedes(marcar) {
    const checkboxes = document.querySelectorAll('.check-sede');
    checkboxes.forEach(cb => cb.checked = marcar);
}

document.getElementById('formTrabajador').addEventListener('submit', function (e) {
    const checks = document.querySelectorAll('.check-sede:checked');
    const err = document.getElementById('errorSedes');
    if (checks.length === 0) {
        e.preventDefault();
        err.classList.remove('hidden');
        err.scrollIntoView({ behavior: 'smooth', block: 'center' });
    } else {
        err.classList.add('hidden');
    }
});
</script>
@endsection
