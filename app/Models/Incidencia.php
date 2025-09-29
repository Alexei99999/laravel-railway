<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incidencia extends Model
{
    use HasFactory;
    protected $table = 'incidencias';
    protected $fillable = ['trabajador', 'ubicacion', 'e_contact', 'fecha_rep', 'e_disponib', 'fecha_e_disponib'];
}
