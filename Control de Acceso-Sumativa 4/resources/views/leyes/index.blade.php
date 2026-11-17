@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="bg-amber-100 text-amber-900 text-xs font-black px-2.5 py-0.5 rounded-full uppercase">Panel Macro SaaS</span>
                <h1 class="text-2xl font-black text-slate-900">Parámetros Legales y Normativas Laborales (LOTTT)</h1>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Actualice las leyes, decretos de cestaticket, retenciones obligatorias y descansos legales que rigen el cálculo de nómina de todas las empresas clientes.
            </p>
        </div>
        <div class="flex space-x-2">
            <button type="button" onclick="document.getElementById('modalNuevaLey').classList.remove('hidden')" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow transition">
                + Nueva Ley / Parámetro
            </button>
            <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 self-center">
                &larr; Volver
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-xl text-xs font-bold">
        ✓ {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Normativas Vigentes Configuradas</h2>
            <p class="text-xs text-slate-500 mt-0.5">Al modificar un valor aquí (ej: nuevo Cestaticket por Gaceta Oficial), el sistema ajustará automáticamente los cálculos de ley en las nóminas.</p>
        </div>

        <div class="divide-y divide-slate-100 text-xs">
            @foreach($parametros as $p)
            <div class="p-5 hover:bg-slate-50 transition flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="space-y-1 max-w-2xl">
                    <div class="flex items-center space-x-2">
                        <span class="font-mono text-xxs font-bold px-2 py-0.5 rounded uppercase {{ $p->categoria === 'salarial' ? 'bg-emerald-100 text-emerald-800' : ($p->categoria === 'deducciones_ley' ? 'bg-rose-100 text-rose-800' : 'bg-indigo-100 text-indigo-800') }}">
                            {{ str_replace('_', ' ', $p->categoria) }}
                        </span>
                        <h3 class="font-bold text-slate-900 text-sm">{{ $p->nombre }}</h3>
                        <span class="text-xxs font-mono text-slate-400">({{ $p->codigo }})</span>
                    </div>
                    <p class="text-slate-600 text-xs">{{ $p->descripcion }}</p>
                    @if($p->gaceta_oficial)
                    <p class="text-xxs text-slate-400 font-mono">📜 Base Legal: <strong>{{ $p->gaceta_oficial }}</strong></p>
                    @endif
                </div>

                <div class="flex items-center space-x-4 self-end md:self-center">
                    <div class="text-right">
                        <span class="text-xxs text-slate-400 uppercase font-bold block">Valor Actual</span>
                        <span class="text-lg font-black font-mono text-indigo-950">{{ $p->valor }} <span class="text-xs font-normal text-slate-500">{{ $p->unidad }}</span></span>
                    </div>

                    <button type="button" onclick="editarLey({{ $p->id }}, '{{ addslashes($p->nombre) }}', '{{ $p->categoria }}', '{{ $p->valor }}', '{{ $p->unidad }}', '{{ addslashes($p->gaceta_oficial ?? '') }}', '{{ addslashes($p->descripcion ?? '') }}')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-3 py-1.5 rounded-lg transition">
                        Editar
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Modal para Registrar Nueva Ley -->
<div id="modalNuevaLey" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="text-sm font-bold text-slate-900">Incorporar Nueva Ley o Parámetro Laboral</h3>
            <button type="button" onclick="document.getElementById('modalNuevaLey').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
        </div>

        <form method="POST" action="{{ route('leyes.store') }}" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Nombre de la Ley / Parámetro *</label>
                <input type="text" name="nombre" placeholder="Ej: Nuevo Salario Mínimo Nacional" required class="w-full border border-slate-300 rounded-lg px-3 py-2">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Código Identificador *</label>
                    <input type="text" name="codigo" placeholder="Ej: SALARIO_MIN_2026" required class="w-full border border-slate-300 rounded-lg px-3 py-2 font-mono">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Categoría *</label>
                    <select name="categoria" class="w-full border border-slate-300 rounded-lg px-3 py-2 font-medium">
                        <option value="salarial">Salarial y Bonos</option>
                        <option value="deducciones_ley">Deducciones de Ley</option>
                        <option value="descansos">Descansos y Horarios</option>
                        <option value="beneficios">Beneficios Sociales</option>
                        <option value="jornada">Jornada Laboral</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Valor Numérico o Monto *</label>
                    <input type="text" name="valor" placeholder="Ej: 4.00 o 1800.00" required class="w-full border border-slate-300 rounded-lg px-3 py-2 font-mono">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Unidad de Medida *</label>
                    <input type="text" name="unidad" placeholder="Ej: %, Bs., Horas, Días" required class="w-full border border-slate-300 rounded-lg px-3 py-2">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Gaceta Oficial / Decreto</label>
                <input type="text" name="gaceta_oficial" placeholder="Ej: Gaceta Oficial N° 6.800" class="w-full border border-slate-300 rounded-lg px-3 py-2">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Descripción / Alcance</label>
                <textarea name="descripcion" rows="2" placeholder="Resumen del decreto y su aplicación..." class="w-full border border-slate-300 rounded-lg px-3 py-2"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('modalNuevaLey').classList.add('hidden')" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg font-bold">Cancelar</button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-bold shadow">Guardar Normativa</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para Editar Ley Existente -->
<div id="modalEditarLey" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="text-sm font-bold text-slate-900">Actualizar Valor de Parámetro Legal</h3>
            <button type="button" onclick="document.getElementById('modalEditarLey').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
        </div>

        <form method="POST" id="formEditarLey" action="" class="space-y-3 text-xs">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Nombre *</label>
                <input type="text" name="nombre" id="editNombre" required class="w-full border border-slate-300 rounded-lg px-3 py-2">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Categoría *</label>
                    <select name="categoria" id="editCategoria" class="w-full border border-slate-300 rounded-lg px-3 py-2">
                        <option value="salarial">Salarial y Bonos</option>
                        <option value="deducciones_ley">Deducciones de Ley</option>
                        <option value="descansos">Descansos y Horarios</option>
                        <option value="beneficios">Beneficios Sociales</option>
                        <option value="jornada">Jornada Laboral</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Valor *</label>
                    <input type="text" name="valor" id="editValor" required class="w-full border border-slate-300 rounded-lg px-3 py-2 font-mono">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Unidad *</label>
                    <input type="text" name="unidad" id="editUnidad" required class="w-full border border-slate-300 rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Gaceta Oficial</label>
                    <input type="text" name="gaceta_oficial" id="editGaceta" class="w-full border border-slate-300 rounded-lg px-3 py-2">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Descripción</label>
                <textarea name="descripcion" id="editDescripcion" rows="2" class="w-full border border-slate-300 rounded-lg px-3 py-2"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('modalEditarLey').classList.add('hidden')" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg font-bold">Cancelar</button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-bold shadow">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
function editarLey(id, nombre, categoria, valor, unidad, gaceta, descripcion) {
    document.getElementById('editNombre').value = nombre;
    document.getElementById('editCategoria').value = categoria;
    document.getElementById('editValor').value = valor;
    document.getElementById('editUnidad').value = unidad;
    document.getElementById('editGaceta').value = gaceta;
    document.getElementById('editDescripcion').value = descripcion;
    document.getElementById('formEditarLey').action = '/leyes/' + id;
    document.getElementById('modalEditarLey').classList.remove('hidden');
}
</script>
@endsection
