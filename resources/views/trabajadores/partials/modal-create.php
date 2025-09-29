<!-- Modal de creación (Bootstrap 4 compatible) -->
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <!-- Modal header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Nuevo Trabajador</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Modal body -->
            <form id="createForm" action="{{ route('trabajadores.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="cedula" class="font-weight-bold">Cédula*</label>
                            <input type="text" name="cedula" id="cedula" class="form-control" placeholder="Ej: 12345678" required maxlength="8">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="nombre1" class="font-weight-bold">Primer Nombre*</label>
                            <input type="text" name="nombre1" id="nombre1" class="form-control" placeholder="Ej: Juan" required maxlength="15">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="nombre2" class="font-weight-bold">Segundo Nombre</label>
                            <input type="text" name="nombre2" id="nombre2" class="form-control" placeholder="Ej: Carlos" maxlength="15">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="e_mail" class="font-weight-bold">Email*</label>
                            <input type="email" name="e_mail" id="e_mail" class="form-control" placeholder="Ej: ejemplo@correo.com" required maxlength="50">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="apellido1" class="font-weight-bold">Primer Apellido*</label>
                            <input type="text" name="apellido1" id="apellido1" class="form-control" placeholder="Ej: Pérez" required maxlength="20">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="apellido2" class="font-weight-bold">Segundo Apellido</label>
                            <input type="text" name="apellido2" id="apellido2" class="form-control" placeholder="Ej: López" maxlength="20">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="telefono" class="font-weight-bold">Teléfono</label>
                            <input type="text" name="telefono" id="telefono" class="form-control" placeholder="Ej: 04121234567" maxlength="11">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="rol" class="font-weight-bold">Rol*</label>
                            <input type="text" name="rol" id="rol" class="form-control" placeholder="Ej: Administrador" required maxlength="21">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="e_registro" class="font-weight-bold">Estado Registro*</label>
                            <input type="text" name="e_registro" id="e_registro" class="form-control" placeholder="Ej: Activo" required maxlength="8">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
