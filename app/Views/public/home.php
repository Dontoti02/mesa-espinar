<div class="row align-items-center py-5 mb-4">
    <div class="col-12 col-lg-7">
        <span class="badge px-3 py-2 text-white fw-bold mb-3" style="background-color: var(--secondary, #F59E0B); border-radius: 20px; font-size: 0.8rem;">
            ATENCIÓN CIUDADANA DIGITAL
        </span>
        <h1 class="display-5 fw-bold mb-3" style="color: var(--primary, #0B4F8A); letter-spacing: -1px;">
            Mesa de Partes Virtual Institucional
        </h1>
        <p class="lead text-muted mb-4">
            Bienvenido a la plataforma digital de recepción documental del <strong><?= e(config('institucion_nombre', 'IESTP ESPINAR')) ?></strong>. Presenta tus solicitudes, documentos y realiza el seguimiento de tus trámites en línea desde cualquier dispositivo.
        </p>
        <div class="d-flex flex-wrap gap-3">
            <a href="<?= url('/tramite') ?>" class="btn btn-primary-portal px-4 py-3 fs-6 d-inline-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-plus-fill fs-5"></i>
                <span>Presentar un Nuevo Trámite</span>
            </a>
            <a href="<?= url('/consulta') ?>" class="btn btn-outline-secondary px-4 py-3 fs-6 fw-semibold d-inline-flex align-items-center gap-2" style="border-radius: 8px;">
                <i class="bi bi-search fs-5"></i>
                <span>Consultar Estado de Expediente</span>
            </a>
        </div>
    </div>
    <div class="col-12 col-lg-5 text-center mt-4 mt-lg-0">
        <div class="card card-portal p-4 border-0 shadow" style="background: linear-gradient(145deg, #ffffff 0%, #f1f5f9 100%);">
            <div class="mb-3">
                <div class="d-inline-flex p-3 rounded-circle" style="background-color: var(--primary-subtle, rgba(11,79,138,0.1)); color: var(--primary, #0B4F8A);">
                    <i class="bi bi-clock-history fs-1"></i>
                </div>
            </div>
            <h5 class="fw-bold mb-2">Horario de Recepción</h5>
            <p class="text-muted small mb-3">
                <?= e(config('institucion_horario', 'Lunes a Viernes de 08:00 a.m. a 04:30 p.m.')) ?>
            </p>
            <div class="p-3 bg-white rounded border text-start small text-muted">
                <div class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Los documentos ingresados después del horario oficial se consideran recepcionados al siguiente día hábil.</div>
                <div><i class="bi bi-shield-check text-primary me-2"></i>Emisión inmediata de cargo oficial de recepción en formato PDF con código QR.</div>
            </div>
        </div>
    </div>
</div>

<!-- Trámites Frecuentes -->
<div class="mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--primary, #0B4F8A);">Trámites Más Frecuentes</h4>
            <p class="text-muted small mb-0">Principales gestiones académicas y administrativas disponibles de forma virtual.</p>
        </div>
    </div>

    <div class="row g-3">
        <?php foreach ($tramites as $t): ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card card-portal p-4 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-light text-primary border border-primary-subtle fw-bold">
                                <?= e($t['codigo']) ?>
                            </span>
                            <span class="badge bg-secondary-subtle text-secondary small">
                                <i class="bi bi-clock me-1"></i><?= $t['plazo_referencial_dias'] ?> días
                            </span>
                        </div>
                        <h6 class="fw-bold text-dark mb-2"><?= e($t['nombre']) ?></h6>
                        <p class="small text-muted mb-3">
                            <?= e($t['descripcion'] ?: 'Presentación de solicitud formal con documentos de sustento.') ?>
                        </p>
                    </div>
                    <div>
                        <a href="<?= url('/tramite?tipo=' . $t['id']) ?>" class="btn btn-outline-primary btn-sm w-100 fw-semibold">
                            Iniciar Trámite <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
