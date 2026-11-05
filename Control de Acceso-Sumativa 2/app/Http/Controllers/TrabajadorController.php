<?php

namespace App\Http\Controllers;

use App\Models\Trabajador;
use Illuminate\Http\Request;

/**
 * Controlador de Gestión de Personal (Módulo de Trabajadores)
 * Responsable de coordinar el ciclo CRUD de los empleados y sus parámetros salariales.
 */
class TrabajadorController extends Controller
{
    /**
     * Muestra el listado general de los trabajadores registrados.
     */
    public function index()
    {
        $trabajadores = Trabajador::orderBy('apellidos', 'asc')->get();
        return view('trabajadores.index', compact('trabajadores'));
    }

    /**
     * Muestra el formulario para dar de alta a un nuevo trabajador.
     */
    public function create()
    {
        return view('trabajadores.create');
    }

    /**
     * Almacena un nuevo trabajador validando cédula y tipos de datos numéricos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cedula'            => 'required|string|max:20|unique:trabajadores,cedula',
            'nombres'           => 'required|string|max:100',
            'apellidos'         => 'required|string|max:100',
            'telefono'          => 'nullable|string|max:20',
            'correo'            => 'nullable|email|max:100',
            'cargo'             => 'required|string|max:100',
            'departamento'      => 'required|string|max:100',
            'fecha_ingreso'     => 'required|date',
            'frecuencia_pago'   => 'required|in:quincenal,semanal',
            'salario_base'      => 'required|numeric|min:0',
            'bono_alimentacion' => 'required|numeric|min:0',
            'bono_asistencia'   => 'nullable|numeric|min:0',
            'cuenta_bancaria'   => 'nullable|string|max:30',
        ]);

        Trabajador::create($validated);

        return redirect()->route('trabajadores.index')
            ->with('success', 'Trabajador registrado exitosamente en el sistema.');
    }

    /**
     * Muestra el perfil integral del trabajador y su expediente digital.
     */
    public function show(Trabajador $trabajador)
    {
        $trabajador->load(['expedientes', 'asistencias' => function ($q) {
            $q->orderBy('fecha', 'desc')->take(15);
        }]);
        
        return view('trabajadores.show', compact('trabajador'));
    }
}
