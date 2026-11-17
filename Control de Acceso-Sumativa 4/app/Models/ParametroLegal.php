<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParametroLegal extends Model
{
    use HasFactory;

    protected $table = 'parametros_legales';

    protected $fillable = [
        'codigo',
        'nombre',
        'categoria',
        'valor',
        'unidad',
        'gaceta_oficial',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}
