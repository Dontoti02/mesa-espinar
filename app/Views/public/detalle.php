<div class="mb-4">
    <a href="<?= url('/consulta') ?>" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1 mb-2">
        <i class="bi bi-arrow-left"></i> Realizar otra consulta
    </a>

    <!-- Tarjeta Principal del Estado -->
    <div class="card card-portal p-4 p-md-5 mb-4 border-0 shadow-sm">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4 pb-3 border-bottom">
            <div>
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem;">EXPEDIENTE INSTITUCIONAL</small>
                <h3 class="fw-bold text-primary mb-1"><?= e($expediente['numero_expediente']) ?></h3>
                <div class="small text-muted font-monospace">Código: <?= e($expediente['codigo_seguimiento']) ?></div>
            </div>

            <div class="text-end">
                <span class="badge px-4 py-2 text-white fw-bold fs-6" style="background-color: <?= e($expediente['estado_color']) ?>; border-radius: 8px;">
                    <i class="bi <?= e($expediente['estado_icono'] ?? 'bi-check2-circle') ?> me-1"></i>
                    <?= e($expediente['estado_nombre']) ?>
                </span>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-md-3">
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">OFICINA ACTUAL</small>
                <div class="fw-bold text-dark fs-6 mt-1">
                    <i class="bi bi-building text-primary me-1"></i>
                    <?= e($expediente['oficina_actual_nombre']) ?> (<?= e($expediente['oficina_actual_sigla']) ?>)
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">TIPO DE TRÁMITE</small>
                <div class="fw-semibold text-dark mt-1"><?= e($expediente['tipo_tramite_nombre']) ?></div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">FECHA DE INGRESO</small>
                <div class="fw-semibold text-dark mt-1"><?= formatDateTime($expediente['fecha_ingreso']) ?></div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">ÚLTIMA ACTUALIZACIÓN</small>
                <div class="fw-semibold text-dark mt-1"><?= formatDateTime($expediente['updated_at']) ?></div>
            </div>
        </div>

        <div class="p-3 bg-light rounded border mb-2">
            <small class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: 0.7rem;">ASUNTO DECLARADO</small>
            <div class="fw-medium text-dark"><?= e($expediente['asunto']) ?></div>
        </div>

        <?php if (!empty($expediente['observacion_publica'])): ?>
            <div class="p-3 bg-success-subtle rounded border border-success-subtle mt-3">
                <small class="text-success text-uppercase fw-bold d-block mb-1" style="font-size: 0.7rem;">
                    <i class="bi bi-info-circle-fill me-1"></i> COMUNICACIÓN INSTITUCIONAL PARA EL CIUDADANO
                </small>
                <div class="fw-semibold text-dark"><?= nl2br(e($expediente['observacion_publica'])) ?></div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Línea de Tiempo Pública (Sección 9 y 10) -->
    <div class="card card-portal p-4 p-md-5 border-0 shadow-sm">
        <h5 class="fw-bold text-primary mb-4 pb-2 border-bottom">
            <i class="bi bi-diagram-3-fill me-2"></i> Trazabilidad y Avance del Trámite
        </h5>

        <div class="timeline position-relative ps-4" style="border-left: 2px solid #e2e8f0; margin-left: 10px;">
            <?php foreach ($movimientos as $mov): ?>
                <div class="timeline-item mb-4 position-relative">
                    <span class="position-absolute" style="left: -29px; top: 2px; width: 14px; height: 14px; border-radius: 50%; background-color: <?= e($mov['estado_nvo_color'] ?? '#0B4F8A') ?>; border: 2px solid #ffffff; box-shadow: 0 0 0 2px rgba(11,79,138,0.2);"></span>
                    
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1">
                        <span class="badge px-3 py-1 text-white fw-semibold" style="background-color: <?= e($mov['estado_nvo_color'] ?? '#0B4F8A') ?>; font-size: 0.75rem;">
                            <?= e($mov['estado_nvo_nombre']) ?>
                        </span>
                        <small class="text-muted" style="font-size: 0.8rem;">
                            <i class="bi bi-calendar-event me-1"></i><?= formatDateTime($mov['created_at']) ?>
                        </small>
                    </div>

                    <div class="p-3 bg-light rounded border mt-2">
                        <div class="small fw-semibold text-dark">
                            <i class="bi bi-geo-alt-fill text-primary me-1"></i>
                            Ubicación: 
                            <?php if (!empty($mov['oficina_origen_sigla'])): ?>
                                <?= e($mov['oficina_origen_sigla']) ?> <i class="bi bi-arrow-right mx-1 text-muted"></i>
                            <?php endif; ?>
                            <?= e($mov['oficina_destino_sigla'] ?? 'Mesa de Partes') ?>
                        </div>

                        <?php if (!empty($mov['observacion'])): ?>
                            <div class="small text-secondary mt-2 pt-2 border-top">
                                <?= nl2br(e($mov['observacion'])) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
