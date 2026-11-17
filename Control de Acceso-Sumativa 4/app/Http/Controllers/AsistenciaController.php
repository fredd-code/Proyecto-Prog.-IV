<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Trabajador;
use App\Models\Empresa;
use App\Models\Sede;
use App\Models\NotificacionEmpresa;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Controlador de Marcaje Anti-Fraude, Geofencing Multi-Sede,
 * Control de Horario Individual y Alternativa Kiosco.
 */
class AsistenciaController extends Controller
{
    /**
     * Panel general de auditoría de asistencias.
     */
    public function index()
    {
        $user = auth()->user();
        if (!$user->tienePermiso('ver_asistencias')) {
            abort(403, 'No tiene permisos para auditar asistencias.');
        }

        $empresaId = $user->isSuperAdmin() ? session('empresa_activa_id') : $user->empresa_id;

        $query = Asistencia::with(['trabajador', 'sede', 'empresa'])
            ->orderBy('fecha', 'desc')
            ->orderBy('created_at', 'desc');

        $notifQuery = NotificacionEmpresa::with('trabajador')->latest();

        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
            $notifQuery->where('empresa_id', $empresaId);
        }

        $asistencias = $query->take(50)->get();
        $notificaciones = $notifQuery->take(10)->get();

        return view('asistencias.index', compact('asistencias', 'notificaciones'));
    }

    /**
     * Marcaje Inteligente Anti-Fraude (Móvil GPS o Web personal).
     * Sin selector desplegable: Detecta automáticamente Entrada, Salida Almuerzo,
     * Regreso Almuerzo o Salida Fin de Jornada según el horario del empleado.
     */
    public function registrar(Request $request)
    {
        $request->validate([
            'cedula'     => 'required|string',
            'pin_acceso' => 'required|string',
            'latitud'    => 'nullable|numeric',
            'longitud'   => 'nullable|numeric',
        ]);

        $trabajador = Trabajador::with(['empresa', 'sede'])
            ->where('cedula', trim($request->cedula))
            ->where('estado', 'activo')
            ->first();

        if (!$trabajador) {
            return redirect()->back()->with('error', 'Cédula no encontrada en el registro de trabajadores activos.');
        }

        // Validación de PIN de Seguridad Anti-Fraude
        if ($trabajador->pin_acceso && $trabajador->pin_acceso !== trim($request->pin_acceso)) {
            return redirect()->back()->with('error', 'PIN de seguridad incorrecto. Verifique sus credenciales.');
        }

        $hoy = Carbon::today()->toDateString();
        $horaActual = Carbon::now();
        $empresa = $trabajador->empresa ?? Empresa::first();

        // 1. Ubicación y Geocerca contra las Sedes Asignadas al Trabajador (Una o Varias)
        $sedesAutorizadas = $trabajador->sedes;
        if ($sedesAutorizadas->isEmpty()) {
            if ($trabajador->sede) {
                $sedesAutorizadas = collect([$trabajador->sede]);
            } else {
                $sedesAutorizadas = $empresa->sedes;
            }
        }

        $distanciaMetros = null;
        $sedeMasCercana = null;
        $sedeValida = null;
        $dentroGeocerca = true;

        if ($request->filled('latitud') && $request->filled('longitud')) {
            $dentroGeocerca = false;

            foreach ($sedesAutorizadas as $s) {
                $dist = $this->calcularDistanciaMetros(
                    $request->latitud,
                    $request->longitud,
                    $s->latitud,
                    $s->longitud
                );

                if (is_null($distanciaMetros) || $dist < $distanciaMetros) {
                    $distanciaMetros = $dist;
                    $sedeMasCercana = $s;
                }

                // Si está dentro de la geocerca de esta sede autorizada
                if ($dist <= $s->radio_geocerca_metros) {
                    $dentroGeocerca = true;
                    $sedeValida = $s;
                    $distanciaMetros = $dist;
                    break;
                }
            }

            $sede = $sedeValida ?? $sedeMasCercana ?? $empresa->sedePrincipal ?? $empresa->sedes()->first();

            if (!$dentroGeocerca) {
                $nombreSedeCercana = $sedeMasCercana ? $sedeMasCercana->nombre : 'Sede Principal';
                $radioPermitido = $sedeMasCercana ? $sedeMasCercana->radio_geocerca_metros : 200;

                // Despacho de alerta anti-fraude inmediata
                $this->enviarNotificacionEmpresa(
                    $empresa,
                    $trabajador,
                    'fuera_geocerca',
                    "ALERTA ANTI-FRAUDE: El empleado {$trabajador->nombre_completo} (C.I: {$trabajador->cedula}) marcó fuera de sus sedes autorizadas. Distancia a la más próxima '{$nombreSedeCercana}': {$distanciaMetros}m (Radio autorizado: {$radioPermitido}m)."
                );
            }
        } else {
            $sede = $sedesAutorizadas->first() ?? $empresa->sedePrincipal ?? $empresa->sedes()->first();
        }

        $nombreSede = $sede ? $sede->nombre : 'Sede Principal';

        // 2. Parámetros de Horario Individual del Trabajador
        $horaEntradaPautada = Carbon::parse($trabajador->hora_entrada ?? '08:00:00');
        $horaSalidaPautada  = Carbon::parse($trabajador->hora_salida ?? '17:00:00');
        $aplicaAlmuerzo     = (bool) ($trabajador->aplica_almuerzo ?? true);
        $toleranciaMin      = (int) ($trabajador->tolerancia_minutos ?? 15);

        // Control de Ventana de Horario (Permite marcar desde 60 min antes de entrada hasta 120 min post salida)
        $ventanaInicio = $horaEntradaPautada->copy()->subMinutes(60);
        $ventanaFin    = $horaSalidaPautada->copy()->addMinutes(120);

        if ($horaActual->format('H:i:s') < $ventanaInicio->format('H:i:s') || $horaActual->format('H:i:s') > $ventanaFin->format('H:i:s')) {
            return redirect()->back()->with('error', "Marcaje no permitido fuera de horario. Su jornada asignada es de " . $horaEntradaPautada->format('h:i A') . " a " . $horaSalidaPautada->format('h:i A') . ".");
        }

        // 3. Máquina de Estados de Marcaje Automático (Sin selector desplegable)
        $asistencia = Asistencia::where('trabajador_id', $trabajador->id)
            ->where('fecha', $hoy)
            ->first();

        // CASO 1: PRIMER MARCAJE DEL DÍA -> ENTRADA
        if (!$asistencia) {
            $horaLimiteTolerancia = $horaEntradaPautada->copy()->addMinutes($toleranciaMin);
            $esRetardo = $horaActual->greaterThan($horaLimiteTolerancia);

            Asistencia::create([
                'empresa_id'            => $empresa->id,
                'sede_id'               => $sede ? $sede->id : null,
                'trabajador_id'         => $trabajador->id,
                'fecha'                 => $hoy,
                'hora_entrada'          => $horaActual->toTimeString(),
                'estado'                => 'presente',
                'tipo_marcaje'          => 'entrada',
                'latitud'               => $request->latitud,
                'longitud'              => $request->longitud,
                'distancia_sede_metros' => $distanciaMetros,
                'dentro_de_geocerca'    => $dentroGeocerca,
                'es_retardo'            => $esRetardo,
                'metodo_marcaje'        => 'movil_gps',
                'dispositivo_info'      => $request->header('User-Agent'),
                'ip_origen'             => $request->ip(),
                'observacion'           => $esRetardo 
                    ? "Entrada con retardo de " . $horaActual->diffInMinutes($horaEntradaPautada) . " min (Sede: {$nombreSede})." 
                    : "Entrada puntual en horario asignado (Sede: {$nombreSede}).",
            ]);

            if ($esRetardo) {
                $this->enviarNotificacionEmpresa(
                    $empresa,
                    $trabajador,
                    'retardo',
                    "LLEGADA TARDÍA: {$trabajador->nombre_completo} marcó entrada a las {$horaActual->format('h:i A')} (Horario pautado: {$horaEntradaPautada->format('h:i A')}, tolerancia: {$toleranciaMin} min)."
                );
            }

            $mensaje = "¡Bienvenido {$trabajador->nombres}! ENTRADA registrada a las " . $horaActual->format('h:i A') . " en {$nombreSede}" . ($esRetardo ? " (CON RETARDO)." : ".");
            return redirect()->back()->with('success', $mensaje);
        }

        // Si ya completó la salida de jornada o retiro
        if (!is_null($asistencia->hora_salida)) {
            return redirect()->back()->with('error', "El trabajador {$trabajador->nombre_completo} ya completó todos los marcajes correspondientes al día de hoy.");
        }

        // CASO 2: CON DESCANSO LEGAL / ALMUERZO
        if ($aplicaAlmuerzo) {
            // A) Salida a Almuerzo / Descanso
            if (is_null($asistencia->hora_salida_almuerzo)) {
                $minutosDesdeEntrada = Carbon::parse($asistencia->hora_entrada)->diffInMinutes($horaActual);
                if ($minutosDesdeEntrada < 15) {
                    return redirect()->back()->with('error', "Marcaje de Entrada registrado hace solo {$minutosDesdeEntrada} minutos. Espere a su receso de almuerzo para marcar salida a descanso.");
                }

                $asistencia->update([
                    'hora_salida_almuerzo'  => $horaActual->toTimeString(),
                    'tipo_marcaje'          => 'salida_almuerzo',
                    'distancia_sede_metros' => $distanciaMetros ?? $asistencia->distancia_sede_metros,
                    'dentro_de_geocerca'    => $dentroGeocerca,
                    'observacion'           => $asistencia->observacion . " | Salida a Almuerzo: " . $horaActual->format('h:i A'),
                ]);

                return redirect()->back()->with('success', "¡Buen provecho, {$trabajador->nombres}! SALIDA A ALMUERZO / DESCANSO registrada a las " . $horaActual->format('h:i A') . ".");
            }

            // B) Regreso de Almuerzo / Reincorporación
            if (is_null($asistencia->hora_entrada_almuerzo)) {
                $minutosAlmuerzo = Carbon::parse($asistencia->hora_salida_almuerzo)->diffInMinutes($horaActual);
                if ($minutosAlmuerzo < 5) {
                    return redirect()->back()->with('error', "Marcó salida a almuerzo hace apenas {$minutosAlmuerzo} minutos. Debe cumplir su período de descanso antes de marcar el regreso.");
                }

                $asistencia->update([
                    'hora_entrada_almuerzo' => $horaActual->toTimeString(),
                    'tipo_marcaje'          => 'regreso_almuerzo',
                    'distancia_sede_metros' => $distanciaMetros ?? $asistencia->distancia_sede_metros,
                    'dentro_de_geocerca'    => $dentroGeocerca,
                    'observacion'           => $asistencia->observacion . " | Regreso de Almuerzo: " . $horaActual->format('h:i A'),
                ]);

                return redirect()->back()->with('success', "¡Bienvenido de vuelta, {$trabajador->nombres}! REGRESO DE ALMUERZO registrado a las " . $horaActual->format('h:i A') . ".");
            }
        }

        // CASO 3: SALIDA FINAL DE LA JORNADA
        $asistencia->update([
            'hora_salida'           => $horaActual->toTimeString(),
            'tipo_marcaje'          => 'salida',
            'distancia_sede_metros' => $distanciaMetros ?? $asistencia->distancia_sede_metros,
            'dentro_de_geocerca'    => $dentroGeocerca,
            'observacion'           => $asistencia->observacion . " | Salida final de jornada a las " . $horaActual->format('h:i A') . ".",
        ]);

        return redirect()->back()->with('success', "¡Hasta luego {$trabajador->nombres}! SALIDA FINAL registrada a las " . $horaActual->format('h:i A') . ". Feliz descanso.");
    }

    /**
     * Botón de Retiro por Imprevisto, Permiso o Ausencia Justificada.
     */
    public function retiroImprevisto(Request $request)
    {
        $request->validate([
            'cedula'        => 'required|string',
            'pin_acceso'    => 'required|string',
            'motivo_retiro' => 'required|string|max:150',
            'observacion'   => 'nullable|string|max:255',
        ]);

        $trabajador = Trabajador::where('cedula', trim($request->cedula))->where('estado', 'activo')->first();

        if (!$trabajador || $trabajador->pin_acceso !== trim($request->pin_acceso)) {
            return redirect()->back()->with('error', 'Credenciales inválidas (Cédula o PIN erróneo).');
        }

        $hoy = Carbon::today()->toDateString();
        $horaActual = Carbon::now();
        $empresa = Empresa::find($trabajador->empresa_id) ?? Empresa::first();

        $asistencia = Asistencia::where('trabajador_id', $trabajador->id)->where('fecha', $hoy)->first();

        if (!$asistencia) {
            $asistencia = Asistencia::create([
                'empresa_id'     => $empresa->id,
                'sede_id'        => $trabajador->sede_id,
                'trabajador_id'  => $trabajador->id,
                'fecha'          => $hoy,
                'hora_salida'    => $horaActual->toTimeString(),
                'estado'         => 'falta_justificada',
                'tipo_marcaje'   => 'retiro_imprevisto',
                'motivo_retiro'  => $request->motivo_retiro,
                'metodo_marcaje' => 'movil_gps',
                'observacion'    => "Ausencia/Retiro justificado: " . $request->observacion,
            ]);
        } else {
            $asistencia->update([
                'hora_salida'   => $horaActual->toTimeString(),
                'tipo_marcaje'  => 'retiro_imprevisto',
                'estado'        => 'falta_justificada',
                'motivo_retiro' => $request->motivo_retiro,
                'observacion'   => $asistencia->observacion . " | Retiro anticipado por: " . $request->motivo_retiro . " ({$request->observacion})",
            ]);
        }

        // Notificación inmediata al teléfono de la empresa
        $this->enviarNotificacionEmpresa(
            $empresa,
            $trabajador,
            'retiro_imprevisto',
            "RETIRO JUSTIFICADO: {$trabajador->nombre_completo} se retiró a las {$horaActual->format('h:i A')} por: {$request->motivo_retiro}. Observación: {$request->observacion}"
        );

        return redirect()->back()->with('success', "Retiro por imprevisto registrado exitosamente. Se notificó al teléfono de la empresa.");
    }

    /**
     * Vista de Terminal Kiosco de Recepción.
     */
    public function kiosco()
    {
        $empresa = Empresa::with('sedes')->first();
        return view('asistencias.kiosco', compact('empresa'));
    }

    /**
     * Marcaje presencial en Kiosco (Detección automática sin teléfono móvil).
     */
    public function registrarKiosco(Request $request)
    {
        $request->validate([
            'cedula'     => 'required|string',
            'pin_acceso' => 'required|string',
        ]);

        $trabajador = Trabajador::with(['empresa', 'sede'])
            ->where('cedula', trim($request->cedula))
            ->where('estado', 'activo')
            ->first();

        if (!$trabajador || $trabajador->pin_acceso !== trim($request->pin_acceso)) {
            return redirect()->back()->with('error', 'Cédula o PIN incorrecto en terminal Kiosco.');
        }

        $hoy = Carbon::today()->toDateString();
        $horaActual = Carbon::now();
        $empresa = $trabajador->empresa ?? Empresa::first();
        $sede = $trabajador->sede ?? $empresa->sedes()->first();
        $nombreSede = $sede ? $sede->nombre : 'Recepción Central';

        $asistencia = Asistencia::where('trabajador_id', $trabajador->id)->where('fecha', $hoy)->first();

        $horaEntradaPautada = Carbon::parse($trabajador->hora_entrada ?? '08:00:00');
        $toleranciaMin      = (int) ($trabajador->tolerancia_minutos ?? 15);
        $aplicaAlmuerzo     = (bool) ($trabajador->aplica_almuerzo ?? true);

        // 1. ENTRADA KIOSCO
        if (!$asistencia) {
            $esRetardo = $horaActual->greaterThan($horaEntradaPautada->copy()->addMinutes($toleranciaMin));

            Asistencia::create([
                'empresa_id'         => $empresa->id,
                'sede_id'            => $sede ? $sede->id : null,
                'trabajador_id'      => $trabajador->id,
                'fecha'              => $hoy,
                'hora_entrada'       => $horaActual->toTimeString(),
                'estado'             => 'presente',
                'tipo_marcaje'       => 'entrada',
                'dentro_de_geocerca' => true,
                'es_retardo'         => $esRetardo,
                'metodo_marcaje'     => 'kiosco_sede',
                'dispositivo_info'   => 'Terminal Kiosco ' . $nombreSede,
                'observacion'        => 'Marcaje presencial por Kiosco en sede ' . $nombreSede,
            ]);

            if ($esRetardo) {
                $this->enviarNotificacionEmpresa(
                    $empresa,
                    $trabajador,
                    'retardo',
                    "LLEGADA TARDÍA (KIOSCO): {$trabajador->nombre_completo} marcó a las {$horaActual->format('h:i A')} en terminal presencial de {$nombreSede}."
                );
            }

            return redirect()->back()->with('success', "ENTRADA registrada en Kiosco: {$trabajador->nombre_completo} a las " . $horaActual->format('h:i A') . ($esRetardo ? " (Con Retardo)." : "."));
        }

        if (!is_null($asistencia->hora_salida)) {
            return redirect()->back()->with('error', "{$trabajador->nombre_completo} ya completó todos sus marcajes de hoy.");
        }

        // 2. ALMUERZO KIOSCO
        if ($aplicaAlmuerzo) {
            if (is_null($asistencia->hora_salida_almuerzo)) {
                $asistencia->update([
                    'hora_salida_almuerzo' => $horaActual->toTimeString(),
                    'tipo_marcaje'         => 'salida_almuerzo',
                    'metodo_marcaje'       => 'kiosco_sede',
                    'observacion'          => $asistencia->observacion . " | Salida a almuerzo en Kiosco a las " . $horaActual->format('h:i A'),
                ]);
                return redirect()->back()->with('success', "SALIDA A ALMUERZO registrada en Kiosco: {$trabajador->nombre_completo} a las " . $horaActual->format('h:i A'));
            }

            if (is_null($asistencia->hora_entrada_almuerzo)) {
                $asistencia->update([
                    'hora_entrada_almuerzo' => $horaActual->toTimeString(),
                    'tipo_marcaje'          => 'regreso_almuerzo',
                    'metodo_marcaje'        => 'kiosco_sede',
                    'observacion'           => $asistencia->observacion . " | Regreso de almuerzo en Kiosco a las " . $horaActual->format('h:i A'),
                ]);
                return redirect()->back()->with('success', "REGRESO DE ALMUERZO registrado en Kiosco: {$trabajador->nombre_completo} a las " . $horaActual->format('h:i A'));
            }
        }

        // 3. SALIDA FINAL KIOSCO
        $asistencia->update([
            'hora_salida'    => $horaActual->toTimeString(),
            'tipo_marcaje'   => 'salida',
            'metodo_marcaje' => 'kiosco_sede',
            'observacion'    => $asistencia->observacion . " | Salida final en Kiosco a las " . $horaActual->format('h:i A'),
        ]);

        return redirect()->back()->with('success', "SALIDA FINAL registrada en Kiosco: {$trabajador->nombre_completo} a las " . $horaActual->format('h:i A'));
    }

    /**
     * Cálculo de distancia geodésica mediante fórmula de Haversine (en metros).
     */
    private function calcularDistanciaMetros($lat1, $lon1, $lat2, $lon2)
    {
        $radioTierra = 6371000; // metros
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return round($radioTierra * $c, 1);
    }

    /**
     * Despacho y registro de notificaciones al teléfono registrado de la empresa.
     */
    private function enviarNotificacionEmpresa($empresa, $trabajador, $tipo, $mensaje)
    {
        NotificacionEmpresa::create([
            'empresa_id'       => $empresa->id,
            'trabajador_id'    => $trabajador->id,
            'tipo_evento'      => $tipo,
            'telefono_destino' => $empresa->telefono_notificaciones,
            'mensaje'          => $mensaje,
            'canal'            => 'whatsapp',
            'estado'           => 'enviado',
        ]);
    }
}
