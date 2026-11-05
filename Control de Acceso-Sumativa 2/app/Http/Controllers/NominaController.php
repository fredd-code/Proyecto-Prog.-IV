<?php

namespace App\Http\Controllers;

use App\Models\Nomina;
use App\Models\NominaDetalle;
use App\Models\Trabajador;
use App\Models\Asistencia;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Controlador del Motor de Nómina y Obligaciones Laborales Venezolanas (LOTTT)
 * Calcula asignaciones y deducciones obligatorias (IVSS 4%, RPE 0.5%, FAOV 1%, Cestaticket).
 */
class NominaController extends Controller
{
    /**
     * Lista histórica de nóminas procesadas.
     */
    public function index()
    {
        $nominas = Nomina::orderBy('fecha_inicio', 'desc')->get();
        return view('nominas.index', compact('nominas'));
    }

    /**
     * Formulario para configurar y generar un nuevo período de nómina.
     */
    public function create()
    {
        return view('nominas.create');
    }

    /**
     * Procesa la nómina calculando días asistidos y retenciones legales.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tipo'         => 'required|in:quincenal,semanal',
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $tipo = $request->tipo;
        $inicio = $request->fecha_inicio;
        $fin = $request->fecha_fin;

        $diasBasePeriodo = ($tipo === 'quincenal') ? 15 : 7;

        $trabajadores = Trabajador::where('estado', 'activo')
            ->where('frecuencia_pago', $tipo)
            ->get();

        if ($trabajadores->isEmpty()) {
            return redirect()->back()->with('error', "No existen trabajadores activos con frecuencia de pago {$tipo}.");
        }

        $codigo = 'NOM-' . strtoupper(substr($tipo, 0, 1)) . '-' . Carbon::parse($inicio)->format('Ymd');
        $nomina = Nomina::create([
            'codigo'             => $codigo,
            'tipo'               => $tipo,
            'fecha_inicio'       => $inicio,
            'fecha_fin'          => $fin,
            'total_asignaciones' => 0,
            'total_deducciones'  => 0,
            'total_neto'         => 0,
            'estado'             => 'procesada',
        ]);

        $acumAsignaciones = 0;
        $acumDeducciones = 0;
        $acumNeto = 0;

        foreach ($trabajadores as $emp) {
            $diasAsistidos = Asistencia::where('trabajador_id', $emp->id)
                ->whereBetween('fecha', [$inicio, $fin])
                ->where('estado', 'presente')
                ->count();

            $diasEfectivos = ($diasAsistidos > 0) ? min($diasAsistidos, $diasBasePeriodo) : $diasBasePeriodo;

            // 1. Salario ordinario devengado según días laborados (Base mes de 30 días)
            $salarioDiario = $emp->salario_base / 30;
            $salarioDevengado = round($salarioDiario * $diasEfectivos, 2);

            // 2. Beneficio de Alimentación (Cestaticket proporcional a la quincena/semana)
            $cestaticketPeriodo = round(($emp->bono_alimentacion / 30) * $diasEfectivos, 2);

            // 3. Bonificaciones adicionales
            $bonoPeriodo = ($tipo === 'quincenal') ? round($emp->bono_asistencia / 2, 2) : round($emp->bono_asistencia / 4, 2);

            $totalAsig = round($salarioDevengado + $cestaticketPeriodo + $bonoPeriodo, 2);

            // 4. Deducciones de Ley LOTTT sobre salario devengado
            $ivss = round($salarioDevengado * 0.04, 2);
            $rpe  = round($salarioDevengado * 0.005, 2);
            $faov = round($salarioDevengado * 0.01, 2);

            $totalDed = round($ivss + $rpe + $faov, 2);
            $neto = round($totalAsig - $totalDed, 2);

            NominaDetalle::create([
                'nomina_id'             => $nomina->id,
                'trabajador_id'         => $emp->id,
                'dias_trabajados'       => $diasEfectivos,
                'salario_devengado'     => $salarioDevengado,
                'cestaticket_devengado' => $cestaticketPeriodo,
                'bono_adicional'        => $bonoPeriodo,
                'total_asignaciones'    => $totalAsig,
                'deduccion_ivss'        => $ivss,
                'deduccion_rpe'         => $rpe,
                'deduccion_faov'        => $faov,
                'total_deducciones'     => $totalDed,
                'neto_a_pagar'          => $neto,
            ]);

            $acumAsignaciones += $totalAsig;
            $acumDeducciones  += $totalDed;
            $acumNeto         += $neto;
        }

        $nomina->update([
            'total_asignaciones' => $acumAsignaciones,
            'total_deducciones'  => $acumDeducciones,
            'total_neto'         => $acumNeto,
        ]);

        return redirect()->route('nominas.show', $nomina->id)
            ->with('success', "Nómina {$codigo} generada exitosamente.");
    }

    /**
     * Muestra el resumen y la lista de empleados calculados en la nómina.
     */
    public function show(Nomina $nomina)
    {
        $nomina->load('detalles.trabajador');
        return view('nominas.show', compact('nomina'));
    }

    /**
     * Emite el Recibo de Pago individual con formato imprimible.
     */
    public function recibo(NominaDetalle $detalle)
    {
        $detalle->load(['nomina', 'trabajador']);
        return view('nominas.recibo', compact('detalle'));
    }
}
