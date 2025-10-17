<?php

namespace App\Exports;

use App\Models\IncidenciasFeria;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TotalIncidenciasFeriasExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return IncidenciasFeria::select('cedula', 'trabajador', 'ubicacion', 'incidencia', 'fecha_incidencia', 'hora_incidencia')->get();
    }

    public function headings(): array
    {
        return [
            'Cédula',
            'Trabajador',
            'Ubicación',
            'Incidencia',
            'Fecha Incidencia',
            'Hora Incidencia',
        ];
    }
}
