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
                <input type="text" class="form-control" name="telefono" value="<?= e(old('telefono')) ?>" placeholder="Ej. 984123456">
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
                            <?= e($tt['nombre']) ?> (Plazo: <?= $tt['plazo_referencial_dias'] ?> días)
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
                <input type="file" class="form-control" name="anexos[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.png">
                <small class="text-muted d-block mt-1">Puedes adjuntar boletas de notas, recibos de pago u otros sustentos.</small>
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
    });
</script>
