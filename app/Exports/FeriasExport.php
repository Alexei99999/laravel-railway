<?php

namespace App\Exports;

use App\Models\Feria;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FeriasExport implements FromCollection, WithHeadings
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
    )
    ->where(function ($query) {
        $query->where('disponibilidad', '!=', 'No trabajará')
              ->orWhereNull('disponibilidad');
    })
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
            'Correo'
        ];
    }
}
