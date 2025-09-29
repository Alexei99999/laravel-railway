<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ubicacion extends Model
{
    use HasFactory;

    protected $table = 'ubicacions';

    protected $fillable = [
        'estado',
        'cod_est',
        'municipio',
        'cod_mun',
        'parroquia',
        'cod_parroq',
        'circuns',
        'e_registro'
    ];
}
