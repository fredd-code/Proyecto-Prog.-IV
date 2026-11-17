<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terminal Kiosco — Control de Acceso Presencial en Sede</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-900 text-white min-h-screen flex flex-col justify-between items-center p-6">

    <div class="text-center mt-6">
        <h1 class="text-3xl font-black tracking-wider text-indigo-400">SIGEN KIOSCO</h1>
        <p class="text-sm text-slate-400 mt-1">Terminal de Marcaje Presencial en Sede Central</p>
        <p class="text-xs text-emerald-400 font-mono mt-0.5">Empresa: {{ $empresa->nombre_comercial ?? 'Sede Central' }}</p>
    </div>

    <!-- Panel de Marcaje Táctil -->
    <div class="bg-slate-800 p-8 rounded-2xl shadow-2xl border border-slate-700 max-w-md w-full space-y-6">
        <div class="text-center">
            <span class="text-4xl">🏢</span>
            <h2 class="text-xl font-bold text-white mt-2">Marcaje Presencial</h2>
            <p class="text-xs text-slate-400 mt-1">Alternativa ante falla de teléfono móvil o batería agotada.</p>
        </div>

        @if(session('success'))
            <div class="bg-emerald-900 border-l-4 border-emerald-400 text-emerald-100 p-3 rounded text-xs">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-rose-900 border-l-4 border-rose-400 text-rose-100 p-3 rounded text-xs">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('asistencias.kiosco.registrar') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Cédula de Identidad *</label>
                <input type="text" name="cedula" placeholder="Ej: V-21202137" required autofocus class="w-full bg-slate-900 border border-slate-600 rounded-xl px-4 py-3 text-base text-white font-mono focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">PIN Personal de Seguridad *</label>
                <input type="password" name="pin_acceso" maxlength="6" placeholder="****" required class="w-full bg-slate-900 border border-slate-600 rounded-xl px-4 py-3 text-xl text-center text-indigo-400 font-mono tracking-widest focus:ring-2 focus:ring-indigo-500">
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold py-3.5 rounded-xl text-base shadow-lg transition">
                CONFIRMAR MARCAJE
            </button>
        </form>
    </div>

    <div class="text-center text-xs text-slate-500 mb-4 font-mono">
        Terminal validada físicamente en sede &bull; {{ date('d/m/Y') }} &bull; <span id="reloj"></span>
    </div>

    <script>
    function actualizarReloj() {
        const ahora = new Date();
        document.getElementById('reloj').innerText = ahora.toLocaleTimeString();
    }
    setInterval(actualizarReloj, 1000);
    actualizarReloj();
    </script>
</body>
</html>
