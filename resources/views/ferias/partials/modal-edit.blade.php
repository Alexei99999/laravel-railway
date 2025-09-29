<div class="modal fade custom-modal" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Editar Feria</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_estado">Estado *</label>
                                <input type="text" class="form-control" id="edit_estado" name="estado" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_municipio">Municipio *</label>
                                <input type="text" class="form-control" id="edit_municipio" name="municipio" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_parroquia">Parroquia *</label>
                                <input type="text" class="form-control" id="edit_parroquia" name="parroquia" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_nombre_pto">Nombre del Punto *</label>
                                <input type="text" class="form-control" id="edit_nombre_pto" name="nombre_pto" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_cedula">Cédula *</label>
                                <input type="text" class="form-control" id="edit_cedula" name="cedula" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_apellidos">Apellidos *</label>
                                <input type="text" class="form-control" id="edit_apellidos" name="apellidos" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_nombres">Nombres *</label>
                                <input type="text" class="form-control" id="edit_nombres" name="nombres" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_telefono">Teléfono *</label>
                                <input type="text" class="form-control" id="edit_telefono" name="telefono" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_correo">Correo</label>
                                <input type="email" class="form-control" id="edit_correo" name="correo">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_rectoria">Rectoría</label>
                                <input type="text" class="form-control" id="edit_rectoria" name="rectoria" value="ACME NOGAL" readonly>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_status_contact1">Status Contacto 1</label>
                                <select class="form-control" id="edit_status_contact1" name="status_contact1">
                                    <option value="">Seleccione</option>
                                    <option value="No contactado">No contactado</option>
                                    <option value="Contactado">Contactado</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_fecha_hora1">Fecha y Hora 1</label>
                                <input type="text" class="form-control flatpickr-datetime" id="edit_fecha_hora1" name="fecha_hora1">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_status_contact2">Status Contacto 2</label>
                                <select class="form-control" id="edit_status_contact2" name="status_contact2">
                                    <option value="">Seleccione</option>
                                    <option value="No contactado">No contactado</option>
                                    <option value="Contactado">Contactado</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_fecha_hora2">Fecha y Hora 2</label>
                                <input type="text" class="form-control flatpickr-datetime" id="edit_fecha_hora2" name="fecha_hora2">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_status_contact3">Status Contacto 3</label>
                                <select class="form-control" id="edit_status_contact3" name="status_contact3">
                                    <option value="">Seleccione</option>
                                    <option value="No contactado">No contactado</option>
                                    <option value="Contactado">Contactado</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_fecha_hora3">Fecha y Hora 3</label>
                                <input type="text" class="form-control flatpickr-datetime" id="edit_fecha_hora3" name="fecha_hora3">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_disponibilidad">Disponibilidad</label>
                                <select class="form-control" id="edit_disponibilidad" name="disponibilidad">
                                    <option value="">Seleccione</option>
                                    <option value="Sin información">Sin información</option>
                                    <option value="Trabajará">Trabajará</option>
                                    <option value="No trabajará">No trabajará</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_incidencias">Incidencias</label>
                                <input type="text" class="form-control" id="edit_incidencias" name="incidencias">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_fecha_incidencia">Fecha Incidencia</label>
                                <input type="text" class="form-control flatpickr-date" id="edit_fecha_incidencia" name="fecha_incidencia">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_hora_incidencia">Hora Incidencia</label>
                                <input type="text" class="form-control flatpickr-datetime" id="edit_hora_incidencia" name="hora_incidencia">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_observaciones">Observaciones</label>
                                <textarea class="form-control" id="edit_observaciones" name="observaciones"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_e_registro">Estado Registro</label>
                                <select class="form-control" id="edit_e_registro" name="e_registro">
                                    <option value="">Seleccione</option>
                                    <option value="Activo">Activo</option>
                                    <option value="Inactivo">Inactivo</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" form="editForm">Guardar</button>
            </div>
        </div>
    </div>
</div>
