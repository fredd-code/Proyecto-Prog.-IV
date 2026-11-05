<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nomina extends Model
{
    use HasFactory;

    protected $table = 'nominas';

    protected $fillable = [
        'codigo',
        'tipo',
        'fecha_inicio',
        'fecha_fin',
        'total_asignaciones',
        'total_deducciones',
        'total_neto',
        'estado',
    ];

    public function detalles()
    {
        return $this->hasMany(NominaDetalle::class);
    }
}
