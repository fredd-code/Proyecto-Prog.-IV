<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NominaDetalle extends Model
{
    use HasFactory;

    protected $table = 'nomina_detalles';

    protected $fillable = [
        'nomina_id',
        'trabajador_id',
        'dias_trabajados',
        'salario_devengado',
        'cestaticket_devengado',
        'bono_adicional',
        'total_asignaciones',
        'deduccion_ivss',
        'deduccion_rpe',
        'deduccion_faov',
        'total_deducciones',
        'neto_a_pagar',
    ];

    public function nomina()
    {
        return $this->belongsTo(Nomina::class);
    }

    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class);
    }
}
