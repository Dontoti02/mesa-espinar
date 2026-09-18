<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--primary, #0B4F8A);">Reportes y Estadísticas Documentarias</h4>
        <p class="text-muted small mb-0">Generación de informes de gestión, tiempos de atención y productividad por dependencia.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('/reportes/exportar-excel?' . http_build_query($filtros)) ?>" class="btn btn-success d-inline-flex align-items-center gap-2">
            <i class="bi bi-file-earmark-spreadsheet-fill"></i>
            <span>Exportar a Excel (CSV)</span>
        </a>
        <a href="<?= url('/reportes/imprimir?' . http_build_query($filtros)) ?>" target="_blank" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
            <i class="bi bi-printer-fill"></i>
            <span>Vista de Impresión</span>
        </a>
    </div>
</div>

<!-- Filtros Multidimensionales (Sección 19) -->
<div class="card card-custom p-4 mb-4">
    <form action="<?= url('/reportes') ?>" method="GET" class="row g-3 align-items-end">
        <div class="col-12 col-md-3">
            <label class="form-label small fw-semibold">Fecha Desde</label>
            <input type="date" class="form-control" name="fecha_desde" value="<?= e($filtros['fecha_desde']) ?>">
        </div>

        <div class="col-12 col-md-3">
            <label class="form-label small fw-semibold">Fecha Hasta</label>
            <input type="date" class="form-control" name="fecha_hasta" value="<?= e($filtros['fecha_hasta']) ?>">
        </div>

        <div class="col-12 col-md-3">
            <label class="form-label small fw-semibold">Oficina Actual</label>
            <select name="oficina_id" class="form-select">
                <option value="">-- Todas las Oficinas --</option>
                <?php foreach ($oficinas as $o): ?>
                    <option value="<?= $o['id'] ?>" <?= ($filtros['oficina_id'] == $o['id']) ? 'selected' : '' ?>>
                        <?= e($o['nombre']) ?> (<?= e($o['sigla']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-12 col-md-3">
            <label class="form-label small fw-semibold">Estado del Expediente</label>
            <select name="estado_id" class="form-select">
                <option value="">-- Todos los Estados --</option>
                <?php foreach ($estados as $es): ?>
                    <option value="<?= $es['id'] ?>" <?= ($filtros['estado_id'] == $es['id']) ? 'selected' : '' ?>>
                        <?= e($es['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-12 col-md-4">
            <label class="form-label small fw-semibold">Tipo de Trámite</label>
            <select name="tipo_tramite_id" class="form-select">
                <option value="">-- Todos los Tipos --</option>
                <?php foreach ($tiposTramite as $tt): ?>
                    <option value="<?= $tt['id'] ?>" <?= ($filtros['tipo_tramite_id'] == $tt['id']) ? 'selected' : '' ?>>
                        <?= e($tt['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-12 col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary-custom w-100 py-2">
                <i class="bi bi-funnel-fill me-1"></i> Filtrar
            </button>
        </div>
    </form>
</div>

<!-- Resumen Estadístico Rápido -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="card card-custom p-3 text-center border-start border-4 border-primary">
            <div class="text-muted small fw-semibold">TOTAL EN EL PERIODO</div>
            <div class="fs-3 fw-bold text-primary mt-1"><?= $resumen['total'] ?></div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card card-custom p-3 text-center border-start border-4 border-warning">
            <div class="text-muted small fw-semibold">EN TRÁMITE / PENDIENTES</div>
            <div class="fs-3 fw-bold text-warning mt-1"><?= $resumen['pendientes'] ?></div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card card-custom p-3 text-center border-start border-4 border-success">
            <div class="text-muted small fw-semibold">FINALIZADOS / ATENDIDOS</div>
            <div class="fs-3 fw-bold text-success mt-1"><?= $resumen['finalizados'] ?></div>
        </div>
    </div>
</div>

<!-- Tabla Detallada del Reporte -->
<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
            <thead class="table-light">
                <tr>
                    <th>Nº Expediente</th>
                    <th>Fecha Ingreso</th>
                    <th>Solicitante</th>
                    <th>Tipo Trámite</th>
                    <th>Asunto</th>
                    <th>Oficina Actual</th>
                    <th>Estado</th>
                    <th class="text-end">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($expedientes)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            No se encontraron registros que coincidan con los filtros seleccionados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($expedientes as $exp): ?>
                        <tr>
                            <td>
                                <strong class="text-primary"><?= e($exp['numero_expediente']) ?></strong>
                                <small class="text-muted d-block font-monospace"><?= e($exp['codigo_seguimiento']) ?></small>
                            </td>
                            <td class="small text-muted"><?= formatDateTime($exp['fecha_ingreso']) ?></td>
                            <td>
                                <div class="fw-semibold text-dark">
                                    <?= e($exp['tipo_persona'] === 'JURIDICA' ? $exp['razon_social'] : $exp['nombres'] . ' ' . $exp['apellidos']) ?>
                                </div>
                                <small class="text-muted"><?= e($exp['numero_documento']) ?></small>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?= e($exp['tipo_tramite_nombre']) ?></span></td>
                            <td>
                                <div class="text-truncate" style="max-width: 240px;"><?= e($exp['asunto']) ?></div>
                            </td>
                            <td>
                                <span class="badge bg-light text-primary border"><?= e($exp['oficina_actual_sigla']) ?></span>
                            </td>
                            <td>
                                <span class="badge px-2 py-1 text-white" style="background-color: <?= e($exp['estado_color']) ?>; font-size: 0.72rem;">
                                    <?= e($exp['estado_nombre']) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="<?= url("/expedientes/{$exp['id']}") ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
