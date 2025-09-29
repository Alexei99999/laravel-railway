@extends('adminlte::page')

@section('title', 'REPANMCOPAFI')

@section('content_header')
    <!-- AdminLTE CSS con rutas corregidas -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/jqvmap/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}?v=3.2.0">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/daterangepicker/daterangepicker.css') }}">

    <!-- Fallback a CDN si los assets no se publican -->
    <script>
        if (!document.querySelector('link[href*="fontawesome-free"]')) {
            document.write('<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">');
        }
        if (!document.querySelector('link[href*="tempusdominus-bootstrap-4"]')) {
            document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@eonasdan/tempus-dominus@6.7.14/dist/css/tempus-dominus.min.css">');
        }
    </script>

    <!-- FullCalendar v6 CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/google-calendar@6.1.11/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.24.0/min/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.0/dist/jquery.min.js"></script>

    <div>
        <center><b><h1>Comisión de Participación Política y Financiamiento</h1></b></center>
        <center><i><p>Consejo Nacional Electoral</p></i></center>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card-body shadow-lg  mb-5 rounded">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}">
    <style>
        #calendar {
            max-width: 100%;
            margin: 0 auto;
            font-family: 'Source Sans Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .fc-daygrid-day-number {
            color: #343a40;
            font-weight: 600;
            font-size: 1.1em;
        }
        .fc-event {
            background-color: #17a2b8;
            border-color: #17a2b8;
            color: white;
            border-radius: 5px;
            padding: 4px 8px;
            font-size: 0.9em;
        }
        .fc-button {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
            border-radius: 0.25rem;
        }
        .fc-button:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .fc-button-active {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .card-primary {
            border-top: 3px solid #007bff;
        }
        .fc .fc-toolbar-title {
            font-size: 1.5em;
            color: #2c3e50;
        }
        @media (max-width: 768px) {
            #calendar {
                font-size: 0.9em;
            }
            .fc-button {
                padding: 0.25rem 0.5rem;
                font-size: 0.8em;
            }
        }
    </style>
@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                googleCalendarApiKey: 'AIzaSyAjFXyq3cO8OQXx-9Gt8NBIW9lYMlb5pXg', // Reemplaza con tu clave API
                eventSources: [
                    {
                        googleCalendarId: 'es.ve#holiday@group.v.calendar.google.com', // Calendario público de ejemplo
                        className: 'google-calendar-event'
                    }
                ],
                height: 'auto'
            });
            calendar.render();
        });
    </script>
@stop
