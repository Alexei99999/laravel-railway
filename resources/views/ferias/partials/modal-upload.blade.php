<div class="modal fade custom-modal" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);">
            <div class="modal-header" style="background: linear-gradient(to right, #e6f0fa, #edf2f7); border-bottom: 1px solid #d1d9e6; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 15px 20px;">
                <h5 class="modal-title" id="uploadModalLabel" style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 1.5rem; font-weight: 600; color: #1a202c; margin: 0;">
                    Carga Masiva
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="font-size: 1.5rem; color: #4a5568;">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" style="background-color: #ffffff; padding: 25px; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #4a5568;">
                <form id="uploadForm">
                    <div class="form-group" style="text-align: center;">
                        <label for="upload_file" style="font-weight: 500; margin-bottom: 10px; display: block;">Seleccionar Archivo CSV</label>
                        <input type="file" class="form-control-file" id="upload_file" accept=".csv" style="display: inline-block; width: auto; padding: 8px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 1rem;">
                        <small class="form-text text-muted" style="margin-top: 5px;">Formatos aceptados: .csv. Tamaño máximo: 5MB.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="background-color: #f9fafb; border-top: 1px solid #d1d9e6; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 12px; justify-content: center; gap: 10px;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="background-color: #a0aec0; border-color: #a0aec0; border-radius: 8px; padding: 8px 20px; font-weight: 500; color: #ffffff; transition: background-color 0.2s;">
                    Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="uploadCsvButton" style="background-color: #4a90e2; border-color: #4a90e2; border-radius: 8px; padding: 8px 20px; font-weight: 500; color: #ffffff; transition: background-color 0.2s;">
                    Cargar
                </button>
            </div>
        </div>
    </div>
</div>
