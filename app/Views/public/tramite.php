<?php
$validationErrors = \App\Core\Session::get('_validation_errors', []);
\App\Core\Session::remove('_validation_errors');
$tipoSeleccionado = $_GET['tipo'] ?? old('tipo_tramite_id', '');
?>

<div class="row justify-content-center py-4">
    <div class="col-12 col-xl-10">
        <div class="text-center mb-4">
            <span class="badge px-3 py-2 text-white fw-bold mb-2" style="background-color: var(--primary, #0B4F8A); border-radius: 20px; font-size: 0.8rem;">
                MESA DE PARTES DIGITAL
            </span>
            <h2 class="fw-bold" style="color: var(--primary, #0B4F8A);">Presentación de Trámite Virtual</h2>
            <p class="text-muted">Completa el siguiente formulario para ingresar formalmente tu solicitud institucional.</p>
        </div>

        <div class="card card-portal p-4 p-md-5">
            <form action="<?= url('/tramite') ?>" method="POST" enctype="multipart/form-data" autocomplete="off">
                <?= csrf_field() ?>

                <!-- SECCIÓN 1: DATOS DEL SOLICITANTE -->
                <h5 class="fw-bold text-primary mb-3 pb-2 border-bottom d-flex align-items-center gap-2">
                    <span class="badge rounded-circle bg-primary text-white" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem;">1</span>
                    Identificación del Solicitante
                </h5>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Tipo de Persona <span class="text-danger">*</span></label>
                        <select name="tipo_persona" id="tipoPersonaSelect" class="form-select" required>
                            <option value="NATURAL" <?= (old('tipo_persona') === 'NATURAL') ? 'selected' : '' ?>>Persona Natural (Ciudadano / Estudiante / Egresado)</option>
                            <option value="JURIDICA" <?= (old('tipo_persona') === 'JURIDICA') ? 'selected' : '' ?>>Persona Jurídica (Empresa / Entidad)</option>
                        </select>
                    </div>

                    <div class="col-6 col-md-3">
                        <label class="form-label fw-semibold">Tipo de Documento <span class="text-danger">*</span></label>
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
                        <label class="form-label fw-semibold">Nombres Completos <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control <?= isset($validationErrors['nombres']) ? 'is-invalid' : '' ?>" 
                               name="nombres" 
                               id="nombresInput"
                               value="<?= e(old('nombres')) ?>" 
                               placeholder="Tus nombres">
                        <?php if (isset($validationErrors['nombres'])): ?>
                            <div class="invalid-feedback"><?= e($validationErrors['nombres'][0]) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Apellidos Completos <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               name="apellidos" 
                               id="apellidosInput"
                               value="<?= e(old('apellidos')) ?>" 
                               placeholder="Tus apellidos">
                    </div>
                </div>

                <div class="mb-3" id="campoPersonaJuridica" style="display: none;">
                    <label class="form-label fw-semibold">Razón Social <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control" 
                           name="razon_social" 
                           id="razonSocialInput"
                           value="<?= e(old('razon_social')) ?>" 
                           placeholder="Nombre oficial de la persona jurídica o entidad">
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Correo Electrónico (Para Notificaciones) <span class="text-danger">*</span></label>
                        <input type="email" 
                               class="form-control <?= isset($validationErrors['correo']) ? 'is-invalid' : '' ?>" 
                               name="correo" 
                               value="<?= e(old('correo')) ?>" 
                               placeholder="tu_correo@gmail.com" 
                               required>
                        <?php if (isset($validationErrors['correo'])): ?>
                            <div class="invalid-feedback"><?= e($validationErrors['correo'][0]) ?></div>
                        <?php endif; ?>
                        <small class="text-muted" style="font-size: 0.75rem;">Aquí te llegará la confirmación y respuestas oficiales.</small>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Teléfono / WhatsApp <span class="text-danger">*</span></label>
                        <input type="tel" 
                               class="form-control" 
                               id="inputTelefono"
                               name="telefono" 
                               inputmode="numeric"
                               pattern="[0-9]{6,15}"
                               autocomplete="tel"
                               maxlength="15"
                               value="<?= e(old('telefono')) ?>" 
                               placeholder="Ej. 984000000" 
                               title="Ingrese solo números (6 a 15 dígitos)"
                               required
                               oninput="this.value = this.value.replace(/\D/g, '')">
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Dirección Actual</label>
                        <input type="text" 
                               class="form-control" 
                               name="direccion" 
                               value="<?= e(old('direccion')) ?>" 
                               placeholder="Dirección referencial">
                    </div>
                </div>

                <!-- SECCIÓN 2: DETALLE DEL TRÁMITE -->
                <h5 class="fw-bold text-primary mb-3 pb-2 border-bottom d-flex align-items-center gap-2">
                    <span class="badge rounded-circle bg-primary text-white" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem;">2</span>
                    Información del Documento y Petición
                </h5>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-8">
                        <label class="form-label fw-semibold">Tipo de Trámite a Solicitar <span class="text-danger">*</span></label>
                        <select name="tipo_tramite_id" id="tipoTramiteSelect" class="form-select" required>
                            <option value="">-- Seleccionar Trámite --</option>
                            <?php foreach ($tramites as $tr): ?>
                                <option value="<?= $tr['id'] ?>" 
                                        data-plazo="<?= $tr['plazo_referencial_dias'] ?>"
                                        data-requisitos="<?= e($tr['requisitos']) ?>"
                                        data-costo="<?= $tr['requiere_pago'] ? 'S/ ' . number_format($tr['monto'], 2) : 'Gratuito' ?>"
                                        <?= ($tipoSeleccionado == $tr['id']) ? 'selected' : '' ?>>
                                    <?= e($tr['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Cantidad Total de Folios (Hojas) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="folios" value="<?= e(old('folios', 1)) ?>" min="1" required>
                        <small class="text-muted" style="font-size: 0.75rem;">Suma del documento principal y todos los anexos.</small>
                    </div>
                </div>

                <!-- Caja de requisitos del trámite seleccionado -->
                <div id="requisitosBox" class="p-3 bg-light rounded border mb-3" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong class="text-primary small"><i class="bi bi-info-circle me-1"></i> Requisitos y Condiciones del Trámite:</strong>
                        <span class="badge bg-secondary" id="tramiteCostoBadge"></span>
                    </div>
                    <div class="small text-muted" id="requisitosText"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Asunto de la Solicitud <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control <?= isset($validationErrors['asunto']) ? 'is-invalid' : '' ?>" 
                           name="asunto" 
                           value="<?= e(old('asunto')) ?>" 
                           placeholder="Ej. Solicito emisión de Certificado Oficial de Estudios Modulares" 
                           required>
                    <?php if (isset($validationErrors['asunto'])): ?>
                        <div class="invalid-feedback"><?= e($validationErrors['asunto'][0]) ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Exposición de Motivos / Detalle</label>
                    <textarea class="form-control" name="descripcion" rows="3" placeholder="Fundamenta tu petición, periodo cursado, programa de estudios u observaciones..."><?= e(old('descripcion')) ?></textarea>
                </div>

                <!-- SECCIÓN 3: ARCHIVOS ADJUNTOS -->
                <h5 class="fw-bold text-primary mb-3 pb-2 border-bottom d-flex align-items-center gap-2">
                    <span class="badge rounded-circle bg-primary text-white" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem;">3</span>
                    Documentos Digitales
                </h5>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Documento Principal (FUT / Carta Solicitud) <span class="text-danger">*</span></label>
                        <input type="file" 
                               class="form-control <?= isset($validationErrors['archivo_principal']) ? 'is-invalid' : '' ?>" 
                               name="archivo_principal" 
                               accept=".pdf,.doc,.docx,.jpg,.png" 
                               required>
                        <small class="text-muted" style="font-size: 0.75rem;">Documento firmado en PDF o imagen legible (Máx. 25 MB).</small>
                        <?php if (isset($validationErrors['archivo_principal'])): ?>
                            <div class="invalid-feedback"><?= e($validationErrors['archivo_principal'][0]) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Archivos de Sustento o Anexos (Opcional)</label>
                        <input type="file" class="form-control" id="inputAnexos" name="anexos[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                        <small class="text-muted" style="font-size: 0.75rem;">Comprobantes de pago, boletas, DNI escaneado, etc. (Máx. 25 MB por archivo).</small>
                        
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

                <!-- SECCIÓN 4: DECLARACIONES Y SEGURIDAD -->
                <div class="p-3 bg-light rounded border mb-4">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="declaracion_veracidad" value="1" id="declVeracidadCheck" required>
                        <label class="form-check-label small fw-semibold" for="declVeracidadCheck">
                            Declaro bajo juramento que toda la información consignada y los documentos adjuntos son verdaderos y fidedignos, sujetándome a las sanciones legales pertinentes. <span class="text-danger">*</span>
                        </label>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="tratamiento_datos" value="1" id="declDatosCheck" checked required>
                        <label class="form-check-label small text-muted" for="declDatosCheck">
                            Autorizo la notificación electrónica mediante el correo consignado y el tratamiento de mis datos personales para la atención exclusiva de este trámite. <span class="text-danger">*</span>
                        </label>
                    </div>

                    <!-- Captcha Anti-Spam -->
                    <div class="row align-items-center g-2 pt-2 border-top">
                        <div class="col-12 col-md-5">
                            <label class="form-label fw-bold text-dark small mb-0">
                                <i class="bi bi-shield-check text-success me-1"></i> Control de Seguridad Anti-Spam:
                            </label>
                            <div class="small text-muted">Resuelve: <strong><?= e($captchaQuestion) ?></strong></div>
                        </div>
                        <div class="col-12 col-md-3">
                            <input type="number" class="form-control" name="captcha" placeholder="Tu respuesta" required>
                        </div>
                    </div>
                </div>

                <div class="text-center pt-2">
                    <button type="submit" class="btn btn-primary-portal px-5 py-3 fs-6">
                        <i class="bi bi-send-check-fill me-2 fs-5"></i> Enviar Trámite y Generar Cargo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tipoPersona = document.getElementById('tipoPersonaSelect');
        const camposNat = document.getElementById('camposPersonaNatural');
        const campoJur = document.getElementById('campoPersonaJuridica');
        const tipoDoc = document.getElementById('tipoDocSelect');
        const numDocInput = document.getElementById('numDocInput');
        const nombresInp = document.getElementById('nombresInput');

        function actualizarTipoDoc() {
            if (tipoDoc.value === 'DNI') {
                numDocInput.maxLength = 8;
                numDocInput.placeholder = 'Ej. 77088658 (8 dígitos)';
            } else if (tipoDoc.value === 'RUC') {
                numDocInput.maxLength = 11;
                numDocInput.placeholder = 'Ej. 20490000001 (11 dígitos)';
            } else if (tipoDoc.value === 'CE') {
                numDocInput.maxLength = 12;
                numDocInput.placeholder = 'Carné de Extranjería';
            } else {
                numDocInput.maxLength = 20;
                numDocInput.placeholder = 'Número de Documento';
            }
        }

        tipoDoc.addEventListener('change', actualizarTipoDoc);

        if (numDocInput) {
            numDocInput.addEventListener('input', function() {
                if (tipoDoc.value === 'DNI' || tipoDoc.value === 'RUC') {
                    this.value = this.value.replace(/\D/g, '');
                }
            });
        }

        function actualizarTipoPersona() {
            if (tipoPersona.value === 'JURIDICA') {
                camposNat.style.display = 'none';
                campoJur.style.display = 'block';
                tipoDoc.value = 'RUC';
                nombresInp.value = 'Representante';
            } else {
                camposNat.style.display = 'flex';
                campoJur.style.display = 'none';
                if (tipoDoc.value === 'RUC') {
                    tipoDoc.value = 'DNI';
                }
                if (nombresInp.value === 'Representante') {
                    nombresInp.value = '';
                }
            }
            actualizarTipoDoc();
        }

        tipoPersona.addEventListener('change', actualizarTipoPersona);
        actualizarTipoPersona();

        // Actualizar caja de requisitos al cambiar de trámite
        const tramiteSelect = document.getElementById('tipoTramiteSelect');
        const reqBox = document.getElementById('requisitosBox');
        const reqText = document.getElementById('requisitosText');
        const costoBadge = document.getElementById('tramiteCostoBadge');

        function actualizarRequisitos() {
            const opt = tramiteSelect.selectedOptions[0];
            if (opt && opt.value) {
                const reqs = opt.dataset.requisitos;
                const costo = opt.dataset.costo;
                reqText.textContent = reqs || 'Presentar solicitud formal con fundamentación y copias legibles.';
                costoBadge.textContent = 'Arancel: ' + costo;
                reqBox.style.display = 'block';
            } else {
                reqBox.style.display = 'none';
            }
        }

        tramiteSelect.addEventListener('change', actualizarRequisitos);
        actualizarRequisitos();

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
