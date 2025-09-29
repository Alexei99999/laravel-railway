@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
<link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
<link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
<link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
<link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
<link rel="stylesheet" href="dist/css/adminlte.min.css?v=3.2.0">
<link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
<link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">

<div class="float-left mb-2">
    <h1>Auditorias</h1>
</div>

@stop

@section('content')

<br>
<div class="card-body">
    <table class="table table-striped table-bordered" style="">
        <thead>
            {{-- 01 --}}<th>ID</th>
            {{-- 02 --}}<th>Tipo De Usuario</th>
            {{-- 03 --}}<th>Usuario ID</th>
            {{-- 04 --}}<th>Evento</th>
            {{-- 05 --}}<th>Tipo De Auditoria</th>
            {{-- 06 --}}<th>Auditoria ID</th>
            {{-- 07 --}}<th>Valores</th>
            {{-- 08 --}}<th>Nuevos Valores</th>
            {{-- 09 --}}<th>URL</th>
            {{-- 10 --}}<th>Direccion IP</th>
            {{-- 11 --}}<th>Agente De Usuario</th>
            {{-- 12 --}}<th>Tags</th>
            {{-- 13 --}}<th>Creado</th>
            {{-- 14 --}}<th>Actualizado</th>
        </thead>
        <tbody>
            @foreach($audits as $audit)
                <tr>
                    {{-- 01 --}}<td>{{$audit->id}}</td>
                    {{-- 02 --}}<td>{{$audit->user_type}}</td>
                    {{-- 03 --}}<td>{{$audit->user_id}}</td>
                    {{-- 04 --}}<td>{{$audit->event}}</td>
                    {{-- 05 --}}<td>{{$audit->auditable_type}}</td>
                    {{-- 06 --}}<td>{{$audit->auditable_id}}</td>
                    {{-- 07 --}}<td>{{$audit->old_values}}</td>
                    {{-- 08 --}}<td>{{$audit->new_values}}</td>
                    {{-- 09 --}}<td>{{$audit->url}}</td>
                    {{-- 10 --}}<td>{{$audit->ip_address}}</td>
                    {{-- 11 --}}<td>{{$audit->user_agent}}</td>
                    {{-- 12 --}}<td>{{$audit->tags}}</td>
                    {{-- 13 --}}<td>{{$audit->created_at}}</td>
                    {{-- 14 --}}<td>{{$audit->updated_at}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
@stop
