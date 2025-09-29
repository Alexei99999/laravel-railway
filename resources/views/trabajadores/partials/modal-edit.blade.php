<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Editar Trabajador</h5>
                <button type="button" class="modal-close-btn" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="cedula_edit" class="form-label">Cédula</label>
                        <input type="text" name="cedula" id="cedula_edit" class="form-control" placeholder="Cédula" required>
                    </div>
                    <div class="form-group">
                        <label for="nombre1_edit" class="form-label">Primer Nombre</label>
                        <input type="text" name="nombre1" id="nombre1_edit" class="form-control" placeholder="Primer Nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="nombre2_edit" class="form-label">Segundo Nombre</label>
                        <input type="text" name="nombre2" id="nombre2_edit" class="form-control" placeholder="Segundo Nombre">
                    </div>
                    <div class="form-group">
                        <label for="apellido1_edit" class="form-label">Primer Apellido</label>
                        <input type="text" name="apellido1" id="apellido1_edit" class="form-control" placeholder="Primer Apellido" required>
                    </div>
                    <div class="form-group">
                        <label for="apellido2_edit" class="form-label">Segundo Apellido</label>
                        <input type="text" name="apellido2" id="apellido2_edit" class="form-control" placeholder="Segundo Apellido">
                    </div>
                    <div class="form-group">
                        <label for="telefono_edit" class="form-label">Teléfono</label>
                        <input type="text" name="telefono" id="telefono_edit" class="form-control" placeholder="Teléfono">
                    </div>
                    <div class="form-group">
                        <label for="e_mail_edit" class="form-label">Email</label>
                        <input type="email" name="e_mail" id="e_mail_edit" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <label for="rol_edit" class="form-label">Rol</label>
                        <input type="text" name="rol" id="rol_edit" class="form-control" placeholder="Rol" required>
                    </div>
                    <div class="form-group">
                        <label for="e_registro_edit" class="form-label">Estado Registro</label>
                        <select name="e_registro" id="e_registro_edit" class="form-control-select" required>
                            <option value="" disabled>Seleccione una opción</option>
                            @foreach ($statustrabs as $statustrab)
                                <option value="{{ $statustrab->name }}">{{ $statustrab->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" onclick="deleteTrabajador()">Inactivar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Trabajador</button>
                </div>
            </form>
        </div>
    </div>
</div>