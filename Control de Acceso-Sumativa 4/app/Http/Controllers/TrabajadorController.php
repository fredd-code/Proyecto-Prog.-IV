<?php

namespace App\Http\Controllers;

use App\Models\Trabajador;
use App\Models\Empresa;
use App\Models\Sede;
use Illuminate\Http\Request;

class TrabajadorController extends Controller
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
        if (!$user->tienePermiso('ver_expedientes') && !$user->tienePermiso('modificar_trabajadores')) {
            abort(403, 'No tiene permisos para acceder al listado de trabajadores.');
        }

        $empresaId = $this->getEmpresaId();
        $query = Trabajador::with(['empresa', 'sede', 'sedes'])->orderBy('apellidos', 'asc');

        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }

        $trabajadores = $query->get();
        return view('trabajadores.index', compact('trabajadores'));
    }

    public function create()
    {
        $user = auth()->user();
        if (!$user->tienePermiso('modificar_trabajadores')) {
            abort(403, 'No tiene permisos para registrar nuevos trabajadores.');
        }

        $empresaId = $this->getEmpresaId();
        $query = Empresa::with(['sedes' => function ($q) {
            $q->where('estado', 'activa');
        }])->where('estado', 'activa');

        if ($empresaId) {
            $query->where('id', $empresaId);
        }

        $empresas = $query->get();
        return view('trabajadores.create', compact('empresas'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user->tienePermiso('modificar_trabajadores')) {
            abort(403, 'No tiene permisos para registrar trabajadores.');
        }

        $validated = $request->validate([
            'empresa_id'         => 'required|exists:empresas,id',
            'sedes'              => 'required|array|min:1',
            'sedes.*'            => 'exists:sedes,id',
            'sede_id'            => 'nullable|exists:sedes,id',
            'cedula'             => 'required|string|max:20|unique:trabajadores,cedula',
            'nombres'            => 'required|string|max:100',
            'apellidos'          => 'required|string|max:100',
            'telefono'           => 'required|string|max:25',
            'correo'             => 'nullable|email|max:100',
            'pin_acceso'         => 'required|string|min:4|max:10',
            'cargo'              => 'required|string|max:100',
            'departamento'       => 'required|string|max:100',
            'fecha_ingreso'      => 'required|date',
            'frecuencia_pago'    => 'required|in:quincenal,semanal',
            'salario_base'       => 'required|numeric|min:0',
            'bono_alimentacion'  => 'required|numeric|min:0',
            'bono_asistencia'    => 'nullable|numeric|min:0',
            'cuenta_bancaria'    => 'nullable|string|max:30',
            // Horarios individuales y Descanso legal (LOTTT)
            'hora_entrada'       => 'required',
            'hora_salida'        => 'required',
            'aplica_almuerzo'    => 'nullable|boolean',
            'inicio_almuerzo'    => 'nullable',
            'fin_almuerzo'       => 'nullable',
            'tolerancia_minutos' => 'required|integer|min:0|max:120',
        ]);

        // Verificación de Cupo SaaS (Límite impuesto por el Operador Macro)
        $empresa = Empresa::findOrFail($request->empresa_id);
        if ($empresa->haAlcanzadoLimite()) {
            return back()->withInput()->with('error', "LÍMITE ALCANZADO: La empresa '{$empresa->nombre_comercial}' tiene contratado un cupo máximo de {$empresa->limite_trabajadores} trabajadores. Para poder registrar personal adicional, el Operador Macro (SuperAdmin) debe ampliar el límite en el panel de Empresas.");
        }

        $sedesIds = $request->input('sedes', []);
        $validated['sede_id'] = $sedesIds[0] ?? $validated['sede_id'] ?? null;

        $validated['aplica_almuerzo'] = $request->has('aplica_almuerzo');
        if (!$validated['aplica_almuerzo']) {
            $validated['inicio_almuerzo'] = null;
            $validated['fin_almuerzo'] = null;
        }

        $trabajador = Trabajador::create($validated);
        
        // Asociar a una o varias sedes autorizadas
        if (!empty($sedesIds)) {
            $trabajador->sedes()->sync($sedesIds);
        }

        $totalSedesAsignadas = count($sedesIds);

        return redirect()->route('trabajadores.index')
            ->with('success', "Trabajador {$validated['nombres']} {$validated['apellidos']} registrado exitosamente con {$totalSedesAsignadas} sede(s) autorizada(s) para marcaje satelital.");
    }

    public function show(Trabajador $trabajador)
    {
        $user = auth()->user();
        if (!$user->tienePermiso('ver_expedientes') && !$user->tienePermiso('modificar_trabajadores')) {
            abort(403, 'No tiene permisos para ver el expediente del trabajador.');
        }

        $trabajador->load(['empresa', 'sede', 'sedes', 'expedientes', 'asistencias' => function ($q) {
            $q->orderBy('fecha', 'desc')->take(15);
        }, 'ajustesSalariales']);

        return view('trabajadores.show', compact('trabajador'));
    }

    public function edit(Trabajador $trabajador)
    {
        $user = auth()->user();
        if (!$user->tienePermiso('modificar_trabajadores')) {
            abort(403, 'No tiene permisos para modificar la ficha del trabajador.');
        }

        $empresaId = $this->getEmpresaId();
        $query = Empresa::with(['sedes' => function ($q) {
            $q->where('estado', 'activa');
        }])->where('estado', 'activa');

        if ($empresaId) {
            $query->where('id', $empresaId);
        }

        $empresas = $query->get();
        $trabajador->load('sedes');

        return view('trabajadores.edit', compact('trabajador', 'empresas'));
    }

    public function update(Request $request, Trabajador $trabajador)
    {
        $user = auth()->user();
        if (!$user->tienePermiso('modificar_trabajadores')) {
            abort(403, 'No tiene permisos para modificar los datos del trabajador.');
        }

        $validated = $request->validate([
            'empresa_id'         => 'required|exists:empresas,id',
            'sedes'              => 'required|array|min:1',
            'sedes.*'            => 'exists:sedes,id',
            'sede_id'            => 'nullable|exists:sedes,id',
            'cedula'             => 'required|string|max:20|unique:trabajadores,cedula,' . $trabajador->id,
            'nombres'            => 'required|string|max:100',
            'apellidos'          => 'required|string|max:100',
            'telefono'           => 'required|string|max:25',
            'correo'             => 'nullable|email|max:100',
            'pin_acceso'         => 'required|string|min:4|max:10',
            'cargo'              => 'required|string|max:100',
            'departamento'       => 'required|string|max:100',
            'fecha_ingreso'      => 'required|date',
            'frecuencia_pago'    => 'required|in:quincenal,semanal',
            'salario_base'       => 'required|numeric|min:0',
            'bono_alimentacion'  => 'required|numeric|min:0',
            'bono_asistencia'    => 'nullable|numeric|min:0',
            'cuenta_bancaria'    => 'nullable|string|max:30',
            'hora_entrada'       => 'required',
            'hora_salida'        => 'required',
            'aplica_almuerzo'    => 'nullable|boolean',
            'inicio_almuerzo'    => 'nullable',
            'fin_almuerzo'       => 'nullable',
            'tolerancia_minutos' => 'required|integer|min:0|max:120',
            'estado'             => 'required|in:activo,inactivo,vacaciones',
        ]);

        $sedesIds = $request->input('sedes', []);
        if (!empty($sedesIds)) {
            $validated['sede_id'] = $sedesIds[0];
            $trabajador->sedes()->sync($sedesIds);
        }

        $validated['aplica_almuerzo'] = $request->has('aplica_almuerzo');
        if (!$validated['aplica_almuerzo']) {
            $validated['inicio_almuerzo'] = null;
            $validated['fin_almuerzo'] = null;
        }

        $trabajador->update($validated);

        return redirect()->route('trabajadores.show', $trabajador->id)
            ->with('success', 'Ficha laboral, sedes autorizadas y horario del trabajador actualizados exitosamente.');
    }
}
