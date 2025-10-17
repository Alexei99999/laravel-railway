<?php

namespace App\Exports;

use App\Models\IncidenciasFiscalizacion; // <-- con S
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class IncidenciasFiscalizacionesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return IncidenciasFiscalizacion::select(
            'cedula',
            'trabajador',
            'ubicacion',
            'contacto',
            'incidencia',
            'fecha_incidencia',
            'hora_incidencia'
        )->get();
    }

    public function headings(): array
    {
        return [
            'Cédula',
            'Trabajador',
            'Ubicación',
            'Contacto',
            'Incidencia',
            'Fecha de Incidencia',
            'Hora de Incidencia',
        ];
    }
}
