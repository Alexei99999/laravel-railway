@extends('adminlte::page')

@section('title', 'Editar Trabajador')

@section('content_header')
    <h1 class="text-xl font-bold text-gray-800">Editar Trabajador</h1>
@stop

@section('content')
    <div class="py-4">
        <form action="{{ route('trabajadores.update', $trabajador->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Columna Izquierda -->
                    <div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="cedula">
                                Cédula*
                            </label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="cedula" name="cedula" type="text"
                                value="{{ old('cedula', $trabajador->cedula) }}" required maxlength="8">
                        </div>

                        <!-- Resto de campos similares a create pero con valores -->
                    </div>

                    <!-- Columna Derecha -->
                    <div>
                        <!-- Todos los campos iguales a create pero con:
                        value="{{ old('field', $trabajador->field) }}" -->
                    </div>
                </div>

                <div class="flex items-center justify-between mt-6">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                        Actualizar
                    </button>
                    <a href="{{ route('trabajadores.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
@stop