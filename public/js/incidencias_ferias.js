(function($) {
    $(document).ready(function() {
        // Initialize Flatpickr
        let flatpickrInstances = {};
        try {
            if (typeof flatpickr !== 'undefined') {
                $('#createModal, #editModal').on('shown.bs.modal', function() {
                    $('.flatpickr-date, .flatpickr-time', this).each(function() {
                        const id = $(this).attr('id');
                        if (!flatpickrInstances[id]) {
                            flatpickrInstances[id] = flatpickr(this, {
                                dateFormat: $(this).hasClass('flatpickr-date') ? 'd/m/y' : 'H:i',
                                enableTime: $(this).hasClass('flatpickr-time'),
                                noCalendar: $(this).hasClass('flatpickr-time'),
                                time_24hr: true,
                                allowInput: true,
                                placeholder: $(this).hasClass('flatpickr-date') ? 'DD/MM/AA' : 'HH:MM',
                                onClose: function() {}
                            });
                        }
                    });
                }).on('hidden.bs.modal', function() {
                    $('.flatpickr-date, .flatpickr-time', this).each(function() {
                        const id = $(this).attr('id');
                        if (flatpickrInstances[id]) {
                            flatpickrInstances[id].destroy();
                            delete flatpickrInstances[id];
                        }
                    });
                });
            } else {
                console.warn('Flatpickr library not loaded.');
                Swal.fire({
                    icon: 'warning',
                    title: 'Advertencia',
                    text: 'Flatpickr no cargado. Los selectores de fecha/hora no funcionarán.',
                    confirmButtonText: 'Aceptar',
                    customClass: { confirmButton: 'btn btn-primary' },
                });
            }
        } catch (e) {
            console.error('Flatpickr initialization error:', e);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al inicializar Flatpickr: ' + e.message,
                confirmButtonText: 'Aceptar',
                customClass: { confirmButton: 'btn btn-primary' },
            });
        }

        // Initialize Select2 for create modal
        try {
            if (typeof $.fn.select2 !== 'undefined') {
                $('#createModal').on('shown.bs.modal', function() {
                    $('#create_cedula').select2({
                        placeholder: 'Seleccione un trabajador',
                        allowClear: true,
                        ajax: {
                            url: window.IncidenciasRoutes.active_workers,
                            dataType: 'json',
                            delay: 250,
                            data: function(params) {
                                return {
                                    q: params.term || '',
                                    page: params.page || 1
                                };
                            },
                            processResults: function(data) {
                                return {
                                    results: data.results.map(item => ({
                                        id: item.id,
                                        text: item.text,
                                        trabajador: item.trabajador,
                                        ubicacion: item.ubicacion,
                                        contacto: item.contacto
                                    })),
                                    pagination: data.pagination
                                };
                            },
                            cache: true
                        },
                        minimumInputLength: 0,
                        language: {
                            noResults: function() {
                                return 'No se encontraron trabajadores activos.';
                            },
                            searching: function() {
                                return 'Buscando...';
                            }
                        }
                    }).on('select2:select', function(e) {
                        var data = e.params.data;
                        $('#create_trabajador').val(data.trabajador || '');
                        $('#create_ubicacion').val(data.ubicacion || '');
                        $('#create_contacto').val(data.contacto || '');
                    });
                });
                $('#createModal').on('hidden.bs.modal', function() {
                    $('#create_cedula').val(null).trigger('change').select2('destroy');
                });
            } else {
                console.warn('Select2 library not loaded.');
                Swal.fire({
                    icon: 'warning',
                    title: 'Advertencia',
                    text: 'Select2 no cargado. El selector de trabajadores no funcionará.',
                    confirmButtonText: 'Aceptar',
                    customClass: { confirmButton: 'btn btn-primary' },
                });
            }
        } catch (e) {
            console.error('Select2 initialization error:', e);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al inicializar Select2: ' + e.message,
                confirmButtonText: 'Aceptar',
                customClass: { confirmButton: 'btn btn-primary' },
            });
        }

        // Custom renderer for responsive accordion
        function responsiveRenderer(api, rowIdx, columns) {
            var data = $.map(columns, function(col) {
                if (col.hidden && col.data && col.data !== 'N/A') {
                    return `<div class="col-md-4 col-sm-6 mb-3">
                        <div class="d-flex flex-column">
                            <strong>${col.title}:</strong>
                            <span>${col.data}</span>
                        </div>
                    </div>`;
                }
                return '';
            }).join('');
            return data ? `<div class="row p-3">${data}</div>` : false;
        }

        // Fetch feria data by cedula
        function fetchFeriaByCedula(cedula, prefix, callback) {
            $.ajax({
                url: window.IncidenciasRoutes.options,
                method: 'POST',
                data: { cedula: cedula },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    if (response.exists) {
                        $(`#${prefix}_trabajador`).val(response.data.trabajador);
                        $(`#${prefix}_ubicacion`).val(response.data.ubicacion);
                        $(`#${prefix}_contacto`).val(response.data.contacto);
                        callback(true);
                    } else {
                        $(`#${prefix}_trabajador`).val('');
                        $(`#${prefix}_ubicacion`).val('');
                        $(`#${prefix}_contacto`).val('');
                        callback(false);
                    }
                },
                error: function(xhr) {
                    console.error('Error fetching feria:', xhr.responseText);
                    $(`#${prefix}_trabajador`).val('');
                    $(`#${prefix}_ubicacion`).val('');
                    $(`#${prefix}_contacto`).val('');
                    callback(false);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al buscar la cédula: ' + (xhr.responseJSON?.message || 'Error desconocido.'),
                        confirmButtonText: 'Aceptar',
                        customClass: { confirmButton: 'btn btn-primary' },
                    });
                },
            });
        }

        // Initialize DataTable
        function initializeDataTable() {
            if (!$('#incidenciasTable').length || !$('#incidenciasTable').is('table')) {
                console.error('Table #incidenciasTable not found or is not a table element');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'La tabla #incidenciasTable no se encuentra en la página o no es una tabla válida.',
                    confirmButtonText: 'Aceptar',
                    customClass: { confirmButton: 'btn btn-primary' },
                });
                return;
            }

            try {
                var table = $('#incidenciasTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: window.IncidenciasRoutes.datatables,
                        dataSrc: function(json) {
                            if (!json || !json.data) {
                                console.error('Invalid AJAX response:', json);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Respuesta de datos inválida desde el servidor.',
                                    confirmButtonText: 'Aceptar',
                                    customClass: { confirmButton: 'btn btn-primary' },
                                });
                                return [];
                            }
                            json.data.forEach(function(row) {
                                row.trabajador = row.trabajador || 'N/A';
                                row.ubicacion = row.ubicacion || 'N/A';
                                row.contacto = row.contacto || 'N/A';
                                row.fecha_reporte = row.fecha_reporte || 'N/A';
                                row.estado = row.estado || 'N/A';
                                row.fecha_estado = row.fecha_estado || 'N/A';
                                row.cedula = row.cedula || 'N/A'; // Mostrar cedula
                                row.DT_RowId = row.id;
                            });
                            return json.data;
                        },
                        error: function(xhr) {
                            console.error('DataTable AJAX error:', xhr);
                            let errorMessage = 'Error al cargar los datos de la tabla.';
                            if (xhr.status === 404) {
                                errorMessage = 'Ruta AJAX no encontrada.';
                            } else if (xhr.status === 500) {
                                errorMessage = 'Error del servidor.';
                            } else if (xhr.responseJSON?.error) {
                                errorMessage = xhr.responseJSON.error + ': ' + xhr.responseJSON.details;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: errorMessage,
                                confirmButtonText: 'Aceptar',
                                customClass: { confirmButton: 'btn btn-primary' },
                            });
                        },
                    },
                    columns: [
                        { data: 'cedula', title: 'Cédula', responsivePriority: 1 }, // Nueva columna
                        { data: 'trabajador', title: 'Trabajador', responsivePriority: 2 },
                        { data: 'ubicacion', title: 'Ubicación', responsivePriority: 3 },
                        { data: 'contacto', title: 'Contacto', responsivePriority: 4 },
                        { data: 'fecha_reporte', title: 'Fecha Reporte', responsivePriority: 5 },
                        { data: 'estado', title: 'Incidencia', responsivePriority: 6 },
                        { data: 'fecha_estado', title: 'Hora Incidencia', responsivePriority: 7 }
                    ],
                    paging: true,
                    pageLength: 10,
                    searching: true,
                    ordering: true,
                    info: true,
                    lengthChange: false, // Quita el control "Mostrar registros"
                    responsive: {
                        details: {
                            renderer: responsiveRenderer,
                        },
                    },
                    language: {
                        url: '/js/es-ES.json',
                    },
                });

                // Handle row click for editing
                $('#incidenciasTable tbody').on('click', 'tr', function() {
                    console.log('Row clicked');
                    var id = table.row(this).data().id;
                    if (id) {
                        console.log('Row ID:', id);
                        $.ajax({
                            url: window.IncidenciasRoutes.showIncidencia.replace(':id', id),
                            method: 'GET',
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            success: function(response) {
                                console.log('AJAX success, response:', response);
                                if (response.data) {
                                    var data = response.data;
                                    $('#edit_id').val(data.id);
                                    $('#edit_cedula').val(data.cedula);
                                    $('#edit_trabajador').val(data.trabajador);
                                    $('#edit_ubicacion').val(data.ubicacion);
                                    $('#edit_contacto').val(data.contacto);
                                    $('#edit_incidencia').val(data.incidencia);
                                    $('#edit_fecha_incidencia').val(data.fecha_incidencia);
                                    $('#edit_hora_incidencia').val(data.hora_incidencia);

                                    $('.flatpickr-date').each(function() {
                                        if (this._flatpickr) this._flatpickr.destroy();
                                        flatpickrInstances[$(this).attr('id')] = flatpickr(this, {
                                            dateFormat: 'd/m/y',
                                            allowInput: true,
                                            defaultDate: data.fecha_incidencia,
                                            placeholder: 'DD/MM/AA'
                                        });
                                    });
                                    $('.flatpickr-time').each(function() {
                                        if (this._flatpickr) this._flatpickr.destroy();
                                        flatpickrInstances[$(this).attr('id')] = flatpickr(this, {
                                            enableTime: true,
                                            noCalendar: true,
                                            dateFormat: 'H:i',
                                            time_24hr: true,
                                            allowInput: true,
                                            defaultDate: data.hora_incidencia,
                                            placeholder: 'HH:MM'
                                        });
                                    });

                                    $('#editModal').modal('show');
                                    console.log('Modal should be visible now');
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'No se encontraron datos para esta incidencia.',
                                        confirmButtonText: 'Aceptar',
                                        customClass: { confirmButton: 'btn btn-primary' },
                                    });
                                }
                            },
                            error: function(xhr) {
                                console.error('Edit AJAX error:', xhr.status, xhr.responseText);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Error al cargar los datos de la incidencia: ' + (xhr.responseJSON?.error || 'Error desconocido'),
                                    confirmButtonText: 'Aceptar',
                                    customClass: { confirmButton: 'btn btn-primary' },
                                });
                            },
                        });
                    } else {
                        console.warn('No ID found for this row');
                    }
                });

                // Handle create button
                $('#createModal').on('show.bs.modal', function() {
                    $('#createForm')[0].reset();
                    $('#createForm').find('.is-invalid').removeClass('is-invalid');
                    $('#createForm').find('.invalid-feedback').text('');
                    $('#create_cedula').val(null).trigger('change');
                });

                // Handle create form submission
                $('#submitCreate').on('click', function() {
                    if (!validateForm('#createForm')) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Por favor, corrige los errores en el formulario.',
                            confirmButtonText: 'Aceptar',
                            customClass: { confirmButton: 'btn btn-primary' },
                        });
                        return;
                    }

                    var formData = $('#createForm').serialize();
                    console.log('Creating with form data:', formData); // Depuración
                    $.ajax({
                        url: window.IncidenciasRoutes.store,
                        method: 'POST',
                        data: formData,
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function(response) {
                            $('#createModal').modal('hide');
                            $('.modal-backdrop').remove();
                            $('body').removeClass('modal-open').css('padding-right', '');
                            Swal.fire({
                                icon: 'success', // Aseguramos que sea 'success'
                                title: 'Éxito', // Texto en español
                                text: response.message,
                                confirmButtonText: 'Aceptar',
                                customClass: { confirmButton: 'btn btn-primary' },
                            });
                            table.ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            console.error('Create error:', xhr.responseText);
                            let errorMessage = 'Error al crear la incidencia.';
                            if (xhr.status === 422 && xhr.responseJSON?.details) {
                                errorMessage = '<ul>' + Object.entries(xhr.responseJSON.details).map(([field, errors]) => `<li>${field}: ${errors.join(', ')}</li>`).join('') + '</ul>';
                            } else if (xhr.responseJSON?.error) {
                                errorMessage = xhr.responseJSON.error + ': ' + xhr.responseJSON.details;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                html: errorMessage,
                                confirmButtonText: 'Aceptar',
                                customClass: { confirmButton: 'btn btn-primary' },
                            });
                        },
                    });
                });

                // Handle edit form submission
                $('#submitEdit').on('click', function() {
                    if (!validateForm('#editForm', true)) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Por favor, corrige los errores en el formulario.',
                            confirmButtonText: 'Aceptar',
                            customClass: { confirmButton: 'btn btn-primary' },
                        });
                        return;
                    }

                    var id = $('#edit_id').val();
                    var formData = $('#editForm').serialize();
                    console.log('Submitting edit form data:', formData); // Depuración
                    $.ajax({
                        url: window.IncidenciasRoutes.update.replace(':id', id),
                        method: 'PUT',
                        data: formData,
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function(response) {
                            $('#editModal').modal('hide');
                            $('.modal-backdrop').remove();
                            $('body').removeClass('modal-open').css('padding-right', '');
                            Swal.fire({
                                icon: 'success', // Aseguramos que sea 'success'
                                title: 'Éxito', // Texto en español
                                text: response.message,
                                confirmButtonText: 'Aceptar',
                                customClass: { confirmButton: 'btn btn-primary' },
                            });
                            table.ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            console.error('Update error:', xhr.responseText);
                            let errorMessage = 'Error al actualizar la incidencia.';
                            if (xhr.status === 422 && xhr.responseJSON?.details) {
                                errorMessage = '<ul>' + Object.entries(xhr.responseJSON.details).map(([field, errors]) => `<li>${field}: ${errors.join(', ')}</li>`).join('') + '</ul>';
                            } else if (xhr.responseJSON?.error) {
                                errorMessage = xhr.responseJSON.error + ': ' + xhr.responseJSON.details;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                html: errorMessage,
                                confirmButtonText: 'Aceptar',
                                customClass: { confirmButton: 'btn btn-primary' },
                            });
                        },
                    });
                });

                // Handle delete button (if still needed)
                $('#incidenciasTable').on('click', '.delete-incidencia', function() {
                    var id = $(this).data('id');
                    Swal.fire({
                        icon: 'warning',
                        title: 'Confirmar eliminación',
                        text: '¿Estás seguro de que deseas eliminar esta incidencia?',
                        showCancelButton: true,
                        confirmButtonText: 'Eliminar',
                        cancelButtonText: 'Cancelar',
                        customClass: {
                            confirmButton: 'btn btn-danger',
                            cancelButton: 'btn btn-secondary',
                        },
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: window.IncidenciasRoutes.destroy.replace(':id', id),
                                method: 'DELETE',
                                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                                success: function(response) {
                                    Swal.fire({
                                        icon: 'success', // Aseguramos que sea 'success'
                                        title: 'Éxito', // Texto en español
                                        text: response.message,
                                        confirmButtonText: 'Aceptar',
                                        customClass: { confirmButton: 'btn btn-primary' },
                                    });
                                    table.ajax.reload(null, false);
                                },
                                error: function(xhr) {
                                    console.error('Delete error:', xhr.responseText);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Error al eliminar la incidencia: ' + (xhr.responseJSON?.error || 'Error desconocido.'),
                                        confirmButtonText: 'Aceptar',
                                        customClass: { confirmButton: 'btn btn-primary' },
                                    });
                                },
                            });
                        }
                    });
                });

                return table;
            } catch (e) {
                console.error('DataTable initialization error:', e);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al inicializar la tabla: ' + e.message,
                    confirmButtonText: 'Aceptar',
                    customClass: { confirmButton: 'btn btn-primary' },
                });
            }
        }

        // Form validation
        function validateForm(formId, isEdit = false) {
            let isValid = true;
            $(formId).find('.is-invalid').removeClass('is-invalid');
            $(formId).find('.invalid-feedback').text('');

            const prefix = isEdit ? 'edit_' : 'create_';
            const requiredFields = isEdit ? [
                `${prefix}incidencia`,
                `${prefix}fecha_incidencia`,
                `${prefix}hora_incidencia`
            ] : [
                `${prefix}cedula`,
                `${prefix}incidencia`,
                `${prefix}fecha_incidencia`,
                `${prefix}hora_incidencia`
            ];

            requiredFields.forEach(field => {
                const $input = $(`#${field}`);
                if ($input.length === 0) {
                    console.warn(`Field ${field} not found in form ${formId}`);
                    return;
                }
                if (!$input.val().trim()) {
                    isValid = false;
                    $input.addClass('is-invalid');
                    $input.siblings('.invalid-feedback').text('Este campo es obligatorio.');
                }
            });

            if (!isEdit && $(`#${prefix}cedula`).val() && !/^\d{7,8}$/.test($(`#${prefix}cedula`).val())) {
                isValid = false;
                $(`#${prefix}cedula`).addClass('is-invalid');
                $(`#${prefix}cedula`).siblings('.invalid-feedback').text('La cédula debe ser un número de 7 u 8 dígitos.');
            }

            if ($(`#${prefix}incidencia`).val() && $(`#${prefix}incidencia`).val().length > 300) {
                isValid = false;
                $(`#${prefix}incidencia`).addClass('is-invalid');
                $(`#${prefix}incidencia`).siblings('.invalid-feedback').text('La incidencia no debe exceder 300 caracteres.');
            }

            if ($(`#${prefix}fecha_incidencia`).val() && !/^\d{2}\/\d{2}\/\d{2}$/.test($(`#${prefix}fecha_incidencia`).val())) {
                isValid = false;
                $(`#${prefix}fecha_incidencia`).addClass('is-invalid');
                $(`#${prefix}fecha_incidencia`).siblings('.invalid-feedback').text('La fecha debe tener el formato DD/MM/AA.');
            }

            if ($(`#${prefix}hora_incidencia`).val() && !/^\d{2}:\d{2}$/.test($(`#${prefix}hora_incidencia`).val())) {
                isValid = false;
                $(`#${prefix}hora_incidencia`).addClass('is-invalid');
                $(`#${prefix}hora_incidencia`).siblings('.invalid-feedback').text('La hora debe tener el formato HH:MM.');
            }

            return isValid;
        }

        // Initialize DataTable
        initializeDataTable();

        // Clean up modals
        $('#createModal, #editModal').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset();
            $(this).find('.is-invalid').removeClass('is-invalid');
            $(this).find('.invalid-feedback').text('');
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css('padding-right', '');
        });
    });
})(jQuery);
