$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#usuariosTable').DataTable({
        responsive: true,
        language: {
            url: '/js/es-ES.json'
        },
        serverSide: false,
        processing: false
    });

    $('#createModal').on('show.bs.modal', function() {
        $('#createForm')[0].reset();
        $('#create_roles').empty();
        $.ajax({
            url: window.UsuarioRoutes.create,
            method: 'GET',
            success: function(response) {
                if (Array.isArray(response.roles)) {
                    response.roles.forEach(function(role) {
                        $('#create_roles').append(`
                            <div class="col-12 col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="roles[]" value="${role}" id="create_role_${role}">
                                    <label class="form-check-label" for="create_role_${role}">${role}</label>
                                </div>
                            </div>
                        `);
                    });
                } else {
                    console.error('response.roles no es un arreglo:', response.roles);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudieron cargar los roles.',
                        confirmButtonText: 'Aceptar'
                    });
                }
            },
            error: function(xhr) {
                console.error('Error al cargar roles:', xhr.responseJSON);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'No se pudieron cargar los roles.',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    });

    $('#createForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serializeArray();
        console.log('Datos enviados (create):', formData); // Depuración
        $.ajax({
            url: window.UsuarioRoutes.store,
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: response.message,
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                console.error('Error al crear usuario:', xhr.responseJSON);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'No se pudo crear el usuario.',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    });

    $('.edit-user').on('click', function() {
        var id = $(this).data('id');
        var editUrl = window.UsuarioRoutes.edit.replace(':id', id);
        $.ajax({
            url: editUrl,
            method: 'GET',
            success: function(response) {
                if (response.user && Array.isArray(response.available_roles)) {
                    $('#edit_id').val(response.user.id);
                    $('#edit_name').val(response.user.name);
                    $('#edit_email').val(response.user.email);
                    $('#edit_password').val('');
                    $('#edit_confirm_password').val('');
                    $('#edit_roles').empty();
                    response.available_roles.forEach(function(role) {
                        var checked = response.user.roles.includes(role) ? 'checked' : '';
                        $('#edit_roles').append(`
                            <div class="col-12 col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="roles[]" value="${role}" id="edit_role_${role}" ${checked}>
                                    <label class="form-check-label" for="edit_role_${role}">${role}</label>
                                </div>
                            </div>
                        `);
                    });
                    $('#editModal').modal('show');
                } else {
                    console.error('response.available_roles no es un arreglo:', response.available_roles);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudieron cargar los roles.',
                        confirmButtonText: 'Aceptar'
                    });
                }
            },
            error: function(xhr) {
                console.error('Error al cargar usuario:', xhr.responseJSON);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'No se pudo cargar el usuario.',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    });

    $('#editForm').on('submit', function(e) {
        e.preventDefault();
        var id = $('#edit_id').val();
        var updateUrl = window.UsuarioRoutes.update.replace(':id', id);
        const formData = $(this).serializeArray();
        console.log('Datos enviados (update):', formData); // Depuración
        $.ajax({
            url: updateUrl,
            method: 'PUT',
            data: formData,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: response.message,
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                console.error('Error al actualizar usuario:', xhr.responseJSON);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'No se pudo actualizar el usuario.',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    });

    $('.delete-form').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        Swal.fire({
            icon: 'warning',
            title: '¿Estás seguro?',
            text: '¿Deseas eliminar este usuario? Esta acción no se puede deshacer.',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: $(form).attr('action'),
                    method: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Éxito',
                                text: response.message,
                                confirmButtonText: 'Aceptar'
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error('Error al eliminar usuario:', xhr.responseJSON);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'No se pudo eliminar el usuario.',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                });
            }
        });
    });
});
