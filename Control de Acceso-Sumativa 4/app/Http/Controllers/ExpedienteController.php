<?php

namespace App\Http\Controllers;

use App\Models\Trabajador;
use App\Models\Expediente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Controlador de Expediente Digital
 * Gestiona el almacenamiento seguro y la descarga de documentos probatorios de los empleados.
 */
class ExpedienteController extends Controller
{
    /**
     * Sube un documento al almacenamiento local y crea el registro en la base de datos.
     */
    public function store(Request $request, Trabajador $trabajador)
    {
        $request->validate([
            'titulo'         => 'required|string|max:150',
            'tipo_documento' => 'required|in:cedula,rif,contrato,constancia_medica,sintesis_curricular,otro',
            'archivo'        => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // Máx 5MB
        ]);

        $file = $request->file('archivo');
        $path = $file->store("expedientes/{$trabajador->id}", 'public');

        Expediente::create([
            'trabajador_id'  => $trabajador->id,
            'titulo'         => $request->titulo,
            'tipo_documento' => $request->tipo_documento,
            'ruta_archivo'   => $path,
            'peso_kb'        => round($file->getSize() / 1024),
        ]);

        return redirect()->route('trabajadores.show', $trabajador->id)
            ->with('success', 'Documento anexado correctamente al expediente digital.');
    }

    /**
     * Elimina un archivo del almacenamiento y de la base de datos.
     */
    public function destroy(Expediente $expediente)
    {
        $trabajadorId = $expediente->trabajador_id;
        
        if (Storage::disk('public')->exists($expediente->ruta_archivo)) {
            Storage::disk('public')->delete($expediente->ruta_archivo);
        }

        $expediente->delete();

        return redirect()->route('trabajadores.show', $trabajadorId)
            ->with('success', 'Documento eliminado del expediente.');
    }
}
