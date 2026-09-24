<?php
$validationErrors = \App\Core\Session::get('_validation_errors', []);
\App\Core\Session::remove('_validation_errors');
?>

<div class="mb-4">
    <a href="<?= url('/expedientes') ?>" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1 mb-2">
        <i class="bi bi-arrow-left"></i> Volver a la bandeja
    </a>
    <h4 class="fw-bold mb-1" style="color: var(--primary, #0B4F8A);">Registro de Trámite Presencial (Mesa de Partes)</h4>
    <p class="text-muted small mb-0">Recepción de documentación física presentada en ventanilla institucional.</p>
</div>

<div class="card card-custom p-4" style="max-width: 900px;">
    <form action="<?= url('/expedientes') ?>" method="POST" enctype="multipart/form-data" autocomplete="off">
        <?= csrf_field() ?>

        <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom">
            <i class="bi bi-person-lines-fill me-2"></i> 1. Datos del Solicitante
        </h6>

        <div class="row g-3 mb-3">
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Tipo de Persona <span class="text-danger">*</span></label>
                <select name="tipo_persona" id="tipoPersonaSelect" class="form-select" required>
                    <option value="NATURAL" <?= (old('tipo_persona') === 'NATURAL') ? 'selected' : '' ?>>Persona Natural</option>
                    <option value="JURIDICA" <?= (old('tipo_persona') === 'JURIDICA') ? 'selected' : '' ?>>Persona Jurídica (Empresa/Institución)</option>
                </select>
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold">Tipo Doc. <span class="text-danger">*</span></label>
                <select name="tipo_documento" id="tipoDocSelect" class="form-select" required>
                    <option value="DNI">DNI</option>
                    <option value="CE">Carné de Extranjería</option>
                    <option value="RUC">RUC</option>
                    <option value="PASAPORTE">Pasaporte</option>
                </select>
            </div>

            <div class="col-6 col-md-5">
                <label class="form-label fw-semibold">Número de Documento <span class="text-danger">*</span></label>
                <input type="text" 
                       class="form-control <?= isset($validationErrors['numero_documento']) ? 'is-invalid' : '' ?>" 
                       name="numero_documento" 
                       id="numDocInput"
                       maxlength="20"
                       value="<?= e(old('numero_documento')) ?>" 
                       placeholder="Ej. 70123456"
                       required>
                <?php if (isset($validationErrors['numero_documento'])): ?>
                    <div class="invalid-feedback"><?= e($validationErrors['numero_documento'][0]) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="row g-3 mb-3" id="camposPersonaNatural">
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Nombres <span class="text-danger">*</span></label>
                <input type="text" 
                       class="form-control <?= isset($validationErrors['nombres']) ? 'is-invalid' : '' ?>" 
                       name="nombres" 
                       id="nombresInput"
                       value="<?= e(old('nombres')) ?>">
                <?php if (isset($validationErrors['nombres'])): ?>
                    <div class="invalid-feedback"><?= e($validationErrors['nombres'][0]) ?></div>
                <?php endif; ?>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Apellidos <span class="text-danger">*</span></label>
                <input type="text" 
                       class="form-control" 
                       name="apellidos" 
                       id="apellidosInput"
                       value="<?= e(old('apellidos')) ?>">
            </div>
        </div>

        <div class="mb-3" id="campoPersonaJuridica" style="display: none;">
            <label class="form-label fw-semibold">Razón Social <span class="text-danger">*</span></label>
            <input type="text" 
                   class="form-control" 
                   name="razon_social" 
                   id="razonSocialInput"
                   value="<?= e(old('razon_social')) ?>" 
                   placeholder="Nombre oficial de la empresa o entidad">
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Correo Electrónico <span class="text-danger">*</span></label>
                <input type="email" 
                       class="form-control <?= isset($validationErrors['correo']) ? 'is-invalid' : '' ?>" 
                       name="correo" 
                       value="<?= e(old('correo')) ?>" 
                       required>
                <?php if (isset($validationErrors['correo'])): ?>
                    <div class="invalid-feedback"><?= e($validationErrors['correo'][0]) ?></div>
                <?php endif; ?>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Teléfono / Celular</label>
                <input type="tel" 
                       class="form-control <?= isset($validationErrors['telefono']) ? 'is-invalid' : '' ?>" 
                       name="telefono" 
                       inputmode="numeric"
                       pattern="[0-9]{6,15}"
                       autocomplete="tel"
                       maxlength="15"
                       value="<?= e(old('telefono')) ?>" 
                       placeholder="Ej. 984123456"
                       title="Ingrese solo números (6 a 15 dígitos)"
                       oninput="this.value = this.value.replace(/\D/g, '')">
                <?php if (isset($validationErrors['telefono'])): ?>
                    <div class="invalid-feedback"><?= e($validationErrors['telefono'][0]) ?></div>
                <?php endif; ?>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Dirección</label>
                <input type="text" class="form-control" name="direccion" value="<?= e(old('direccion')) ?>" placeholder="Ciudad, calle, número">
            </div>
        </div>

        <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom">
            <i class="bi bi-file-earmark-text me-2"></i> 2. Detalle del Documento
        </h6>

        <div class="row g-3 mb-3">
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Tipo de Trámite <span class="text-danger">*</span></label>
                <select name="tipo_tramite_id" class="form-select" required>
                    <option value="">-- Seleccionar Trámite --</option>
                    <?php foreach ($tiposTramite as $tt): ?>
                        <option value="<?= $tt['id'] ?>" <?= (old('tipo_tramite_id') == $tt['id']) ? 'selected' : '' ?>>
                            <?= e($tt['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold">Prioridad <span class="text-danger">*</span></label>
                <select name="prioridad_id" class="form-select" required>
                    <?php foreach ($prioridades as $pr): ?>
                        <option value="<?= $pr['id'] ?>" <?= (old('prioridad_id', 1) == $pr['id']) ? 'selected' : '' ?>>
                            <?= e($pr['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold">Nº de Folios <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="folios" value="<?= e(old('folios', 1)) ?>" min="1" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Asunto del Trámite <span class="text-danger">*</span></label>
            <input type="text" 
                   class="form-control <?= isset($validationErrors['asunto']) ? 'is-invalid' : '' ?>" 
                   name="asunto" 
                   placeholder="Ej. Solicito emisión de constancia de egresado" 
                   value="<?= e(old('asunto')) ?>" 
                   required>
            <?php if (isset($validationErrors['asunto'])): ?>
                <div class="invalid-feedback"><?= e($validationErrors['asunto'][0]) ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Descripción o Fundamentación</label>
            <textarea class="form-control" name="descripcion" rows="3" placeholder="Detalle adicional de la solicitud o exposición de motivos..."><?= e(old('descripcion')) ?></textarea>
        </div>

        <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom">
            <i class="bi bi-paperclip me-2"></i> 3. Documentación Digitalizada
        </h6>

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Documento Principal (FUT / Carta / Solicitud) <span class="text-danger">*</span></label>
                <input type="file" 
                       class="form-control <?= isset($validationErrors['archivo_principal']) ? 'is-invalid' : '' ?>" 
                       name="archivo_principal" 
                       accept=".pdf,.doc,.docx,.jpg,.png" 
                       required>
                <small class="text-muted d-block mt-1">Formatos admitidos: PDF, DOCX, JPG, PNG (Máx. 25 MB).</small>
                <?php if (isset($validationErrors['archivo_principal'])): ?>
                    <div class="invalid-feedback"><?= e($validationErrors['archivo_principal'][0]) ?></div>
                <?php endif; ?>
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Documentos Anexos (Opcional)</label>
                <input type="file" class="form-control" id="inputAnexos" name="anexos[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                <small class="text-muted d-block mt-1">Puedes adjuntar boletas de notas, recibos de pago u otros sustentos (Máx. 25 MB por archivo).</small>
                
                <!-- Lista visual interactiva de documentos adjuntos seleccionados -->
                <div id="contenedorListaAnexos" class="mt-2" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-bold text-secondary">
                            <i class="bi bi-paperclip me-1"></i> Documentos seleccionados (<span id="contadorAnexos">0</span>):
                        </span>
                        <button type="button" class="btn btn-link btn-sm text-danger p-0 text-decoration-none" id="btnLimpiarAnexos" style="font-size: 0.75rem;">
                            Limpiar todos
                        </button>
                    </div>
                    <div id="listaVisualAnexos" class="d-flex flex-column gap-2"></div>
                </div>
                <div id="alertaErrorAnexos" class="alert alert-danger py-1 px-2 small mt-2" style="display: none;"></div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 pt-3 border-top">
            <button type="submit" class="btn btn-primary-custom px-4 py-2">
                <i class="bi bi-check-circle-fill me-1"></i> Generar Expediente y Registrar
            </button>
            <a href="<?= url('/expedientes') ?>" class="btn btn-outline-secondary px-4 py-2">
                Cancelar
            </a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tipoPersonaSelect = document.getElementById('tipoPersonaSelect');
        const camposNatural = document.getElementById('camposPersonaNatural');
        const campoJuridica = document.getElementById('campoPersonaJuridica');
        const tipoDocSelect = document.getElementById('tipoDocSelect');
        const numDocInput = document.getElementById('numDocInput');
        const nombresInput = document.getElementById('nombresInput');
        const razonSocialInput = document.getElementById('razonSocialInput');

        function actualizarTipoDoc() {
            if (tipoDocSelect.value === 'DNI') {
                numDocInput.maxLength = 8;
                numDocInput.placeholder = 'Ej. 77088658 (8 dígitos)';
            } else if (tipoDocSelect.value === 'RUC') {
                numDocInput.maxLength = 11;
                numDocInput.placeholder = 'Ej. 20490000001 (11 dígitos)';
            } else if (tipoDocSelect.value === 'CE') {
                numDocInput.maxLength = 12;
                numDocInput.placeholder = 'Carné de Extranjería';
            } else {
                numDocInput.maxLength = 20;
                numDocInput.placeholder = 'Número de Documento';
            }
        }

        tipoDocSelect.addEventListener('change', actualizarTipoDoc);

        if (numDocInput) {
            numDocInput.addEventListener('input', function() {
                if (tipoDocSelect.value === 'DNI' || tipoDocSelect.value === 'RUC') {
                    this.value = this.value.replace(/\D/g, '');
                }
            });
        }

        function togglePersona() {
            if (tipoPersonaSelect.value === 'JURIDICA') {
                camposNatural.style.display = 'none';
                campoJuridica.style.display = 'block';
                tipoDocSelect.value = 'RUC';
                nombresInput.value = 'Representante';
            } else {
                camposNatural.style.display = 'flex';
                campoJuridica.style.display = 'none';
                if (tipoDocSelect.value === 'RUC') {
                    tipoDocSelect.value = 'DNI';
                }
                if (nombresInput.value === 'Representante') {
                    nombresInput.value = '';
                }
            }
            actualizarTipoDoc();
        }

        tipoPersonaSelect.addEventListener('change', togglePersona);
        togglePersona();

        // Gestión de archivos adjuntos con DataTransfer (Observación 2)
        const inputAnexos = document.getElementById('inputAnexos');
        const contAnexos = document.getElementById('contenedorListaAnexos');
        const listAnexos = document.getElementById('listaVisualAnexos');
        const countSpan = document.getElementById('contadorAnexos');
        const errorAnexos = document.getElementById('alertaErrorAnexos');
        const btnLimpiar = document.getElementById('btnLimpiarAnexos');

        if (inputAnexos && contAnexos && listAnexos) {
            let dtAnexos = new DataTransfer();
            const maxMbAnexo = 25;
            const extsValidas = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png'];

            function formatTamano(bytes) {
                if (bytes === 0) return '0 B';
                const k = 1024;
                const unidades = ['B', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + unidades[i];
            }

            function getIconoExt(ext) {
                switch(ext) {
                    case 'pdf': return { icon: 'bi-file-earmark-pdf-fill', color: 'danger' };
                    case 'doc':
                    case 'docx': return { icon: 'bi-file-earmark-word-fill', color: 'primary' };
                    case 'xls':
                    case 'xlsx': return { icon: 'bi-file-earmark-excel-fill', color: 'success' };
                    case 'jpg':
                    case 'jpeg':
                    case 'png': return { icon: 'bi-file-earmark-image-fill', color: 'info' };
                    default: return { icon: 'bi-file-earmark-fill', color: 'secondary' };
                }
            }

            function escapeTexto(str) {
                const div = document.createElement('div');
                div.textContent = str;
                return div.innerHTML;
            }

            function refrescarListaAnexos() {
                listAnexos.innerHTML = '';
                if (dtAnexos.files.length === 0) {
                    contAnexos.style.display = 'none';
                    inputAnexos.value = '';
                    return;
                }

                contAnexos.style.display = 'block';
                countSpan.textContent = dtAnexos.files.length;

                Array.from(dtAnexos.files).forEach((file, index) => {
                    const ext = file.name.split('.').pop().toLowerCase();
                    const icono = getIconoExt(ext);
                    const peso = formatTamano(file.size);

                    const card = document.createElement('div');
                    card.className = 'p-2 border rounded bg-white d-flex align-items-center justify-content-between gap-2 shadow-sm';
                    card.innerHTML = `
                        <div class="d-flex align-items-center gap-2 text-truncate" style="min-width: 0;">
                            <i class="bi ${icono.icon} text-${icono.color} fs-5 flex-shrink-0"></i>
                            <div class="text-truncate">
                                <div class="fw-semibold text-truncate text-dark small" title="${escapeTexto(file.name)}">${escapeTexto(file.name)}</div>
                                <small class="text-muted" style="font-size: 0.72rem;">
                                    <span class="badge bg-light text-dark border me-1">${ext.toUpperCase()}</span> ${peso}
                                </small>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-danger btn-sm px-2 py-1 flex-shrink-0" aria-label="Eliminar archivo ${escapeTexto(file.name)}" title="Eliminar archivo">
                            <i class="bi bi-trash3"></i> <span class="d-none d-sm-inline ms-1" style="font-size: 0.75rem;">Eliminar</span>
                        </button>
                    `;

                    card.querySelector('button').addEventListener('click', function(e) {
                        e.preventDefault();
                        eliminarArchivoAnexo(index);
                    });

                    listAnexos.appendChild(card);
                });
            }

            function eliminarArchivoAnexo(indice) {
                const nuevoDt = new DataTransfer();
                Array.from(dtAnexos.files).forEach((file, i) => {
                    if (i !== indice) {
                        nuevoDt.items.add(file);
                    }
                });
                dtAnexos = nuevoDt;
                inputAnexos.files = dtAnexos.files;
                refrescarListaAnexos();
            }

            inputAnexos.addEventListener('change', function() {
                errorAnexos.style.display = 'none';
                errorAnexos.textContent = '';
                const errores = [];

                Array.from(this.files).forEach(file => {
                    const ext = file.name.split('.').pop().toLowerCase();
                    if (!extsValidas.includes(ext)) {
                        errores.push(`"${file.name}": Formato .${ext} no permitido.`);
                        return;
                    }
                    if (file.size > maxMbAnexo * 1024 * 1024) {
                        errores.push(`"${file.name}": Supera el tamaño máximo de ${maxMbAnexo} MB.`);
                        return;
                    }
                    const esDuplicado = Array.from(dtAnexos.files).some(f => f.name === file.name && f.size === file.size);
                    if (esDuplicado) {
                        errores.push(`"${file.name}": Ya está en la lista de archivos seleccionados.`);
                        return;
                    }
                    dtAnexos.items.add(file);
                });

                inputAnexos.files = dtAnexos.files;
                refrescarListaAnexos();

                if (errores.length > 0) {
                    errorAnexos.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> ' + errores.join('<br>');
                    errorAnexos.style.display = 'block';
                }
            });

            if (btnLimpiar) {
                btnLimpiar.addEventListener('click', function(e) {
                    e.preventDefault();
                    dtAnexos = new DataTransfer();
                    inputAnexos.files = dtAnexos.files;
                    inputAnexos.value = '';
                    errorAnexos.style.display = 'none';
                    refrescarListaAnexos();
                });
            }
        }
    });
</script>
