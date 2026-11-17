<?php

namespace App\Http\Controllers;

use App\Models\Nomina;
use App\Models\NominaDetalle;
use App\Models\Trabajador;
use App\Models\Asistencia;
use App\Models\AjusteSalarial;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NominaController extends Controller
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
        if (!$user->tienePermiso('procesar_nominas')) {
            abort(403, 'No tiene permisos para consultar o procesar nóminas.');
        }

        $empresaId = $this->getEmpresaId();
        $query = Nomina::with('empresa')->orderBy('fecha_inicio', 'desc');

        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }

        $nominas = $query->get();
        return view('nominas.index', compact('nominas'));
    }

    public function create()
    {
        $user = auth()->user();
        if (!$user->tienePermiso('procesar_nominas')) {
            abort(403, 'No tiene permisos para procesar nóminas.');
        }

        $empresaId = $this->getEmpresaId();
        if ($empresaId) {
            $empresas = Empresa::where('id', $empresaId)->where('estado', 'activa')->get();
        } else {
            $empresas = Empresa::where('estado', 'activa')->get();
        }

        return view('nominas.create', compact('empresas'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user->tienePermiso('procesar_nominas')) {
            abort(403, 'No tiene permisos para procesar nóminas.');
        }

        $request->validate([
            'empresa_id'   => 'required|exists:empresas,id',
            'tipo'         => 'required|in:quincenal,semanal',
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $empresaId = $request->empresa_id;
        $tipo = $request->tipo;
        $inicio = $request->fecha_inicio;
        $fin = $request->fecha_fin;

        $diasBasePeriodo = ($tipo === 'quincenal') ? 15 : 7;

        $trabajadores = Trabajador::where('empresa_id', $empresaId)
            ->where('estado', 'activo')
            ->where('frecuencia_pago', $tipo)
            ->get();

        if ($trabajadores->isEmpty()) {
            return redirect()->back()->with('error', "No existen trabajadores activos en esta empresa con frecuencia {$tipo}.");
        }

        $codigo = 'NOM-' . strtoupper(substr($tipo, 0, 1)) . '-' . Carbon::parse($inicio)->format('Ymd');
        $nomina = Nomina::create([
            'empresa_id'                  => $empresaId,
            'codigo'                      => $codigo,
            'tipo'                        => $tipo,
            'fecha_inicio'                => $inicio,
            'fecha_fin'                   => $fin,
            'total_asignaciones'          => 0,
            'total_deducciones'           => 0,
            'total_prestamos_deducciones' => 0,
            'total_abonos_asignaciones'   => 0,
            'total_neto'                  => 0,
            'estado'                      => 'procesada',
        ]);

        $acumAsignaciones = 0;
        $acumDeducciones = 0;
        $acumPrestamos = 0;
        $acumAbonosExtras = 0;
        $acumNeto = 0;

        foreach ($trabajadores as $emp) {
            $diasAsistidos = Asistencia::where('trabajador_id', $emp->id)
                ->whereBetween('fecha', [$inicio, $fin])
                ->whereIn('estado', ['presente', 'falta_justificada'])
                ->count();

            $diasEfectivos = ($diasAsistidos > 0) ? min($diasAsistidos, $diasBasePeriodo) : $diasBasePeriodo;

            // 1. Salario devengado
            $salarioDiario = $emp->salario_base / 30;
            $salarioDevengado = round($salarioDiario * $diasEfectivos, 2);

            // 2. Beneficio de Alimentación (Cestaticket)
            $cestaticketPeriodo = round(($emp->bono_alimentacion / 30) * $diasEfectivos, 2);

            // 3. Bonificaciones adicionales
            $bonoPeriodo = ($tipo === 'quincenal') ? round($emp->bono_asistencia / 2, 2) : round($emp->bono_asistencia / 4, 2);

            // 4. Procesamiento de Préstamos, Anticipos y Abonos Extras
            $ajustesActivos = AjusteSalarial::where('trabajador_id', $emp->id)
                ->where('estado', 'activo')
                ->where('fecha_inicio', '<=', $fin)
                ->get();

            $totalDeduccionesAjustes = 0;
            $totalAsignacionesAjustes = 0;
            $desgloseAjustes = [];

            foreach ($ajustesActivos as $ajuste) {
                if ($ajuste->tipo === 'deduccion') {
                    $totalDeduccionesAjustes += $ajuste->monto_cuota;
                } else {
                    $totalAsignacionesAjustes += $ajuste->monto_cuota;
                }

                // Guardar para recibo
                $desgloseAjustes[] = [
                    'concepto'           => $ajuste->concepto,
                    'tipo'               => $ajuste->tipo,
                    'monto'              => $ajuste->monto_cuota,
                    'cuota'              => ($ajuste->cuotas_cobradas + 1) . '/' . $ajuste->cuotas_totales,
                    'reflejar_en_recibo' => (bool)$ajuste->reflejar_en_recibo,
                ];

                // Actualizar cuota
                $ajuste->increment('cuotas_cobradas');
                if ($ajuste->cuotas_cobradas >= $ajuste->cuotas_totales) {
                    $ajuste->update(['estado' => 'completado']);
                }
            }

            $totalAsig = round($salarioDevengado + $cestaticketPeriodo + $bonoPeriodo + $totalAsignacionesAjustes, 2);

            // 5. Deducciones de Ley LOTTT sobre salario devengado ordinario
            $ivss = round($salarioDevengado * 0.04, 2);
            $rpe  = round($salarioDevengado * 0.005, 2);
            $faov = round($salarioDevengado * 0.01, 2);

            $totalDedLey = round($ivss + $rpe + $faov, 2);
            $totalDedTotal = round($totalDedLey + $totalDeduccionesAjustes, 2);
            $neto = round($totalAsig - $totalDedTotal, 2);

            NominaDetalle::create([
                'nomina_id'             => $nomina->id,
                'trabajador_id'         => $emp->id,
                'dias_trabajados'       => $diasEfectivos,
                'salario_devengado'     => $salarioDevengado,
                'cestaticket_devengado' => $cestaticketPeriodo,
                'bono_adicional'        => $bonoPeriodo,
                'abonos_extras'         => $totalAsignacionesAjustes,
                'total_asignaciones'    => $totalAsig,
                'deduccion_ivss'        => $ivss,
                'deduccion_rpe'         => $rpe,
                'deduccion_faov'        => $faov,
                'prestamos_deduccion'   => $totalDeduccionesAjustes,
                'total_deducciones'     => $totalDedTotal,
                'neto_a_pagar'          => $neto,
                'ajustes_detalle_json'  => $desgloseAjustes,
            ]);

            $acumAsignaciones += $totalAsig;
            $acumDeducciones  += $totalDedTotal;
            $acumPrestamos    += $totalDeduccionesAjustes;
            $acumAbonosExtras += $totalAsignacionesAjustes;
            $acumNeto         += $neto;
        }

        $nomina->update([
            'total_asignaciones'          => $acumAsignaciones,
            'total_deducciones'           => $acumDeducciones,
            'total_prestamos_deducciones' => $acumPrestamos,
            'total_abonos_asignaciones'   => $acumAbonosExtras,
            'total_neto'                  => $acumNeto,
        ]);

        return redirect()->route('nominas.show', $nomina->id)
            ->with('success', "Nómina {$codigo} procesada con préstamos y deducciones extras.");
    }

    public function show(Nomina $nomina)
    {
        $nomina->load(['empresa', 'detalles.trabajador']);
        return view('nominas.show', compact('nomina'));
    }

    public function recibo(NominaDetalle $detalle)
    {
        $detalle->load(['nomina.empresa', 'trabajador']);
        return view('nominas.recibo', compact('detalle'));
    }
}
