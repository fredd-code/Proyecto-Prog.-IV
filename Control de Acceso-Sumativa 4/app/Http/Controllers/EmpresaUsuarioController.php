<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Controlador de Gestión de Usuarios y Permisos de la Empresa (RBAC Granular)
 * Permite al Administrador de la Empresa crear usuarios secundarios y asignarles
 * permisos específicos por módulo (préstamos, retiros, expedientes, nóminas, etc.).
 */
class EmpresaUsuarioController extends Controller
{
    private function getEmpresaActual()
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            $empresaId = session('empresa_activa_id', 1);
            return Empresa::findOrFail($empresaId);
        }
        return $user->empresa;
    }

    public function index()
    {
        $empresa = $this->getEmpresaActual();
        $usuarios = User::where('empresa_id', $empresa->id)->orderBy('name', 'asc')->get();

        return view('usuarios_empresa.index', compact('empresa', 'usuarios'));
    }

    public function create()
    {
        $empresa = $this->getEmpresaActual();
        return view('usuarios_empresa.create', compact('empresa'));
    }

    public function store(Request $request)
    {
        $empresa = $this->getEmpresaActual();

        $validated = $request->validate([
            'name'                         => 'required|string|max:100',
            'email'                        => 'required|email|max:100|unique:users,email',
            'password'                     => 'required|string|min:6',
            'cargo_interno'                => 'required|string|max:100',
            'puede_gestionar_prestamos'    => 'nullable|boolean',
            'puede_aprobar_retiros'        => 'nullable|boolean',
            'puede_ver_expedientes'        => 'nullable|boolean',
            'puede_modificar_trabajadores' => 'nullable|boolean',
            'puede_procesar_nominas'       => 'nullable|boolean',
            'puede_ver_asistencias'        => 'nullable|boolean',
        ]);

        User::create([
            'name'                         => $validated['name'],
            'email'                        => $validated['email'],
            'password'                     => Hash::make($validated['password']),
            'empresa_id'                   => $empresa->id,
            'rol'                          => 'supervisor',
            'cargo_interno'                => $validated['cargo_interno'],
            'puede_gestionar_prestamos'    => $request->has('puede_gestionar_prestamos'),
            'puede_aprobar_retiros'        => $request->has('puede_aprobar_retiros'),
            'puede_ver_expedientes'        => $request->has('puede_ver_expedientes'),
            'puede_modificar_trabajadores' => $request->has('puede_modificar_trabajadores'),
            'puede_procesar_nominas'       => $request->has('puede_procesar_nominas'),
            'puede_ver_asistencias'        => $request->has('puede_ver_asistencias'),
            'estado_usuario'               => 'activo',
        ]);

        return redirect()->route('usuarios_empresa.index')
            ->with('success', "Usuario '{$validated['name']}' registrado con permisos configurados.");
    }

    public function edit(User $usuario)
    {
        $empresa = $this->getEmpresaActual();

        // Seguridad: asegurar que pertenezca a la empresa actual
        if ($usuario->empresa_id !== $empresa->id && !auth()->user()->isSuperAdmin()) {
            abort(403, 'No autorizado.');
        }

        return view('usuarios_empresa.edit', compact('empresa', 'usuario'));
    }

    public function update(Request $request, User $usuario)
    {
        $empresa = $this->getEmpresaActual();

        if ($usuario->empresa_id !== $empresa->id && !auth()->user()->isSuperAdmin()) {
            abort(403, 'No autorizado.');
        }

        $validated = $request->validate([
            'name'                         => 'required|string|max:100',
            'email'                        => 'required|email|max:100|unique:users,email,' . $usuario->id,
            'cargo_interno'                => 'required|string|max:100',
            'password'                     => 'nullable|string|min:6',
            'estado_usuario'               => 'required|in:activo,inactivo',
            'puede_gestionar_prestamos'    => 'nullable|boolean',
            'puede_aprobar_retiros'        => 'nullable|boolean',
            'puede_ver_expedientes'        => 'nullable|boolean',
            'puede_modificar_trabajadores' => 'nullable|boolean',
            'puede_procesar_nominas'       => 'nullable|boolean',
            'puede_ver_asistencias'        => 'nullable|boolean',
        ]);

        $data = [
            'name'                         => $validated['name'],
            'email'                        => $validated['email'],
            'cargo_interno'                => $validated['cargo_interno'],
            'estado_usuario'               => $validated['estado_usuario'],
            'puede_gestionar_prestamos'    => $request->has('puede_gestionar_prestamos'),
            'puede_aprobar_retiros'        => $request->has('puede_aprobar_retiros'),
            'puede_ver_expedientes'        => $request->has('puede_ver_expedientes'),
            'puede_modificar_trabajadores' => $request->has('puede_modificar_trabajadores'),
            'puede_procesar_nominas'       => $request->has('puede_procesar_nominas'),
            'puede_ver_asistencias'        => $request->has('puede_ver_asistencias'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);

        return redirect()->route('usuarios_empresa.index')
            ->with('success', "Permisos de '{$usuario->name}' actualizados correctamente.");
    }

    public function destroy(User $usuario)
    {
        $empresa = $this->getEmpresaActual();

        if ($usuario->id === auth()->id()) {
            return redirect()->back()->with('error', 'No puede eliminarse a sí mismo.');
        }

        if ($usuario->empresa_id !== $empresa->id && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $usuario->delete();

        return redirect()->route('usuarios_empresa.index')
            ->with('success', "Usuario eliminado del equipo de la empresa.");
    }
}
