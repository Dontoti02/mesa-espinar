<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--primary, #0B4F8A);">Gestión de Usuarios</h4>
        <p class="text-muted small mb-0">Administración de cuentas de acceso, roles y asignación de oficinas.</p>
    </div>
    <?php if (hasPermission('usuarios.crear')): ?>
        <a href="<?= url('/usuarios/crear') ?>" class="btn btn-primary-custom d-inline-flex align-items-center gap-2">
            <i class="bi bi-person-plus-fill"></i>
            <span>Nuevo Usuario</span>
        </a>
    <?php endif; ?>
</div>

<!-- Filtros de búsqueda -->
<div class="card card-custom mb-4 p-3">
    <form action="<?= url('/usuarios') ?>" method="GET" class="row g-2 align-items-center">
        <div class="col-12 col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0 ps-0" name="buscar" placeholder="Buscar por nombre, usuario, DNI o correo..." value="<?= e($filtros['buscar']) ?>">
            </div>
        </div>
        <div class="col-6 col-md-3">
            <select name="rol_id" class="form-select">
                <option value="">-- Todos los Roles --</option>
                <?php foreach ($roles as $r): ?>
                    <option value="<?= $r['id'] ?>" <?= ($filtros['rol_id'] == $r['id']) ? 'selected' : '' ?>>
                        <?= e($r['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-6 col-md-3">
            <select name="oficina_id" class="form-select">
                <option value="">-- Todas las Oficinas --</option>
                <?php foreach ($oficinas as $o): ?>
                    <option value="<?= $o['id'] ?>" <?= ($filtros['oficina_id'] == $o['id']) ? 'selected' : '' ?>>
                        <?= e($o['nombre']) ?> (<?= e($o['sigla']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12 col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary-custom w-100">
                Filtrar
            </button>
            <?php if (!empty($filtros['buscar']) || !empty($filtros['rol_id']) || !empty($filtros['oficina_id'])): ?>
                <a href="<?= url('/usuarios') ?>" class="btn btn-outline-secondary" title="Limpiar filtros">
                    <i class="bi bi-x-circle"></i>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Tabla de Usuarios -->
<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Usuario / Colaborador</th>
                    <th>DNI / Contacto</th>
                    <th>Rol Asignado</th>
                    <th>Oficina</th>
                    <th>Estado</th>
                    <th>Último Acceso</th>
                    <th class="text-end" style="width: 140px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($usuarios)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            No se encontraron usuarios registrados con los criterios seleccionados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td class="text-muted small"><?= $u['id'] ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 38px; height: 38px; background-color: var(--primary, #0B4F8A); flex-shrink: 0;">
                                        <?= strtoupper(substr($u['nombres'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark"><?= e($u['nombres'] . ' ' . $u['apellidos']) ?></div>
                                        <small class="text-muted">@<?= e($u['usuario']) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div><i class="bi bi-person-badge text-muted me-1"></i><?= e($u['dni'] ?: 'S/D') ?></div>
                                <small class="text-muted"><i class="bi bi-envelope text-muted me-1"></i><?= e($u['correo']) ?></small>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold px-2 py-1">
                                    <?= e($u['rol_nombre']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($u['oficina_nombre'])): ?>
                                    <span class="badge bg-light text-dark border">
                                        <?= e($u['oficina_sigla']) ?> - <?= e($u['oficina_nombre']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted small">Sin Oficina</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ((int)$u['estado'] === 1): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="bi bi-check-circle me-1"></i> Activo
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                        <i class="bi bi-x-circle me-1"></i> Inactivo
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted">
                                <?= formatDateTime($u['ultimo_acceso']) ?>
                            </td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <?php if (hasPermission('usuarios.editar')): ?>
                                            <li>
                                                <a class="dropdown-item py-2" href="<?= url("/usuarios/{$u['id']}/editar") ?>">
                                                    <i class="bi bi-pencil-square text-primary me-2"></i> Editar
                                                </a>
                                            </li>
                                            <li>
                                                <form action="<?= url("/usuarios/{$u['id']}/reset-password") ?>" method="POST" onsubmit="return confirm('¿Seguro que deseas restablecer la contraseña a una temporal?');">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="dropdown-item py-2">
                                                        <i class="bi bi-key text-warning me-2"></i> Restablecer Clave
                                                    </button>
                                                </form>
                                            </li>
                                        <?php endif; ?>

                                        <?php if (hasPermission('usuarios.eliminar') && (int)$u['id'] !== auth()['id']): ?>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="<?= url("/usuarios/{$u['id']}/toggle-estado") ?>" method="POST" onsubmit="return confirm('¿Confirmas cambiar el estado de este usuario?');">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="dropdown-item py-2 text-<?= (int)$u['estado'] === 1 ? 'danger' : 'success' ?>">
                                                        <i class="bi bi-power me-2"></i> <?= (int)$u['estado'] === 1 ? 'Desactivar' : 'Activar' ?>
                                                    </button>
                                                </form>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
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
                Total: <strong><?= $pagination['total_records'] ?></strong> usuarios registrados
            </small>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <?php for ($p = 1; $p <= $pagination['total_pages']; $p++): ?>
                        <li class="page-item <?= ($p == $pagination['current_page']) ? 'active' : '' ?>">
                            <a class="page-link" href="<?= url('/usuarios?page=' . $p . '&buscar=' . urlencode($filtros['buscar']) . '&rol_id=' . $filtros['rol_id'] . '&oficina_id=' . $filtros['oficina_id']) ?>">
                                <?= $p ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>
