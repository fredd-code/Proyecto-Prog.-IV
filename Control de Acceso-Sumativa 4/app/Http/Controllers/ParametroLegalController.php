<?php

namespace App\Http\Controllers;

use App\Models\ParametroLegal;
use Illuminate\Http\Request;

/**
 * Controlador de Parámetros Legales y Normativas Laborales (Panel Macro)
 * Permite al Operador Macro (SuperAdmin) actualizar valores de leyes laborales
 * como Cestaticket, retenciones IVSS/FAOV/SPF, descansos LOTTT y normativas.
 */
class ParametroLegalController extends Controller
{
    public function index()
    {
        // Solo el SuperAdmin puede acceder al gestor macro de leyes
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Acceso restringido al Operador Macro del Sistema.');
        }

        $parametros = ParametroLegal::orderBy('categoria', 'asc')->get();
        return view('leyes.index', compact('parametros'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Acceso denegado.');
        }

        $validated = $request->validate([
            'codigo'         => 'required|string|max:50|unique:parametros_legales,codigo',
            'nombre'         => 'required|string|max:150',
            'categoria'      => 'required|in:salarial,deducciones_ley,descansos,beneficios,jornada',
            'valor'          => 'required|string|max:255',
            'unidad'         => 'required|string|max:30',
            'gaceta_oficial' => 'nullable|string|max:120',
            'descripcion'    => 'nullable|string',
        ]);

        $validated['activo'] = true;

        ParametroLegal::create($validated);

        return redirect()->route('leyes.index')
            ->with('success', "Parámetro legal '{$validated['nombre']}' incorporado al sistema.");
    }

    public function update(Request $request, ParametroLegal $ley)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Acceso denegado.');
        }

        $validated = $request->validate([
            'nombre'         => 'required|string|max:150',
            'categoria'      => 'required|in:salarial,deducciones_ley,descansos,beneficios,jornada',
            'valor'          => 'required|string|max:255',
            'unidad'         => 'required|string|max:30',
            'gaceta_oficial' => 'nullable|string|max:120',
            'descripcion'    => 'nullable|string',
            'activo'         => 'nullable|boolean',
        ]);

        $validated['activo'] = $request->has('activo');

        $ley->update($validated);

        return redirect()->route('leyes.index')
            ->with('success', "Parámetro legal '{$ley->nombre}' actualizado con éxito.");
    }

    public function destroy(ParametroLegal $ley)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Acceso denegado.');
        }

        $ley->delete();

        return redirect()->route('leyes.index')
            ->with('success', "Parámetro legal eliminado.");
    }
}
