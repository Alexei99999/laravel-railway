<?php

namespace App\Exports;

use App\Models\Feria;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class FeriasExport implements FromCollection, WithHeadings, WithCustomCsvSettings
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
            'e_registro',
            'correo'
        )
        ->where(function ($query) {
            $query->where('disponibilidad', '!=', 'No trabajará')
                ->orWhereNull('disponibilidad');
        })
        ->where('e_registro', 'Activo')  // Solo registros activos
        ->get();
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
            'e_registro',
            'Correo'
        ];
    }

    // Ajustes del CSV para que se vea igual que Excel
    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';',      // Excel interpreta mejor el punto y coma
            'enclosure' => '"',      // Encierra los valores textuales
            'line_ending' => "\r\n", // Compatible con Windows
            'use_bom' => true,       // Asegura codificación UTF-8 en Excel
        ];
    }
}
