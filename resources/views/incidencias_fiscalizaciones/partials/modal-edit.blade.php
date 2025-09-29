<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-3">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editModalLabel">Editar Incidencia</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editForm">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label for="edit_cedula" class="form-label">Cédula del Trabajador</label>
                            <input type="text" id="edit_cedula" name="cedula" class="form-control" readonly style="border-radius: 8px; background-color: #f9fafb;">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="edit_trabajador" class="form-label">Trabajador</label>
                            <input type="text" id="edit_trabajador" name="trabajador" class="form-control" readonly style="border-radius: 8px; background-color: #f9fafb;">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="edit_ubicacion" class="form-label">Ubicación</label>
                            <input type="text" id="edit_ubicacion" name="ubicacion" class="form-control" readonly style="border-radius: 8px; background-color: #f9fafb;">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="edit_contacto" class="form-label">Contacto</label>
                            <input type="text" id="edit_contacto" name="contacto" class="form-control" readonly style="border-radius: 8px; background-color: #f9fafb;">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="edit_incidencia" class="form-label">Incidencia *</label>
                            <textarea id="edit_incidencia" name="incidencia" class="form-control" rows="3" maxlength="300" style="border-radius: 8px;"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="edit_fecha_incidencia" class="form-label">Fecha Incidencia *</label>
                            <input type="text" id="edit_fecha_incidencia" name="fecha_incidencia" class="form-control flatpickr-date" placeholder="DD/MM/AA" style="border-radius: 8px; height: 38px;">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="edit_hora_incidencia" class="form-label">Hora Incidencia *</label>
                            <input type="text" id="edit_hora_incidencia" name="hora_incidencia" class="form-control flatpickr-time" placeholder="HH:MM" style="border-radius: 8px; height: 38px;">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" id="submitEdit" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>
