<div class="modal fade custom-modal" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width: 90vw;">
        <div class="modal-content shadow-lg border-0 rounded-3" style="background-color: #ffffff;">
            <div class="modal-header" style="background: linear-gradient(to right, #e6f0fa, #edf2f7); border-bottom: 1px solid #d1d9e6; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 20px 25px; text-align: center;">
                <h5 class="modal-title" id="createModalLabel" style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 1.75rem; font-weight: 700; color: #1a2e44; letter-spacing: 0.5px; margin: 0 auto; max-width: 80%;">
                    Crear Nueva Incidencia
                </h5>
                <button type="button" class="close btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 1.5rem; color: #4a5568; position: absolute; right: 20px; top: 15px;"></button>
            </div>
            <div class="modal-body p-4" style="background-color: #ffffff; max-height: 500px; overflow-y: auto; text-align: center;">
                <form id="createForm">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="create_cedula" class="form-label" style="font-weight: 600; color: #1a202c;">Cédula o Nombre del Trabajador *</label>
                            <select id="create_cedula" name="cedula" class="form-select select2-custom" style="width: 100%; border-radius: 8px; border-color: #ced4da; height: 38px;">
                                <option value="">Busca por cédula o nombre...</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="create_trabajador" class="form-label" style="font-weight: 600; color: #1a202c;">Trabajador</label>
                            <input type="text" id="create_trabajador" name="trabajador" class="form-control" readonly style="border-radius: 8px; background-color: #f9fafb;">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="create_ubicacion" class="form-label" style="font-weight: 600; color: #1a202c;">Ubicación</label>
                            <input type="text" id="create_ubicacion" name="ubicacion" class="form-control" readonly style="border-radius: 8px; background-color: #f9fafb;">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="create_contacto" class="form-label" style="font-weight: 600; color: #1a202c;">Contacto</label>
                            <input type="text" id="create_contacto" name="contacto" class="form-control" readonly style="border-radius: 8px; background-color: #f9fafb;">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="create_incidencia" class="form-label" style="font-weight: 600; color: #1a202c;">Incidencia *</label>
                            <textarea id="create_incidencia" name="incidencia" class="form-control" rows="3" maxlength="300" style="border-radius: 8px;"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="create_fecha_incidencia" class="form-label" style="font-weight: 600; color: #1a202c;">Fecha Incidencia *</label>
                            <input type="text" id="create_fecha_incidencia" name="fecha_incidencia" class="form-control flatpickr-date" placeholder="DD/MM/AA" style="border-radius: 8px; height: 38px;">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="create_hora_incidencia" class="form-label" style="font-weight: 600; color: #1a202c;">Hora Incidencia *</label>
                            <input type="text" id="create_hora_incidencia" name="hora_incidencia" class="form-control flatpickr-time" placeholder="HH:MM" style="border-radius: 8px; height: 38px;">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="background-color: #f9fafb; border-top: 1px solid #d1d9e6; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 12px; justify-content: center; gap: 10px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background-color: #a0aec0; border-color: #a0aec0; border-radius: 8px; padding: 8px 20px; font-weight: 500;">Cerrar</button>
                <button type="button" id="submitCreate" class="btn btn-primary" style="background-color: #4a90e2; border-color: #4a90e2; border-radius: 8px; padding: 8px 20px; font-weight: 500; transition: background-color 0.2s;">Guardar</button>
            </div>
        </div>
    </div>
</div>
