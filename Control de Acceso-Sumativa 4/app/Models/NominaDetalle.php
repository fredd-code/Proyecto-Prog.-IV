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
        'abonos_extras',
        'total_asignaciones',
        'deduccion_ivss',
        'deduccion_rpe',
        'deduccion_faov',
        'prestamos_deduccion',
        'total_deducciones',
        'neto_a_pagar',
        'ajustes_detalle_json',
    ];

    protected $casts = [
        'salario_devengado'     => 'decimal:2',
        'cestaticket_devengado' => 'decimal:2',
        'bono_adicional'        => 'decimal:2',
        'abonos_extras'         => 'decimal:2',
        'total_asignaciones'    => 'decimal:2',
        'deduccion_ivss'        => 'decimal:2',
        'deduccion_rpe'         => 'decimal:2',
        'deduccion_faov'        => 'decimal:2',
        'prestamos_deduccion'   => 'decimal:2',
        'total_deducciones'     => 'decimal:2',
        'neto_a_pagar'          => 'decimal:2',
        'ajustes_detalle_json'  => 'array',
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
