<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    use HasFactory;

    protected $table = 'asistencias';

    protected $fillable = [
        'trabajador_id',
        'fecha',
        'hora_entrada',
        'hora_salida',
        'estado',
        'latitud',
        'longitud',
        'observacion',
    ];

    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class);
    }
}
