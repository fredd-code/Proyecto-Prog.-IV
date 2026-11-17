@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <div class="flex items-center space-x-2">
                <span class="bg-amber-100 text-amber-900 text-xs font-black px-2.5 py-0.5 rounded-full uppercase">Panel Macro SaaS</span>
                <h1 class="text-2xl font-black text-slate-900">Registrar Nueva Empresa Cliente</h1>
            </div>
            <p class="text-xs text-slate-500 mt-1">Alta de cliente comercial B2B: Cuotas de trabajadores y sedes, geocercas satelitales y generación de credenciales para el cliente.</p>
        </div>
        <a href="{{ route('empresas.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
            &larr; Volver al listado
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

    <form method="POST" action="{{ route('empresas.store') }}" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-6">
        @csrf

        <!-- 1. Datos Fiscales y Cuotas SaaS Macro -->
        <div class="border-b border-slate-100 pb-5">
            <h2 class="text-xs font-bold uppercase tracking-wider text-indigo-900 mb-3">1. Datos Fiscales y Cuotas del Plan (Operador Macro)</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">RIF de la Empresa *</label>
                    <input type="text" name="rif" value="{{ old('rif') }}" placeholder="Ej: J-12345678-0" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nombre Comercial *</label>
                    <input type="text" name="nombre_comercial" id="nombreComercial" value="{{ old('nombre_comercial') }}" placeholder="Ej: Supermercado Central C.A." required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500" oninput="actualizarCamposEmpresa(this.value)">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Razón Social *</label>
                    <input type="text" name="razon_social" value="{{ old('razon_social') }}" placeholder="Ej: Inversiones y Alimentos Central C.A." required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Teléfono Notificaciones (WhatsApp) *</label>
                    <input type="text" name="telefono_notificaciones" value="{{ old('telefono_notificaciones') }}" placeholder="Ej: +584121234567" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-amber-900 uppercase mb-1">Límite de Trabajadores (Cupo SaaS) *</label>
                    <input type="number" name="limite_trabajadores" value="{{ old('limite_trabajadores', 15) }}" min="1" max="5000" required class="w-full border-2 border-amber-400 bg-amber-50 rounded-lg px-3 py-2 text-sm font-bold font-mono text-amber-900 focus:ring-2 focus:ring-amber-500">
                    <span class="text-xxs text-amber-700">Tope máximo de empleados que el cliente podrá registrar.</span>
                </div>
                <div>
                    <label class="block text-xs font-bold text-indigo-900 uppercase mb-1">Límite de Sedes Permitidas *</label>
                    <input type="number" name="limite_sedes" value="{{ old('limite_sedes', 3) }}" min="1" max="50" required class="w-full border-2 border-indigo-400 bg-indigo-50 rounded-lg px-3 py-2 text-sm font-bold font-mono text-indigo-950 focus:ring-2 focus:ring-indigo-500">
                    <span class="text-xxs text-indigo-700">Número de sucursales autorizadas para su geocerca.</span>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Dirección Fiscal</label>
                    <input type="text" name="direccion" value="{{ old('direccion') }}" placeholder="Av. Principal, Edif. Torre Centro, Piso 3" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <!-- 2. Cuenta de Usuario Administrador Principal (Acceso Cliente) -->
        <div class="border-b border-slate-100 pb-5 space-y-3">
            <div class="flex justify-between items-center">
                <h2 class="text-xs font-bold uppercase tracking-wider text-indigo-900">2. Usuario Administrador Principal (Credenciales del Cliente)</h2>
                <span class="text-xxs font-bold bg-emerald-100 text-emerald-800 px-2.5 py-0.5 rounded-full">Acceso Titular con Todos los Permisos</span>
            </div>
            <p class="text-xs text-slate-500">Estas credenciales permitirán al cliente iniciar sesión en su empresa para gestionar trabajadores, nóminas y crear usuarios secundarios con permisos.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-1">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nombre del Administrador *</label>
                    <input type="text" name="admin_name" id="admin_name" value="{{ old('admin_name', 'Administrador Principal') }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Correo Electrónico (Usuario Login) *</label>
                    <input type="email" name="admin_email" id="admin_email" value="{{ old('admin_email') }}" placeholder="admin@empresa.com" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-xs font-bold text-slate-700 uppercase">Contraseña de Acceso *</label>
                        <button type="button" onclick="generarClaveAleatoria()" class="text-xxs font-bold text-indigo-600 hover:underline">🎲 Generar Clave</button>
                    </div>
                    <input type="text" name="admin_password" id="admin_password" value="{{ old('admin_password', 'Admin' . rand(1000, 9999) . '!') }}" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono font-bold text-indigo-950 bg-indigo-50/50 focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <!-- 3. Menú de Sedes y Puntos de Ubicación GPS (Multi-Sede Dinámico) -->
        <div class="border-b border-slate-100 pb-5 space-y-4">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-2">
                <div>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-indigo-900">3. Menú de Ubicaciones y Sedes de la Empresa</h2>
                    <p class="text-xs text-slate-500">Cada sede estará identificada con su código y geocerca para asignar personal.</p>
                </div>
                <button type="button" onclick="agregarMenuSede()" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 text-xs font-bold px-3 py-2 rounded-lg transition flex items-center space-x-1">
                    <span>➕ Añadir Otra Sede</span>
                </button>
            </div>

            <!-- Contenedor dinámico de sedes -->
            <div id="contenedorSedes" class="space-y-4">
                <!-- Sede 1: Principal -->
                <div class="sede-card bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-3" id="sede_0">
                    <div class="flex justify-between items-center border-b border-slate-200 pb-2">
                        <div class="flex items-center space-x-2">
                            <span class="bg-indigo-600 text-white font-mono text-xxs font-bold px-2 py-0.5 rounded">SEDE 1</span>
                            <span class="text-xs font-bold text-indigo-950">Sede Principal</span>
                            <span class="bg-amber-100 text-amber-800 text-xxs font-bold px-2 py-0.5 rounded-full">Base Principal</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="md:col-span-2">
                            <label class="block text-xxs font-bold text-slate-700 uppercase mb-1">Nombre Identificador de la Sede *</label>
                            <input type="text" name="sedes[0][nombre]" id="inputNombreSedePrincipal" value="Sede Principal" required class="w-full border border-slate-300 rounded-lg px-3 py-1.5 text-xs bg-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xxs font-bold text-slate-700 uppercase mb-1">Código de Sede</label>
                            <input type="text" name="sedes[0][codigo]" value="SEDE-01" class="w-full border border-slate-300 rounded-lg px-3 py-1.5 text-xs bg-white font-mono focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xxs font-bold text-slate-700 uppercase mb-1">Dirección de la Sede</label>
                            <input type="text" name="sedes[0][direccion]" placeholder="Ej: Av. Principal, San Antonio de los Altos" class="w-full border border-slate-300 rounded-lg px-3 py-1.5 text-xs bg-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xxs font-bold text-slate-700 uppercase mb-1">Teléfono Sede</label>
                            <input type="text" name="sedes[0][telefono_contacto]" placeholder="Ej: 0212-3214567" class="w-full border border-slate-300 rounded-lg px-3 py-1.5 text-xs bg-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xxs font-bold text-slate-700 uppercase mb-1">Latitud GPS *</label>
                            <input type="number" step="any" name="sedes[0][latitud]" value="10.380000" required class="w-full border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs font-mono bg-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xxs font-bold text-slate-700 uppercase mb-1">Longitud GPS *</label>
                            <input type="number" step="any" name="sedes[0][longitud]" value="-66.960000" required class="w-full border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs font-mono bg-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xxs font-bold text-slate-700 uppercase mb-1">Radio Geocerca (Metros) *</label>
                            <input type="number" name="sedes[0][radio_geocerca_metros]" value="250" min="20" max="5000" required class="w-full border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs font-mono bg-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botón Grande para añadir más sedes -->
            <button type="button" onclick="agregarMenuSede()" class="w-full py-2.5 border-2 border-dashed border-indigo-300 hover:border-indigo-500 text-indigo-700 bg-indigo-50/50 hover:bg-indigo-50 rounded-xl text-xs font-bold transition flex items-center justify-center space-x-2">
                <span>➕ Añadir Sede Adicional (Sucursal / Almacén / Planta)</span>
            </button>
        </div>

        <div class="pt-4 flex justify-end space-x-3">
            <a href="{{ route('empresas.index') }}" class="px-4 py-2 border border-slate-300 text-slate-700 rounded-lg text-xs font-semibold hover:bg-slate-50">Cancelar</a>
            <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-2 rounded-lg text-xs font-bold shadow transition">
                Guardar Empresa, Sedes y Generar Usuario
            </button>
        </div>
    </form>
</div>

<script>
let contadorSedes = 1;

function actualizarCamposEmpresa(nombreEmpresa) {
    const input = document.getElementById('inputNombreSedePrincipal');
    if (input && (input.value === 'Sede Principal' || input.value.startsWith('Sede Principal -'))) {
        input.value = nombreEmpresa ? 'Sede Principal - ' + nombreEmpresa : 'Sede Principal';
    }
}

function generarClaveAleatoria() {
    const chars = "abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#$%";
    let pass = "";
    for (let i = 0; i < 10; i++) {
        pass += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('admin_password').value = pass;
}

function agregarMenuSede() {
    const contenedor = document.getElementById('contenedorSedes');
    const idx = contadorSedes;
    contadorSedes++;

    const div = document.createElement('div');
    div.className = 'sede-card bg-slate-50 p-4 rounded-xl border border-indigo-200 space-y-3 transition animate-fadeIn';
    div.id = 'sede_' + idx;

    div.innerHTML = `
        <div class="flex justify-between items-center border-b border-slate-200 pb-2">
            <div class="flex items-center space-x-2">
                <span class="bg-indigo-900 text-white font-mono text-xxs font-bold px-2 py-0.5 rounded">SEDE ${idx + 1}</span>
                <span class="text-xs font-bold text-slate-800">Nueva Sucursal / Ubicación</span>
                <span class="bg-slate-100 text-slate-600 text-xxs font-bold px-2 py-0.5 rounded-full">Sucursal</span>
            </div>
            <button type="button" onclick="eliminarMenuSede('sede_${idx}')" class="text-rose-600 hover:text-rose-800 text-xs font-bold">
                ✕ Quitar Sede
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="md:col-span-2">
                <label class="block text-xxs font-bold text-slate-700 uppercase mb-1">Nombre Identificador de la Sede *</label>
                <input type="text" name="sedes[${idx}][nombre]" placeholder="Ej: Sucursal Los Teques / Almacén Guatire" required class="w-full border border-slate-300 rounded-lg px-3 py-1.5 text-xs bg-white focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xxs font-bold text-slate-700 uppercase mb-1">Código de Sede</label>
                <input type="text" name="sedes[${idx}][codigo]" value="SEDE-0${idx + 1}" class="w-full border border-slate-300 rounded-lg px-3 py-1.5 text-xs bg-white font-mono focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xxs font-bold text-slate-700 uppercase mb-1">Dirección de la Sede</label>
                <input type="text" name="sedes[${idx}][direccion]" placeholder="Ej: C.C. La Cascada, Nivel 2, Local 45" class="w-full border border-slate-300 rounded-lg px-3 py-1.5 text-xs bg-white focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xxs font-bold text-slate-700 uppercase mb-1">Teléfono Sede</label>
                <input type="text" name="sedes[${idx}][telefono_contacto]" placeholder="Ej: 0212-3214567" class="w-full border border-slate-300 rounded-lg px-3 py-1.5 text-xs bg-white focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xxs font-bold text-slate-700 uppercase mb-1">Latitud GPS *</label>
                <input type="number" step="any" name="sedes[${idx}][latitud]" value="10.246900" required class="w-full border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs font-mono bg-white focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xxs font-bold text-slate-700 uppercase mb-1">Longitud GPS *</label>
                <input type="number" step="any" name="sedes[${idx}][longitud]" value="-67.595800" required class="w-full border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs font-mono bg-white focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xxs font-bold text-slate-700 uppercase mb-1">Radio Geocerca (Metros) *</label>
                <input type="number" name="sedes[${idx}][radio_geocerca_metros]" value="200" min="20" max="5000" required class="w-full border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs font-mono bg-white focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>
    `;

    contenedor.appendChild(div);
}

function eliminarMenuSede(id) {
    const el = document.getElementById(id);
    if (el) {
        el.remove();
    }
}
</script>
@endsection
