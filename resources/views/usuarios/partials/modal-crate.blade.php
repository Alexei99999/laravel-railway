@extends('adminlte::page')

@section('title', 'Crear Usuario')

@section('content_header')
<h1>Crear Usuario</h1>
@stop

@section('content')
<div class="container-fluid">
    <form id="createForm" action="{{ route('usuarios.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="create_name">Nombre <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="create_name" name="name" required>
            <div class="invalid-feedback"></div>
        </div>
        <div class="form-group">
            <label for="create_email">E-mail <span class="text-danger">*</span></label>
            <input type="email" class="form-control" id="create_email" name="email" required>
            <div class="invalid-feedback"></div>
        </div>
        <div class="form-group">
            <label for="create_password">Password <span class="text-danger">*</span></label>
            <input type="password" class="form-control" id="create_password" name="password" required>
            <div class="invalid-feedback"></div>
        </div>
        <div class="form-group">
            <label for="create_confirm_password">Confirmar Password <span class="text-danger">*</span></label>
            <input type="password" class="form-control" id="create_confirm_password" name="password_confirmation" required>
            <div class="invalid-feedback"></div>
        </div>
        <div class="form-group">
            <label for="create_roles">Roles <span class="text-danger">*</span></label>
            <select class="form-control" id="create_roles" name="roles[]" multiple required>
                @foreach($roles as $role)
                    <option value="{{ $role }}">{{ $role }}</option>
                @endforeach
            </select>
            <div class="invalid-feedback"></div>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/usuarios.js') }}"></script>
@stop
