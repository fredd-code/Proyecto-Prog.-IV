<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Trabajador;
use App\Models\Sede;
use App\Models\Asistencia;
use App\Models\AjusteSalarial;
use App\Models\Nomina;
use App\Models\ParametroLegal;
use Carbon\Carbon;

/**
 * Controlador de Dashboard Contextual:
 * - Si es SuperAdmin (y no está en modo auditor) -> Despliega el Dashboard Macro SaaS (Solo gestión de clientes y leyes).
 * - Si es Usuario de Empresa (o SuperAdmin en modo auditor) -> Despliega el Dashboard Operativo de la Empresa.
 */
class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 1. Dashboard Macro SaaS (SuperAdmin sin impersonación)
        if ($user->isSuperAdmin() && !session()->has('empresa_activa_id')) {
            $totalEmpresas = Empresa::count();
            $empresasActivas = Empresa::where('estado', 'activa')->count();
            $empresasSuspendidas = Empresa::where('estado', 'suspendida')->count();
            $totalTrabajadores = Trabajador::count();
            $totalSedes = Sede::count();
            $totalParametrosLegales = ParametroLegal::where('activo', true)->count();
            $empresas = Empresa::withCount(['trabajadores', 'sedes', 'nominas'])->latest()->take(6)->get();

            return view('dashboard_macro', compact(
                'totalEmpresas',
                'empresasActivas',
                'empresasSuspendidas',
                'totalTrabajadores',
                'totalSedes',
                'totalParametrosLegales',
                'empresas'
            ));
        }

        // 2. Dashboard Operativo de la Empresa Cliente (Tenant)
        $empresaId = $user->isSuperAdmin() ? session('empresa_activa_id') : $user->empresa_id;
        $empresa = Empresa::with(['sedes'])->findOrFail($empresaId ?? 1);

        $hoy = Carbon::today()->toDateString();
        $totalTrabajadores = Trabajador::where('empresa_id', $empresa->id)->count();
        $asistenciasHoy = Asistencia::where('empresa_id', $empresa->id)->where('fecha', $hoy)->count();
        $retardosHoy = Asistencia::where('empresa_id', $empresa->id)->where('fecha', $hoy)->where('es_retardo', true)->count();
        $prestamosPendientes = AjusteSalarial::where('empresa_id', $empresa->id)->where('estado', 'pendiente')->count();
        $totalNominas = Nomina::where('empresa_id', $empresa->id)->count();

        $trabajadoresRecientes = Trabajador::where('empresa_id', $empresa->id)
            ->with('sedes')
            ->latest()
            ->take(5)
            ->get();

        $asistenciasRecientes = Asistencia::where('empresa_id', $empresa->id)
            ->with(['trabajador', 'sede'])
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard_empresa', compact(
            'empresa',
            'totalTrabajadores',
            'asistenciasHoy',
            'retardosHoy',
            'prestamosPendientes',
            'totalNominas',
            'trabajadoresRecientes',
            'asistenciasRecientes'
        ));
    }
}
