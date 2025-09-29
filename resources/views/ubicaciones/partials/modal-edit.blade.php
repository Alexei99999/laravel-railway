<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 24rem;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Editar Ubicación</h5>
                <button type="button" class="modal-close-btn" data-dismiss="modal" aria-label="Close">×</button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <div class="form-group">
                        <label for="estado" class="form-label">Estado</label>
                        <input type="text" class="form-control" name="estado" id="estado" placeholder="Ej. Distrito Capital">
                    </div>
                    <div class="form-group">
                        <label for="cod_est" class="form-label">Cód. Estado</label>
                        <input type="text" class="form-control" name="cod_est" id="cod_est" placeholder="Ej. 01">
                    </div>
                    <div class="form-group">
                        <label for="municipio" class="form-label">Municipio</label>
                        <input type="text" class="form-control" name="municipio" id="municipio" placeholder="Ej. Libertador">
                    </div>
                    <div class="form-group">
                        <label for="cod_mun" class="form-label">Cód. Municipio</label>
                        <input type="text" class="form-control" name="cod_mun" id="cod_mun" placeholder="Ej. 01">
                    </div>
                    <div class="form-group">
                        <label for="parroquia" class="form-label">Parroquia</label>
                        <input type="text" class="form-control" name="parroquia" id="parroquia" placeholder="Ej. La Candelaria">
                    </div>
                    <div class="form-group">
                        <label for="cod_parroq" class="form-label">Cód. Parroquia</label>
                        <input type="text" class="form-control" name="cod_parroq" id="cod_parroq" placeholder="Ej. 01">
                    </div>
                    <div class="form-group">
                        <label for="circuns" class="form-label">Circuns.</label>
                        <input type="text" class="form-control" name="circuns" id="circuns" placeholder="Ej. 01">
                    </div>
                    <div class="form-group">
                        <label for="e_registro" class="form-label">Estado Registro</label>
                        <select class="form-control form-control-select" name="e_registro" id="e_registro">
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <button type="button" class="btn btn-danger" onclick="deleteUbicacion()">Inactivar</button>
                </form>
            </div>
        </div>
    </div>
</div>
