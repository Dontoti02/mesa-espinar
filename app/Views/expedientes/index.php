<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--primary, #0B4F8A);">Bandeja General de Expedientes</h4>
        <p class="text-muted small mb-0">Consulta general de trámites documentarios institucionales y estado actual.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <?php if (hasPermission('expedientes.crear')): ?>
            <a href="<?= url('/expedientes/crear') ?>" class="btn btn-primary-custom d-inline-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-plus-fill"></i>
                <span>Nuevo Trámite Presencial</span>
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Filtros de Expedientes -->
<div class="card card-custom mb-4 p-3">
    <form action="<?= url('/expedientes') ?>" method="GET" class="row g-2 align-items-end">
        <div class="col-12 col-md-4">
            <label class="form-label small fw-semibold text-muted mb-1">Buscar</label>
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0 ps-0" name="buscar" placeholder="Nº Expediente, DNI/RUC, solicitante, asunto..." value="<?= e($filtros['buscar']) ?>">
            </div>
        </div>

        <div class="col-6 col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1">Estado</label>
            <select name="estado_id" class="form-select form-select-sm">
                <option value="">-- Todos --</option>
                <?php foreach ($estados as $est): ?>
                    <option value="<?= $est['id'] ?>" <?= ($filtros['estado_id'] == $est['id']) ? 'selected' : '' ?>>
                        <?= e($est['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-6 col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1">Oficina Actual</label>
            <select name="oficina_id" class="form-select form-select-sm">
                <option value="">-- Todas --</option>
                <?php foreach ($oficinas as $o): ?>
                    <option value="<?= $o['id'] ?>" <?= ($filtros['oficina_id'] == $o['id']) ? 'selected' : '' ?>>
                        <?= e($o['sigla']) ?> - <?= e($o['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-6 col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1">Prioridad</label>
            <select name="prioridad_id" class="form-select form-select-sm">
                <option value="">-- Todas --</option>
                <?php foreach ($prioridades as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= ($filtros['prioridad_id'] == $p['id']) ? 'selected' : '' ?>>
                        <?= e($p['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-6 col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary-custom btn-sm w-100 py-2">
                <i class="bi bi-funnel me-1"></i> Filtrar
            </button>
            <?php if (!empty($filtros['buscar']) || !empty($filtros['estado_id']) || !empty($filtros['oficina_id']) || !empty($filtros['prioridad_id'])): ?>
                <a href="<?= url('/expedientes') ?>" class="btn btn-outline-secondary btn-sm py-2" title="Limpiar">
                    <i class="bi bi-x-circle"></i>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Tabla de Expedientes -->
<div class="card card-custom">
    <div class="table-responsive">
        <?php if (hasPermission('expedientes.eliminar')): ?>
            <form action="<?= url('/expedientes/eliminar') ?>" method="POST" id="formEliminarExpedientes" onsubmit="return confirmarEliminarExpedientes(event);">
                <?= csrf_field() ?>
        <?php endif; ?>
        <table class="table table-hover align-middle mb-0" id="tablaExpedientes">
            <thead class="table-light">
                <tr>
                    <?php if (hasPermission('expedientes.eliminar')): ?>
                        <th style="width: 40px;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkAllExpedientes" onchange="toggleAllExpedientes(this)">
                            </div>
                        </th>
                    <?php endif; ?>
                    <th>Nº Expediente</th>
                    <th>Fecha Ingreso</th>
                    <th>Solicitante</th>
                    <th>Asunto / Trámite</th>
                    <th>Oficina Actual</th>
                    <th>Estado</th>
                    <th>Prioridad</th>
                    <th class="text-end" style="width: 100px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($expedientes)): ?>
                    <tr>
                        <td colspan="<?= hasPermission('expedientes.eliminar') ? 9 : 8 ?>" class="text-center py-5 text-muted">
                            <i class="bi bi-folder-x fs-1 d-block mb-2 opacity-50"></i>
                            No se encontraron expedientes con los criterios seleccionados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($expedientes as $exp): ?>
                        <tr>
                            <?php if (hasPermission('expedientes.eliminar')): ?>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input checkExpediente" type="checkbox" name="ids[]" value="<?= $exp['id'] ?>" onchange="actualizarBotonEliminar()">
                                    </div>
                                </td>
                            <?php endif; ?>
                            <td>
                                <div class="fw-bold text-primary">
                                    <a href="<?= url("/expedientes/{$exp['id']}") ?>" class="text-decoration-none">
                                        <?= e($exp['numero_expediente']) ?>
                                    </a>
                                </div>
                                <small class="text-muted font-monospace" style="font-size: 0.75rem;">
                                    Cod: <?= e($exp['codigo_seguimiento']) ?>
                                </small>
                            </td>
                            <td class="small text-muted">
                                <?= formatDateTime($exp['fecha_ingreso']) ?>
                                <?php if ((int)$exp['es_virtual'] === 1): ?>
                                    <span class="badge bg-info-subtle text-info border border-info-subtle d-block mt-1" style="font-size: 0.65rem;">Virtual</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted border d-block mt-1" style="font-size: 0.65rem;">Presencial</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">
                                    <?= e($exp['tipo_persona'] === 'JURIDICA' ? $exp['razon_social'] : $exp['nombres'] . ' ' . $exp['apellidos']) ?>
                                </div>
                                <small class="text-muted">
                                    <?= e($exp['tipo_documento']) ?>: <?= e($exp['numero_documento']) ?>
                                </small>
                            </td>
                            <td>
                                <div class="text-truncate fw-medium text-dark" style="max-width: 280px;" title="<?= e($exp['asunto']) ?>">
                                    <?= e($exp['asunto']) ?>
                                </div>
                                <span class="badge bg-light text-secondary border mt-1" style="font-size: 0.72rem;">
                                    <?= e($exp['tipo_tramite_nombre']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    <i class="bi bi-building me-1 text-primary"></i>
                                    <?= e($exp['oficina_actual_sigla']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge px-2 py-1 text-white fw-semibold" style="background-color: <?= e($exp['estado_color']) ?>; border-radius: 6px; font-size: 0.75rem;">
                                    <?= e($exp['estado_nombre']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge px-2 py-1 text-white fw-semibold" style="background-color: <?= e($exp['prioridad_color']) ?>; border-radius: 6px; font-size: 0.72rem;">
                                    <?= e($exp['prioridad_nombre']) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="<?= url("/expedientes/{$exp['id']}") ?>" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-eye"></i> Ver
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <?php if (hasPermission('expedientes.eliminar')): ?>
            </form>
        <?php endif; ?>
    </div>

    <!-- Paginación y botón eliminar -->
    <?php if (($pagination['total_pages'] ?? 1) > 1 || hasPermission('expedientes.eliminar')): ?>
        <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center py-3 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <?php if (($pagination['total_pages'] ?? 1) > 1): ?>
                    <small class="text-muted">
                        Total: <strong><?= $pagination['total_records'] ?></strong> expedientes
                    </small>
                <?php endif; ?>
                <?php if (hasPermission('expedientes.eliminar')): ?>
                    <button type="submit" form="formEliminarExpedientes" class="btn btn-danger btn-sm d-inline-flex align-items-center gap-1" disabled id="btnEliminarExpedientes">
                        <i class="bi bi-trash3-fill"></i>
                        <span>Eliminar Seleccionados</span>
                    </button>
                <?php endif; ?>
            </div>
            <?php if (($pagination['total_pages'] ?? 1) > 1): ?>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <?php for ($p = 1; $p <= $pagination['total_pages']; $p++): ?>
                            <li class="page-item <?= ($p == $pagination['current_page']) ? 'active' : '' ?>">
                                <a class="page-link" href="<?= url('/expedientes?page=' . $p . '&buscar=' . urlencode($filtros['buscar']) . '&estado_id=' . $filtros['estado_id'] . '&oficina_id=' . $filtros['oficina_id']) ?>">
                                    <?= $p ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    function toggleAllExpedientes(source) {
        const checkboxes = document.querySelectorAll('.checkExpediente');
        checkboxes.forEach(checkbox => {
            checkbox.checked = source.checked;
        });
        actualizarBotonEliminar();
    }

    function actualizarBotonEliminar() {
        const checkboxes = document.querySelectorAll('.checkExpediente:checked');
        const btn = document.getElementById('btnEliminarExpedientes');
        const checkAll = document.getElementById('checkAllExpedientes');
        
        if (btn) {
            btn.disabled = checkboxes.length === 0;
        }
        
        if (checkAll) {
            const allCheckboxes = document.querySelectorAll('.checkExpediente');
            checkAll.checked = checkboxes.length === allCheckboxes.length && allCheckboxes.length > 0;
            checkAll.indeterminate = checkboxes.length > 0 && checkboxes.length < allCheckboxes.length;
        }
    }

    function confirmarEliminarExpedientes(event) {
        const checkboxes = document.querySelectorAll('.checkExpediente:checked');
        if (checkboxes.length === 0) {
            event.preventDefault();
            return false;
        }
        
        if (!confirm(`¿Está seguro de eliminar ${checkboxes.length} expediente(s)?\n\nEsta acción es irreversible y eliminará permanentemente:\n- Los expedientes seleccionados\n- Sus documentos adjuntos\n- Su historial de movimientos\n- Sus solicitudes internas relacionadas\n\nEsta acción SOLO puede ser realizada por el Superadministrador.`)) {
            event.preventDefault();
            return false;
        }
        return true;
    }

    document.addEventListener('DOMContentLoaded', () => {
        actualizarBotonEliminar();
    });
</script>
