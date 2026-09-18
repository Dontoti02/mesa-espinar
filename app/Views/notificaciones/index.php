<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--primary, #0B4F8A);">Centro de Notificaciones</h4>
        <p class="text-muted small mb-0">Avisos automáticos de expedientes recibidos, derivaciones y respuestas técnicas.</p>
    </div>
    <?php if (!empty($notificaciones)): ?>
        <form action="<?= url('/notificaciones/marcar-todas') ?>" method="POST">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-check2-all me-1"></i> Marcar todas como leídas
            </button>
        </form>
    <?php endif; ?>
</div>

<div class="card card-custom">
    <?php if (empty($notificaciones)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-bell-slash fs-1 d-block mb-2 opacity-50"></i>
            No tienes notificaciones registradas en este momento.
        </div>
    <?php else: ?>
        <div class="list-group list-group-flush">
            <?php foreach ($notificaciones as $n): ?>
                <div class="list-group-item p-3 d-flex justify-content-between align-items-start <?= (int)$n['leido'] === 0 ? 'bg-light' : '' ?>">
                    <div class="d-flex align-items-start gap-3">
                        <div class="p-2 rounded-circle mt-1 <?= (int)$n['leido'] === 0 ? 'bg-primary text-white' : 'bg-secondary-subtle text-secondary' ?>">
                            <i class="bi bi-bell-fill"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="fw-bold text-dark"><?= e($n['titulo']) ?></span>
                                <?php if ((int)$n['leido'] === 0): ?>
                                    <span class="badge bg-danger" style="font-size: 0.65rem;">Nueva</span>
                                <?php endif; ?>
                            </div>
                            <p class="small text-secondary mb-1"><?= e($n['mensaje']) ?></p>
                            <small class="text-muted" style="font-size: 0.75rem;">
                                <i class="bi bi-clock me-1"></i><?= formatDateTime($n['created_at']) ?>
                            </small>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <?php if (!empty($n['enlace'])): ?>
                            <a href="<?= e($n['enlace']) ?>" class="btn btn-sm btn-primary-custom">
                                Ver Expediente <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        <?php endif; ?>
                        <?php if ((int)$n['leido'] === 0): ?>
                            <form action="<?= url("/notificaciones/{$n['id']}/marcar") ?>" method="POST" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-secondary" title="Marcar como leída">
                                    <i class="bi bi-check2"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
