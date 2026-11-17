<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
    use HasFactory;

    protected $table = 'sedes';

    protected $fillable = [
        'empresa_id',
        'nombre',
        'codigo',
        'telefono_contacto',
        'direccion',
        'latitud',
        'longitud',
        'radio_geocerca_metros',
        'es_principal',
        'estado',
    ];

    protected $casts = [
        'latitud'               => 'float',
        'longitud'              => 'float',
        'radio_geocerca_metros' => 'integer',
        'es_principal'          => 'boolean',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function trabajadores()
    {
        return $this->belongsToMany(Trabajador::class, 'sede_trabajador');
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }
}
