@extends('adminlte::page')

@section('title', 'Fiscalizaciones')

@section('content_header')
    <div class="d-flex justify-content-between ml-1 mr-1 mt-2 mb-2">
        <div>
            <h1><em><b>Fiscalización</b></em></h1>
            <p>Comisión de Participación Política y Financiamiento <b>COPAFI</b></p>
        </div>
        <div class="d-flex justify-content-end mb-3">
            <button type="button" class="btn btn-primary mr-2" data-toggle="modal" data-target="#createModal">
                <i class="fas fa-plus"></i> REGISTRO
            </button>
            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#uploadModal">
                <i class="fas fa-upload"></i> Importar CSV
            </button>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <table id="fiscalizacionesTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
            <th>Estado</th>
            <th>Municipio</th>
            <th>Parroquia</th>
            <th>Nombre Pto</th>
            <th>Cédula</th>
            <th>Apellidos</th>
            <th>Nombres</th>
            <th>Teléfono</th>
            <th>Correo</th>
            <th>Incidencia</th>
            <th>Fecha Incidencia</th>
            <th>Hora Incidencia</th>
        </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('fiscalizacions.partials.modal-create')
    @include('fiscalizacions.partials.modal-edit')
    @include('fiscalizacions.partials.modal-upload')
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="/css/fiscalizacions.css">
@stop

@section('js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>
    <script>
        window.FiscalizacionesRoutes = {
            edit: "{{ route('fiscalizacions.edit', ['id' => ':id']) }}",
            update: "{{ route('fiscalizacions.update', ['id' => ':id']) }}",
            datatables: "{{ route('fiscalizacions.datatables') }}",
            store: "{{ route('fiscalizacions.store') }}",
            editAll: "{{ route('fiscalizacions-all.edit', ['id' => ':id']) }}",
            updateAll: "{{ route('fiscalizacions-all.update', ['id' => ':id']) }}",
            datatablesAll: "{{ route('fiscalizacions-all.datatables') }}",
            storeAll: "{{ route('fiscalizacions-all.store') }}",
            checkDuplicates: "{{ route('fiscalizacions.checkDuplicates') }}",
            storeMassive: "{{ route('fiscalizacions.storeMassive') }}"
        };
    </script>
    <script src="/js/fiscalizacions.js"></script>
@stop
