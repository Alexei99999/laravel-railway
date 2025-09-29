$(document).ready(function() {
    if (!window.IncidenciasRoutes) {
        console.error('IncidenciasRoutes is undefined');
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Rutas no definidas. Por favor, recarga la página.'
        });
        return;
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize Flatpickr for date inputs
    flatpickr('.datepicker', {
        dateFormat: 'd/m/y',
        allowInput: true,
        locale: {
            firstDayOfWeek: 1,
            weekdays: {
                shorthand: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
                longhand: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']
            },
            months: {
                shorthand: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                longhand: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
            }
        }
    });

    // Populate select options for create modal
    $.ajax({
        url: window.IncidenciasRoutes.options,
        method: 'GET',
        success: function(data) {
            const trabajadorSelect = $('#createForm [name="trabajador"]');
            const ubicacionSelect = $('#createForm [name="ubicacion"]');
            trabajadorSelect.empty().append('<option value="">Seleccione un trabajador</option>');
            ubicacionSelect.empty().append('<option value="">Seleccione una ubicación</option>');
            data.trabajadores.forEach(trabajador => {
                trabajadorSelect.append(`<option value="${trabajador.nombre}">${trabajador.nombre}</option>`);
            });
            data.ubicaciones.forEach(ubicacion => {
                ubicacionSelect.append(`<option value="${ubicacion.nombre}">${ubicacion.nombre}</option>`);
            });
        },
        error: function(xhr) {
            console.error('Options error:', xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al cargar opciones de trabajador y ubicación: ' + (xhr.responseJSON?.error || 'Error interno')
            });
        }
    });

    window.incidenciasTable = $('#incidenciasTable').DataTable({
        ajax: {
            url: window.IncidenciasRoutes.datatables,
            error: function(xhr, error, thrown) {
                console.error('DataTables AJAX error:', {
                    status: xhr.status,
                    responseText: xhr.responseText,
                    error: error,
                    thrown: thrown
                });
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al cargar los datos: ' + (xhr.status === 404 ? 'Ruta no encontrada' : xhr.responseJSON?.error || 'Error interno del servidor')
                });
            }
        },
        columns: [
            { data: 'trabajador', responsivePriority: 1 },
            { data: 'ubicacion', responsivePriority: 2 },
            { data: 'e_contact', responsivePriority: 3 },
            { data: 'fecha_rep', responsivePriority: 4 },
            { data: 'e_disponib', responsivePriority: 1 },
            { data: 'fecha_e_disponib', responsivePriority: 5 }
        ],
        language: {
            url: '/js/es-ES.json'
        },
        responsive: true,
        autoWidth: false,
        lengthChange: false,
        dom: 'frtip',
        createdRow: function(row, data, dataIndex) {
            if (data.e_disponib === 'Trabaja') {
                $(row).addClass('row-activo');
            } else if (data.e_disponib === 'No trabaja') {
                $(row).addClass('row-inactivo');
            }
        }
    });

    $('#incidenciasTable tbody').on('click', 'tr', function() {
        var data = window.incidenciasTable.row(this).data();
        if (data && data.id) {
            openEditModal(data.id);
        }
    });

    function showSuccess(message) {
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: message
        });
    }

    function showFormErrors(formId, errors) {
        let message = errors.message || errors.error || 'Error desconocido';
        if (errors.errors) {
            message = Object.values(errors.errors).join('\n');
        }
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message
        });
    }

    window.openEditModal = function(id) {
        if (!id || isNaN(id)) {
            console.error('Invalid ID for edit:', id);
            return;
        }
        $.ajax({
            url: window.IncidenciasRoutes.edit.replace(':id', id),
            method: 'GET',
            success: function(data) {
                const trabajadorSelect = $('#editForm [name="trabajador"]');
                const ubicacionSelect = $('#editForm [name="ubicacion"]');
                trabajadorSelect.empty().append('<option value="">Seleccione un trabajador</option>');
                ubicacionSelect.empty().append('<option value="">Seleccione una ubicación</option>');
                data.trabajadores.forEach(trabajador => {
                    trabajadorSelect.append(`<option value="${trabajador.nombre}" ${trabajador.nombre === data.incidencia.trabajador ? 'selected' : ''}>${trabajador.nombre}</option>`);
                });
                data.ubicaciones.forEach(ubicacion => {
                    ubicacionSelect.append(`<option value="${ubicacion.nombre}" ${ubicacion.nombre === data.incidencia.ubicacion ? 'selected' : ''}>${ubicacion.nombre}</option>`);
                });
                $('#editForm [name="e_contact"]').val(data.incidencia.e_contact);
                $('#editForm [name="fecha_rep"]').val(data.incidencia.fecha_rep);
                $('#editForm [name="e_disponib"]').val(data.incidencia.e_disponib);
                $('#editForm [name="fecha_e_disponib"]').val(data.incidencia.fecha_e_disponib);
                $('#editForm').attr('data-action', window.IncidenciasRoutes.update.replace(':id', id));
                $('#editForm').attr('data-id', id);
                $('#editModal').modal('show');
                // Re-initialize Flatpickr for edit modal inputs
                flatpickr('#editForm .datepicker', {
                    dateFormat: 'd/m/y',
                    allowInput: true,
                    locale: {
                        firstDayOfWeek: 1,
                        weekdays: {
                            shorthand: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
                            longhand: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']
                        },
                        months: {
                            shorthand: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                            longhand: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
                        }
                    }
                });
            },
            error: function(xhr) {
                console.error('Edit error:', xhr.responseText);
                showFormErrors('editForm', xhr.responseJSON);
            }
        });
    };

    $('#createForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        console.log('Form data-action:', window.IncidenciasRoutes.store);
        $.ajax({
            url: window.IncidenciasRoutes.store,
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                showSuccess(response.message || 'Incidencia creada exitosamente');
                $('#createModal').modal('hide');
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                window.incidenciasTable.ajax.reload();
                form[0].reset();
            },
            error: function(xhr) {
                console.error('Create error:', xhr.responseText);
                showFormErrors('createForm', xhr.responseJSON);
            }
        });
    });

    $('#editForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        $.ajax({
            url: form.data('action'),
            method: 'POST',
            data: form.serialize() + '&_method=PUT',
            success: function(response) {
                showSuccess(response.message || 'Incidencia actualizada exitosamente');
                $('#editModal').modal('hide');
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                window.incidenciasTable.ajax.reload();
            },
            error: function(xhr) {
                console.error('Update error:', xhr.responseText);
                showFormErrors('editForm', xhr.responseJSON);
            }
        });
    });

    window.deleteIncidencia = function() {
        const id = $('#editForm').attr('data-id');
        if (!id || isNaN(id)) {
            console.error('Invalid ID for delete:', id);
            return;
        }
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'La incidencia será marcada como inactiva.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Inactivar',
            cancelButtonText: 'Cancelar',
            dangerMode: true
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: window.IncidenciasRoutes.destroy.replace(':id', id),
                    method: 'DELETE',
                    success: function(response) {
                        showSuccess(response.message || 'Incidencia inactivada exitosamente');
                        $('#editModal').modal('hide');
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                        window.incidenciasTable.ajax.reload();
                    },
                    error: function(xhr) {
                        console.error('Delete error:', xhr.responseText);
                        showFormErrors('editForm', xhr.responseJSON);
                    }
                });
            }
        });
    };

    $('#createModal, #editModal').on('hidden.bs.modal', function() {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
    });
});
