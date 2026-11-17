<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificacionEmpresa extends Model
{
    use HasFactory;

    protected $table = 'notificaciones_empresa';

    protected $fillable = [
        'empresa_id',
        'trabajador_id',
        'tipo_evento',
        'telefono_destino',
        'mensaje',
        'canal',
        'estado',
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
