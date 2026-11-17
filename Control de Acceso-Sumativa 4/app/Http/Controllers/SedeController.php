<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Sede;
use Illuminate\Http\Request;

class SedeController extends Controller
{
    /**
     * Registrar una nueva sede o sucursal para una empresa.
     */
    public function store(Request $request, Empresa $empresa)
    {
        $validated = $request->validate([
            'nombre'                => 'required|string|max:150',
            'codigo'                => 'nullable|string|max:30',
            'telefono_contacto'     => 'nullable|string|max:30',
            'direccion'             => 'nullable|string',
            'latitud'               => 'required|numeric',
            'longitud'              => 'required|numeric',
            'radio_geocerca_metros' => 'required|integer|min:20|max:5000',
            'es_principal'          => 'nullable|boolean',
        ]);

        $validated['empresa_id'] = $empresa->id;
        $validated['es_principal'] = $request->has('es_principal');

        // Si se marca como principal, desmarcar las demás
        if ($validated['es_principal']) {
            $empresa->sedes()->update(['es_principal' => false]);
        }

        Sede::create($validated);

        return redirect()->route('empresas.show', $empresa->id)
            ->with('success', "Nueva sede '{$validated['nombre']}' agregada exitosamente a la empresa.");
    }

    /**
     * Actualizar los datos o geocerca de una sede existente.
     */
    public function update(Request $request, Sede $sede)
    {
        $validated = $request->validate([
            'nombre'                => 'required|string|max:150',
            'codigo'                => 'nullable|string|max:30',
            'telefono_contacto'     => 'nullable|string|max:30',
            'direccion'             => 'nullable|string',
            'latitud'               => 'required|numeric',
            'longitud'              => 'required|numeric',
            'radio_geocerca_metros' => 'required|integer|min:20|max:5000',
            'estado'                => 'required|in:activa,inactiva',
            'es_principal'          => 'nullable|boolean',
        ]);

        $validated['es_principal'] = $request->has('es_principal');

        if ($validated['es_principal']) {
            Sede::where('empresa_id', $sede->empresa_id)
                ->where('id', '!=', $sede->id)
                ->update(['es_principal' => false]);
        }

        $sede->update($validated);

        return redirect()->route('empresas.show', $sede->empresa_id)
            ->with('success', "Configuración de la sede '{$sede->nombre}' actualizada.");
    }

    /**
     * Eliminar sede si no tiene trabajadores vinculados.
     */
    public function destroy(Sede $sede)
    {
        $empresaId = $sede->empresa_id;

        if ($sede->trabajadores()->count() > 0) {
            return redirect()->route('empresas.show', $empresaId)
                ->with('error', "No se puede eliminar la sede '{$sede->nombre}' porque tiene trabajadores asignados. Reasígnelos primero a otra sede.");
        }

        $sede->delete();

        return redirect()->route('empresas.show', $empresaId)
            ->with('success', "Sede eliminada correctamente.");
    }
}
