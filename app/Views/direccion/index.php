<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--primary, #0B4F8A);">Bandeja de Dirección General</h4>
        <p class="text-muted small mb-0">Revisión de expedientes remitidos por Mesa de Partes, asignación de oficinas y resoluciones.</p>
    </div>
</div>

<!-- Tarjetas de Resumen Rápido -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="card card-custom p-3 border-start border-4 border-warning">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">PENDIENTES POR DERIVAR</div>
                    <div class="fs-3 fw-bold text-dark"><?= count($pendientes) ?></div>
                </div>
                <div class="p-3 bg-warning-subtle text-warning rounded-circle">
                    <i class="bi bi-inbox-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="card card-custom p-3 border-start border-4 border-primary">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">RESPUESTAS DE OFICINAS</div>
                    <div class="fs-3 fw-bold text-dark"><?= count($respuestas) ?></div>
                </div>
                <div class="p-3 bg-primary-subtle text-primary rounded-circle">
                    <i class="bi bi-reply-all-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="card card-custom p-3 border-start border-4 border-success">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">TRÁMITES FINALIZADOS</div>
                    <div class="fs-3 fw-bold text-dark"><?= count($finalizados) ?></div>
                </div>
                <div class="p-3 bg-success-subtle text-success rounded-circle">
                    <i class="bi bi-check-circle-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs de Navegación -->
<ul class="nav nav-tabs mb-4 border-bottom" id="direccionTabs">
    <li class="nav-item">
        <a class="nav-link <?= ($tab === 'pendientes') ? 'active fw-bold' : '' ?>" href="<?= url('/direccion?tab=pendientes') ?>">
            <i class="bi bi-arrow-right-circle me-1 text-warning"></i> Por Derivar (<?= count($pendientes) ?>)
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($tab === 'respuestas') ? 'active fw-bold' : '' ?>" href="<?= url('/direccion?tab=respuestas') ?>">
            <i class="bi bi-chat-left-check me-1 text-primary"></i> Respuestas de Oficinas (<?= count($respuestas) ?>)
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($tab === 'finalizados') ? 'active fw-bold' : '' ?>" href="<?= url('/direccion?tab=finalizados') ?>">
            <i class="bi bi-archive me-1 text-success"></i> Finalizados (<?= count($finalizados) ?>)
        </a>
    </li>
</ul>

<!-- Contenido del Tab Activo -->
<div class="card card-custom">
    <?php if ($tab === 'pendientes'): ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nº Expediente</th>
                        <th>Fecha Ingreso</th>
                        <th>Solicitante</th>
                        <th>Asunto</th>
                        <th>Trámite</th>
                        <th>Prioridad</th>
                        <th class="text-end" style="width: 140px;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pendientes)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-check2-circle fs-1 text-success d-block mb-2"></i>
                                ¡Al día! No hay expedientes pendientes de derivación en Dirección.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pendientes as $p): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-primary">
                                        <a href="<?= url("/expedientes/{$p['id']}") ?>" class="text-decoration-none">
                                            <?= e($p['numero_expediente']) ?>
                                        </a>
                                    </div>
                                    <small class="text-muted font-monospace"><?= e($p['codigo_seguimiento']) ?></small>
                                </td>
                                <td class="small text-muted">
                                    <?= formatDateTime($p['fecha_ingreso']) ?>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">
                                        <?= e($p['tipo_persona'] === 'JURIDICA' ? $p['razon_social'] : $p['nombres'] . ' ' . $p['apellidos']) ?>
                                    </div>
                                    <small class="text-muted"><?= e($p['tipo_documento']) ?>: <?= e($p['numero_documento']) ?></small>
                                </td>
                                <td>
                                    <div class="text-truncate fw-medium" style="max-width: 250px;" title="<?= e($p['asunto']) ?>">
                                        <?= e($p['asunto']) ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?= e($p['tipo_tramite_nombre']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge px-2 py-1 text-white fw-semibold" style="background-color: <?= e($p['prioridad_color']) ?>; font-size: 0.75rem;">
                                        <?= e($p['prioridad_nombre']) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="<?= url("/expedientes/{$p['id']}") ?>" class="btn btn-sm btn-primary-custom">
                                        <i class="bi bi-arrow-right-circle me-1"></i> Derivar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    <?php elseif ($tab === 'respuestas'): ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nº Expediente</th>
                        <th>Oficina que Respondió</th>
                        <th>Solicitante</th>
                        <th>Asunto</th>
                        <th>Estado Actual</th>
                        <th class="text-end" style="width: 140px;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($respuestas)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                No hay respuestas pendientes de revisión.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($respuestas as $r): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-primary">
                                        <a href="<?= url("/expedientes/{$r['id']}") ?>" class="text-decoration-none">
                                            <?= e($r['numero_expediente']) ?>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-primary border border-primary-subtle fw-bold">
                                        <?= e($r['oficina_resp_sigla']) ?> - <?= e($r['oficina_resp_nombre']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">
                                        <?= e($r['tipo_persona'] === 'JURIDICA' ? $r['razon_social'] : $r['nombres'] . ' ' . $r['apellidos']) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-truncate fw-medium" style="max-width: 250px;">
                                        <?= e($r['asunto']) ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge px-2 py-1 text-white fw-semibold" style="background-color: <?= e($r['estado_color']) ?>;">
                                        <?= e($r['estado_nombre']) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="<?= url("/expedientes/{$r['id']}") ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i> Revisar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nº Expediente</th>
                        <th>Fecha Finalización</th>
                        <th>Solicitante</th>
                        <th>Asunto</th>
                        <th>Estado</th>
                        <th class="text-end" style="width: 120px;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($finalizados as $f): ?>
                        <tr>
                            <td class="fw-bold text-primary"><?= e($f['numero_expediente']) ?></td>
                            <td class="small text-muted"><?= formatDateTime($f['fecha_finalizacion']) ?></td>
                            <td><?= e($f['tipo_persona'] === 'JURIDICA' ? $f['razon_social'] : $f['nombres'] . ' ' . $f['apellidos']) ?></td>
                            <td><?= e($f['asunto']) ?></td>
                            <td>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <?= e($f['estado_nombre']) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="<?= url("/expedientes/{$f['id']}") ?>" class="btn btn-sm btn-light border">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
