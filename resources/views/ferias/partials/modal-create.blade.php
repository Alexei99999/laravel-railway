<!-- ferias.partials.modal-create -->
<div class="modal fade custom-modal" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">Crear Nueva Feria</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="createForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="create_estado">Estado *</label>
                                <input type="text" class="form-control" id="create_estado" name="estado" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="create_municipio">Municipio *</label>
                                <input type="text" class="form-control" id="create_municipio" name="municipio" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="create_parroquia">Parroquia *</label>
                                <input type="text" class="form-control" id="create_parroquia" name="parroquia" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="create_nombre_pto">Nombre del Punto *</label>
                                <input type="text" class="form-control" id="create_nombre_pto" name="nombre_pto" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="create_cedula">Cédula *</label>
                                <input type="text" class="form-control" id="create_cedula" name="cedula" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="create_apellidos">Apellidos *</label>
                                <input type="text" class="form-control" id="create_apellidos" name="apellidos" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="create_nombres">Nombres *</label>
                                <input type="text" class="form-control" id="create_nombres" name="nombres" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="create_telefono">Teléfono *</label>
                                <input type="text" class="form-control" id="create_telefono" name="telefono" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="create_correo">Correo</label>
                                <input type="email" class="form-control" id="create_correo" name="correo">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <!-- Campos ocultos con valores predeterminados -->
                        <input type="hidden" id="create_rectoria" name="rectoria" value="ACME NOGAL">
                        <input type="hidden" id="create_e_registro" name="e_registro" value="Activo">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" form="createForm">Guardar</button>
            </div>
        </div>
    </div>
</div>
