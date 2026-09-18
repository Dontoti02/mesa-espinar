<div class="row justify-content-center py-5">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="text-center mb-4">
            <span class="badge px-3 py-2 text-white fw-bold mb-2" style="background-color: var(--secondary, #F59E0B); border-radius: 20px; font-size: 0.8rem;">
                SEGUIMIENTO CIUDADANO
            </span>
            <h2 class="fw-bold" style="color: var(--primary, #0B4F8A);">Consulta el Estado de tu Trámite</h2>
            <p class="text-muted">Introduce tu número de expediente y/o código de seguimiento para conocer su ubicación y estado actual.</p>
        </div>

        <div class="card card-portal p-4 p-md-5 shadow-sm">
            <form action="<?= url('/consulta') ?>" method="POST" autocomplete="off" id="formConsulta">
                <?= csrf_field() ?>

                <div class="alert alert-light border small text-muted mb-4 py-2 px-3 d-flex align-items-center gap-2" style="border-radius: 8px;">
                    <i class="bi bi-info-circle-fill text-primary fs-5"></i>
                    <span>Puedes ingresar <strong>ambos datos</strong> o <strong>al menos uno de ellos</strong> para realizar la búsqueda.</span>
                </div>

                <!-- Campo: Número de Expediente -->
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark">
                        <i class="bi bi-folder-fill text-primary me-1"></i> Número de Expediente
                    </label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-light text-primary border-end-0"><i class="bi bi-folder2-open"></i></span>
                        <input type="text" 
                               class="form-control border-start-0 font-monospace text-uppercase" 
                               name="numero_expediente" 
                               id="inputNumeroExpediente"
                               placeholder="Ej. EXP-2026-000002" 
                               value="<?= e($expedientePrellenado ?? '') ?>" 
                               autofocus>
                    </div>
                    <small class="text-muted" style="font-size: 0.75rem;">Formato institucional: EXP-AAAA-XXXXXX (indicado en tu cargo).</small>
                </div>

                <!-- Campo: Código de Seguimiento -->
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark">
                        <i class="bi bi-key-fill text-warning me-1"></i> Código de Seguimiento
                    </label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-light text-warning border-end-0"><i class="bi bi-shield-lock"></i></span>
                        <input type="text" 
                               class="form-control border-start-0 font-monospace text-uppercase" 
                               name="codigo_seguimiento" 
                               id="inputCodigoSeguimiento"
                               placeholder="Ej. 93A464B37B" 
                               value="<?= e($codigoPrellenado ?? '') ?>">
                    </div>
                    <small class="text-muted" style="font-size: 0.75rem;">Código único alfanumérico generado al registrar tu solicitud.</small>
                </div>

                <!-- Campo: DNI / RUC (Opcional) -->
                <div class="mb-4">
                    <label class="form-label fw-semibold text-dark">
                        <i class="bi bi-person-vcard text-secondary me-1"></i> Número de Documento del Titular <small class="text-muted fw-normal">(Opcional - Seguridad)</small>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-secondary border-end-0"><i class="bi bi-person-badge"></i></span>
                        <input type="text" 
                               class="form-control border-start-0" 
                               name="documento" 
                               id="inputDocumento"
                               placeholder="DNI o RUC con el que se registró">
                    </div>
                    <small class="text-muted" style="font-size: 0.75rem;">Para mayor privacidad y confirmación de identidad.</small>
                </div>

                <div id="errorAlert" class="alert alert-danger py-2 small mb-3 d-none">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    Por favor ingresa al menos el <strong>Número de Expediente</strong> o el <strong>Código de Seguimiento</strong>.
                </div>

                <button type="submit" class="btn btn-primary-portal w-100 py-3 fs-6 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-search fs-5"></i>
                    <span>Consultar Trámite</span>
                </button>
            </form>

            <div class="mt-4 pt-3 border-top text-center">
                <small class="text-muted">
                    ¿No encuentras tus datos? Revisa el cargo emitido al momento del registro o comunícate a 
                    <strong><?= e(config('institucion_correo', 'mesadepartes@iestpespinar.edu.pe')) ?></strong>.
                </small>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('formConsulta');
        const inputExp = document.getElementById('inputNumeroExpediente');
        const inputCod = document.getElementById('inputCodigoSeguimiento');
        const errorAlert = document.getElementById('errorAlert');

        form.addEventListener('submit', (e) => {
            const expVal = inputExp.value.trim();
            const codVal = inputCod.value.trim();

            if (!expVal && !codVal) {
                e.preventDefault();
                errorAlert.classList.remove('d-none');
                inputExp.focus();
                return false;
            } else {
                errorAlert.classList.add('d-none');
            }
        });

        // Ocultar mensaje al escribir en cualquiera de los dos campos
        inputExp.addEventListener('input', () => errorAlert.classList.add('d-none'));
        inputCod.addEventListener('input', () => errorAlert.classList.add('d-none'));
    });
</script>
