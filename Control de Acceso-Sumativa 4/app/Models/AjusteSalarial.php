<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AjusteSalarial extends Model
{
    use HasFactory;

    protected $table = 'ajustes_salariales';

    protected $fillable = [
        'empresa_id',
        'trabajador_id',
        'tipo',
        'concepto',
        'monto_total',
        'monto_cuota',
        'cuotas_totales',
        'cuotas_cobradas',
        'reflejar_en_recibo',
        'fecha_inicio',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'reflejar_en_recibo' => 'boolean',
        'monto_total'        => 'decimal:2',
        'monto_cuota'        => 'decimal:2',
        'cuotas_totales'     => 'integer',
        'cuotas_cobradas'    => 'integer',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class);
    }
}
