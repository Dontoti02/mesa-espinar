<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--primary, #0B4F8A);">Bandeja de Operaciones: <?= e($oficina) ?></h4>
        <p class="text-muted small mb-0">Gestión de expedientes derivados, recepción formal, respuestas y colaboración interoficinas.</p>
    </div>
</div>

<!-- Tarjetas Rápidas de la Oficina -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="card card-custom p-3 border-start border-4 border-warning">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">POR RECEPCIONAR</div>
                    <div class="fs-3 fw-bold text-dark"><?= count($porRecepcionar) ?></div>
                </div>
                <div class="p-3 bg-warning-subtle text-warning rounded-circle">
                    <i class="bi bi-box-arrow-in-down fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="card card-custom p-3 border-start border-4 border-primary">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">EN TRÁMITE / ATENCIÓN</div>
                    <div class="fs-3 fw-bold text-dark"><?= count($enAtencion) ?></div>
                </div>
                <div class="p-3 bg-primary-subtle text-primary rounded-circle">
                    <i class="bi bi-gear-wide-connected fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="card card-custom p-3 border-start border-4 border-info">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">CONSULTAS INTERNAS</div>
                    <div class="fs-3 fw-bold text-dark"><?= count($solicitudes) ?></div>
                </div>
                <div class="p-3 bg-info-subtle text-info rounded-circle">
                    <i class="bi bi-chat-dots-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs de Operación -->
<ul class="nav nav-tabs mb-4 border-bottom">
    <li class="nav-item">
        <a class="nav-link <?= ($tab === 'pendientes') ? 'active fw-bold' : '' ?>" href="<?= url('/mi-oficina?tab=pendientes') ?>">
            <i class="bi bi-inbox me-1 text-warning"></i> Por Recepcionar (<?= count($porRecepcionar) ?>)
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($tab === 'atencion') ? 'active fw-bold' : '' ?>" href="<?= url('/mi-oficina?tab=atencion') ?>">
            <i class="bi bi-hourglass-split me-1 text-primary"></i> En Atención (<?= count($enAtencion) ?>)
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($tab === 'solicitudes') ? 'active fw-bold' : '' ?>" href="<?= url('/mi-oficina?tab=solicitudes') ?>">
            <i class="bi bi-question-circle me-1 text-info"></i> Consultas Internas Pendientes (<?= count($solicitudes) ?>)
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($tab === 'respondidos') ? 'active fw-bold' : '' ?>" href="<?= url('/mi-oficina?tab=respondidos') ?>">
            <i class="bi bi-check2-all me-1 text-success"></i> Respondidos (<?= count($respondidos) ?>)
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
                        <th>Fecha Derivado</th>
                        <th>Solicitante</th>
                        <th>Asunto</th>
                        <th>Prioridad</th>
                        <th class="text-end" style="width: 170px;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($porRecepcionar)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-check2-all fs-1 text-success d-block mb-2"></i>
                                No hay expedientes pendientes de recepción. Todos han sido recepcionados.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($porRecepcionar as $p): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-primary"><?= e($p['numero_expediente']) ?></div>
                                    <small class="text-muted font-monospace"><?= e($p['codigo_seguimiento']) ?></small>
                                </td>
                                <td class="small text-muted"><?= formatDateTime($p['updated_at']) ?></td>
                                <td><?= e($p['tipo_persona'] === 'JURIDICA' ? $p['razon_social'] : $p['nombres'] . ' ' . $p['apellidos']) ?></td>
                                <td>
                                    <div class="text-truncate fw-medium" style="max-width: 280px;"><?= e($p['asunto']) ?></div>
                                </td>
                                <td>
                                    <span class="badge px-2 py-1 text-white fw-semibold" style="background-color: <?= e($p['prioridad_color']) ?>;">
                                        <?= e($p['prioridad_nombre']) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <form action="<?= url("/expedientes/{$p['id']}/recibir") ?>" method="POST" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-success fw-semibold">
                                            <i class="bi bi-inbox-fill me-1"></i> Recepcionar
                                        </button>
                                    </form>
                                    <a href="<?= url("/expedientes/{$p['id']}") ?>" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    <?php elseif ($tab === 'atencion'): ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nº Expediente</th>
                        <th>Solicitante</th>
                        <th>Asunto</th>
                        <th>Estado Actual</th>
                        <th>Prioridad</th>
                        <th class="text-end" style="width: 140px;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($enAtencion)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                No tienes trámites en proceso de atención en este momento.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($enAtencion as $ea): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-primary">
                                        <a href="<?= url("/expedientes/{$ea['id']}") ?>" class="text-decoration-none">
                                            <?= e($ea['numero_expediente']) ?>
                                        </a>
                                    </div>
                                </td>
                                <td><?= e($ea['tipo_persona'] === 'JURIDICA' ? $ea['razon_social'] : $ea['nombres'] . ' ' . $ea['apellidos']) ?></td>
                                <td>
                                    <div class="text-truncate fw-medium" style="max-width: 280px;"><?= e($ea['asunto']) ?></div>
                                </td>
                                <td>
                                    <span class="badge px-2 py-1 text-white fw-semibold" style="background-color: <?= e($ea['estado_color']) ?>;">
                                        <?= e($ea['estado_nombre']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge px-2 py-1 text-white fw-semibold" style="background-color: <?= e($ea['prioridad_color']) ?>;">
                                        <?= e($ea['prioridad_nombre']) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="<?= url("/expedientes/{$ea['id']}") ?>" class="btn btn-sm btn-primary-custom">
                                        <i class="bi bi-reply-fill me-1"></i> Atender
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    <?php elseif ($tab === 'solicitudes'): ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Expediente</th>
                        <th>Oficina Solicitante</th>
                        <th>Consulta / Requerimiento</th>
                        <th>Fecha Solicitud</th>
                        <th class="text-end" style="width: 140px;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($solicitudes)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-chat-heart fs-1 text-muted opacity-50 d-block mb-2"></i>
                                No hay consultas o solicitudes de colaboración pendientes dirigidas a tu oficina.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($solicitudes as $s): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-primary"><?= e($s['numero_expediente']) ?></div>
                                    <small class="text-muted"><?= e($s['expediente_asunto']) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold">
                                        <?= e($s['solicitante_sigla']) ?> - <?= e($s['solicitante_nombre']) ?>
                                    </span>
                                    <div class="small text-muted">Por: <?= e($s['solicitante_usuario']) ?></div>
                                </td>
                                <td>
                                    <div class="p-2 bg-light rounded border small">
                                        <?= nl2br(e($s['motivo'])) ?>
                                    </div>
                                </td>
                                <td class="small text-muted"><?= formatDateTime($s['fecha_solicitud']) ?></td>
                                <td class="text-end">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-success btn-responder-solicitud" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalResponderSolicitud"
                                            data-id="<?= $s['id'] ?>"
                                            data-expediente="<?= e($s['numero_expediente']) ?>"
                                            data-motivo="<?= e($s['motivo']) ?>">
                                        <i class="bi bi-reply-fill me-1"></i> Responder
                                    </button>
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
                        <th>Solicitante</th>
                        <th>Asunto</th>
                        <th>Estado Actual</th>
                        <th class="text-end" style="width: 100px;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($respondidos as $res): ?>
                        <tr>
                            <td class="fw-bold text-primary"><?= e($res['numero_expediente']) ?></td>
                            <td><?= e($res['tipo_persona'] === 'JURIDICA' ? $res['razon_social'] : $res['nombres'] . ' ' . $res['apellidos']) ?></td>
                            <td><?= e($res['asunto']) ?></td>
                            <td>
                                <span class="badge px-2 py-1 text-white" style="background-color: <?= e($res['estado_color']) ?>;">
                                    <?= e($res['estado_nombre']) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="<?= url("/expedientes/{$res['id']}") ?>" class="btn btn-sm btn-light border">
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

<!-- MODAL RESPONDER CONSULTA INTERNA -->
<div class="modal fade" id="modalResponderSolicitud" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 12px;">
            <form id="formResponderSolicitud" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="color: var(--primary, #0B4F8A);">Responder Consulta Interna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">Expediente:</label>
                        <div class="fw-bold" id="modalSolExpediente"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">Consulta Recibida:</label>
                        <div class="p-2 bg-light rounded border small" id="modalSolMotivo"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Detalle de la Respuesta / Información Proporcionada <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="respuesta" rows="4" placeholder="Escribe aquí la respuesta o información de sustento..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success fw-bold">Enviar Respuesta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const respBtns = document.querySelectorAll('.btn-responder-solicitud');
        const respForm = document.getElementById('formResponderSolicitud');

        respBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                respForm.action = '<?= url("/expedientes") ?>/' + id + '/responder-solicitud';
                document.getElementById('modalSolExpediente').textContent = btn.dataset.expediente;
                document.getElementById('modalSolMotivo').textContent = btn.dataset.motivo;
            });
        });
    });
</script>
