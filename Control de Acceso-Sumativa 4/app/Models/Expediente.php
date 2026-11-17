<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expediente extends Model
{
    use HasFactory;

    protected $table = 'expedientes';

    protected $fillable = [
        'trabajador_id',
        'titulo',
        'tipo_documento',
        'ruta_archivo',
        'peso_kb',
    ];

    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class);
    }
}
