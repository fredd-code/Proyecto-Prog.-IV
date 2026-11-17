<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Controlador de Gestión de Empresas Clientes (Panel Multi-Tenant / SaaS)
 * Permite al Operador Macro (SuperAdmin) dar de alta y administrar empresas clientes,
 * asignar cuotas (trabajadores y sedes), generar credenciales de acceso para el cliente,
 * y habilitar/suspender cuentas por pago o mora.
 */
class EmpresaController extends Controller
{
    public function index()
    {
        // El listado de empresas es exclusivo del SuperAdmin (Operador Macro)
        if (!auth()->user()->isSuperAdmin()) {
            return redirect()->route('dashboard');
        }

        $empresas = Empresa::withCount(['trabajadores', 'nominas', 'sedes', 'usuarios'])->get();
        return view('empresas.index', compact('empresas'));
    }

    public function create()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }
        return view('empresas.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            // Datos de la Empresa
            'rif'                           => 'required|string|max:20|unique:empresas,rif',
            'nombre_comercial'              => 'required|string|max:150',
            'razon_social'                  => 'required|string|max:200',
            'telefono_notificaciones'       => 'required|string|max:30',
            'email_contacto'                => 'nullable|email|max:100',
            'direccion'                     => 'nullable|string',
            // Cuotas del Plan SaaS
            'limite_trabajadores'           => 'required|integer|min:1|max:5000',
            'limite_sedes'                  => 'required|integer|min:1|max:50',
            // Credenciales del Usuario Administrador del Cliente
            'admin_name'                    => 'required|string|max:100',
            'admin_email'                   => 'required|email|max:100|unique:users,email',
            'admin_password'                => 'nullable|string|min:6',
            // Sedes dinámicas
            'sedes'                         => 'nullable|array|min:1',
            'sedes.*.nombre'                => 'required_with:sedes|string|max:150',
            'sedes.*.codigo'                => 'nullable|string|max:30',
            'sedes.*.telefono_contacto'     => 'nullable|string|max:30',
            'sedes.*.direccion'             => 'nullable|string',
            'sedes.*.latitud'               => 'required_with:sedes|numeric',
            'sedes.*.longitud'              => 'required_with:sedes|numeric',
            'sedes.*.radio_geocerca_metros' => 'required_with:sedes|integer|min:20|max:5000',
            'latitud_sede'                  => 'nullable|numeric',
            'longitud_sede'                 => 'nullable|numeric',
            'radio_geocerca_metros'         => 'nullable|integer|min:20|max:5000',
        ]);

        // Determinar coordenadas base para la empresa
        if (!empty($validated['sedes']) && count($validated['sedes']) > 0) {
            $primeraSede = $validated['sedes'][0];
            $validated['latitud_sede'] = $primeraSede['latitud'];
            $validated['longitud_sede'] = $primeraSede['longitud'];
            $validated['radio_geocerca_metros'] = $primeraSede['radio_geocerca_metros'];
        } else {
            $validated['latitud_sede'] = $validated['latitud_sede'] ?? 10.380000;
            $validated['longitud_sede'] = $validated['longitud_sede'] ?? -66.960000;
            $validated['radio_geocerca_metros'] = $validated['radio_geocerca_metros'] ?? 250;
        }

        // Valores por defecto
        $validated['horario_entrada'] = '08:00:00';
        $validated['horario_salida'] = '17:00:00';
        $validated['tolerancia_minutos'] = 15;
        $validated['estado'] = 'activa';

        $empresa = Empresa::create($validated);

        // Guardar sedes dinámicas
        if (!empty($validated['sedes']) && count($validated['sedes']) > 0) {
            foreach ($validated['sedes'] as $idx => $sedeData) {
                $empresa->sedes()->create([
                    'nombre'                => $sedeData['nombre'],
                    'codigo'                => $sedeData['codigo'] ?? ('SEDE-0' . ($idx + 1)),
                    'telefono_contacto'     => $sedeData['telefono_contacto'] ?? $empresa->telefono_notificaciones,
                    'direccion'             => $sedeData['direccion'] ?? $empresa->direccion,
                    'latitud'               => $sedeData['latitud'],
                    'longitud'              => $sedeData['longitud'],
                    'radio_geocerca_metros' => $sedeData['radio_geocerca_metros'] ?? 200,
                    'es_principal'          => ($idx === 0),
                    'estado'                => 'activa',
                ]);
            }
        } else {
            // Sede Principal por defecto
            $empresa->sedes()->create([
                'nombre'                => 'Sede Principal - ' . $empresa->nombre_comercial,
                'codigo'                => 'SEDE-01',
                'telefono_contacto'     => $empresa->telefono_notificaciones,
                'direccion'             => $empresa->direccion ?? 'Sede Central',
                'latitud'               => $empresa->latitud_sede,
                'longitud'              => $empresa->longitud_sede,
                'radio_geocerca_metros' => $empresa->radio_geocerca_metros,
                'es_principal'          => true,
                'estado'                => 'activa',
            ]);
        }

        // 3. Crear Usuario Administrador Principal de la Empresa con todos los permisos
        $rawPassword = $request->filled('admin_password') 
            ? $request->admin_password 
            : Str::random(10);

        $usuarioAdmin = User::create([
            'name'                         => $validated['admin_name'],
            'email'                        => $validated['admin_email'],
            'password'                     => Hash::make($rawPassword),
            'empresa_id'                   => $empresa->id,
            'rol'                          => 'admin_empresa',
            'cargo_interno'                => 'Administrador Principal de la Empresa',
            'puede_gestionar_prestamos'    => true,
            'puede_aprobar_retiros'        => true,
            'puede_ver_expedientes'        => true,
            'puede_modificar_trabajadores' => true,
            'puede_procesar_nominas'       => true,
            'puede_ver_asistencias'        => true,
            'estado_usuario'               => 'activo',
        ]);

        // Guardar credenciales en sesión flash para mostrárselas al Operador Macro
        session()->flash('credenciales_nuevas', [
            'empresa'  => $empresa->nombre_comercial,
            'nombre'   => $usuarioAdmin->name,
            'email'    => $usuarioAdmin->email,
            'password' => $rawPassword,
        ]);

        return redirect()->route('empresas.show', $empresa->id)
            ->with('success', "Empresa '{$empresa->nombre_comercial}' dada de alta exitosamente con cuenta de administrador generada.");
    }

    public function show(Empresa $empresa)
    {
        if (!auth()->user()->isSuperAdmin() && auth()->user()->empresa_id !== $empresa->id) {
            abort(403);
        }

        $empresa->load([
            'sedes' => function ($q) {
                $q->withCount('trabajadores');
            },
            'usuarios',
            'trabajadores.sede',
            'notificaciones' => function ($q) {
                $q->latest()->take(20);
            }
        ]);

        return view('empresas.show', compact('empresa'));
    }

    public function edit(Empresa $empresa)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }
        return view('empresas.edit', compact('empresa'));
    }

    public function update(Request $request, Empresa $empresa)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'nombre_comercial'        => 'required|string|max:150',
            'razon_social'            => 'required|string|max:200',
            'telefono_notificaciones' => 'required|string|max:30',
            'email_contacto'          => 'nullable|email|max:100',
            'direccion'               => 'nullable|string',
            'latitud_sede'            => 'required|numeric',
            'longitud_sede'           => 'required|numeric',
            'radio_geocerca_metros'   => 'required|integer|min:20|max:5000',
            'limite_trabajadores'     => 'required|integer|min:1|max:5000',
            'limite_sedes'            => 'required|integer|min:1|max:50',
            'estado'                  => 'required|in:activa,suspendida',
            'motivo_suspension'       => 'nullable|string|max:255',
        ]);

        if ($validated['estado'] === 'activa') {
            $validated['motivo_suspension'] = null;
        }

        $empresa->update($validated);

        return redirect()->route('empresas.show', $empresa->id)
            ->with('success', 'Configuración de la empresa y cuotas SaaS actualizadas.');
    }

    /**
     * Alternar estado de la empresa (Habilitar / Suspender por mora o falta de pago).
     */
    public function toggleEstado(Request $request, Empresa $empresa)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        if ($empresa->isActiva()) {
            $motivo = $request->input('motivo_suspension', 'Mora o falta de pago del servicio de plataforma SaaS.');
            $empresa->update([
                'estado'            => 'suspendida',
                'motivo_suspension' => $motivo,
            ]);
            $mensaje = "La empresa '{$empresa->nombre_comercial}' fue SUSPENDIDA por falta de pago. Sus usuarios tienen el acceso bloqueado.";
        } else {
            $empresa->update([
                'estado'            => 'activa',
                'motivo_suspension' => null,
            ]);
            $mensaje = "Acceso RESTABLECIDO para la empresa '{$empresa->nombre_comercial}'. Servicio activo.";
        }

        return redirect()->back()->with('success', $mensaje);
    }

    /**
     * Impersonación: El SuperAdmin ingresa como Auditor al panel de una empresa específica.
     */
    public function impersonar(Empresa $empresa)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        session(['empresa_activa_id' => $empresa->id]);

        return redirect()->route('dashboard')
            ->with('success', "Modo Auditor SaaS: Visualizando panel de la empresa '{$empresa->nombre_comercial}'.");
    }

    /**
     * Salir de la impersonación y volver al Panel Macro.
     */
    public function salirImpersonacion()
    {
        session()->forget('empresa_activa_id');

        return redirect()->route('empresas.index')
            ->with('success', 'Ha salido del panel de la empresa y regresado al Panel Macro de SuperAdmin.');
    }
}
