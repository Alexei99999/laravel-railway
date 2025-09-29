<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Feria extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'cod_edo', 'estado', 'cod_mun', 'municipio', 'cod_parroquia', 'parroquia', 'cod_centro',
        'nombre_pto', 'direccion_pto', 'rectoria', 'cedula', 'apellidos', 'nombres', 'telefono',
        'correo', 'rol', 'status_contact1', 'fecha_hora1', 'status_contact2', 'fecha_hora2',
        'status_contact3', 'fecha_hora3', 'disponibilidad', 'incidencias', 'fecha_incidencia',
        'hora_incidencia', 'observaciones', 'e_registro'
    ];

    public function incidencias()
    {
        return $this->hasMany(IncidenciasFeria::class, 'feria_id');
    }
}
