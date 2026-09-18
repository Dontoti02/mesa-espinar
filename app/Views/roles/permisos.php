<div class="mb-4">
    <a href="<?= url('/roles') ?>" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1 mb-2">
        <i class="bi bi-arrow-left"></i> Volver a roles
    </a>
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--primary, #0B4F8A);">
                Permisos del Rol: <?= e($rol['nombre']) ?>
            </h4>
            <p class="text-muted small mb-0">Marca los privilegios que tendrán los usuarios asignados a este rol.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnSelectAll">
                <i class="bi bi-check-all"></i> Marcar Todos
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnUnselectAll">
                <i class="bi bi-dash"></i> Desmarcar Todos
            </button>
        </div>
    </div>
</div>

<form action="<?= url("/roles/{$rol['id']}/permisos") ?>" method="POST">
    <?= csrf_field() ?>

    <div class="row g-4 mb-4">
        <?php foreach ($permisosAgrupados as $modulo => $permisos): ?>
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card card-custom h-100">
                    <div class="card-header bg-light py-3 border-bottom d-flex align-items-center justify-content-between">
                        <span class="fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-folder-check text-primary"></i> <?= e($modulo) ?>
                        </span>
                        <span class="badge bg-secondary-subtle text-secondary small">
                            <?= count($permisos) ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <?php foreach ($permisos as $p): ?>
                            <div class="form-check mb-2">
                                <input class="form-check-input permission-checkbox" 
                                       type="checkbox" 
                                       name="permisos[]" 
                                       value="<?= $p['id'] ?>" 
                                       id="perm_<?= $p['id'] ?>"
                                       <?= in_array($p['id'], $permisosAsignados) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="perm_<?= $p['id'] ?>">
                                    <span class="fw-semibold text-dark d-block"><?= e($p['nombre']) ?></span>
                                    <small class="text-muted" style="font-size: 0.78rem;"><?= e($p['descripcion']) ?></small>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="card card-custom p-3 d-flex flex-row justify-content-between align-items-center sticky-bottom bg-white shadow">
        <span class="text-muted small">
            Los cambios surtirán efecto de inmediato para todos los usuarios con este rol.
        </span>
        <div class="d-flex gap-2">
            <a href="<?= url('/roles') ?>" class="btn btn-outline-secondary px-4">Cancelar</a>
            <button type="submit" class="btn btn-primary-custom px-4">
                <i class="bi bi-save me-1"></i> Guardar Permisos
            </button>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const checkboxes = document.querySelectorAll('.permission-checkbox');
        document.getElementById('btnSelectAll')?.addEventListener('click', () => {
            checkboxes.forEach(cb => cb.checked = true);
        });
        document.getElementById('btnUnselectAll')?.addEventListener('click', () => {
            checkboxes.forEach(cb => cb.checked = false);
        });
    });
</script>
