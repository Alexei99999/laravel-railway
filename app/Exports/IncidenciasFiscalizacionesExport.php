<?php

namespace App\Exports;

use App\Models\IncidenciasFiscalizacion;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class IncidenciasFiscalizacionesExport implements FromCollection, WithHeadings, WithCustomCsvSettings
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

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';',      // Mejor lectura en Excel local con configuración regional en español
            'enclosure' => '"',      // Encierra textos para evitar separación incorrecta
            'line_ending' => "\r\n", // Compatibilidad con Windows
            'use_bom' => true,       // Soporte para caracteres UTF-8 (acentos, ñ)
        ];
    }
}
