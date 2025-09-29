$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    window.trabajadoresTable = $('#trabajadoresTable').DataTable({
        ajax: {
            url: '/trabajadores/datatables',
            error: function (xhr, error, thrown) {
                console.error('DataTables AJAX error:', xhr, error, thrown);
                alert('Error al cargar los datos: ' + (xhr.status === 404 ? 'Ruta no encontrada' : xhr.responseJSON?.error || 'Desconocido'));
            }
        },
        columns: [
            { data: 'cedula', responsivePriority: 1 },
            { data: 'nombres', responsivePriority: 2 },
            { data: 'apellidos', responsivePriority: 3 },
            { data: 'telefono', responsivePriority: 5 },
            { data: 'e_mail', responsivePriority: 4 },
            { data: 'rol', responsivePriority: 6 },
            { data: 'e_registro', responsivePriority: 1 }
        ],
        language: {
            url: '/datatables/i18n/es-ES.json'
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

    $('#trabajadoresTable tbody').on('click', 'tr', function() {
        var data = window.trabajadoresTable.row(this).data();
        if (data) {
            openEditModal(data.id);
        }
    });

    function showSuccess(message) {
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: message,
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false
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
            text: message,
            confirmButtonText: 'Aceptar'
        });
    }

    window.openEditModal = function(id) {
        $.ajax({
            url: '/trabajadores/' + id + '/edit',
            method: 'GET',
            success: function(data) {
                $('#editForm [name="cedula"]').val(data.cedula);
                $('#editForm [name="nombre1"]').val(data.nombre1);
                $('#editForm [name="nombre2"]').val(data.nombre2 || '');
                $('#editForm [name="apellido1"]').val(data.apellido1);
                $('#editForm [name="apellido2"]').val(data.apellido2 || '');
                $('#editForm [name="telefono"]').val(data.telefono || '');
                $('#editForm [name="e_mail"]').val(data.e_mail);
                $('#editForm [name="rol"]').val(data.rol);
                $('#editForm [name="e_registro"]').val(data.e_registro);
                $('#editForm').attr('data-action', '/trabajadores/' + id);
                $('#editForm').attr('data-id', id);
                $('#editModal').modal('show');
            },
            error: function(xhr) {
                console.error(xhr.responseText);
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
                showSuccess('Trabajador creado exitosamente');
                $('#createModal').modal('hide');
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                window.trabajadoresTable.ajax.reload();
                form[0].reset();
            },
            error: function(xhr) {
                console.error(xhr.responseText);
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
                showSuccess('Trabajador actualizado exitosamente');
                $('#editModal').modal('hide');
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                window.trabajadoresTable.ajax.reload();
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                showFormErrors('editForm', xhr.responseJSON);
            }
        });
    });

    window.deleteTrabajador = function() {
        const id = $('#editForm').attr('data-id');
        Swal.fire({
            icon: 'warning',
            title: '¿Estás seguro?',
            text: 'El trabajador será marcado como inactivo.',
            showCancelButton: true,
            confirmButtonText: 'Inactivar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '//trabajadores/' + id,
                    method: 'DELETE',
                    success: function(response) {
                        showSuccess('Trabajador inactivado exitosamente');
                        $('#editModal').modal('hide');
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                        window.trabajadoresTable.ajax.reload();
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
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
