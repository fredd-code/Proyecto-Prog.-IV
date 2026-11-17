@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Registrar Nuevo Trabajador</h1>
            <p class="text-sm text-slate-500">Alta de empleado: Asociación a una o múltiples sedes, jornada individual y receso legal (LOTTT).</p>
        </div>
        <a href="{{ route('trabajadores.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
            &larr; Volver
        </a>
    </div>

    @if(session('error'))
    <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-xl text-sm font-medium">
        {{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-xl text-sm">
        <ul class="list-disc pl-5 space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('trabajadores.store') }}" id="formTrabajador" class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 space-y-6">
        @csrf

        <!-- 1. Empresa Contratante -->
        <div class="border-b border-slate-100 pb-5">
            <h2 class="text-sm font-bold uppercase tracking-wider text-indigo-900 mb-3">1. Empresa Empleadora</h2>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Empresa *</label>
                <select name="empresa_id" id="selectEmpresa" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 bg-slate-50 font-medium">
                    @foreach($empresas as $emp)
                    @php
                        $limite = $emp->limite_trabajadores ?? 15;
                        $usados = $emp->trabajadores()->count();
                        $llena = $usados >= $limite;
                    @endphp
                    <option value="{{ $emp->id }}" data-sedes='@json($emp->sedes)' {{ $llena ? 'disabled' : '' }}>
                        {{ $emp->nombre_comercial }} (Cupo: {{ $usados }}/{{ $limite }} {{ $llena ? '- COMPLETO' : '' }})
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- 2. Asignación a UNA o VARIAS Sedes Identificadas -->
        <div class="border-b border-slate-100 pb-5 space-y-3">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-2">
                <div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-indigo-900">2. Sedes Autorizadas para el Trabajador (Una o Varias)</h2>
                    <p class="text-xs text-slate-500">Seleccione todas las sedes donde el empleado prestará servicios. El marcaje GPS validará si se encuentra en cualquiera de las sedes marcadas.</p>
                </div>
                <div class="flex items-center space-x-2 text-xs">
                    <button type="button" onclick="marcarTodasSedes(true)" class="text-indigo-600 font-bold hover:underline">Marcar Todas</button>
                    <span class="text-slate-300">&bull;</span>
                    <button type="button" onclick="marcarTodasSedes(false)" class="text-slate-500 hover:underline">Desmarcar</button>
                </div>
            </div>

            <!-- Contenedor de Sedes Identificadas -->
            <div id="contenedorSedesTrabajador" class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1">
                <!-- Se poblará vía JavaScript según la empresa seleccionada -->
            </div>
            <p id="errorSedes" class="hidden text-xs text-rose-600 font-bold">Debe seleccionar al menos una sede autorizada para el trabajador.</p>
        </div>

        <!-- 3. Datos Personales y Contacto Obligatorio -->
        <div class="border-b border-slate-100 pb-5">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 mb-3">3. Datos Personales y Claves Anti-Fraude</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Cédula de Identidad *</label>
                    <input type="text" name="cedula" value="{{ old('cedula') }}" placeholder="Ej: V-21202137" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nombres *</label>
                    <input type="text" name="nombres" value="{{ old('nombres') }}" placeholder="Nombres del empleado" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Apellidos *</label>
                    <input type="text" name="apellidos" value="{{ old('apellidos') }}" placeholder="Apellidos del empleado" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-emerald-900 uppercase mb-1">Número de Teléfono (Obligatorio) *</label>
                    <input type="text" name="telefono" value="{{ old('telefono') }}" placeholder="Ej: 0412-1234567" required class="w-full border-2 border-emerald-400 bg-emerald-50 rounded-lg px-3 py-2 text-sm font-mono font-bold text-emerald-900 focus:ring-2 focus:ring-emerald-500">
                    <span class="text-xxs text-emerald-700">Requisito obligatorio para alertas y expediente.</span>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Correo Electrónico</label>
                    <input type="email" name="correo" value="{{ old('correo') }}" placeholder="empleado@correo.com" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-indigo-900 uppercase mb-1">PIN Secreto de Marcaje *</label>
                    <input type="text" name="pin_acceso" value="{{ old('pin_acceso', '1234') }}" maxlength="6" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono tracking-widest text-center focus:ring-2 focus:ring-indigo-500 bg-indigo-50 text-indigo-900 font-bold">
                    <span class="text-xxs text-slate-400">PIN para validar asistencia en móvil o Kiosco.</span>
                </div>
            </div>
        </div>

        <!-- 4. Jornada Laboral Individual y Descanso Legal (LOTTT) -->
        <div class="border-b border-slate-100 pb-5">
            <div class="flex justify-between items-center mb-1">
                <h2 class="text-sm font-bold uppercase tracking-wider text-indigo-900">4. Jornada Laboral Individual y Descanso Legal (LOTTT)</h2>
                <span class="text-xxs font-bold bg-amber-100 text-amber-900 px-2.5 py-0.5 rounded">Asignación por Trabajador</span>
            </div>
            <p class="text-xs text-slate-500 mb-4">El horario asignado define cuándo se permite el marcaje y si cuenta con descanso legal de almuerzo.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Hora de Entrada *</label>
                    <input type="time" name="hora_entrada" value="{{ old('hora_entrada', '08:00') }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Hora de Salida *</label>
                    <input type="time" name="hora_salida" value="{{ old('hora_salida', '17:00') }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tolerancia de Retardo (Minutos) *</label>
                    <input type="number" name="tolerancia_minutos" value="{{ old('tolerancia_minutos', 15) }}" min="0" max="60" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-indigo-500">
                    <span class="text-xxs text-slate-400">Margen de gracia antes de emitir notificación de retardo.</span>
                </div>
            </div>

            <!-- Receso Legal de Almuerzo -->
            <div class="mt-4 p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                <div class="flex items-center space-x-2">
                    <input type="checkbox" name="aplica_almuerzo" id="chkAlmuerzo" value="1" {{ old('aplica_almuerzo', true) ? 'checked' : '' }} onchange="toggleAlmuerzo(this.checked)" class="rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                    <label for="chkAlmuerzo" class="text-xs font-bold text-slate-800 cursor-pointer">
                        ¿Aplica período de descanso legal y alimentación? (LOTTT Art. 168)
                    </label>
                </div>
                <div id="seccionAlmuerzo" class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Inicio de Almuerzo</label>
                        <input type="time" name="inicio_almuerzo" id="inicio_almuerzo" value="{{ old('inicio_almuerzo', '12:00') }}" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono bg-white focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Fin de Almuerzo</label>
                        <input type="time" name="fin_almuerzo" id="fin_almuerzo" value="{{ old('fin_almuerzo', '13:00') }}" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono bg-white focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Ficha Laboral y Remuneración -->
        <div class="border-b border-slate-100 pb-5">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 mb-3">5. Ficha Laboral y Remuneración</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Cargo *</label>
                    <input type="text" name="cargo" value="{{ old('cargo') }}" placeholder="Ej: Desarrollador Backend" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Departamento *</label>
                    <input type="text" name="departamento" value="{{ old('departamento') }}" placeholder="Ej: Tecnología" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Fecha de Ingreso *</label>
                    <input type="date" name="fecha_ingreso" value="{{ old('fecha_ingreso', date('Y-m-d')) }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Frecuencia de Pago *</label>
                    <select name="frecuencia_pago" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="quincenal" {{ old('frecuencia_pago') === 'quincenal' ? 'selected' : '' }}>Quincenal (15 días base)</option>
                        <option value="semanal" {{ old('frecuencia_pago') === 'semanal' ? 'selected' : '' }}>Semanal (7 días base)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Salario Base Mensual (Bs.) *</label>
                    <input type="number" step="0.01" name="salario_base" value="{{ old('salario_base') }}" placeholder="15000.00" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Cestaticket Mensual (Bs.) *</label>
                    <input type="number" step="0.01" name="bono_alimentacion" value="{{ old('bono_alimentacion', '1800.00') }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <div class="pt-4 flex justify-end space-x-3">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow transition">
                Registrar Ficha del Trabajador
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

function actualizarSedes() {
    const selectEmpresa = document.getElementById('selectEmpresa');
    const contenedor = document.getElementById('contenedorSedesTrabajador');
    const selectedOption = selectEmpresa.options[selectEmpresa.selectedIndex];
    
    contenedor.innerHTML = '';
    
    if (selectedOption && selectedOption.dataset.sedes) {
        const sedes = JSON.parse(selectedOption.dataset.sedes);
        if (sedes.length === 0) {
            contenedor.innerHTML = '<div class="col-span-2 p-4 text-center text-xs text-slate-400 bg-slate-50 rounded-xl border border-dashed border-slate-300">No hay sedes registradas para esta empresa. Por favor agregue al menos una sede en la ficha de la empresa.</div>';
            return;
        }

        sedes.forEach((s, idx) => {
            const card = document.createElement('label');
            card.className = 'relative flex items-start p-3.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-indigo-50/50 hover:border-indigo-300 cursor-pointer transition select-none';
            card.innerHTML = `
                <div class="flex items-center h-5 mr-3">
                    <input type="checkbox" name="sedes[]" value="${s.id}" class="check-sede rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4" ${idx === 0 ? 'checked' : ''}>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center space-x-2">
                        <span class="bg-indigo-100 text-indigo-800 font-mono text-xxs font-bold px-1.5 py-0.5 rounded">${s.codigo || 'SEDE'}</span>
                        <span class="text-xs font-bold text-slate-900 truncate">${s.nombre}</span>
                        ${s.es_principal ? '<span class="bg-amber-100 text-amber-800 text-xxs font-bold px-1.5 py-0.2 rounded-full">Principal</span>' : ''}
                    </div>
                    <p class="text-xxs text-slate-500 mt-1 truncate">${s.direccion || 'Sin dirección específica'}</p>
                    <div class="mt-1 text-xxs font-mono text-slate-400 flex items-center space-x-2">
                        <span>📍 GPS: ${parseFloat(s.latitud).toFixed(4)}, ${parseFloat(s.longitud).toFixed(4)}</span>
                        <span>&bull;</span>
                        <span class="text-indigo-600 font-bold">Radio: ${s.radio_geocerca_metros}m</span>
                    </div>
                </div>
            `;
            contenedor.appendChild(card);
        });
    }
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

document.getElementById('selectEmpresa').addEventListener('change', actualizarSedes);
document.addEventListener('DOMContentLoaded', actualizarSedes);
</script>
@endsection
