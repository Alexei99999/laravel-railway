@extends('adminlte::page')

@section('title', 'Detalles Trabajador')

@section('content_header')
    <h1 class="text-xl font-bold text-gray-800">Detalles del Trabajador</h1>
@stop

@section('content')
    <div class="py-4">
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-lg font-semibold mb-4">Información Personal</h2>
                    <p><span class="font-bold">Cédula:</span> {{ $trabajador->cedula }}</p>
                    <p><span class="font-bold">Nombres:</span> {{ $trabajador->nombre1 }} {{ $trabajador->nombre2 }}</p>
                    <p><span class="font-bold">Apellidos:</span> {{ $trabajador->apellido1 }} {{ $trabajador->apellido2 }}</p>
                    <p><span class="font-bold">Teléfono:</span> {{ $trabajador->telefono ?? 'N/A' }}</p>
                </div>

                <div>
                    <h2 class="text-lg font-semibold mb-4">Información Laboral</h2>
                    <p><span class="font-bold">Email:</span> {{ $trabajador->e_mail }}</p>
                    <p><span class="font-bold">Rol:</span> {{ $trabajador->rol }}</p>
                    <p><span class="font-bold">Estado Registro:</span> {{ $trabajador->e_registro }}</p>
                    <p><span class="font-bold">Creado:</span> {{ $trabajador->created_at->format('d/m/Y') }}</p>
                </div>
            </div>

            <div class="mt-8">
                <a href="{{ route('trabajadores.index') }}"
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Volver
                </a>
            </div>
        </div>
    </div>
@stop