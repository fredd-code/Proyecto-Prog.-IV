<?php

namespace App\Http\Controllers;

use App\Models\Trabajador;
use App\Models\Asistencia;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Controlador del Módulo de Asistencias y Marcaje Remoto
 * Administra el registro presencial o móvil para alimentar el cómputo de días trabajados.
 */
class AsistenciaController extends Controller
{
    /**
     * Listado general de asistencias del mes o día seleccionado.
     */
    public function index(Request $request)
    {
        $fecha = $request->get('fecha', Carbon::today()->toDateString());
        $trabajadores = Trabajador::where('estado', 'activo')->get();
        
        $asistencias = Asistencia::with('trabajador')
            ->whereDate('fecha', $fecha)
            ->get()
            ->keyBy('trabajador_id');

        return view('asistencias.index', compact('trabajadores', 'asistencias', 'fecha'));
    }

    /**
     * Registra el marcaje de entrada con captura de coordenadas GPS.
     */
    public function registrar(Request $request)
    {
        $request->validate([
            'trabajador_id' => 'required|exists:trabajadores,id',
            'latitud'       => 'nullable|numeric',
            'longitud'      => 'nullable|numeric',
            'estado'        => 'required|in:presente,falta_injustificada,falta_justificada,reposo',
        ]);

        $hoy = Carbon::today()->toDateString();
        $ahora = Carbon::now()->toTimeString();

        Asistencia::updateOrCreate(
            [
                'trabajador_id' => $request->trabajador_id,
                'fecha'         => $hoy,
            ],
            [
                'hora_entrada' => $ahora,
                'estado'       => $request->estado,
                'latitud'      => $request->latitud,
                'longitud'     => $request->longitud,
                'observacion'  => $request->observacion,
            ]
        );

        return redirect()->back()
            ->with('success', 'Marcación de asistencia registrada exitosamente.');
    }
}
