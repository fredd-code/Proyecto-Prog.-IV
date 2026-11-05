<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Pago - {{ $detalle->trabajador->cedula }}</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none; }
            body { background: white; padding: 0; }
        }
    </style>
</head>
<body class="bg-slate-100 p-8 font-sans">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow border border-slate-300">
        <!-- Botón Imprimir -->
        <div class="no-print flex justify-end mb-4">
            <button onclick="window.print()" class="bg-indigo-600 text-white px-4 py-2 rounded text-xs font-bold hover:bg-indigo-700">
                Imprimir Recibo (Ctrl + P)
            </button>
        </div>

        <!-- Encabezado Membretado -->
        <div class="border-b-2 border-slate-800 pb-4 text-center">
            <h1 class="text-lg font-bold uppercase tracking-wider text-slate-900">EMPRESA EJEMPLO, C.A.</h1>
            <p class="text-xs text-slate-500">RIF: J-12345678-9 &bull; San Joaquín de Turmero - Estado Aragua</p>
            <h2 class="text-sm font-bold uppercase mt-2 text-indigo-900">COMPROBANTE DE PAGO DE NÓMINA ({{ strtoupper($detalle->nomina->tipo) }})</h2>
            <p class="text-xs font-mono text-slate-500">Período: {{ $detalle->nomina->fecha_inicio }} al {{ $detalle->nomina->fecha_fin }}</p>
        </div>

        <!-- Datos del Trabajador -->
        <div class="grid grid-cols-2 gap-4 my-4 text-xs">
            <div>
                <p><span class="font-bold">Trabajador:</span> {{ $detalle->trabajador->nombre_completo }}</p>
                <p><span class="font-bold">C.I:</span> {{ $detalle->trabajador->cedula }}</p>
                <p><span class="font-bold">Cargo:</span> {{ $detalle->trabajador->cargo }}</p>
            </div>
            <div>
                <p><span class="font-bold">Fecha Ingreso:</span> {{ $detalle->trabajador->fecha_ingreso }}</p>
                <p><span class="font-bold">Días Trabajados:</span> {{ $detalle->dias_trabajados }}</p>
                <p><span class="font-bold">Código Nómina:</span> {{ $detalle->nomina->codigo }}</p>
            </div>
        </div>

        <!-- Tabla de Conceptos -->
        <table class="w-full text-xs border border-slate-300 my-4">
            <thead class="bg-slate-100 border-b border-slate-300">
                <tr>
                    <th class="p-2 text-left">Concepto</th>
                    <th class="p-2 text-right">Asignaciones</th>
                    <th class="p-2 text-right">Deducciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 font-mono">
                <tr>
                    <td class="p-2 font-sans">Salario Base (Días Laborados)</td>
                    <td class="p-2 text-right">{{ number_format($detalle->salario_devengado, 2) }}</td>
                    <td class="p-2 text-right">--</td>
                </tr>
                <tr>
                    <td class="p-2 font-sans">Cestaticket Socialista</td>
                    <td class="p-2 text-right">{{ number_format($detalle->cestaticket_devengado, 2) }}</td>
                    <td class="p-2 text-right">--</td>
                </tr>
                @if($detalle->bono_adicional > 0)
                <tr>
                    <td class="p-2 font-sans">Bonificación Complementaria</td>
                    <td class="p-2 text-right">{{ number_format($detalle->bono_adicional, 2) }}</td>
                    <td class="p-2 text-right">--</td>
                </tr>
                @endif
                <tr>
                    <td class="p-2 font-sans text-rose-700">Retención S.S.O. (IVSS 4%)</td>
                    <td class="p-2 text-right">--</td>
                    <td class="p-2 text-right text-rose-700">{{ number_format($detalle->deduccion_ivss, 2) }}</td>
                </tr>
                <tr>
                    <td class="p-2 font-sans text-rose-700">Retención Régimen Paro Forzoso (RPE 0.5%)</td>
                    <td class="p-2 text-right">--</td>
                    <td class="p-2 text-right text-rose-700">{{ number_format($detalle->deduccion_rpe, 2) }}</td>
                </tr>
                <tr>
                    <td class="p-2 font-sans text-rose-700">Retención F.A.O.V. (1%)</td>
                    <td class="p-2 text-right">--</td>
                    <td class="p-2 text-right text-rose-700">{{ number_format($detalle->deduccion_faov, 2) }}</td>
                </tr>
            </tbody>
            <tfoot class="bg-slate-50 font-bold border-t-2 border-slate-800 font-mono">
                <tr>
                    <td class="p-2 text-left font-sans">TOTALES</td>
                    <td class="p-2 text-right">{{ number_format($detalle->total_asignaciones, 2) }}</td>
                    <td class="p-2 text-right text-rose-700">{{ number_format($detalle->total_deducciones, 2) }}</td>
                </tr>
                <tr class="bg-indigo-50 text-indigo-950 text-sm">
                    <td colspan="2" class="p-2 text-left font-sans">NETO A PAGAR:</td>
                    <td class="p-2 text-right font-extrabold">{{ number_format($detalle->neto_a_pagar, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Firmas -->
        <div class="grid grid-cols-2 gap-8 mt-12 pt-8 text-center text-xs text-slate-600">
            <div class="border-t border-slate-400">
                Firma y Sello Patrono
            </div>
            <div class="border-t border-slate-400">
                Recibí Conforme (Trabajador)
            </div>
        </div>
    </div>
</body>
</html>
