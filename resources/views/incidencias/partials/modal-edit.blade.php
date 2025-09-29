<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-slate-800 text-white">
                <h5 class="modal-title" id="editModalLabel">Editar Incidencia</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="editForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="trabajador" class="form-label text-slate-600">Trabajador</label>
                        <select name="trabajador" class="form-control" required>
                            <option value="">Seleccione un trabajador</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="ubicacion" class="form-label text-slate-600">Ubicación</label>
                        <select name="ubicacion" class="form-control" required>
                            <option value="">Seleccione una ubicación</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="e_contact" class="form-label text-slate-600">Contacto</label>
                        <select name="e_contact" class="form-control" required>
                            <option value="Contactado">Contactado</option>
                            <option value="No contactado">No contactado</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="fecha_rep" class="form-label text-slate-600">Fecha Reporte</label>
                        <input type="text" name="fecha_rep" class="form-control datepicker" required placeholder="DD/MM/YY">
                    </div>
                    <div class="form-group">
                        <label for="e_disponib" class="form-label text-slate-600">Estado</label>
                        <select name="e_disponib" class="form-control" required>
                            <option value="Trabaja">Trabaja</option>
                            <option value="No trabaja">No trabaja</option>
                            <option value="Sin información">Sin información</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="fecha_e_disponib" class="form-label text-slate-600">Fecha Estado</label>
                        <input type="text" name="fecha_e_disponib" class="form-control datepicker" placeholder="DD/MM/YY">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <button type="button" class="btn btn-danger" onclick="deleteIncidencia()">Inactivar</button>
                </div>
            </form>
        </div>
    </div>
</div>
