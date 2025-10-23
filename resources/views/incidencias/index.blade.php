@extends('adminlte::page')

@section('title', 'Incidencias')

@section('content_header')
    <h1 class="m-0">Incidencias</h1>
@stop

@section('content')
    <div class="container-fluid" style="background-color: #f4f6f9;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Incidencias</h3>
                <div class="card-tools">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#createModal">Crear Incidencia</button>
                </div>
            </div>
            <div class="card-body">
                <table id="incidenciasTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Trabajador</th>
                            <th>Ubicación</th>
                            <th>Contacto</th>
                            <th>Fecha Reporte</th>
                            <th>Estado</th>
                            <th>Fecha Estado</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    @include('incidencias.partials.modal-create')
    @include('incidencias.partials.modal-edit')
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="/css/incidencias.css">
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        window.IncidenciasRoutes = {
            store: '{{ route('incidencias.store') }}',
            datatables: '{{ route('incidencias.datatables') }}',
            edit: '{{ route('incidencias.edit', ':id') }}',
            update: '{{ route('incidencias.update', ':id') }}',
            destroy: '{{ route('incidencias.destroy', ':id') }}',
            options: '{{ route('incidencias.options') }}';
        };
        console.log('IncidenciasRoutes:', window.IncidenciasRoutes);
    </script>
    <script src="/js/incidencias.js"></script>
@stop
