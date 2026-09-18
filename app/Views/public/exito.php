<div class="row justify-content-center py-5">
    <div class="col-12 col-md-8 col-lg-7 text-center">
        <div class="card card-portal p-5 shadow-sm">
            <div class="mb-3">
                <div class="d-inline-flex p-3 rounded-circle bg-success-subtle text-success">
                    <i class="bi bi-check-circle-fill display-4"></i>
                </div>
            </div>

            <h2 class="fw-bold mb-2 text-dark">¡Trámite Registrado Exitosamente!</h2>
            <p class="text-muted mb-4">
                Tu solicitud ha ingresado formalmente a nuestra Mesa de Partes Virtual y se ha generado tu número único de expediente.
            </p>

            <div class="p-4 bg-light rounded-4 border mb-4 text-start">
                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem;">NÚMERO DE EXPEDIENTE</small>
                        <div class="fs-4 fw-bold text-primary"><?= e($expediente['numero_expediente']) ?></div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem;">CÓDIGO DE SEGUIMIENTO</small>
                        <div class="fs-4 fw-bold font-monospace text-dark"><?= e($expediente['codigo_seguimiento']) ?></div>
                    </div>
                    <div class="col-12 pt-2 border-top">
                        <small class="text-muted d-block">Asunto: <strong><?= e($expediente['asunto']) ?></strong></small>
                        <small class="text-muted d-block">Fecha y Hora: <?= formatDateTime($expediente['fecha_ingreso']) ?></small>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap justify-content-center gap-3 mb-4">
                <a href="<?= url('/tramite/cargo/' . urlencode($expediente['codigo_seguimiento'])) ?>" target="_blank" class="btn btn-primary-portal px-4 py-2 d-inline-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-pdf-fill fs-5"></i>
                    <span>Descargar / Imprimir Cargo Oficial</span>
                </a>
                <a href="<?= url('/consulta/' . urlencode($expediente['codigo_seguimiento'])) ?>" class="btn btn-outline-secondary px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2" style="border-radius: 8px;">
                    <i class="bi bi-search fs-5"></i>
                    <span>Ver Trazabilidad en Línea</span>
                </a>
            </div>

            <div class="p-3 bg-light rounded text-muted small border">
                <i class="bi bi-info-circle text-primary me-1"></i>
                Guarda tu <strong>código de seguimiento</strong>. Con este código y tu número de documento podrás consultar el avance de tu trámite en cualquier momento desde nuestro portal web.
            </div>
        </div>
    </div>
</div>
