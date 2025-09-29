(function($) {
    $(document).ready(function() {
        // Agregar CSS personalizado para modales
        const customStyles = `
            <style>
                .custom-modal .modal-content {
                    border-radius: 12px;
                    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.1);
                    background-color: #f9fafb;
                    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                }
                .custom-modal .modal-header {
                    background: linear-gradient(to right, #e6f0fa, #edf2f7);
                    border-bottom: 1px solid #d1d9e6;
                    color: #1a202c;
                    padding: 15px 20px;
                    text-align: center;
                }
                .custom-modal .modal-title {
                    font-size: 1.5rem;
                    font-weight: 600;
                    margin: 0 auto;
                }
                .custom-modal .modal-body {
                    background-color: #ffffff;
                    padding: 25px;
                    max-height: 500px;
                    overflow-y: auto;
                    text-align: center;
                }
                .custom-modal .modal-footer {
                    background-color: #f9fafb;
                    border-top: 1px solid #d1d9e6;
                    padding: 12px;
                    position: sticky;
                    bottom: 0;
                    z-index: 1000;
                    display: flex;
                    justify-content: center;
                    gap: 10px;
                }
                .custom-modal .btn-primary {
                    background-color: #4a90e2;
                    border-color: #4a90e2;
                    border-radius: 8px;
                    padding: 8px 20px;
                    font-weight: 500;
                    transition: background-color 0.2s;
                }
                .custom-modal .btn-primary:hover {
                    background-color: #3578c6;
                    border-color: #3578c6;
                }
                .custom-modal .btn-secondary {
                    background-color: #a0aec0;
                    border-color: #a0aec0;
                    border-radius: 8px;
                    padding: 8px 20px;
                    font-weight: 500;
                }
                .custom-modal .btn-secondary:hover {
                    background-color: #718096;
                    border-color: #718096;
                }
                .custom-modal .alert-success {
                    background-color: #e6fffa;
                    border-color: #b2f5ea;
                    color: #2d7d68;
                    border-radius: 8px;
                    margin-bottom: 20px;
                }
                .custom-modal .alert-warning {
                    background-color: #fff7e6;
                    border-color: #feebc8;
                    color: #744210;
                    border-radius: 8px;
                    margin-bottom: 20px;
                }
                .custom-modal .table {
                    background-color: #ffffff;
                    border: 1px solid #e2e8f0;
                    margin: 0 auto;
                    width: 100%;
                    max-width: 1200px;
                }
                .custom-modal .table th {
                    background-color: #e6f0fa;
                    color: #1a202c;
                    font-weight: 600;
                    text-align: center;
                    padding: 10px;
                }
                .custom-modal .table td {
                    color: #4a5568;
                    text-align: center;
                    padding: 8px;
                }
                @media (max-width: 576px) {
                    .custom-modal .modal-dialog {
                        margin: 0.5rem;
                    }
                    .custom-modal .table-responsive {
                        font-size: 0.85rem;
                    }
                    .custom-modal .modal-title {
                        font-size: 1.25rem;
                    }
                    .custom-modal .modal-body {
                        padding: 15px;
                    }
                }
            </style>
        `;
        $('head').append(customStyles);

        // Inicializar Flatpickr para entradas de fecha y hora
        try {
            if (typeof flatpickr !== 'undefined') {
                $('#createModal, #editModal, #uploadModal').on('shown.bs.modal', function() {
                    $('.flatpickr-date').each(function() {
                        if (this._flatpickr) this._flatpickr.destroy();
                        flatpickr(this, {
                            dateFormat: 'd/m/y',
                            allowInput: true,
                            placeholder: 'DD/MM/YY'
                        });
                    });
                    $('.flatpickr-time').each(function() {
                        if (this._flatpickr) this._flatpickr.destroy();
                        flatpickr(this, {
                            enableTime: true,
                            noCalendar: true,
                            dateFormat: 'H:i',
                            time_24hr: true,
                            allowInput: true,
                            placeholder: 'HH:MM'
                        });
                    });
                });
            } else {
                console.warn('La biblioteca Flatpickr no está cargada.');
                Swal.fire({
                    icon: 'warning',
                    title: 'Advertencia',
                    text: 'Flatpickr no cargado. Los selectores de fecha/hora no funcionarán.',
                    confirmButtonText: 'Aceptar',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
            }
        } catch (e) {
            console.error('Error al inicializar Flatpickr:', e);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al inicializar Flatpickr: ' + e.message,
                confirmButtonText: 'Aceptar',
                customClass: { confirmButton: 'btn btn-primary' }
            });
        }

        // Renderizador personalizado para acordeón responsivo usando una cuadrícula
        function responsiveRenderer(api, rowIdx, columns) {
            var data = $.map(columns, function(col) {
                if (col.hidden && col.data && col.data !== 'N/A') {
                    return `<div class="col-md-4 col-sm-6 mb-3">
                        <div class="d-flex flex-column">
                            <strong>${col.title}:</strong>
                            <span>${col.data}</span>
                        </div>
                    </div>`;
                }
                return '';
            }).join('');
            return data ? `<div class="row p-3">${data}</div>` : false;
        }

        // Variables globales para compartir entre funciones
        let duplicateCedulasFile = [];
        let duplicateCedulasDB = [];

        // Función para previsualizar archivo CSV/Excel
        function previewCsvFile(file, callback) {
            if (!window.Papa) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'La biblioteca Papa Parse no está cargada.',
                    confirmButtonText: 'Aceptar',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
                return;
            }

            if (!window.FeriasRoutes || !window.FeriasRoutes.checkDuplicates) {
                console.error('FeriasRoutes.checkDuplicates no está definido. Verifica la configuración de rutas. Rutas actuales:', window.FeriasRoutes);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'La ruta para verificar duplicados no está definida. Verifica la configuración de rutas.',
                    confirmButtonText: 'Aceptar',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
                return;
            }

            Papa.parse(file, {
                header: true,
                skipEmptyLines: true,
                transform: function(value, header) {
                    if (header === 'cedula') return String(value).trim();
                    return value;
                },
                complete: function(results) {
                    if (results.errors.length) {
                        console.error('Errores de Papa Parse:', results.errors);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al procesar el archivo: ' + results.errors.map(e => e.message).join(', '),
                            confirmButtonText: 'Aceptar',
                            customClass: { confirmButton: 'btn btn-primary' }
                        });
                        return;
                    }

                    var headers = results.meta.fields || [];
                    var previewData = results.data.slice(0, 5);

                    // Extraer cédulas del archivo
                    var cedulas = results.data.map(row => row.cedula || '').filter(cedula => cedula !== '');
                    var cedulaCounts = {};
                    duplicateCedulasFile = []; // Reiniciar variable global
                    var uniqueCedulas = [];

                    cedulas.forEach((cedula, index) => {
                        cedulaCounts[cedula] = (cedulaCounts[cedula] || 0) + 1;
                        if (cedulaCounts[cedula] > 1 && !duplicateCedulasFile.some(d => d.cedula === cedula)) {
                            duplicateCedulasFile.push({ cedula: cedula, row: index + 2 });
                        } else if (cedulaCounts[cedula] === 1) {
                            uniqueCedulas.push(cedula);
                        }
                    });

                    // Prevalidar contra la base de datos
                    var formData = new FormData();
                    formData.append('cedulas', JSON.stringify(uniqueCedulas));
                    console.log('Enviando solicitud a:', window.FeriasRoutes.checkDuplicates);
                    $.ajax({
                        url: window.FeriasRoutes.checkDuplicates,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function(preResponse) {
                            duplicateCedulasDB = preResponse.already_loaded || []; // Actualizar variable global
                            var validCedulas = uniqueCedulas.filter(cedula => !duplicateCedulasDB.some(dbCedula => dbCedula.cedula === cedula));

                            var summaryTable = `
                                <div class="alert alert-info">
                                    <h6>Resumen de Cédulas</h6>
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Cédula</th>
                                                <th>Fila (si aplica)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${duplicateCedulasFile.map(d => `
                                                <tr>
                                                    <td>Duplicado en archivo</td>
                                                    <td>${d.cedula}</td>
                                                    <td>${d.row}</td>
                                                </tr>
                                            `).join('')}
                                            ${duplicateCedulasDB.map(d => `
                                                <tr>
                                                    <td>Ya cargado en DB</td>
                                                    <td>${d.cedula}</td>
                                                    <td>-</td>
                                                </tr>
                                            `).join('')}
                                            ${validCedulas.map((cedula, index) => `
                                                <tr>
                                                    <td>Válido para registro</td>
                                                    <td>${cedula}</td>
                                                    <td>-</td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                    <p>Se importarán solo las cédulas válidas. ¿Deseas continuar?</p>
                                </div>`;

                            var modalContent = `
                                <div class="modal fade custom-modal" id="csvPreviewModal" tabindex="-1" aria-labelledby="csvPreviewModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-xl" style="max-width: 90vw;">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="csvPreviewModalLabel">Vista Previa del Archivo</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Columnas detectadas:</strong> ${headers.join(', ')}</p>
                                                ${summaryTable}
                                                <h6>Vista previa de los primeros ${previewData.length} registros:</h6>
                                                <div style="overflow-x: auto;">
                                                    <table class="table table-sm table-bordered table-responsive">
                                                        <thead>
                                                            <tr>
                                                                ${headers.map(h => `<th>${h.replace(/_/g, ' ').toUpperCase()}</th>`).join('')}
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            ${previewData.map(row => `
                                                                <tr>
                                                                    ${headers.map(h => `<td>${row[h] || ''}</td>`).join('')}
                                                                </tr>
                                                            `).join('')}
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                <button type="button" class="btn btn-primary" id="proceedWithUpload">Continuar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                            $('body').append(modalContent);
                            $('#csvPreviewModal').modal('show');

                            $('#proceedWithUpload').on('click', function() {
                                $('#csvPreviewModal').modal('hide');
                                submitUpload(file, {});
                            });

                            $('#csvPreviewModal').on('hidden.bs.modal', function() {
                                const $modal = $(this);
                                if ($modal.length) {
                                    $modal.remove();
                                    $('.modal-backdrop').remove();
                                    $('body').removeClass('modal-open').css('padding-right', '');
                                }
                            });
                        },
                        error: function(xhr) {
                            console.error('Error de prevalidación:', xhr.responseText, 'URL:', window.FeriasRoutes.checkDuplicates);
                            let errorMessage = 'Error al verificar duplicados contra la base de datos.';
                            if (xhr.status === 422 && xhr.responseJSON?.details) {
                                errorMessage += ' El backend está aplicando validaciones incorrectas. Verifica que la ruta ' + window.FeriasRoutes.checkDuplicates + ' use el método checkDuplicates sin validaciones adicionales.';
                            } else if (xhr.status !== 200) {
                                errorMessage += ' Estado HTTP: ' + xhr.status;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: errorMessage,
                                confirmButtonText: 'Aceptar',
                                customClass: { confirmButton: 'btn btn-primary' }
                            });
                        }
                    });
                },
                error: function(err) {
                    console.error('Error al analizar el archivo:', err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al leer el archivo: ' + err,
                        confirmButtonText: 'Aceptar',
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                }
            });
        }

        // Función para enviar la carga con datos filtrados
        function submitUpload(file, columnMapping) {
            if (!window.Papa) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'La biblioteca Papa Parse no está cargada.',
                    confirmButtonText: 'Aceptar',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
                return;
            }

            var formData = new FormData();
            formData.append('file', file); // Enviar el archivo original
            formData.append('column_mapping', JSON.stringify(columnMapping));
            console.log('Enviando archivo a:', window.FeriasRoutes.storeMassive, 'Archivo:', file.name, 'Tamaño:', file.size); // Depuración

            $.ajax({
                url: window.FeriasRoutes.storeMassive,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    console.log('Respuesta del servidor en submitUpload:', response); // Depuración
                    $('#uploadModal').modal('hide');
                    showImportSummary(response);
                    if ($('#feriasTable').length) $('#feriasTable').DataTable().ajax.reload(null, false);
                    if ($('#feriasTableAll').length) $('#feriasTableAll').DataTable().ajax.reload(null, false);
                },
                error: function(xhr) {
                    console.error('Error al subir el archivo:', xhr.responseText);
                    let errorMessage = 'Error al procesar el archivo.';
                    if (xhr.status === 422 && xhr.responseJSON?.details) {
                        errorMessage = '<ul>' + Object.entries(xhr.responseJSON.details).map(([field, errors]) => `<li>${field}: ${errors.join(', ')}</li>`).join('') + '</ul>';
                    } else if (xhr.responseJSON?.error) {
                        errorMessage = xhr.responseJSON.error + ': ' + JSON.stringify(xhr.responseJSON.detected_columns || xhr.responseJSON.details);
                    } else {
                        errorMessage += ' Estado HTTP: ' + xhr.status;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: errorMessage,
                        confirmButtonText: 'Aceptar',
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                }
            });
        }

        // Funciones simplificadas
        function selectColumns(table, headers, missingRequired, duplicateCedulas, callback) {
            callback({});
        }

        function showColumnMappingModal(table, headers, fields, callback) {
            callback({});
        }

        // Función showImportSummary
        function showImportSummary(response) {
            console.log('Respuesta del servidor en showImportSummary:', response); // Depuración
            if (!response || typeof response !== 'object') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'La respuesta del servidor es inválida. Verifica la configuración del backend.',
                    confirmButtonText: 'Aceptar',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
                return;
            }

            const displayColumns = ['cedula', 'estado', 'municipio', 'parroquia', 'nombre_pto', 'apellidos', 'nombres', 'telefono'];
            let imported = 0;
            let failedRows = [];
            let successRows = [];

            // Interpretar la respuesta actual
            if (response.summary) {
                imported = response.summary.imported || 0;
                failedRows = response.summary.failed_rows || [];
                successRows = response.summary.success_rows || [];
                // Extraer cédulas duplicadas del message si no hay failed_rows detallados
                if (Array.isArray(failedRows) && failedRows.length === 0 && response.message) {
                    const duplicateCedulas = response.message.match(/\d+/g) || [];
                    failedRows = duplicateCedulas.map(cedula => ({
                        row: { cedula: cedula },
                        errors: { cedula: ['Duplicado en archivo'] }
                    }));
                }
            } else if (response.imported !== undefined) {
                imported = response.imported || 0;
                failedRows = response.failed_rows || [];
                successRows = response.success_rows || [];
            }

            // Generar detalles de filas fallidas
            var failedRowsDetails = (failedRows && Array.isArray(failedRows) ? failedRows : []).length > 0 ? `
                <div class="alert alert-warning mt-3">
                    <strong>Registros fallidos (${(failedRows && Array.isArray(failedRows) ? failedRows.length : 0)}):</strong>
                    <p>Los siguientes registros no se pudieron importar debido a errores:</p>
                    <div style="overflow-x: auto;">
                        <table class="table table-sm table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th>Fila</th>
                                    ${displayColumns.map(col => `<th>${col.replace(/_/g, ' ').toUpperCase()}</th>`).join('')}
                                    <th>Error</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${(failedRows && Array.isArray(failedRows) ? failedRows : []).map((row, index) => `
                                    <tr>
                                        <td>${index + 1}</td>
                                        ${displayColumns.map(col => `<td>${row.row[col] || 'N/A'}</td>`).join('')}
                                        <td>
                                            ${Object.entries(row.errors || {}).map(([field, errors]) => `${field.replace(/_/g, ' ').toUpperCase()}: ${errors.join(', ')}`).join('<br>')}
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>` : '';

            // Generar detalles de filas exitosas
            var successRowsDetails = (successRows && Array.isArray(successRows) ? successRows : []).length > 0 ? `
                <div class="alert alert-success mt-3">
                    <strong>Registros importados (${(successRows && Array.isArray(successRows) ? successRows.length : 0)}):</strong>
                    <p>Los siguientes registros se importaron correctamente:</p>
                    <div style="overflow-x: auto;">
                        <table class="table table-sm table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th>Fila</th>
                                    ${displayColumns.map(col => `<th>${col.replace(/_/g, ' ').toUpperCase()}</th>`).join('')}
                                </tr>
                            </thead>
                            <tbody>
                                ${(successRows && Array.isArray(successRows) ? successRows : []).map((row, index) => `
                                    <tr>
                                        <td>${index + 1}</td>
                                        ${displayColumns.map(col => `<td>${row[col] || 'N/A'}</td>`).join('')}
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>` : (imported > 0 ? '<div class="alert alert-success mt-3">No se detallan registros individuales, pero se importaron ' + imported + ' registros exitosamente.</div>' : '');

            var summaryContent = `
                <div class="modal fade custom-modal" id="importSummaryModal" tabindex="-1" aria-labelledby="importSummaryModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl" style="max-width: 90vw;">
                        <div class="modal-content">
                            <div class="modal-header" style="background: linear-gradient(to right, #e6f0fa, #edf2f7); border-bottom: 1px solid #d1d9e6; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 20px 25px; text-align: center;">
                                <h5 class="modal-title" id="importSummaryModalLabel" style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 1.75rem; font-weight: 700; color: #1a2e44; letter-spacing: 0.5px; margin: 0 auto; max-width: 80%;">
                                    Resumen de Importación
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="font-size: 1.5rem; color: #4a5568; position: absolute; right: 20px; top: 15px;">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Total de registros procesados:</strong> ${imported + (failedRows && Array.isArray(failedRows) ? failedRows.length : 0) || 0}</p>
                                <p><strong>Registros importados exitosamente:</strong> ${imported || 0}</p>
                                <p><strong>Registros fallidos:</strong> ${(failedRows && Array.isArray(failedRows) ? failedRows.length : 0) || 0}</p>
                                ${successRowsDetails}
                                ${failedRowsDetails}
                                ${(!failedRowsDetails && !successRowsDetails && imported === 0) ? '<div class="alert alert-warning mt-3">No se procesaron registros. Verifica el archivo o el backend.</div>' : ''}
                                ${response.message ? `<div class="alert alert-warning mt-3">${response.message}</div>` : ''}
                            </div>
                            <div class="modal-footer" style="background-color: #f9fafb; border-top: 1px solid #d1d9e6; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 15px; justify-content: center;">
                                <button type="button" class="btn btn-primary" data-dismiss="modal" style="background-color: #4a90e2; border-color: #4a90e2; border-radius: 8px; padding: 10px 25px; font-weight: 600; color: #ffffff; transition: background-color 0.2s;">
                                    Aceptar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('body').append(summaryContent);
            $('#importSummaryModal').modal('show');
            $('#importSummaryModal').on('hidden.bs.modal', function() {
                const $modal = $(this);
                if ($modal.length) {
                    $modal.remove();
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open').css('padding-right', '');
                }
            });
        }

        // Inicializar DataTable
        function initializeDataTable(tableId, ajaxUrl, filterActive = false) {
            if (!$(tableId).length || !$(tableId).is('table')) {
                console.error(`La tabla ${tableId} no se encontró o no es un elemento de tabla`);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: `La tabla ${tableId} no se encuentra en la página o no es una tabla válida.`,
                    confirmButtonText: 'Aceptar',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
                return;
            }

            try {
                var $thead = $(`${tableId} thead tr`);
                var columnCount = $thead.find('th').length;
                var configColumns = [
                    { data: 'estado', title: 'Estado', responsivePriority: 1, className: 'no-edit' },
                    { data: 'municipio', title: 'Municipio', responsivePriority: 2 },
                    { data: 'parroquia', title: 'Parroquia', responsivePriority: 3 },
                    { data: 'nombre_pto', title: 'Nombre Punto', responsivePriority: 4 },
                    { data: 'cedula', title: 'Cédula', responsivePriority: 5, name: 'cedula' },
                    { data: 'apellidos', title: 'Apellidos', responsivePriority: 6 },
                    { data: 'nombres', title: 'Nombres', responsivePriority: 7 },
                    { data: 'telefono', title: 'Teléfono', responsivePriority: 8 },
                    { data: 'status_contact1', title: 'Status Contacto 1', responsivePriority: 9 },
                    { data: 'status_contact2', title: 'Status Contacto 2', responsivePriority: 10 },
                    { data: 'status_contact3', title: 'Status Contacto 3', responsivePriority: 11 },
                    { data: 'disponibilidad', title: 'Disponibilidad', responsivePriority: 12 },
                    { data: 'incidencias', title: 'Incidencias', responsivePriority: 13 },
                    { data: 'fecha_incidencia', title: 'Fecha Incidencia', responsivePriority: 14 },
                    { data: 'hora_incidencia', title: 'Hora Incidencia', responsivePriority: 15 },
                    { data: 'cod_edo', title: 'Código Estado', responsivePriority: 16 },
                    { data: 'cod_mun', title: 'Código Municipio', responsivePriority: 17 },
                    { data: 'cod_parroquia', title: 'Código Parroquia', responsivePriority: 18 },
                    { data: 'cod_centro', title: 'Código Centro', responsivePriority: 19 },
                    { data: 'correo', title: 'Correo', responsivePriority: 20 }
                ];

                if (columnCount !== configColumns.length) {
                    console.warn(`Desajuste de columnas: El HTML tiene ${columnCount} columnas, pero la configuración tiene ${configColumns.length}.`);
                    Swal.fire({
                        icon: 'warning',
                        title: 'Advertencia',
                        text: `El número de columnas en la tabla (${columnCount}) no coincide con la configuración (${configColumns.length}). Asegúrate de que el <thead> tenga ${configColumns.length} <th> elementos.`,
                        confirmButtonText: 'Aceptar',
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                    return;
                }

                var table = $(tableId).DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: ajaxUrl,
                        data: function(d) {
                            if (filterActive) d.activeOnly = true;
                        },
                        dataSrc: function(json) {
                            console.log(`Respuesta AJAX para ${tableId}:`, json);
                            if (!json || !json.data) {
                                console.error('Respuesta AJAX inválida:', json);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Respuesta de datos inválida desde el servidor. Verifica la consola para más detalles.',
                                    confirmButtonText: 'Aceptar',
                                    customClass: { confirmButton: 'btn btn-primary' }
                                });
                                return [];
                            }
                            json.data.forEach(function(row) {
                                row.id = row.id || null;
                                row.cod_edo = row.cod_edo || '';
                                row.estado = row.estado || '';
                                row.cod_mun = row.cod_mun || '';
                                row.municipio = row.municipio || '';
                                row.cod_parroquia = row.cod_parroquia || '';
                                row.parroquia = row.parroquia || '';
                                row.cod_centro = row.cod_centro || '';
                                row.nombre_pto = row.nombre_pto || '';
                                row.direccion_pto = row.direccion_pto || '';
                                row.rectoria = row.rectoria || '';
                                row.cedula = row.cedula || '';
                                row.apellidos = row.apellidos || '';
                                row.nombres = row.nombres || '';
                                row.telefono = row.telefono || '';
                                row.correo = row.correo || '';
                                row.rol = row.rol || '';
                                row.status_contact1 = row.status_contact1 || '';
                                row.fecha_hora1 = row.fecha_hora1 || '';
                                row.status_contact2 = row.status_contact2 || '';
                                row.fecha_hora2 = row.fecha_hora2 || '';
                                row.status_contact3 = row.status_contact3 || '';
                                row.fecha_hora3 = row.fecha_hora3 || '';
                                row.disponibilidad = row.disponibilidad || '';
                                row.incidencias = row.incidencias || '';
                                row.fecha_incidencia = row.fecha_incidencia || '';
                                row.hora_incidencia = row.hora_incidencia || '';
                                row.observaciones = row.observaciones || '';
                                row.e_registro = row.e_registro || 'Activo';
                            });
                            return json.data;
                        },
                        error: function(xhr, error, thrown) {
                            console.error(`Error AJAX de DataTable para ${tableId}:`, xhr);
                            let errorMessage = 'Error al cargar los datos de la tabla.';
                            if (xhr.status === 404) errorMessage = 'Ruta AJAX no encontrada. Verifica la configuración de rutas.';
                            else if (xhr.status === 500) errorMessage = 'Error del servidor. Consulta los logs del servidor para más detalles.';
                            else if (xhr.responseJSON?.error) errorMessage = xhr.responseJSON.error + ': ' + xhr.responseJSON.details;
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: errorMessage,
                                confirmButtonText: 'Aceptar',
                                customClass: { confirmButton: 'btn btn-primary' }
                            });
                        }
                    },
                    columns: configColumns,
                    paging: true,
                    pageLength: 10,
                    searching: true,
                    ordering: true,
                    info: true,
                    responsive: {
                        details: { renderer: responsiveRenderer }
                    },
                    language: { url: '/js/es-ES.json' },
                    createdRow: function(row, data) {
                        $(row).addClass(data.e_registro === 'Activo' ? 'row-activo' : 'row-inactivo');
                    },
                    rowId: 'id'
                });

                $('#' + tableId.replace('#', '') + ' tbody').on('click', 'td:not(.no-edit):not(.dtr-control)', function(e) {
                    var tr = $(this).closest('tr');
                    var table = $(tr).closest('table').DataTable();
                    var row = table.row(tr);
                    var data = row.data();
                    if (data && data.id) {
                        $('#editModal').data('id', data.id).modal('show');
                        $('#editForm').attr('action', window.FeriasRoutes.update.replace(':id', data.id));
                        e.stopPropagation();
                    } else {
                        console.error('No se encontró un ID válido para la fila:', data);
                        Swal.fire({
                            icon: 'warning',
                            title: 'Advertencia',
                            text: 'No se encontró un ID válido para esta fila. No se puede abrir el modal de edición.',
                            confirmButtonText: 'Aceptar',
                            customClass: { confirmButton: 'btn btn-primary' }
                        });
                    }
                });

                return table;
            } catch (e) {
                console.error(`Error al inicializar DataTable para ${tableId}:`, e);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: `Error al inicializar la tabla ${tableId}: ` + e.message,
                    confirmButtonText: 'Aceptar',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
            }
        }

        var feriasTable = $('#feriasTable').length ? initializeDataTable('#feriasTable', window.FeriasRoutes.datatables, true) : null;
        var feriasAllTable = $('#feriasTableAll').length ? initializeDataTable('#feriasTableAll', window.FeriasRoutes.datatablesAll, false) : null;

        // Manejar modal de creación
        $('#createModal').on('show.bs.modal', function() {
            $('#createForm')[0].reset();
            $('#createForm').find('.is-invalid').removeClass('is-invalid');
            $('#createForm').find('.invalid-feedback').text('');
            $('#create_rectoria').val('ACME NOGAL');
            $('#create_e_registro').val('Activo');
        });

        // Manejar modal de edición
        $('#editModal').on('show.bs.modal', function(event) {
            const id = $(this).data('id');
            if (!id || isNaN(id)) {
                console.error('ID inválido o no definido:', id);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo identificar el registro a editar. Por favor, intenta de nuevo.',
                    confirmButtonText: 'Aceptar',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
                $(this).modal('hide');
                return;
            }

            $('#editForm')[0].reset();
            $('#editForm').find('.is-invalid').removeClass('is-invalid');
            $('#editForm').find('.invalid-feedback').text('');

            $.ajax({
                url: window.FeriasRoutes.edit.replace(':id', id),
                method: 'GET',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    if (typeof response !== 'object' || response === null) {
                        console.error('Formato de respuesta inesperado:', response);
                        Swal.fire({
                            icon: 'warning',
                            title: 'Advertencia',
                            text: 'Respuesta inesperada del servidor. Usando valores predeterminados.',
                            confirmButtonText: 'Continuar',
                            customClass: { confirmButton: 'btn btn-primary' }
                        });
                        response = {};
                    }
                    const feria = response.feria || {};
                    $('#edit_id').val(feria.id || '');
                    $('#edit_estado').val(feria.estado || '');
                    $('#edit_municipio').val(feria.municipio || '');
                    $('#edit_parroquia').val(feria.parroquia || '');
                    $('#edit_nombre_pto').val(feria.nombre_pto || '');
                    $('#edit_cedula').val(feria.cedula || '');
                    $('#edit_apellidos').val(feria.apellidos || '');
                    $('#edit_nombres').val(feria.nombres || '');
                    $('#edit_telefono').val(feria.telefono || '');
                    $('#edit_correo').val(feria.correo || '');
                    $('#edit_rectoria').val(feria.rectoria || 'ACME NOGAL');

                    const statusOptions = ['Seleccione', 'No contactado', 'Contactado'];
                    const disponibilidadOptions = ['Seleccione', 'Sin información', 'Trabajará', 'No trabajará'];
                    const eRegistroOptions = ['Seleccione', 'Activo', 'Inactivo'];

                    ['status_contact1', 'status_contact2', 'status_contact3'].forEach(field => {
                        let $select = $(`#edit_${field}`);
                        $select.empty().append('<option value="">Seleccione</option>');
                        statusOptions.forEach(opt => $select.append(`<option value="${opt}" ${feria[field] === opt ? 'selected' : ''}>${opt}</option>`));
                    });

                    let $disponibilidad = $('#edit_disponibilidad');
                    $disponibilidad.empty().append('<option value="">Seleccione</option>');
                    disponibilidadOptions.forEach(opt => $disponibilidad.append(`<option value="${opt}" ${feria.disponibilidad === opt ? 'selected' : ''}>${opt}</option>`));

                    let $eRegistro = $('#edit_e_registro');
                    $eRegistro.empty().append('<option value="">Seleccione</option>');
                    eRegistroOptions.forEach(opt => $eRegistro.append(`<option value="${opt}" ${feria.e_registro === opt ? 'selected' : ''}>${opt}</option>`));
                    $eRegistro.prop('disabled', $('#feriasTable').length);

                    $('#edit_fecha_hora1').val(feria.fecha_hora1 || '');
                    $('#edit_fecha_hora2').val(feria.fecha_hora2 || '');
                    $('#edit_fecha_hora3').val(feria.fecha_hora3 || '');
                    $('#edit_incidencias').val(feria.incidencias || '');
                    $('#edit_fecha_incidencia').val(feria.fecha_incidencia || '');
                    $('#edit_hora_incidencia').val(feria.hora_incidencia || '');
                    $('#edit_observaciones').val(feria.observaciones || '');

                    $('#editForm input, #editForm select').on('input change', function() {
                        validateForm('#editForm', true);
                    });
                },
                error: function(xhr) {
                    console.error('Detalles del error AJAX:', { status: xhr.status, response: xhr.responseText });
                    Swal.fire({
                        icon: 'warning',
                        title: 'Advertencia',
                        text: 'No se pudo cargar la feria. Usando valores predeterminados. Detalles: ' + (xhr.responseJSON?.error || xhr.statusText),
                        confirmButtonText: 'Continuar',
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                    $('#edit_rectoria').val('ACME NOGAL');
                    $('#edit_e_registro').val('Activo').prop('disabled', $('#feriasTable').length);
                }
            });
        });

        // Función de validación de formulario
        function validateForm(formId, isEdit = false) {
            let isValid = true;
            $(formId).find('.is-invalid').removeClass('is-invalid');
            $(formId).find('.invalid-feedback').text('');

            const prefix = isEdit ? 'edit_' : 'create_';
            const requiredFields = [`${prefix}cedula`]; // Solo cédula es requerida

            requiredFields.forEach(field => {
                const $input = $(`#${field}`);
                if (!$input.val().trim()) {
                    isValid = false;
                    $input.addClass('is-invalid');
                    $input.siblings('.invalid-feedback').text('Este campo es obligatorio.');
                }
            });

            const fieldsMaxLength = {
                [`${prefix}estado`]: 255,
                [`${prefix}municipio`]: 255,
                [`${prefix}parroquia`]: 255,
                [`${prefix}nombre_pto`]: 255,
                [`${prefix}cedula`]: 20,
                [`${prefix}apellidos`]: 255,
                [`${prefix}nombres`]: 255,
                [`${prefix}telefono`]: 20,
                [`${prefix}correo`]: 255,
                [`${prefix}rectoria`]: 255
            };

            Object.keys(fieldsMaxLength).forEach(field => {
                const $input = $(`#${field}`);
                if ($input.val() && $input.val().length > fieldsMaxLength[field]) {
                    isValid = false;
                    $input.addClass('is-invalid');
                    $input.siblings('.invalid-feedback').text(`Máximo ${fieldsMaxLength[field]} caracteres.`);
                }
            });

            if ($(`#${prefix}cedula`).val() && !/^\d{7,8}$/.test($(`#${prefix}cedula`).val())) {
                isValid = false;
                $(`#${prefix}cedula`).addClass('is-invalid');
                $(`#${prefix}cedula`).siblings('.invalid-feedback').text('La cédula debe ser un número de 7 u 8 dígitos.');
            }

            if ($(`#${prefix}telefono`).val() && !/^\d{10,11}$/.test($(`#${prefix}telefono`).val())) {
                isValid = false;
                $(`#${prefix}telefono`).addClass('is-invalid');
                $(`#${prefix}telefono`).siblings('.invalid-feedback').text('El teléfono debe ser un número de 10 u 11 dígitos.');
            }

            if ($(`#${prefix}correo`).val() && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test($(`#${prefix}correo`).val())) {
                isValid = false;
                $(`#${prefix}correo`).addClass('is-invalid');
                $(`#${prefix}correo`).siblings('.invalid-feedback').text('El correo electrónico no es válido.');
            }

            return isValid;
        }

        // Manejar envío del formulario de creación
        $('#createForm').on('submit', function(e) {
            e.preventDefault();
            if (!validateForm('#createForm')) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Por favor, corrige los errores en el formulario.',
                    confirmButtonText: 'Aceptar',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
                return;
            }

            var formData = $(this).serialize();
            $.ajax({
                url: window.FeriasRoutes.store,
                method: 'POST',
                data: formData,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    $('#createModal').modal('hide');
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open').css('padding-right', '');
                    Swal.fire({
                        icon: 'success', // Cambiado de 'éxito' a 'success'
                        title: 'Éxito',
                        text: response.message,
                        confirmButtonText: 'Aceptar',
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                    if ($('#feriasTable').length) $('#feriasTable').DataTable().ajax.reload(null, false);
                    if ($('#feriasTableAll').length) $('#feriasTableAll').DataTable().ajax.reload(null, false);
                },
                error: function(xhr) {
                    console.error('Error al crear:', xhr.responseText);
                    let errorMessage = 'Error al crear la feria.';
                    if (xhr.status === 422 && xhr.responseJSON?.details) {
                        errorMessage = '<ul>' + Object.entries(xhr.responseJSON.details).map(([field, errors]) => `<li>${field}: ${errors.join(', ')}</li>`).join('') + '</ul>';
                    } else if (xhr.responseJSON?.error) {
                        errorMessage = xhr.responseJSON.error + ': ' + xhr.responseJSON.details;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: errorMessage,
                        confirmButtonText: 'Aceptar',
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                }
            });
        });

        // Manejar envío del formulario de edición
        $('#editForm').on('submit', function(e) {
            e.preventDefault();
            if (!validateForm('#editForm', true)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Por favor, corrige los errores en el formulario.',
                    confirmButtonText: 'Aceptar',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
                return;
            }

            const id = $('#edit_id').val();
            const formData = $(this).serialize();

            $.ajax({
                url: window.FeriasRoutes.update.replace(':id', id),
                method: 'PUT',
                data: formData,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    $('#editModal').modal('hide');
                    Swal.fire({
                        icon: 'success', // Cambiado de 'éxito' a 'success'
                        title: 'Éxito',
                        text: response.message,
                        confirmButtonText: 'Aceptar',
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                    if ($('#feriasTable').length) $('#feriasTable').DataTable().ajax.reload(null, false);
                    if ($('#feriasTableAll').length) $('#feriasTableAll').DataTable().ajax.reload(null, false);
                },
                error: function(xhr) {
                    console.error('Error al actualizar:', xhr.responseText);
                    let errorMessage = 'Error al actualizar la feria.';
                    if (xhr.status === 422 && xhr.responseJSON?.details) {
                        errorMessage = '<ul>' + Object.entries(xhr.responseJSON.details).map(([field, errors]) => `<li>${field}: ${errors.join(', ')}</li>`).join('') + '</ul>';
                    } else if (xhr.responseJSON?.error) {
                        errorMessage = xhr.responseJSON.error + ': ' + xhr.responseJSON.details;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: errorMessage,
                        confirmButtonText: 'Aceptar',
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                }
            });
        });

        // Agregar botón "Guardar y Seguir Editando"
        $('#editForm').append(`
            <button type="button" class="btn btn-secondary mr-2" id="saveAndContinue">Guardar y Seguir Editando</button>
        `);
        $('#saveAndContinue').on('click', function() {
            if (!validateForm('#editForm', true)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Por favor, corrige los errores en el formulario.',
                    confirmButtonText: 'Aceptar',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
                return;
            }

            const id = $('#edit_id').val();
            const formData = $('#editForm').serialize();

            $.ajax({
                url: window.FeriasRoutes.update.replace(':id', id),
                method: 'PUT',
                data: formData,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    Swal.fire({
                        icon: 'success', // Cambiado de 'éxito' a 'success'
                        title: 'Éxito',
                        text: response.message,
                        confirmButtonText: 'Aceptar',
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                    if ($('#feriasTable').length) $('#feriasTable').DataTable().ajax.reload(null, false);
                    if ($('#feriasTableAll').length) $('#feriasTableAll').DataTable().ajax.reload(null, false);
                },
                error: function(xhr) {
                    console.error('Error al actualizar:', xhr.responseText);
                    let errorMessage = 'Error al actualizar la feria.';
                    if (xhr.status === 422 && xhr.responseJSON?.details) {
                        errorMessage = '<ul>' + Object.entries(xhr.responseJSON.details).map(([field, errors]) => `<li>${field}: ${errors.join(', ')}</li>`).join('') + '</ul>';
                    } else if (xhr.responseJSON?.error) {
                        errorMessage = xhr.responseJSON.error + ': ' + xhr.responseJSON.details;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: errorMessage,
                        confirmButtonText: 'Aceptar',
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                }
            });
        });

        // Manejar modal de carga
        $('#uploadModal').on('show.bs.modal', function() {
            console.log('Modal de carga mostrado');
            $('#uploadCsvButton').off('click').on('click', function() {
                console.log('Botón Cargar clicado');
                var file = $('#upload_file')[0].files[0];
                if (!file) {
                    console.log('No se seleccionó archivo');
                    $('#upload_file').addClass('is-invalid');
                    $('#upload_file').siblings('.invalid-feedback').text('Por favor, selecciona un archivo.');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Por favor, selecciona un archivo.',
                        confirmButtonText: 'Aceptar',
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                    return;
                }

                var extension = file.name.split('.').pop().toLowerCase();
                if (!['csv', 'txt', 'xls', 'xlsx'].includes(extension)) {
                    console.log('Extensión de archivo inválida:', extension);
                    $('#upload_file').addClass('is-invalid');
                    $('#upload_file').siblings('.invalid-feedback').text('Por favor, selecciona un archivo CSV, TXT, XLS o XLSX.');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Por favor, selecciona un archivo CSV, TXT, XLS o XLSX.',
                        confirmButtonText: 'Aceptar',
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                    return;
                }
                if (file.size > 5 * 1024 * 1024) {
                    console.log('Tamaño del archivo excede el límite:', file.size);
                    $('#upload_file').addClass('is-invalid');
                    $('#upload_file').siblings('.invalid-feedback').text('El archivo no debe exceder los 5MB.');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'El archivo no debe exceder los 5MB.',
                        confirmButtonText: 'Aceptar',
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                    return;
                }

                $('#upload_file').removeClass('is-invalid');
                $('#upload_file').siblings('.invalid-feedback').text('');

                previewCsvFile(file, function(headers, missingRequired, duplicateCedulas) {
                    submitUpload(file, {});
                });
            });
        }).on('hidden.bs.modal', function() {
            const $modal = $(this);
            if ($modal.length) {
                $modal.find('form')[0]?.reset();
                $modal.find('.is-invalid').removeClass('is-invalid');
                $modal.find('.invalid-feedback').text('');
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css('padding-right', '');
            }
            // Limpiar el evento al cerrar para evitar acumulación
            $('#uploadModal').off('shown.bs.modal');
        });

        // Limpiar modales al cerrar
        $('#createModal, #editModal, #uploadModal, #csvPreviewModal, #importSummaryModal, #columnMappingModal').on('hidden.bs.modal', function() {
            const $modal = $(this);
            if ($modal.length) {
                $modal.find('form')[0]?.reset();
                $modal.find('.is-invalid').removeClass('is-invalid');
                $modal.find('.invalid-feedback').text('');
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css('padding-right', '');
            }
        });
    });
})(jQuery);
