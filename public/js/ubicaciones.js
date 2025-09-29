$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    window.ubicacionesTable = $('#ubicacionesTable').DataTable({
        ajax: {
            url: '/ubicaciones/datatables',
            error: function (xhr, error, thrown) {
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
            { data: 'estado', responsivePriority: 1 },
            { data: 'cod_est', responsivePriority: 2 },
            { data: 'municipio', responsivePriority: 3 },
            { data: 'cod_mun', responsivePriority: 4 },
            { data: 'parroquia', responsivePriority: 5 },
            { data: 'cod_parroq', responsivePriority: 6 },
            { data: 'circuns', responsivePriority: 7 },
            { data: 'e_registro', responsivePriority: 1 }
        ],
        language: {
            url: '/assets/datatables/i18n/es-ES.json'
        },
        responsive: true,
        autoWidth: false,
        lengthChange: false,
        dom: 'frtip',
        createdRow: function(row, data, dataIndex) {
            if (data.e_registro === 'Activo') {
                $(row).addClass('row-activo');
            } else if (data.e_registro === 'Inactivo') {
                $(row).addClass('row-inactivo');
            }
        }
    });

    $('#ubicacionesTable tbody').on('click', 'tr', function() {
        var data = window.ubicacionesTable.row(this).data();
        if (data) {
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
        let message = errors.message || 'Error desconocido';
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
        $.ajax({
            url: '/ubicaciones/' + id + '/edit',
            method: 'GET',
            success: function(data) {
                $('#editForm [name="estado"]').val(data.estado);
                $('#editForm [name="cod_est"]').val(data.cod_est || '');
                $('#editForm [name="municipio"]').val(data.municipio);
                $('#editForm [name="cod_mun"]').val(data.cod_mun || '');
                $('#editForm [name="parroquia"]').val(data.parroquia);
                $('#editForm [name="cod_parroq"]').val(data.cod_parroq || '');
                $('#editForm [name="circuns"]').val(data.circuns);
                $('#editForm [name="e_registro"]').val(data.e_registro);
                $('#editForm').attr('data-action', '/ubicaciones/' + id);
                $('#editForm').attr('data-id', id);
                $('#editModal').modal('show');
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

        $.ajax({
            url: form.data('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                showSuccess(response.message || 'Ubicación creada exitosamente');
                $('#createModal').modal('hide');
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                window.ubicacionesTable.ajax.reload();
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
                showSuccess(response.message || 'Ubicación actualizada exitosamente');
                $('#editModal').modal('hide');
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                window.ubicacionesTable.ajax.reload();
            },
            error: function(xhr) {
                console.error('Update error:', xhr.responseText);
                showFormErrors('editForm', xhr.responseJSON);
            }
        });
    });

    window.deleteUbicacion = function() {
        const id = $('#editForm').attr('data-id');
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'La ubicación será marcada como inactiva.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Inactivar',
            cancelButtonText: 'Cancelar',
            dangerMode: true
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/ubicaciones/' + id,
                    method: 'DELETE',
                    success: function(response) {
                        showSuccess(response.message || 'Ubicación inactivada exitosamente');
                        $('#editModal').modal('hide');
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                        window.ubicacionesTable.ajax.reload();
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
