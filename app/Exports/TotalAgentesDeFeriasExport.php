<?php

namespace App\Exports;

use App\Models\Feria;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class TotalAgentesDeFeriasExport implements FromCollection, WithHeadings, WithCustomCsvSettings
{
    public function collection()
    {
        return Feria::select(
            'estado',
            'municipio',
            'parroquia',
            'nombre_pto',
            'cedula',
            'apellidos',
            'nombres',
            'telefono',
            'status_contact1',
            'status_contact2',
            'status_contact3',
            'disponibilidad',
            'incidencias',
            'fecha_incidencia',
            'hora_incidencia',
            'cod_edo',
            'cod_mun',
            'cod_parroquia',
            'cod_centro',
            'correo'
        )->get();
    }

    public function headings(): array
    {
        return [
            'Estado',
            'Municipio',
            'Parroquia',
            'Nombre Punto',
            'Cédula',
            'Apellidos',
            'Nombres',
            'Teléfono',
            'Status Contacto 1',
            'Status Contacto 2',
            'Status Contacto 3',
            'Disponibilidad',
            'Incidencias',
            'Fecha Incidencia',
            'Hora Incidencia',
            'Código Estado',
            'Código Municipio',
            'Código Parroquia',
            'Código Centro',
            'Correo'
        ];
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';',      // Mejor para Excel en configuraciones regionales en español
            'enclosure' => '"',      // Encierra textos para respetar comas o espacios
            'line_ending' => "\r\n", // Compatible con Windows
            'use_bom' => true,       // Previene problemas con caracteres UTF-8
        ];
    }
}
