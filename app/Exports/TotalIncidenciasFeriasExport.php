<?php

namespace App\Exports;

use App\Models\IncidenciasFeria;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class TotalIncidenciasFeriasExport implements FromCollection, WithHeadings, WithCustomCsvSettings
{
    public function collection()
    {
        return IncidenciasFeria::select(
            'cedula',
            'trabajador',
            'ubicacion',
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
            'Incidencia',
            'Fecha Incidencia',
            'Hora Incidencia',
        ];
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';',      // Para que Excel interprete bien el CSV en configuraciones regionales
            'enclosure' => '"',      // Encerrar texto para respetar espacios y comas
            'line_ending' => "\r\n", // Para compatibilidad en Windows
            'use_bom' => true,       // Evitar problemas con caracteres UTF-8 (acentos, ñ)
        ];
    }
}
