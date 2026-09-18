<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--primary, #0B4F8A);">Registro de Auditoría</h4>
        <p class="text-muted small mb-0">Bitácora inalterable de operaciones críticas, accesos, modificaciones de seguridad y trazabilidad.</p>
    </div>
</div>

<!-- Filtros de Auditoría -->
<div class="card card-custom mb-4 p-3">
    <form action="<?= url('/auditoria') ?>" method="GET" class="row g-2 align-items-center">
        <div class="col-12 col-md-6">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0 ps-0" name="buscar" placeholder="Buscar por acción, detalles, IP o usuario..." value="<?= e($buscar) ?>">
            </div>
        </div>
        <div class="col-6 col-md-4">
            <select name="modulo" class="form-select form-select-sm">
                <option value="">-- Todos los Módulos --</option>
                <?php foreach ($modulos as $m): ?>
                    <option value="<?= e($m) ?>" <?= ($modulo === $m) ? 'selected' : '' ?>>
                        <?= e($m) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-6 col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary-custom btn-sm w-100 py-2">
                Filtrar
            </button>
            <?php if (!empty($buscar) || !empty($modulo)): ?>
                <a href="<?= url('/auditoria') ?>" class="btn btn-outline-secondary btn-sm py-2" title="Limpiar">
                    <i class="bi bi-x-circle"></i>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Tabla de Auditoría Inalterable -->
<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
            <thead class="table-light">
                <tr>
                    <th style="width: 140px;">Fecha y Hora</th>
                    <th>Usuario Responsable</th>
                    <th>Acción</th>
                    <th>Módulo</th>
                    <th>Registro ID</th>
                    <th>Detalles / Observación</th>
                    <th>Dirección IP</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($registros)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-journal-x fs-1 opacity-50 d-block mb-2"></i>
                            No se encontraron registros de auditoría con los criterios seleccionados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($registros as $reg): ?>
                        <tr>
                            <td class="small text-muted font-monospace">
                                <?= formatDateTime($reg['created_at']) ?>
                            </td>
                            <td>
                                <?php if (!empty($reg['usuario_login'])): ?>
                                    <div class="fw-semibold text-dark"><?= e($reg['usuario_nombre']) ?></div>
                                    <small class="text-muted">@<?= e($reg['usuario_login']) ?></small>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted border">Sistema / Público</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold">
                                    <?= e($reg['accion']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <?= e($reg['modulo']) ?>
                                </span>
                            </td>
                            <td>
                                <?= !empty($reg['registro_id']) ? '<code>#' . e($reg['registro_id']) . '</code>' : '-' ?>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 320px;" title="<?= e($reg['detalles']) ?>">
                                    <?= e($reg['detalles'] ?: '-') ?>
                                </div>
                            </td>
                            <td class="small text-muted font-monospace">
                                <?= e($reg['ip'] ?: '127.0.0.1') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <?php if (($pagination['total_pages'] ?? 1) > 1): ?>
        <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center py-3">
            <small class="text-muted">
                Total: <strong><?= $pagination['total_records'] ?></strong> eventos registrados
            </small>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <?php for ($p = 1; $p <= $pagination['total_pages']; $p++): ?>
                        <li class="page-item <?= ($p == $pagination['current_page']) ? 'active' : '' ?>">
                            <a class="page-link" href="<?= url('/auditoria?page=' . $p . '&buscar=' . urlencode($buscar) . '&modulo=' . urlencode($modulo)) ?>">
                                <?= $p ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>
