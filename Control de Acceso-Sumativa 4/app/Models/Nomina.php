<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nomina extends Model
{
    use HasFactory;

    protected $table = 'nominas';

    protected $fillable = [
        'empresa_id',
        'codigo',
        'tipo',
        'fecha_inicio',
        'fecha_fin',
        'total_asignaciones',
        'total_deducciones',
        'total_prestamos_deducciones',
        'total_abonos_asignaciones',
        'total_neto',
        'estado',
    ];

    protected $casts = [
        'total_asignaciones'          => 'decimal:2',
        'total_deducciones'           => 'decimal:2',
        'total_prestamos_deducciones' => 'decimal:2',
        'total_abonos_asignaciones'   => 'decimal:2',
        'total_neto'                  => 'decimal:2',
        'fecha_inicio'                => 'date',
        'fecha_fin'                   => 'date',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function detalles()
    {
        return $this->hasMany(NominaDetalle::class);
    }
}
