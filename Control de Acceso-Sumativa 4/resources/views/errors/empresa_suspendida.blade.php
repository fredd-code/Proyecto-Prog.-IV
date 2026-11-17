<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Suspendido — SIGEN SaaS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-900 text-white min-h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full bg-slate-800 border border-slate-700 rounded-2xl p-8 shadow-2xl text-center space-y-6">
        <div class="w-16 h-16 bg-rose-500/20 text-rose-400 rounded-full flex items-center justify-center mx-auto text-3xl">
            🚫
        </div>

        <div>
            <span class="bg-rose-900/60 text-rose-300 text-xxs font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-rose-700/50">
                Suscripción en Pausa Administrativa
            </span>
            <h1 class="text-xl font-bold text-white mt-3">Acceso Empresarial Suspendido</h1>
            <p class="text-xs text-slate-400 mt-1">El servicio de nómina y control de acceso para esta organización se encuentra inactivo.</p>
        </div>

        <div class="bg-slate-900/80 p-4 rounded-xl border border-slate-700/80 text-left text-xs space-y-2">
            <div class="flex justify-between border-b border-slate-800 pb-1.5">
                <span class="text-slate-400">Empresa:</span>
                <span class="font-bold text-white">{{ $empresa->nombre_comercial }}</span>
            </div>
            <div class="flex justify-between border-b border-slate-800 pb-1.5">
                <span class="text-slate-400">RIF:</span>
                <span class="font-mono text-slate-300">{{ $empresa->rif }}</span>
            </div>
            <div class="pt-1">
                <span class="text-slate-400 block text-xxs uppercase font-bold">Causa / Observación:</span>
                <p class="text-rose-400 font-semibold mt-0.5">{{ $empresa->motivo_suspension ?? 'Mora en el pago de la tarifa mensual de suscripción SaaS.' }}</p>
            </div>
        </div>

        <div class="bg-amber-950/40 border border-amber-800/50 p-3.5 rounded-xl text-xxs text-amber-200 text-left">
            <p class="font-bold text-amber-300 mb-1">¿Cómo reactivar el servicio?</p>
            <p>Comuníquese con el <strong>Operador Macro del Sistema (SuperAdmin)</strong> para procesar la regularización de su cuenta y reactivar de inmediato el acceso a los módulos de trabajadores y nómina.</p>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full bg-slate-700 hover:bg-slate-600 text-slate-200 text-xs font-bold py-2.5 rounded-xl transition">
                Cerrar Sesión
            </button>
        </form>
    </div>
</body>
</html>
