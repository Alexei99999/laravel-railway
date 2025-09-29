<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class IncidenciasFiscalizacion extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'incidencias_fiscalizacions';

    protected $fillable = [
        'fiscalizacion_id', // Clave foránea
        'cedula',
        'trabajador',
        'ubicacion',
        'contacto',
        'incidencia',
        'fecha_incidencia',
        'hora_incidencia',
    ];

    public function fiscalizacion()
    {
        return $this->belongsTo(Fiscalizacion::class, 'fiscalizacion_id', 'id');
    }
}
