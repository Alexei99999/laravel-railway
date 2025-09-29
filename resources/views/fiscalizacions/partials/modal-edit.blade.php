<div class="modal fade custom-modal" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Editar Fiscalización</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_estado">Estado</label>
                            <input type="text" class="form-control" id="edit_estado" name="estado">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_municipio">Municipio</label>
                            <input type="text" class="form-control" id="edit_municipio" name="municipio">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_parroquia">Parroquia</label>
                            <input type="text" class="form-control" id="edit_parroquia" name="parroquia">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_nombre_pto">Nombre del Punto</label>
                            <input type="text" class="form-control" id="edit_nombre_pto" name="nombre_pto">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_cedula">Cédula <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_cedula" name="cedula" required pattern="\d{7,8}" readonly>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_apellidos">Apellidos</label>
                            <input type="text" class="form-control" id="edit_apellidos" name="apellidos">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_nombres">Nombres</label>
                            <input type="text" class="form-control" id="edit_nombres" name="nombres">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_telefono">Teléfono</label>
                            <input type="text" class="form-control" id="edit_telefono" name="telefono" pattern="\d{10,11}">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_correo">Correo</label>
                            <input type="email" class="form-control" id="edit_correo" name="correo">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_e_registro">Estado Registro</label>
                            <select class="form-control" id="edit_e_registro" name="e_registro">
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </form>
            </div>
        </div>
    </div>
</div>
