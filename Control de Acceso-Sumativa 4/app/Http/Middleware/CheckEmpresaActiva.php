<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEmpresaActiva
{
    /**
     * Intercepta peticiones de usuarios de empresas cliente.
     * Si la empresa está suspendida por mora o falta de pago, bloquea el acceso.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Permitir siempre la ruta de cierre de sesión
        if ($request->routeIs('logout')) {
            return $next($request);
        }

        if ($user && !$user->isSuperAdmin()) {
            // Verificar si el usuario individual está activo
            if ($user->estado_usuario === 'inactivo') {
                auth()->logout();
                return redirect()->route('login')
                    ->with('error', 'Su usuario individual ha sido desactivado por el administrador de la empresa.');
            }

            // Verificar si la empresa cliente está suspendida
            if ($user->empresa && $user->empresa->isSuspendida()) {
                return response()->view('errors.empresa_suspendida', [
                    'empresa' => $user->empresa,
                ], 403);
            }
        }

        return $next($request);
    }
}
