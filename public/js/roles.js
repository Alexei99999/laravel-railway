$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#rolesTable').DataTable({
        responsive: true,
        language: {
            url: '/js/es-ES.json'
        },
        serverSide: false,
        processing: false
    });

    $('#createModal').on('show.bs.modal', function() {
        $('#createForm')[0].reset();
        $('#create_permissions').empty();
        $.ajax({
            url: window.RoleRoutes.create,
            method: 'GET',
            success: function(response) {
                // Agrupar permisos por prefijo
                const groupedPermissions = groupPermissions(response.permissions);
                Object.keys(groupedPermissions).sort().forEach(function(category) {
                    $('#create_permissions').append(`
                        <div class="permission-group mb-3">
                            <h5 class="permission-category mt-3">${category}</h5>
                            <div class="row">
                                ${groupedPermissions[category].map(permission => `
                                    <div class="col-12 col-md-6">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="permission[]" value="${permission}" id="create_perm_${permission}">
                                            <label class="form-check-label" for="create_perm_${permission}">${permission}</label>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    `);
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'No se pudieron cargar los permisos.',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    });

    $('#createForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: window.RoleRoutes.store,
            method: 'POST',
            data: $(this).serialize(),
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
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'No se pudo crear el rol.',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    });

    $('.edit-role').on('click', function() {
        var id = $(this).data('id');
        var editUrl = window.RoleRoutes.edit.replace(':id', id);
        $.ajax({
            url: editUrl,
            method: 'GET',
            success: function(response) {
                $('#edit_id').val(response.role.id);
                $('#edit_name').val(response.role.name);
                $('#edit_permissions').empty();
                // Agrupar permisos por prefijo
                const groupedPermissions = groupPermissions(response.available_permissions);
                Object.keys(groupedPermissions).sort().forEach(function(category) {
                    $('#edit_permissions').append(`
                        <div class="permission-group mb-3">
                            <h5 class="permission-category mt-3">${category}</h5>
                            <div class="row">
                                ${groupedPermissions[category].map(permission => {
                                    const checked = response.role.permissions.includes(permission) ? 'checked' : '';
                                    return `
                                        <div class="col-12 col-md-6">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="permission[]" value="${permission}" id="edit_perm_${permission}" ${checked}>
                                                <label class="form-check-label" for="edit_perm_${permission}">${permission}</label>
                                            </div>
                                        </div>
                                    `;
                                }).join('')}
                            </div>
                        </div>
                    `);
                });
                $('#editModal').modal('show');
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'No se pudo cargar el rol.',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    });

    $('#editForm').on('submit', function(e) {
        e.preventDefault();
        var id = $('#edit_id').val();
        var updateUrl = window.RoleRoutes.update.replace(':id', id);
        $.ajax({
            url: updateUrl,
            method: 'PUT',
            data: $(this).serialize(),
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
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'No se pudo actualizar el rol.',
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
            text: '¿Deseas eliminar este rol? Esta acción no se puede deshacer.',
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
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'No se pudo eliminar el rol.',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                });
            }
        });
    });

    // Función para agrupar permisos por prefijo
    function groupPermissions(permissions) {
        const grouped = {};
        permissions.forEach(permission => {
            // Extraer el prefijo (antes del primer guion) o usar "Otros" si no hay guion
            const prefix = permission.includes('-') ? permission.split('-')[0] : 'Otros';
            // Capitalizar el prefijo
            const category = prefix.charAt(0).toUpperCase() + prefix.slice(1);
            if (!grouped[category]) {
                grouped[category] = [];
            }
            grouped[category].push(permission);
        });
        // Ordenar los permisos dentro de cada categoría
        Object.keys(grouped).forEach(category => {
            grouped[category].sort();
        });
        return grouped;
    }
});
