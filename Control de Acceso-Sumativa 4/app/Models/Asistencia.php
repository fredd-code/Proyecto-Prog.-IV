<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    use HasFactory;

    protected $table = 'asistencias';

    protected $fillable = [
        'empresa_id',
        'sede_id',
        'trabajador_id',
        'fecha',
        'hora_entrada',
        'hora_salida',
        'hora_salida_almuerzo',
        'hora_entrada_almuerzo',
        'estado',
        'tipo_marcaje',
        'latitud',
        'longitud',
        'distancia_sede_metros',
        'dentro_de_geocerca',
        'es_retardo',
        'motivo_retiro',
        'metodo_marcaje',
        'dispositivo_info',
        'ip_origen',
        'observacion',
    ];

    protected $casts = [
        'fecha'                 => 'date',
        'dentro_de_geocerca'    => 'boolean',
        'es_retardo'            => 'boolean',
        'distancia_sede_metros' => 'float',
        'latitud'               => 'float',
        'longitud'              => 'float',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class);
    }
}
