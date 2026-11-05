<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trabajador extends Model
{
    use HasFactory;

    protected $table = 'trabajadores';

    protected $fillable = [
        'cedula',
        'nombres',
        'apellidos',
        'telefono',
        'correo',
        'cargo',
        'departamento',
        'fecha_ingreso',
        'frecuencia_pago',
        'salario_base',
        'bono_alimentacion',
        'bono_asistencia',
        'cuenta_bancaria',
        'estado',
    ];

    /**
     * Retorna el nombre completo formateado del empleado.
     */
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombres} {$this->apellidos}";
    }

    /**
     * Relación: Un trabajador posee muchos documentos en su expediente digital.
     */
    public function expedientes()
    {
        return $this->hasMany(Expediente::class);
    }

    /**
     * Relación: Un trabajador tiene múltiples marcaciones de asistencia.
     */
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }

    /**
     * Relación: Histórico de detalles de nómina cobrados.
     */
    public function nominaDetalles()
    {
        return $this->hasMany(NominaDetalle::class);
    }
}
