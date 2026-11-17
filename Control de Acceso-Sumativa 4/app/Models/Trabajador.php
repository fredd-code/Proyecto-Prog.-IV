<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trabajador extends Model
{
    use HasFactory;

    protected $table = 'trabajadores';

    protected $fillable = [
        'empresa_id',
        'sede_id',
        'cedula',
        'nombres',
        'apellidos',
        'telefono',
        'correo',
        'pin_acceso',
        'cargo',
        'departamento',
        'fecha_ingreso',
        'frecuencia_pago',
        'salario_base',
        'bono_alimentacion',
        'bono_asistencia',
        'cuenta_bancaria',
        'hora_entrada',
        'hora_salida',
        'aplica_almuerzo',
        'inicio_almuerzo',
        'fin_almuerzo',
        'tolerancia_minutos',
        'horario_entrada_custom',
        'horario_salida_custom',
        'estado',
    ];

    protected $casts = [
        'salario_base'       => 'decimal:2',
        'bono_alimentacion'  => 'decimal:2',
        'bono_asistencia'    => 'decimal:2',
        'fecha_ingreso'      => 'date',
        'aplica_almuerzo'    => 'boolean',
        'tolerancia_minutos' => 'integer',
    ];

    public function getNombreCompletoAttribute()
    {
        return "{$this->nombres} {$this->apellidos}";
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    public function sedes()
    {
        return $this->belongsToMany(Sede::class, 'sede_trabajador');
    }

    public function expedientes()
    {
        return $this->hasMany(Expediente::class);
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }

    public function nominaDetalles()
    {
        return $this->hasMany(NominaDetalle::class);
    }

    public function ajustesSalariales()
    {
        return $this->hasMany(AjusteSalarial::class);
    }

    public function notificaciones()
    {
        return $this->hasMany(NotificacionEmpresa::class);
    }
}
