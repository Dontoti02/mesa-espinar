<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--primary, #0B4F8A);">Dashboard Institucional</h4>
        <p class="text-muted small mb-0">Resumen operativo de expedientes, flujo documental y métricas en tiempo real.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('/expedientes/crear') ?>" class="btn btn-primary-custom d-inline-flex align-items-center gap-2">
            <i class="bi bi-file-earmark-plus"></i>
            <span>Nuevo Trámite</span>
        </a>
    </div>
</div>

<!-- 8 Tarjetas de KPIs (Sección 18) -->
<div class="row g-3 mb-4">
    <!-- 1. Ingresados Hoy -->
    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-primary h-100">
            <div class="text-muted small fw-semibold">INGRESADOS HOY</div>
            <div class="d-flex align-items-center justify-content-between mt-2">
                <span class="fs-2 fw-bold text-dark"><?= $stats['hoy'] ?></span>
                <div class="p-2 bg-primary-subtle text-primary rounded-3"><i class="bi bi-calendar-check fs-4"></i></div>
            </div>
        </div>
    </div>

    <!-- 2. Del Mes -->
    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-info h-100">
            <div class="text-muted small fw-semibold">EXPEDIENTES DEL MES</div>
            <div class="d-flex align-items-center justify-content-between mt-2">
                <span class="fs-2 fw-bold text-dark"><?= $stats['mes'] ?></span>
                <div class="p-2 bg-info-subtle text-info rounded-3"><i class="bi bi-calendar3 fs-4"></i></div>
            </div>
        </div>
    </div>

    <!-- 3. Pendientes Totales -->
    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-warning h-100">
            <div class="text-muted small fw-semibold">PENDIENTES GLOBALES</div>
            <div class="d-flex align-items-center justify-content-between mt-2">
                <span class="fs-2 fw-bold text-dark"><?= $stats['pendientes'] ?></span>
                <div class="p-2 bg-warning-subtle text-warning rounded-3"><i class="bi bi-hourglass-split fs-4"></i></div>
            </div>
        </div>
    </div>

    <!-- 4. En Dirección -->
    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-secondary h-100">
            <div class="text-muted small fw-semibold">EN DIRECCIÓN</div>
            <div class="d-flex align-items-center justify-content-between mt-2">
                <span class="fs-2 fw-bold text-dark"><?= $stats['direccion'] ?></span>
                <div class="p-2 bg-secondary-subtle text-secondary rounded-3"><i class="bi bi-building-fill-gear fs-4"></i></div>
            </div>
        </div>
    </div>

    <!-- 5. En Oficinas -->
    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-dark h-100">
            <div class="text-muted small fw-semibold">EN OFICINAS TÉCNICAS</div>
            <div class="d-flex align-items-center justify-content-between mt-2">
                <span class="fs-2 fw-bold text-dark"><?= $stats['oficinas'] ?></span>
                <div class="p-2 bg-light text-dark rounded-3 border"><i class="bi bi-diagram-3 fs-4"></i></div>
            </div>
        </div>
    </div>

    <!-- 6. Observados / Devueltos -->
    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-danger h-100">
            <div class="text-muted small fw-semibold">OBSERVADOS / DEVUELTOS</div>
            <div class="d-flex align-items-center justify-content-between mt-2">
                <span class="fs-2 fw-bold text-dark"><?= $stats['observados'] ?></span>
                <div class="p-2 bg-danger-subtle text-danger rounded-3"><i class="bi bi-exclamation-triangle fs-4"></i></div>
            </div>
        </div>
    </div>

    <!-- 7. Finalizados -->
    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-success h-100">
            <div class="text-muted small fw-semibold">FINALIZADOS CON ÉXITO</div>
            <div class="d-flex align-items-center justify-content-between mt-2">
                <span class="fs-2 fw-bold text-dark"><?= $stats['finalizados'] ?></span>
                <div class="p-2 bg-success-subtle text-success rounded-3"><i class="bi bi-check2-all fs-4"></i></div>
            </div>
        </div>
    </div>

    <!-- 8. Urgentes -->
    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 border-start border-4 h-100" style="border-color: var(--accent, #DC2626) !important;">
            <div class="text-muted small fw-semibold">PRIORIDAD URGENTE</div>
            <div class="d-flex align-items-center justify-content-between mt-2">
                <span class="fs-2 fw-bold" style="color: var(--accent, #DC2626);"><?= $stats['urgentes'] ?></span>
                <div class="p-2 rounded-3 text-white" style="background-color: var(--accent, #DC2626);"><i class="bi bi-lightning-charge-fill fs-4"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos Interactivos con Chart.js -->
<div class="row g-4 mb-4">
    <!-- Gráfico 1: Expedientes por Estado -->
    <div class="col-12 col-lg-6">
        <div class="card card-custom p-4 h-100">
            <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom">
                <i class="bi bi-pie-chart-fill me-2"></i> Distribución por Estado
            </h6>
            <div style="position: relative; height: 260px;">
                <canvas id="chartEstados"></canvas>
            </div>
        </div>
    </div>

    <!-- Gráfico 2: Expedientes por Oficina Actual -->
    <div class="col-12 col-lg-6">
        <div class="card card-custom p-4 h-100">
            <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom">
                <i class="bi bi-bar-chart-line-fill me-2"></i> Carga de Trámites por Oficina
            </h6>
            <div style="position: relative; height: 260px;">
                <canvas id="chartOficinas"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tablas Rápidas: Últimos y Urgentes -->
<div class="row g-4">
    <!-- Últimos Expedientes -->
    <div class="col-12 col-lg-7">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <h6 class="fw-bold text-primary mb-0">
                    <i class="bi bi-clock-history me-2"></i> Últimos Expedientes Registrados
                </h6>
                <a href="<?= url('/expedientes') ?>" class="small text-decoration-none fw-semibold">Ver todos</a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                    <thead class="table-light">
                        <tr>
                            <th>Expediente</th>
                            <th>Solicitante</th>
                            <th>Oficina</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ultimosExpedientes)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-3">Sin expedientes registrados aún.</td></tr>
                        <?php else: ?>
                            <?php foreach ($ultimosExpedientes as $u): ?>
                                <tr>
                                    <td>
                                        <a href="<?= url("/expedientes/{$u['id']}") ?>" class="fw-bold text-decoration-none">
                                            <?= e($u['numero_expediente']) ?>
                                        </a>
                                        <small class="text-muted d-block"><?= formatDateTime($u['fecha_ingreso']) ?></small>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 170px;">
                                            <?= e($u['tipo_persona'] === 'JURIDICA' ? $u['razon_social'] : $u['nombres'] . ' ' . $u['apellidos']) ?>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border"><?= e($u['oficina_actual_sigla']) ?></span></td>
                                    <td>
                                        <span class="badge px-2 py-1 text-white" style="background-color: <?= e($u['estado_color']) ?>; font-size: 0.72rem;">
                                            <?= e($u['estado_nombre']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Trámites Urgentes -->
    <div class="col-12 col-lg-5">
        <div class="card card-custom p-4 h-100">
            <h6 class="fw-bold text-danger mb-3 pb-2 border-bottom">
                <i class="bi bi-exclamation-circle-fill me-2"></i> Trámites de Atención Prioritaria
            </h6>
            <?php if (empty($urgentesExpedientes)): ?>
                <div class="text-center py-5 text-muted small">
                    <i class="bi bi-shield-check fs-2 text-success d-block mb-2"></i>
                    No hay trámites urgentes pendientes de atención.
                </div>
            <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($urgentesExpedientes as $urg): ?>
                        <a href="<?= url("/expedientes/{$urg['id']}") ?>" class="list-group-item list-group-item-action px-0 py-2 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="text-primary small"><?= e($urg['numero_expediente']) ?></strong>
                                <span class="badge px-2 py-1 text-white" style="background-color: <?= e($urg['prioridad_color']) ?>; font-size: 0.7rem;">
                                    <?= e($urg['prioridad_nombre']) ?>
                                </span>
                            </div>
                            <div class="small text-truncate text-dark fw-medium"><?= e($urg['asunto']) ?></div>
                            <div class="d-flex justify-content-between text-muted" style="font-size: 0.75rem;">
                                <span>Oficina: <?= e($urg['oficina_actual_sigla']) ?></span>
                                <span><?= formatDate($urg['fecha_ingreso']) ?></span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Datos para gráfico de Estados
        const estadosData = <?= json_encode($estadosData) ?>;
        const ctxEstados = document.getElementById('chartEstados')?.getContext('2d');
        if (ctxEstados && estadosData.length > 0) {
            new Chart(ctxEstados, {
                type: 'doughnut',
                data: {
                    labels: estadosData.map(e => e.nombre),
                    datasets: [{
                        data: estadosData.map(e => parseInt(e.total)),
                        backgroundColor: estadosData.map(e => e.color),
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
                    }
                }
            });
        }

        // Datos para gráfico de Oficinas
        const oficinasData = <?= json_encode($oficinasData) ?>;
        const ctxOficinas = document.getElementById('chartOficinas')?.getContext('2d');
        if (ctxOficinas && oficinasData.length > 0) {
            new Chart(ctxOficinas, {
                type: 'bar',
                data: {
                    labels: oficinasData.map(o => o.sigla),
                    datasets: [{
                        label: 'Expedientes',
                        data: oficinasData.map(o => parseInt(o.total)),
                        backgroundColor: '#0B4F8A',
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1 } }
                    }
                }
            });
        }
    });
</script>
