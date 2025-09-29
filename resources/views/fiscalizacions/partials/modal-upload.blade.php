<div class="modal fade custom-modal" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Importar Registros desde CSV</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <form id="uploadForm">
                    <div class="form-group">
                        <label for="upload_file" class="form-label">Selecciona el archivo CSV</label>
                        <input type="file" class="form-control" id="upload_file" name="upload_file" accept=".csv, .txt, .xls, .xlsx">
                        <div class="invalid-feedback"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="padding: 10px; border-top: 1px solid #dee2e6;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="uploadCsvButton">Subir</button>
            </div>
        </div>
    </div>
</div>
