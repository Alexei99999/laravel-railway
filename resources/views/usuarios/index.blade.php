@extends('adminlte::page')

@section('title', 'Usuarios')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1><i><b>Usuarios</b> Registrados</i></h1>
        <button type="button" class="btn btn-warning shadow-lg" data-toggle="modal" data-target="#createModal">Registrar Nuevo Usuario</button>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <div class="card">
                    <div class="card-body">
                        <table id="usuariosTable" class="table table-bordered table-striped shadow-lg text-center w-100">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>E-mail</th>
                                    <th>Rol</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($usuarios as $usuario)
                                    <tr>
                                        <td>{{ $usuario->id }}</td>
                                        <td>{{ $usuario->name }}</td>
                                        <td>{{ $usuario->email }}</td>
                                        <td>{{ $usuario->roles->pluck('name')->implode(', ') }}</td>
                                        <td>
                                            <button class="btn btn-primary btn-sm edit-user" data-id="{{ $usuario->id }}" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Registrar Usuario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="createForm">
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
                            <input type="password" class="form-control" id="create_password" name="password" required placeholder="Mínimo 9 caracteres">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label for="create_confirm_password">Confirmar Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="create_confirm_password" name="password_confirmation" required placeholder="Repite tu contraseña">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label>Roles <span class="text-danger">*</span></label>
                            <div id="create_roles" class="row">
                                <!-- Se llenará con AJAX -->
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Editar Usuario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <input type="hidden" id="edit_id" name="id">
                        <div class="form-group">
                            <label for="edit_name">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label for="edit_email">E-mail <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label for="edit_password">Password</label>
                            <input type="password" class="form-control" id="edit_password" name="password">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label for="edit_confirm_password">Confirmar Password</label>
                            <input type="password" class="form-control" id="edit_confirm_password" name="password_confirmation">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label>Roles <span class="text-danger">*</span></label>
                            <div id="edit_roles" class="row">
                                <!-- Se llenará con AJAX -->
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        #usuariosTable_wrapper {
            margin: 0 auto;
            max-width: 100%;
        }
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            margin-bottom: 15px;
        }
        @media (max-width: 767px) {
            #usuariosTable_wrapper .dataTables_length,
            #usuariosTable_wrapper .dataTables_filter {
                text-align: center;
            }
        }
        .form-check {
            margin-bottom: 0.5rem;
        }
    </style>
@stop

@section('js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.UsuarioRoutes = {
            create: '{{ route("usuarios.create") }}',
            store: '{{ route("usuarios.store") }}',
            edit: '{{ route("usuarios.edit", ":id") }}',
            update: '{{ route("usuarios.update", ":id") }}',
            destroy: '{{ route("usuarios.destroy", ":id") }}'
        };
    </script>
    <script src="{{ asset('js/usuarios.js') }}"></script>
@stop
