@extends('adminlte::page')

@section('title', 'Todas las Ferias')

@section('content_header')
    <div class="d-flex justify-content-between ml-1 mr-1 mt-2 mb-2">
        <div>
            <h1><em><b>Ferias</b></em></h1>
            <p>Comisión de Participación Política y Financiamiento <b>COPAFI</b></p>
        </div>
        <div class="d-flex justify-content-end mb-3">
            <button type="button" class="btn btn-primary mr-2" data-toggle="modal" data-target="#createModal">
                <i class="fas fa-plus"></i> REGISTRO
            </button>
            <button type="button" class="btn btn-success" id="newUploadCsvButton" data-toggle="modal" data-target="#uploadModal">
                <i class="fas fa-upload"></i> Importar CSV
            </button>
                   <button type="button" class="btn btn-info ml-2" data-toggle="modal" data-target="#exportModal">
    <i class="fas fa-file-export"></i> Exportar
</button>

        </div>
    </div>
    <div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="{{ route('ferias.total-agentes.export') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="exportModalLabel">Exportar Ferias</h5>
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
                        <table id="feriasTableAll" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Estado</th>
                                    <th>Municipio</th>
                                    <th>Parroquia</th>
                                    <th>Nombre Punto</th>
                                    <th>Cédula</th>
                                    <th>Apellidos</th>
                                    <th>Nombres</th>
                                    <th>Teléfono</th>
                                    <th>Status Contacto 1</th>
                                    <th>Status Contacto 2</th>
                                    <th>Status Contacto 3</th>
                                    <th>Disponibilidad</th>
                                    <th>Incidencias</th>
                                    <th>Fecha Incidencia</th>
                                    <th>Hora Incidencia</th>
                                    <th>Código Estado</th>
                                    <th>Código Municipio</th>
                                    <th>Código Parroquia</th>
                                    <th>Código Centro</th>
                                    <th>Correo</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('ferias.partials.modal-create')
    @include('ferias.partials.modal-edit')
    @include('ferias.partials.modal-upload')
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="{{ asset('css/ferias.css') }}">
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="{{ asset('js/ferias.js') }}"></script>
    <script>
        window.FeriasRoutes = {
            datatables: "{{ route('ferias.datatables') }}",
            datatablesAll: "{{ route('ferias.datatablesAll') }}",
            store: "{{ route('ferias.store') }}",
            edit: "{{ route('ferias.edit', ['feria' => ':id']) }}",
            update: "{{ route('ferias.update', ['feria' => ':id']) }}",
            destroy: "{{ route('ferias.destroy', ['feria' => ':id']) }}",
            storeMassive: "{{ route('ferias.storeMassive') }}",
            checkDuplicates: "{{ route('ferias.checkDuplicates') }}",
        };
    </script>
@stop
