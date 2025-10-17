<?php

namespace App\Exports;

use App\Models\Fiscalizacion;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TotalFiscalizacionesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Fiscalizacion::select(
            'cod_edo', 'estado', 'cod_mun', 'municipio', 'cod_parroquia', 'parroquia',
            'cod_centro', 'nombre_pto', 'direccion_pto', 'circuns', 'rectoria', 'cedula',
            'apellidos', 'nombres', 'telefono', 'correo', 'rol', 'status_contact1', 'fecha_hora1',
            'status_contact2', 'fecha_hora2', 'status_contact3', 'fecha_hora3', 'disponibilidad',
            'incidencias', 'fecha_incidencia', 'hora_incidencia', 'observaciones', 'e_registro',
            'created_at', 'updated_at'
        )->get();
    }

    public function headings(): array
    {
        return [
            'Código Estado',
            'Estado',
            'Código Municipio',
            'Municipio',
            'Código Parroquia',
            'Parroquia',
            'Código Centro',
            'Nombre del Punto',
            'Dirección Punto',
            'Circunscripción',
            'Rectoría',
            'Cédula',
            'Apellidos',
            'Nombres',
            'Teléfono',
            'Correo',
            'Rol',
            'Estado Contacto 1',
            'Fecha Hora 1',
            'Estado Contacto 2',
            'Fecha Hora 2',
            'Estado Contacto 3',
            'Fecha Hora 3',
            'Disponibilidad',
            'Incidencias',
            'Fecha de Incidencia',
            'Hora de Incidencia',
            'Observaciones',
            'Estado Registro',
            'Creado en',
            'Modificado en',
        ];
    }
}
