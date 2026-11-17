<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Pago — {{ $detalle->trabajador->nombre_completo }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            body { background: white !important; font-size: 11pt; }
            .no-print { display: none !important; }
            .recibo-card { box-shadow: none !important; border: 1px solid #94a3b8 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen p-4 md:p-8 flex flex-col items-center">

    <div class="w-full max-w-3xl flex justify-between items-center mb-4 no-print">
        <a href="{{ route('nominas.show', $detalle->nomina_id) }}" class="text-xs font-bold text-indigo-700 hover:underline">
            &larr; Volver al Resumen de Nómina
        </a>
        <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-4 py-2 rounded-lg shadow transition flex items-center space-x-1">
            <span>🖨️ Imprimir Recibo / Guardar PDF</span>
        </button>
    </div>

    <!-- Boleta de Recibo de Pago Oficial -->
    <div class="bg-white border border-slate-300 rounded-xl shadow-lg p-8 w-full max-w-3xl recibo-card space-y-6">
        
        <!-- Membrete Corporativo de la Empresa -->
        <div class="border-b-2 border-slate-800 pb-4 flex justify-between items-start">
            <div>
                <h1 class="text-xl font-black text-slate-900 uppercase tracking-wide">{{ $detalle->nomina->empresa->nombre_comercial ?? 'CORPORACIÓN COMERCIAL' }}</h1>
                <p class="text-xs font-mono text-slate-500 font-bold">RIF: {{ $detalle->nomina->empresa->rif ?? 'J-00000000-0' }} &bull; {{ $detalle->nomina->empresa->razon_social ?? '' }}</p>
                <p class="text-xxs text-slate-400 mt-0.5">{{ $detalle->nomina->empresa->direccion ?? 'San Antonio de los Altos, Miranda' }}</p>
            </div>
            <div class="text-right">
                <span class="inline-block bg-slate-900 text-white text-xs font-black px-3 py-1 rounded uppercase tracking-wider">
                    Recibo de Pago
                </span>
                <p class="text-xs font-mono font-bold text-slate-600 mt-1">Nº {{ $detalle->nomina->codigo }}-{{ str_pad($detalle->id, 4, '0', STR_PAD_LEFT) }}</p>
            </div>
        </div>

        <!-- Ficha del Trabajador y Período -->
        <div class="grid grid-cols-2 gap-4 text-xs bg-slate-50 p-4 rounded-lg border border-slate-200">
            <div>
                <p><span class="font-bold text-slate-600 uppercase">Trabajador:</span> <span class="font-bold text-slate-900 text-sm">{{ $detalle->trabajador->nombre_completo }}</span></p>
                <p><span class="font-bold text-slate-600 uppercase">Cédula:</span> <span class="font-mono font-bold">{{ $detalle->trabajador->cedula }}</span></p>
                <p><span class="font-bold text-slate-600 uppercase">Cargo:</span> {{ $detalle->trabajador->cargo }} ({{ $detalle->trabajador->departamento }})</p>
                <p><span class="font-bold text-slate-600 uppercase">Teléfono:</span> <span class="font-mono">{{ $detalle->trabajador->telefono ?? 'N/A' }}</span></p>
            </div>
            <div class="text-right">
                <p><span class="font-bold text-slate-600 uppercase">Período de Pago:</span> {{ $detalle->nomina->fecha_inicio->format('d/m/Y') }} al {{ $detalle->nomina->fecha_fin->format('d/m/Y') }}</p>
                <p><span class="font-bold text-slate-600 uppercase">Frecuencia:</span> {{ ucfirst($detalle->nomina->tipo) }}</p>
                <p><span class="font-bold text-slate-600 uppercase">Días Laborados:</span> <span class="font-bold">{{ $detalle->dias_trabajados }} días</span></p>
                <p><span class="font-bold text-slate-600 uppercase">Salario Base Mensual:</span> <span class="font-mono font-bold">Bs. {{ number_format($detalle->trabajador->salario_base, 2) }}</span></p>
            </div>
        </div>

        <!-- Tabla de Asignaciones y Deducciones -->
        <div class="border border-slate-200 rounded-lg overflow-hidden">
            <table class="min-w-full text-xs">
                <thead>
                    <tr class="bg-slate-100 text-slate-700 font-bold uppercase border-b border-slate-200">
                        <th class="py-2.5 px-4 text-left w-1/2">Concepto</th>
                        <th class="py-2.5 px-4 text-right">Asignaciones (Bs.)</th>
                        <th class="py-2.5 px-4 text-right">Deducciones (Bs.)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <!-- Asignaciones ordinarias -->
                    <tr>
                        <td class="py-2 px-4 font-medium text-slate-800">Salario Ordinario Devengado ({{ $detalle->dias_trabajados }} días)</td>
                        <td class="py-2 px-4 text-right font-mono font-bold text-slate-900">{{ number_format($detalle->salario_devengado, 2) }}</td>
                        <td class="py-2 px-4 text-right font-mono text-slate-300">--</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4 font-medium text-slate-800">Beneficio de Alimentación (Cestaticket Proporcional)</td>
                        <td class="py-2 px-4 text-right font-mono font-bold text-slate-900">{{ number_format($detalle->cestaticket_devengado, 2) }}</td>
                        <td class="py-2 px-4 text-right font-mono text-slate-300">--</td>
                    </tr>
                    @if($detalle->bono_adicional > 0)
                    <tr>
                        <td class="py-2 px-4 font-medium text-slate-800">Bono de Asistencia Perfecta</td>
                        <td class="py-2 px-4 text-right font-mono font-bold text-slate-900">{{ number_format($detalle->bono_adicional, 2) }}</td>
                        <td class="py-2 px-4 text-right font-mono text-slate-300">--</td>
                    </tr>
                    @endif

                    <!-- Deducciones de Ley LOTTT -->
                    <tr>
                        <td class="py-2 px-4 font-medium text-slate-700">Seguro Social Obligatorio (IVSS 4%)</td>
                        <td class="py-2 px-4 text-right font-mono text-slate-300">--</td>
                        <td class="py-2 px-4 text-right font-mono text-rose-700">{{ number_format($detalle->deduccion_ivss, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4 font-medium text-slate-700">Régimen Prestacional de Empleo (RPE / Paro Forzoso 0.5%)</td>
                        <td class="py-2 px-4 text-right font-mono text-slate-300">--</td>
                        <td class="py-2 px-4 text-right font-mono text-rose-700">{{ number_format($detalle->deduccion_rpe, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4 font-medium text-slate-700">Fondo de Ahorro para la Vivienda (FAOV 1%)</td>
                        <td class="py-2 px-4 text-right font-mono text-slate-300">--</td>
                        <td class="py-2 px-4 text-right font-mono text-rose-700">{{ number_format($detalle->deduccion_faov, 2) }}</td>
                    </tr>

                    <!-- Desglose de Préstamos y Ajustes Extras (Condicionado a Switch del Patrono) -->
                    @if(!empty($detalle->ajustes_detalle_json))
                        @foreach($detalle->ajustes_detalle_json as $aj)
                            @if(!empty($aj['reflejar_en_recibo']))
                            <tr class="bg-indigo-50/50">
                                <td class="py-2 px-4 font-semibold text-indigo-900">
                                    {{ $aj['concepto'] }} <span class="text-xxs text-indigo-600 font-mono">(Cuota {{ $aj['cuota'] }})</span>
                                </td>
                                @if($aj['tipo'] === 'asignacion')
                                    <td class="py-2 px-4 text-right font-mono font-bold text-indigo-900">{{ number_format($aj['monto'], 2) }}</td>
                                    <td class="py-2 px-4 text-right font-mono text-slate-300">--</td>
                                @else
                                    <td class="py-2 px-4 text-right font-mono text-slate-300">--</td>
                                    <td class="py-2 px-4 text-right font-mono text-rose-800 font-bold">{{ number_format($aj['monto'], 2) }}</td>
                                @endif
                            </tr>
                            @endif
                        @endforeach
                    @endif
                </tbody>
                <tfoot class="bg-slate-100 font-bold text-slate-900 border-t border-slate-200">
                    <tr>
                        <td class="py-2.5 px-4 text-left uppercase">Totales Consolidados</td>
                        <td class="py-2.5 px-4 text-right font-mono text-emerald-800 font-extrabold">Bs. {{ number_format($detalle->total_asignaciones, 2) }}</td>
                        <td class="py-2.5 px-4 text-right font-mono text-rose-800 font-extrabold">Bs. {{ number_format($detalle->total_deducciones, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Monto Neto a Cobrar -->
        <div class="bg-slate-900 text-white p-4 rounded-xl flex justify-between items-center">
            <div>
                <p class="text-xxs uppercase tracking-wider text-slate-400 font-bold">Total Líquido Transferible al Trabajador</p>
                <p class="text-xs text-indigo-300">Banco: {{ $detalle->trabajador->cuenta_bancaria ?? 'Transferencia Bancaria Nacional' }}</p>
            </div>
            <div class="text-right">
                <p class="text-2xl font-black font-mono text-emerald-400">Bs. {{ number_format($detalle->neto_a_pagar, 2) }}</p>
            </div>
        </div>

        <!-- Firmas Institucionales -->
        <div class="grid grid-cols-2 gap-12 pt-10 text-center text-xs">
            <div class="border-t border-slate-400 pt-2">
                <p class="font-bold text-slate-900">{{ $detalle->nomina->empresa->nombre_comercial ?? 'LA EMPRESA' }}</p>
                <p class="text-slate-500 text-xxs">Firma y Sello de la Gerencia de RRHH</p>
            </div>
            <div class="border-t border-slate-400 pt-2">
                <p class="font-bold text-slate-900">{{ $detalle->trabajador->nombre_completo }}</p>
                <p class="text-slate-500 text-xxs">C.I: {{ $detalle->trabajador->cedula }} &bull; Recibí Conforme</p>
            </div>
        </div>
    </div>
</body>
</html>
