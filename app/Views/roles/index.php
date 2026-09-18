<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--primary, #0B4F8A);">Roles y Control de Acceso (RBAC)</h4>
        <p class="text-muted small mb-0">Gestión de perfiles de usuario y matriz de permisos por módulo institucional.</p>
    </div>
</div>

<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Rol Institucional</th>
                    <th>Identificador (Slug)</th>
                    <th>Descripción</th>
                    <th class="text-center">Usuarios Asignados</th>
                    <th class="text-end" style="width: 180px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($roles as $r): ?>
                    <tr>
                        <td class="text-muted small"><?= $r['id'] ?></td>
                        <td>
                            <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                <i class="bi bi-shield-shaded text-primary"></i>
                                <?= e($r['nombre']) ?>
                            </div>
                        </td>
                        <td>
                            <code><?= e($r['slug']) ?></code>
                        </td>
                        <td class="text-muted small">
                            <?= e($r['descripcion'] ?: '-') ?>
                        </td>
                        <td class="text-center">
                            <span class="badge rounded-pill bg-light text-dark border px-3 py-1">
                                <?= (int)$r['total_usuarios'] ?> usuarios
                            </span>
                        </td>
                        <td class="text-end">
                            <?php if (hasPermission('roles.gestionar')): ?>
                                <a href="<?= url("/roles/{$r['id']}/permisos") ?>" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-key-fill"></i>
                                    <span>Configurar Permisos</span>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
