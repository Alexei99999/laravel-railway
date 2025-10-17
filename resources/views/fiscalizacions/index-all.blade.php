@extends('adminlte::page')

@section('title', 'Todas las Fiscalizaciones')

@section('content_header')
    <div class="d-flex justify-content-between ml-1 mr-1 mt-2 mb-2">
        <div>
            <h1><em><b>Todas las Fiscalizaciones</b></em></h1>
            <p>Comisión de Participación Política y Financiamiento <b>COPAFI</b></p>
        </div>
        <div class="d-flex justify-content-end mb-3">
            <button type="button" class="btn btn-primary mr-2" data-toggle="modal" data-target="#createModal">
                <i class="fas fa-plus"></i> REGISTRO
            </button>
            <button type="button" class="btn btn-success mr-2" data-toggle="modal" data-target="#uploadModal">
                <i class="fas fa-upload"></i> Importar CSV
            </button>
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#exportModal">
                <i class="fas fa-file-export"></i> Exportar
            </button>
        </div>
    </div>

    {{-- Modal de exportación fuera del contenedor de botones --}}
    <div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <form action="{{ route('fiscalizacions.exportTodo') }}" method="POST">
            @csrf
            <div class="modal-header">
              <h5 class="modal-title" id="exportModalLabel">Exportar Fiscalización</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <p>Elija el formato para exportar:</p>
              <div class="form-group">
                <select name="format" class="form-control" required>
                  <option value="">Seleccione formato</option>
                  <option value="csv">CSV</option>
                  <option value="excel">Excel</option>
                </select>
              </div>
            </div>
          <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-info">Exportar</button>
        </div>
          </form>
        </div>
      </div>
    </div>
@stop


@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <table id="fiscalizacionesTableAll" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Estado</th>
                                    <th>Municipio</th>
                                    <th>Parroquia</th>
                                    <th>Nombre del Punto</th>
                                    <th>Cédula</th>
                                    <th>Apellidos</th>
                                    <th>Nombres</th>
                                    <th>Teléfono</th>
                                    <th>Correo</th>
                                    <th>Incidencia</th>
                                    <th>Fecha de Incidencia</th>
                                    <th>Hora de Incidencia</th>
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
