@extends('adminlte::page')

@section('title', 'PROGRAMAintegral')

@section('content_header')

<div>
  <center><b><h1>Oficina Nacional De Participación Política</h1></b></center>
  <center><i><p>Consejo Nacional Electoral</p></i></center>
</div>
<script src="{{ asset('js/jscalendario.js') }}"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/csscalendario.css') }}">
@stop

@section('content')

<div id="calendar"></div>
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Gestión de Evento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="eventForm">
                    <input type="hidden" id="eventId">
                    <div class="mb-3">
                        <label class="form-label">Título *</label>
                        <input type="text" id="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha *</label>
                        <input type="date" id="start" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo *</label>
                        <select id="tipo" class="form-select" required>
                            <option value="cumpleaños">Cumpleaños</option>
                            <option value="comentario">Comentario</option>
                            <option value="feriado">Feriado</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Comentario</label>
                        <textarea id="comentario" class="form-control" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="btnDelete">Eliminar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnSave">Guardar</button>
            </div>
        </div>
    </div>
</div>

@stop


@section('js')
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales/es.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const calendarEl = document.getElementById('calendar');
        const modal = new bootstrap.Modal('#eventModal');
        let calendar;
        let currentEvent = null;

        // Configurar rutas
        const routes = {
            fetch: @json(route('events.fetch')),
            store: @json(route('events.store')),
            update: (id) => @json(route('events.update', ['id' => ':id'])).replace(':id', id),
            delete: (id) => @json(route('events.delete', ['id' => ':id'])).replace(':id', id)
        };

        // Inicializar calendario
        calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'es',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            editable: true,
            events: async () => {
                try {
                    const response = await fetch(routes.fetch);
                    return await response.json();
                } catch (error) {
                    showToast('error', 'Error cargando eventos');
                    return [];
                }
            },
            eventClick: (info) => {
                currentEvent = info.event;
                loadEventData(currentEvent);
                modal.show();
            },
            dateClick: (info) => {
                currentEvent = null;
                loadEventData(null, info.dateStr);
                modal.show();
            }
        });

        calendar.render();

        // Cargar datos en el modal
        function loadEventData(event, dateStr = null) {
            const form = document.getElementById('eventForm');
            form.reset();

            document.getElementById('btnDelete').style.display = event ? 'block' : 'none';
            document.querySelector('.modal-title').textContent = event ? 'Editar Evento' : 'Nuevo Evento';

            if(event) {
                document.getElementById('title').value = event.title;
                document.getElementById('start').value = event.startStr.substr(0, 10);
                document.getElementById('tipo').value = event.extendedProps.tipo;
                document.getElementById('comentario').value = event.extendedProps.comentario;
            } else {
                document.getElementById('start').value = dateStr;
            }
        }

        // Manejar Guardar
        document.getElementById('btnSave').addEventListener('click', async () => {
            const formData = {
                title: document.getElementById('title').value,
                start: document.getElementById('start').value,
                tipo: document.getElementById('tipo').value,
                comentario: document.getElementById('comentario').value
            };

            try {
                const url = currentEvent ? routes.update(currentEvent.id) : routes.store;
                const method = currentEvent ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if(!response.ok) throw new Error(data.message || 'Error en la operación');

                if(currentEvent) {
                    calendar.getEventById(currentEvent.id).remove();
                }
                calendar.addEvent(data.event);

                modal.hide();
                showToast('success', currentEvent ? 'Evento actualizado' : 'Evento creado');

            } catch (error) {
                showToast('error', error.message);
                console.error('Error:', error);
            }
        });

        // Manejar Eliminar
        document.getElementById('btnDelete').addEventListener('click', async () => {
            if(confirm('¿Estás seguro de eliminar este evento permanentemente?')) {
                try {
                    const response = await fetch(routes.delete(currentEvent.id), {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if(!response.ok || !data.success) {
                        throw new Error(data.message || 'Error al eliminar el evento');
                    }

                    calendar.getEventById(currentEvent.id)?.remove();
                    modal.hide();
                    currentEvent = null;
                    showToast('success', 'Evento eliminado');

                } catch (error) {
                    showToast('error', error.message);
                    console.error('Error:', error);
                }
            }
        });

        // Función auxiliar para notificaciones
        function showToast(type, message) {
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type} border-0`;
            toast.innerHTML = `
            <div class="d-flex p-2">
                <div class="toast-body" style="padding: 0.8rem">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
            `;

        const container = document.querySelector('.toast-container') || createToastContainer();
        container.appendChild(toast);

        new bootstrap.Toast(toast, {
            autohide: true,
            delay: 3000,
            animation: true
        }).show();

        setTimeout(() => toast.remove(), 3000);
    }

        function createToastContainer() {
            const container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
            return container;
        }
    });
</script>

@stop
