<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

//spatie
use Spatie\Permission\Models\Permission;


class SeederTablaPermisos extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permisos =[
            //tabla de roles
            'ver-rol',
            'crear-rol',
            'editar-rol',
            'borrar-rol',
            //tabla de auditorias
            'ver-auditoria',
            'crear-auditoria',
            'editar-auditoria',
            'borrar-auditoria',
            //tabla de trabajadores
            'ver-trabajadores',
            'crear-trabajadores',
            'editar-trabajadores',
            'borrar-trabajadores',
            //tabla de ubicacion
            'ver-ubicacion',
            'crear-ubicacion',
            'editar-ubicacion',
            'borrar-ubicacion',
            //tabla de fiscalizacion
            'ver-fiscalizacion',
            'crear-fiscalizacion',
            'editar-fiscalizacion',
            'borrar-fiscalizacion',
            //tabla de fiscalizacion-all
            'ver-fiscalizacion-all',
            'crear-fiscalizacion-all',
            'editar-fiscalizacion-all',
            'borrar-fiscalizacion-all',
            //tabla de incidenciasfiscalizacións
            'ver-incidenciasfiscalizacion',
            'crear-incidenciasfiscalizacion',
            'editar-incidenciasfiscalizacion',
            'borrar-incidenciasfiscalizacion',
            //tabla de ferias
            'ver-ferias',
            'crear-ferias',
            'editar-ferias',
            'borrar-ferias',
            //tabla de ferias
            'ver-ferias-all',
            'crear-ferias-all',
            'editar-ferias-all',
            'borrar-ferias-all',
            //tabla de incidenciasferias
            'ver-incidenciasferias',
            'crear-incidenciasferias',
            'editar-incidenciasferias',
            'borrar-incidenciasferias',
            //tabla de incidencias
            'ver-incidencias',
            'crear-incidencias',
            'editar-incidencias',
            'borrar-incidencias',
        ];

        foreach($permisos as $permiso){
            Permission::create(['name'=>$permiso]);
        }
    }
}


