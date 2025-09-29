(function($) {
    $(document).ready(function() {
        // Agregar CSS personalizado para modales y tablas
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
                .clickable-row {
                    cursor: pointer;
                }
                .clickable-row:hover {
                    background-color: #f0f0f0;
                }
                .table-success {
                    background-color: #d4edda !important; /* Verde para Activo */
                }
                .table-secondary {
                    background-color: #e9ecef !important; /* Gris para Inactivo */
                }
            </style>
        `;
        $('head').append(customStyles);

        // Variables globales
        let duplicateCedulasFile = [];
        let duplicateCedulasDB = [];

        // Función para inicializar DataTables con observador de mutaciones
        function initializeDataTable(tableId, datatablesRoute) {
            const tableElement = $(tableId)[0];
            if (!tableElement) {
                console.warn(`Table ${tableId} not found in DOM, skipping initialization`);
                return;
            }

            const observer = new MutationObserver((mutations, obs) => {
                if ($(tableId).length && !$.fn.DataTable.isDataTable(tableId)) {
                    console.log(`Table ${tableId} detected in DOM, initializing DataTable`);
                    $(tableId).DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: datatablesRoute,
                            dataSrc: function(response) {
                                console.log(`AJAX response for ${tableId}:`, response);
                                if (!response.data || !Array.isArray(response.data)) {
                                    console.error(`Invalid AJAX response format for ${tableId}:`, response);
                                    return [];
                                }
                                return response.data;
                            },
                            error: function(xhr, error, thrown) {
                                console.error(`AJAX error for ${tableId}:`, xhr.status, error, thrown, xhr.responseText);
                            }
                        },
                        columns: [
                            { data: 'estado' },
                            { data: 'municipio' },
                            { data: 'parroquia' },
                            { data: 'nombre_pto' },
                            { data: 'cedula' },
                            { data: 'apellidos' },
                            { data: 'nombres' },
                            { data: 'telefono' },
                            { data: 'correo' },
                            { data: 'incidencia', responsivePriority: 1 },
                            { data: 'fecha_incidencia', responsivePriority: 2 },
                            { data: 'hora_incidencia', responsivePriority: 3 }
                        ],
                        responsive: true,
                        createdRow: function(row, data, dataIndex) {
                            $(row).addClass('clickable-row').attr('data-id', data.id);
                            if (data.e_registro === 'Activo') {
                                $(row).addClass('table-success');
                            } else if (tableId === '#fiscalizacionesTableAll' && data.e_registro === 'Inactivo') {
                                $(row).addClass('table-secondary');
                            }
                        },
                        initComplete: function() {
                            console.log(`DataTable initialized successfully for ${tableId}`);
                        }
                    }).on('error.dt', function(e, settings, techNote, message) {
                        console.error(`DataTable error for ${tableId}:`, message, techNote);
                    });
                    obs.disconnect();
                }
            });

            observer.observe(document.body, { childList: true, subtree: true });
            if ($(tableId).length && !$.fn.DataTable.isDataTable(tableId)) {
                observer.takeRecords();
            }
        }

        // Inicializar DataTables solo para tablas existentes
        if ($('#fiscalizacionesTable').length) {
            initializeDataTable('#fiscalizacionesTable', window.FiscalizacionesRoutes.datatables);
        }
        if ($('#fiscalizacionesTableAll').length) {
            initializeDataTable('#fiscalizacionesTableAll', window.FiscalizacionesRoutes.datatablesAll);
        }

        // Controlar editabilidad de e_registro basado en tabla existente
        if ($('#fiscalizacionesTable').length) {
            $('#create_e_registro').val('Activo').prop('disabled', true);
            $('#edit_e_registro').val('Activo').prop('disabled', true);
        } else if ($('#fiscalizacionesTableAll').length) {
            $('#create_e_registro').val('Activo');
            $('#edit_e_registro').prop('disabled', false);
        } else {
            console.warn('No se encontró ninguna tabla de fiscalizaciones para configurar e_registro.');
        }

        // Manejar clic en fila para abrir el modal de edición
        $('.table').on('click', '.clickable-row', function() {
            const id = $(this).data('id');
            console.log('ID obtenido:', id);
            if (!id) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo obtener el ID de la fila.',
                    confirmButtonText: 'Aceptar',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
                return;
            }
            let route;
            if ($('#fiscalizacionesTable').length) {
                route = window.FiscalizacionesRoutes.edit.replace(':id', id);
            } else if ($('#fiscalizacionesTableAll').length) {
                route = window.FiscalizacionesRoutes.editAll.replace(':id', id);
            } else {
                console.error('No se encontró ninguna tabla de fiscalizaciones.');
                return;
            }
            if (!route) {
                console.error('Ruta de edición no definida para la tabla actual.');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'La ruta de edición no está configurada correctamente.',
                    confirmButtonText: 'Aceptar',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
                return;
            }
            console.log('Enviando solicitud a:', route);
            $.ajax({
                url: route,
                method: 'GET',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    if (response && response.fiscalizacion) {
                        const fiscalizacion = response.fiscalizacion;
                        $('#edit_id').val(fiscalizacion.id);
                        $('#edit_estado').val(fiscalizacion.estado);
                        $('#edit_municipio').val(fiscalizacion.municipio);
                        $('#edit_parroquia').val(fiscalizacion.parroquia);
                        $('#edit_nombre_pto').val(fiscalizacion.nombre_pto);
                        $('#edit_cedula').val(fiscalizacion.cedula);
                        $('#edit_apellidos').val(fiscalizacion.apellidos);
                        $('#edit_nombres').val(fiscalizacion.nombres);
                        $('#edit_telefono').val(fiscalizacion.telefono);
                        $('#edit_correo').val(fiscalizacion.correo);
                        $('#edit_incidencias').val(fiscalizacion.incidencias?.length ? fiscalizacion.incidencias[0].incidencia : '');
                        $('#edit_fecha_incidencia').val(fiscalizacion.incidencias?.length ? fiscalizacion.incidencias[0].fecha_incidencia : '');
                        $('#edit_hora_incidencia').val(fiscalizacion.incidencias?.length ? fiscalizacion.incidencias[0].hora_incidencia : '');
                        $('#edit_e_registro').val(fiscalizacion.e_registro);
                        $('#editModal').modal('show');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se recibieron datos válidos del servidor.',
                            confirmButtonText: 'Aceptar',
                            customClass: { confirmButton: 'btn btn-primary' }
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error al cargar la fiscalización:', xhr.responseText);
                    let errorMessage = 'No se pudo cargar la fiscalización.';
                    if (xhr.status === 500) {
                        errorMessage += ' Error interno del servidor. Consulta los logs para más detalles.';
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
        });

        // Función para previsualizar archivo CSV/Excel
        function previewCsvFile(file) {
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

            if (!window.FiscalizacionesRoutes || !window.FiscalizacionesRoutes.checkDuplicates) {
                console.error('FiscalizacionesRoutes.checkDuplicates no está definido. Verifica la configuración de rutas. Rutas actuales:', window.FiscalizacionesRoutes);
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
                    console.log('Resultados de Papa Parse:', results);
                    if (results.errors.length > 0) {
                        console.error('Errores de Papa Parse:', results.errors);
                        let errorMessage = 'Errores al procesar el archivo:\n' + results.errors.map((e, index) => {
                            return `Error ${index + 1}: Fila ${e.row + 2}: ${e.message}`;
                        }).join('\n');
                        Swal.fire({
                            icon: 'warning',
                            title: 'Advertencia',
                            text: errorMessage + '\nSe intentará procesar las filas válidas.',
                            confirmButtonText: 'Continuar',
                            customClass: { confirmButton: 'btn btn-primary' }
                        });
                    }

                    var headers = results.meta.fields || [];
                    var previewData = results.data.slice(0, 5);

                    var cedulas = results.data.map(row => row.cedula || '').filter(cedula => cedula !== '');
                    var cedulaCounts = {};
                    duplicateCedulasFile = [];
                    var uniqueCedulas = [];

                    cedulas.forEach((cedula, index) => {
                        cedulaCounts[cedula] = (cedulaCounts[cedula] || 0) + 1;
                        if (cedulaCounts[cedula] > 1 && !duplicateCedulasFile.some(d => d.cedula === cedula)) {
                            duplicateCedulasFile.push({ cedula: cedula, row: index + 2 });
                        } else if (cedulaCounts[cedula] === 1 && /^\d{7,8}$/.test(cedula)) {
                            uniqueCedulas.push(cedula);
                        }
                    });

                    if (uniqueCedulas.length === 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se encontraron cédulas válidas (7 u 8 dígitos, no vacías ni duplicadas).',
                            confirmButtonText: 'Aceptar',
                            customClass: { confirmButton: 'btn btn-primary' }
                        });
                        return;
                    }

                    var formData = new FormData();
                    formData.append('cedulas', JSON.stringify(uniqueCedulas));
                    console.log('Enviando solicitud a:', window.FiscalizacionesRoutes.checkDuplicates);
                    $.ajax({
                        url: window.FiscalizacionesRoutes.checkDuplicates,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function(preResponse) {
                            console.log('Respuesta de prevalidación:', preResponse);
                            duplicateCedulasDB = preResponse.already_loaded || [];
                            var validCedulas = uniqueCedulas.filter(cedula => !duplicateCedulasDB.some(dbCedula => dbCedula.cedula === cedula));

                            var summaryTable = `
                                <div class="alert alert-info">
                                    <h6>Resumen de Cédulas</h6>
                                    <table class="table table-sm table-bordered">
                                        <thead><tr><th>Tipo</th><th>Cédula</th><th>Fila (si aplica)</th></tr></thead>
                                        <tbody>
                                            ${duplicateCedulasFile.map(d => `<tr><td>Duplicado en archivo</td><td style="background-color: #fff7e6;">${d.cedula}</td><td>${d.row}</td></tr>`).join('')}
                                            ${duplicateCedulasDB.map(d => `<tr><td>Ya cargado en DB</td><td style="background-color: #fff7e6;">${d.cedula}</td><td>-</td></tr>`).join('')}
                                            ${validCedulas.map((cedula) => `<tr><td>Válido para registro</td><td style="background-color: #d4edda;">${cedula}</td><td>-</td></tr>`).join('')}
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
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Columnas detectadas:</strong> ${headers.join(', ')}</p>
                                                ${summaryTable}
                                                <h6>Vista previa de los primeros ${previewData.length} registros:</h6>
                                                <div style="overflow-x: auto;"><table class="table table-sm table-bordered table-responsive"><thead><tr>${headers.map(h => `<th>${h.replace(/_/g, ' ').toUpperCase()}</th>`).join('')}</tr></thead><tbody>${previewData.map(row => `<tr>${headers.map(h => `<td>${row[h] || ''}</td>`).join('')}</tr>`).join('')}</tbody></table></div>
                                            </div>
                                            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button><button type="button" class="btn btn-primary" id="proceedWithUpload">Continuar</button></div>
                                        </div>
                                    </div>
                                </div>`;
                            $('body').append(modalContent);
                            $('#csvPreviewModal').modal('show');

                            $('#proceedWithUpload').on('click', function() {
                                $('#csvPreviewModal').modal('hide');
                                submitUpload(file, { cedulas: validCedulas });
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
                            console.error('Error de prevalidación:', xhr.responseText, 'URL:', window.FiscalizacionesRoutes.checkDuplicates);
                            let errorMessage = 'Error al verificar duplicados contra la base de datos.';
                            if (xhr.status === 422 && xhr.responseJSON?.details) {
                                errorMessage += ' El backend está aplicando validaciones incorrectas.';
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

            if (!window.FiscalizacionesRoutes || !window.FiscalizacionesRoutes.storeMassive) {
                console.error('FiscalizacionesRoutes.storeMassive no está definido. Verifica la configuración de rutas.');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'La ruta para almacenar datos masivos no está definida. Verifica la configuración de rutas.',
                    confirmButtonText: 'Aceptar',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
                return;
            }

            var formData = new FormData();
            formData.append('file', file);
            formData.append('cedulas', JSON.stringify(columnMapping.cedulas));
            formData.append('is_fiscalizaciones_table', $('#fiscalizacionesTable').length ? '1' : '0');
            console.log('Enviando archivo a:', window.FiscalizacionesRoutes.storeMassive, 'Archivo:', file.name, 'Tamaño:', file.size);

            $.ajax({
                url: window.FiscalizacionesRoutes.storeMassive,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    console.log('Respuesta del servidor en submitUpload:', response);
                    $('#uploadModal').modal('hide');
                    showImportSummary(response);
                    var tableId = $('#fiscalizacionesTable').length ? '#fiscalizacionesTable' : '#fiscalizacionesTableAll';
                    if ($(tableId).length && $.fn.DataTable.isDataTable(tableId)) {
                        $(tableId).DataTable().ajax.reload(null, false);
                    }
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

        function showImportSummary(response) {
            console.log('Respuesta del servidor en showImportSummary:', response);
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

            const displayColumns = ['estado', 'municipio', 'parroquia', 'nombre_pto', 'cedula', 'apellidos', 'nombres', 'telefono', 'correo', 'incidencia', 'fecha_incidencia', 'hora_incidencia'];
            let imported = response.imported || 0;
            let failedRows = response.failed_rows || [];
            let successRows = response.success_rows || [];

            var failedRowsDetails = failedRows.length > 0 ? `
                <div class="alert alert-warning mt-3">
                    <strong>Registros fallidos (${failedRows.length}):</strong>
                    <p>Los siguientes registros no se pudieron importar debido a errores:</p>
                    <div style="overflow-x: auto;">
                        <table class="table table-sm table-bordered table-responsive">
                            <thead><tr><th>Fila</th>${displayColumns.map(col => `<th>${col.replace(/_/g, ' ').toUpperCase()}</th>`).join('')}<th>Error</th></tr></thead>
                            <tbody>${failedRows.map((row, index) => `
                                <tr><td>${index + 1}</td>${displayColumns.map(col => `<td>${row.row[col] || 'N/A'}</td>`).join('')}<td>${Object.entries(row.errors || {}).map(([field, errors]) => `${field.replace(/_/g, ' ').toUpperCase()}: ${errors.join(', ')}`).join('<br>')}</td></tr>`).join('')}</tbody>
                        </table>
                    </div>
                </div>` : '';

            var successRowsDetails = successRows.length > 0 ? `
                <div class="alert alert-success mt-3">
                    <strong>Registros importados (${successRows.length}):</strong>
                    <p>Los siguientes registros se importaron correctamente:</p>
                    <div style="overflow-x: auto;">
                        <table class="table table-sm table-bordered table-responsive">
                            <thead><tr><th>Fila</th>${displayColumns.map(col => `<th>${col.replace(/_/g, ' ').toUpperCase()}</th>`).join('')}</tr></thead>
                            <tbody>${successRows.map((row, index) => `
                                <tr><td>${index + 1}</td>${displayColumns.map(col => `<td>${row[col] || 'N/A'}</td>`).join('')}</tr>`).join('')}</tbody>
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
                                <p><strong>Total de registros procesados:</strong> ${imported + failedRows.length || 0}</p>
                                <p><strong>Registros importados exitosamente:</strong> ${imported || 0}</p>
                                <p><strong>Registros fallidos:</strong> ${failedRows.length || 0}</p>
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

                previewCsvFile(file);
            });
        }).on('hidden.bs.modal', function() {
            const $modal = $(this);
            if ($modal.length) {
                $modal.find('form')[0]?.reset();
                $modal.find('.is-invalid').removeClass('is-invalid');
                $modal.find('.invalid-feedback').text('');
                $modal.remove();
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css('padding-right', '');
            }
        });

        // Manejar envío del formulario de creación con campos de incidencias
        $('#createForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            const formData = $(this).serialize();
            let route;
            if ($('#fiscalizacionesTable').length) {
                route = window.FiscalizacionesRoutes.store;
            } else if ($('#fiscalizacionesTableAll').length) {
                route = window.FiscalizacionesRoutes.storeAll;
            } else {
                console.error('No se encontró ninguna tabla de fiscalizaciones para determinar la ruta de creación.');
                return;
            }
            if (!route) {
                console.error('Ruta de creación no definida para la tabla actual.');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'La ruta de creación no está configurada correctamente.',
                    confirmButtonText: 'Aceptar',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
                return;
            }
            $.ajax({
                url: route,
                method: 'POST',
                data: formData,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    $('#createModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: response.message,
                        confirmButtonText: 'Aceptar',
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                    var tableId = $('#fiscalizacionesTable').length ? '#fiscalizacionesTable' : '#fiscalizacionesTableAll';
                    if ($(tableId).length && $.fn.DataTable.isDataTable(tableId)) {
                        $(tableId).DataTable().ajax.reload(null, false);
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Error al crear la fiscalización.';
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
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

        // Manejar envío del formulario de edición con campos de incidencias
        $('#editForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            const id = $('#edit_id').val();
            const formData = $(this).serialize();
            console.log('Datos enviados:', formData);
            let route;
            if ($('#fiscalizacionesTable').length) {
                route = window.FiscalizacionesRoutes.update.replace(':id', id);
            } else if ($('#fiscalizacionesTableAll').length) {
                route = window.FiscalizacionesRoutes.updateAll.replace(':id', id);
            } else {
                console.error('No se encontró ninguna tabla de fiscalizaciones.');
                return;
            }
            if (!route) {
                console.error('Ruta de actualización no definida para la tabla actual.');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'La ruta de actualización no está configurada correctamente.',
                    confirmButtonText: 'Aceptar',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
                return;
            }
            $.ajax({
                url: route,
                method: 'PUT',
                data: formData,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    $('#editModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: response.message,
                        confirmButtonText: 'Aceptar',
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                    var tableId = $('#fiscalizacionesTable').length ? '#fiscalizacionesTable' : '#fiscalizacionesTableAll';
                    if ($(tableId).length && $.fn.DataTable.isDataTable(tableId)) {
                        $(tableId).DataTable().ajax.reload(null, false);
                    }
                },
                error: function(xhr) {
                    console.error('Error al actualizar la fiscalización:', xhr.responseText);
                    let errorMessage = 'Error al actualizar la fiscalización.';
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
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

        // Manejar cierre de todos los modales
        $('#uploadModal, #csvPreviewModal, #importSummaryModal, #createModal, #editModal').on('hidden.bs.modal', function() {
            const $modal = $(this);
            if ($modal.length) {
                const $form = $modal.find('form');
                if ($form.length) {
                    $form[0].reset();
                    $form.find('.is-invalid').removeClass('is-invalid');
                    $form.find('.invalid-feedback').text('');
                }
                $modal.remove();
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css('padding-right', '');
            }
        });
    });
})(jQuery);
