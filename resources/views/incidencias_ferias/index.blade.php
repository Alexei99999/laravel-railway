@extends('adminlte::page')

@section('title', 'Incidencias')

@section('content_header')
    <h1 class="m-0 text-dark" style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-weight: 600; letter-spacing: 0.5px;">Incidencias</h1>
@stop

@section('content')
    <div class="container-fluid py-4" style="background-color: #f9fafb;">
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal" style="background-color: #4a90e2; border-color: #4a90e2; border-radius: 8px; padding: 8px 20px; font-weight: 500; transition: background-color 0.2s;">
                Crear Incidencia
            </button>
        </div>
        <div class="card shadow-sm" style="border-radius: 12px; background-color: #ffffff;">
            <div class="card-header" style="background: linear-gradient(to right, #e6f0fa, #edf2f7); border-bottom: 1px solid #d1d9e6; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 15px 20px;">
                <h3 class="card-title mb-0" style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 1.5rem; font-weight: 600; color: #1a202c;">Lista de Incidencias</h3>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table id="incidenciasTable" class="table table-bordered table-striped w-100" style="background-color: #ffffff; border: 1px solid #e2e8f0;">
                        <thead>
                            <tr>
                                <th style="background-color: #e6f0fa; color: #1a202c; font-weight: 600; text-align: center; padding: 10px;">Cédula</th>
                                <th style="background-color: #e6f0fa; color: #1a202c; font-weight: 600; text-align: center; padding: 10px;">Trabajador</th>
                                <th style="background-color: #e6f0fa; color: #1a202c; font-weight: 600; text-align: center; padding: 10px;">Ubicación</th>
                                <th style="background-color: #e6f0fa; color: #1a202c; font-weight: 600; text-align: center; padding: 10px;">Contacto</th>
                                <th style="background-color: #e6f0fa; color: #1a202c; font-weight: 600; text-align: center; padding: 10px;">Fecha Reporte</th>
                                <th style="background-color: #e6f0fa; color: #1a202c; font-weight: 600; text-align: center; padding: 10px;">Incidencia</th>
                                <th style="background-color: #e6f0fa; color: #1a202c; font-weight: 600; text-align: center; padding: 10px;">Hora Incidencia</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('incidencias_ferias.partials.modal-create')
    @include('incidencias_ferias.partials.modal-edit')
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="/css/incidencias_ferias.css">
@stop

@section('js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        window.IncidenciasRoutes = {
            store: '{{ route("incidencias_ferias.store") }}',
            datatables: '{{ route("incidencias_ferias.datatables") }}',
            showIncidencia: '{{ route("incidencias_ferias.show", ":id") }}',
            update: '{{ route("incidencias_ferias.update", ":id") }}',
            destroy: '{{ route("incidencias_ferias.destroy", ":id") }}',
            options: '{{ route("incidencias_ferias.options") }}',
            active_workers: '{{ route("incidencias_ferias.active_workers") }}'
        };
    </script>
    <script src="/js/incidencias_ferias.js"></script>
@stop
