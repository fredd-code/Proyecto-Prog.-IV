<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    protected $table = 'empresas';

    protected $fillable = [
        'rif',
        'nombre_comercial',
        'razon_social',
        'telefono_notificaciones',
        'email_contacto',
        'direccion',
        'latitud_sede',
        'longitud_sede',
        'radio_geocerca_metros',
        'horario_entrada',
        'horario_salida',
        'tolerancia_minutos',
        'limite_trabajadores',
        'limite_sedes',
        'motivo_suspension',
        'estado',
    ];

    protected $casts = [
        'latitud_sede'          => 'float',
        'longitud_sede'         => 'float',
        'radio_geocerca_metros' => 'integer',
        'tolerancia_minutos'    => 'integer',
        'limite_trabajadores'   => 'integer',
        'limite_sedes'          => 'integer',
    ];

    public function usuarios()
    {
        return $this->hasMany(User::class);
    }

    public function usuarioPrincipal()
    {
        return $this->hasOne(User::class)->where('rol', 'admin_empresa');
    }

    public function isActiva(): bool
    {
        return $this->estado === 'activa';
    }

    public function isSuspendida(): bool
    {
        return $this->estado === 'suspendida';
    }

    public function haAlcanzadoLimiteSedes(): bool
    {
        $limite = $this->limite_sedes ?? 3;
        return $this->sedes()->count() >= $limite;
    }

    public function sedes()
    {
        return $this->hasMany(Sede::class);
    }

    public function sedePrincipal()
    {
        return $this->hasOne(Sede::class)->where('es_principal', true);
    }

    public function cupoDisponible(): int
    {
        $limite = $this->limite_trabajadores ?? 15;
        $usados = $this->trabajadores()->count();
        return max(0, $limite - $usados);
    }

    public function haAlcanzadoLimite(): bool
    {
        $limite = $this->limite_trabajadores ?? 15;
        return $this->trabajadores()->count() >= $limite;
    }

    public function trabajadores()
    {
        return $this->hasMany(Trabajador::class);
    }

    public function nominas()
    {
        return $this->hasMany(Nomina::class);
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }

    public function ajustesSalariales()
    {
        return $this->hasMany(AjusteSalarial::class);
    }

    public function notificaciones()
    {
        return $this->hasMany(NotificacionEmpresa::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
