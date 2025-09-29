<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Fiscalizacion extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = 'fiscalizacions';

    protected $fillable = [
    'cod_edo', 'estado', 'cod_mun', 'municipio', 'cod_parroquia', 'parroquia',
    'cod_centro', 'nombre_pto', 'direccion_pto', 'circuns', 'rectoria', 'cedula',
    'apellidos', 'nombres', 'telefono', 'correo', 'rol', 'status_contact',
    'disponibilidad', 'fecha', 'hora', 'fecha_rep', 'incidencias', 'fecha_incidencia',
    'hora_incidencia', 'observaciones', 'e_registro'
];

    public function incidencias()
{
    return $this->hasMany(IncidenciasFiscalizacion::class, 'fiscalizacion_id');
}
}
