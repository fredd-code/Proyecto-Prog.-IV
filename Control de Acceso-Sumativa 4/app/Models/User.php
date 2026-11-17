<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'empresa_id',
        'rol',
        'cargo_interno',
        'puede_gestionar_prestamos',
        'puede_aprobar_retiros',
        'puede_ver_expedientes',
        'puede_modificar_trabajadores',
        'puede_procesar_nominas',
        'puede_ver_asistencias',
        'estado_usuario',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'            => 'datetime',
            'password'                     => 'hashed',
            'puede_gestionar_prestamos'    => 'boolean',
            'puede_aprobar_retiros'        => 'boolean',
            'puede_ver_expedientes'        => 'boolean',
            'puede_modificar_trabajadores' => 'boolean',
            'puede_procesar_nominas'       => 'boolean',
            'puede_ver_asistencias'        => 'boolean',
        ];
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->rol === 'superadmin';
    }

    public function isAdminEmpresa(): bool
    {
        return $this->rol === 'admin_empresa';
    }

    public function tienePermiso(string $permiso): bool
    {
        // El SuperAdmin y el Administrador principal de la empresa tienen todos los permisos
        if ($this->isSuperAdmin() || $this->isAdminEmpresa()) {
            return true;
        }

        if (isset($this->{$permiso})) {
            return (bool) $this->{$permiso};
        }

        $campoConPrefijo = 'puede_' . $permiso;
        return (bool) ($this->{$campoConPrefijo} ?? false);
    }

}
