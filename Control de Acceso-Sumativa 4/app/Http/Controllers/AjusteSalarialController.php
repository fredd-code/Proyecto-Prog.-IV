<?php

namespace App\Http\Controllers;

use App\Models\AjusteSalarial;
use App\Models\Trabajador;
use App\Models\Empresa;
use Illuminate\Http\Request;

/**
 * Controlador de Préstamos, Anticipos y Ajustes Salariales Extras
 * Permite registrar deducciones o asignaciones adicionales con la opción de reflejarse o no en el recibo.
 */
class AjusteSalarialController extends Controller
{
    private function getEmpresaId()
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            return session('empresa_activa_id');
        }
        return $user->empresa_id;
    }

    public function index()
    {
        $user = auth()->user();
        if (!$user->tienePermiso('gestionar_prestamos')) {
            abort(403, 'No tiene permisos para gestionar préstamos o ajustes salariales.');
        }

        $empresaId = $this->getEmpresaId();
        $query = AjusteSalarial::with('trabajador')->latest();

        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }

        $ajustes = $query->get();
        return view('ajustes.index', compact('ajustes'));
    }

    public function create()
    {
        $user = auth()->user();
        if (!$user->tienePermiso('gestionar_prestamos')) {
            abort(403, 'No tiene permisos para otorgar préstamos o ajustes.');
        }

        $empresaId = $this->getEmpresaId();
        $query = Trabajador::where('estado', 'activo');

        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }

        $trabajadores = $query->orderBy('nombres')->get();
        return view('ajustes.create', compact('trabajadores'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user->tienePermiso('gestionar_prestamos')) {
            abort(403, 'No tiene permisos para otorgar préstamos o registrar deducciones.');
        }

        $validated = $request->validate([
            'trabajador_id'      => 'required|exists:trabajadores,id',
            'tipo'               => 'required|in:deduccion,asignacion',
            'concepto'           => 'required|string|max:150',
            'monto_total'        => 'required|numeric|min:0.01',
            'cuotas_totales'     => 'required|integer|min:1|max:48',
            'reflejar_en_recibo' => 'required|boolean',
            'fecha_inicio'       => 'required|date',
            'observaciones'      => 'nullable|string',
        ]);

        $trabajador = Trabajador::findOrFail($request->trabajador_id);
        $montoCuota = round($request->monto_total / $request->cuotas_totales, 2);

        AjusteSalarial::create([
            'empresa_id'         => $trabajador->empresa_id ?? 1,
            'trabajador_id'      => $trabajador->id,
            'tipo'               => $request->tipo,
            'concepto'           => $request->concepto,
            'monto_total'        => $request->monto_total,
            'monto_cuota'        => $montoCuota,
            'cuotas_totales'     => $request->cuotas_totales,
            'cuotas_cobradas'    => 0,
            'reflejar_en_recibo' => (bool)$request->reflejar_en_recibo,
            'fecha_inicio'       => $request->fecha_inicio,
            'estado'             => 'activo',
            'observaciones'      => $request->observaciones,
        ]);

        $tipoTexto = ($request->tipo === 'deduccion') ? 'Descuento / Préstamo' : 'Abono Extra';
        return redirect()->route('ajustes.index')
            ->with('success', "{$tipoTexto} registrado exitosamente para el trabajador.");
    }

    public function destroy(AjusteSalarial $ajuste)
    {
        $user = auth()->user();
        if (!$user->tienePermiso('gestionar_prestamos')) {
            abort(403, 'No tiene permisos para anular ajustes o préstamos.');
        }

        $ajuste->delete();
        return redirect()->route('ajustes.index')
            ->with('success', 'Ajuste salarial cancelado y eliminado.');
    }
}
