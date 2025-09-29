<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class IncidenciasFeria extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'incidencias_ferias';

    protected $fillable = [
        'cedula',
        'trabajador',
        'ubicacion',
        'incidencia',
        'fecha_incidencia',
        'hora_incidencia',
    ];

    public function feria()
    {
        return $this->belongsTo(Feria::class, 'cedula', 'cedula');
    }
}
