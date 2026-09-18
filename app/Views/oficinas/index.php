<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--primary, #0B4F8A);">Oficinas Institucionales</h4>
        <p class="text-muted small mb-0">Gestión de las 19 dependencias institucionales, responsables y buzones de derivación.</p>
    </div>
    <?php if (hasPermission('oficinas.gestionar')): ?>
        <button type="button" class="btn btn-primary-custom d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalNuevaOficina">
            <i class="bi bi-plus-circle-fill"></i>
            <span>Nueva Oficina</span>
        </button>
    <?php endif; ?>
</div>

<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">Orden</th>
                    <th>Oficina / Dependencia</th>
                    <th>Sigla</th>
                    <th>Responsable</th>
                    <th>Contacto</th>
                    <th class="text-center">Usuarios</th>
                    <th class="text-center">Estado</th>
                    <th class="text-end" style="width: 130px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($oficinas as $o): ?>
                    <tr>
                        <td class="text-muted fw-bold"><?= $o['orden'] ?></td>
                        <td>
                            <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                <i class="bi bi-building text-primary"></i>
                                <?= e($o['nombre']) ?>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-primary border border-primary-subtle fw-bold">
                                <?= e($o['sigla']) ?>
                            </span>
                        </td>
                        <td class="small">
                            <?= e($o['responsable'] ?: 'No asignado') ?>
                        </td>
                        <td class="small text-muted">
                            <?php if (!empty($o['correo'])): ?>
                                <div><i class="bi bi-envelope me-1"></i><?= e($o['correo']) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($o['telefono'])): ?>
                                <div><i class="bi bi-telephone me-1"></i><?= e($o['telefono']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge rounded-pill bg-light text-dark border">
                                <?= (int)$o['total_usuarios'] ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <?php if ((int)$o['activo'] === 1): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Activa</span>
                            <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Inactiva</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <?php if (hasPermission('oficinas.gestionar')): ?>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-primary btn-edit-oficina" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEditarOficina"
                                        data-id="<?= $o['id'] ?>"
                                        data-nombre="<?= e($o['nombre']) ?>"
                                        data-sigla="<?= e($o['sigla']) ?>"
                                        data-responsable="<?= e($o['responsable']) ?>"
                                        data-correo="<?= e($o['correo']) ?>"
                                        data-telefono="<?= e($o['telefono']) ?>"
                                        data-orden="<?= $o['orden'] ?>"
                                        data-activo="<?= $o['activo'] ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="<?= url("/oficinas/{$o['id']}/toggle-estado") ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Confirmas cambiar el estado de esta oficina? Los expedientes previos conservarán su historial.');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-<?= (int)$o['activo'] === 1 ? 'danger' : 'success' ?>" title="<?= (int)$o['activo'] === 1 ? 'Desactivar oficina' : 'Activar oficina' ?>">
                                        <i class="bi bi-power"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL NUEVA OFICINA -->
<div class="modal fade" id="modalNuevaOficina" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 12px;">
            <form action="<?= url('/oficinas') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="color: var(--primary, #0B4F8A);">Registrar Nueva Oficina</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre de la Oficina <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nombre" placeholder="Ej. Área de Bienestar Estudiantil" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Sigla / Abreviatura <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="sigla" placeholder="Ej. BIEN-EST" maxlength="20" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Nº de Orden</label>
                            <input type="number" class="form-control" name="orden" value="20">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Responsable Institucional</label>
                        <input type="text" class="form-control" name="responsable" placeholder="Ej. Lic. Nombre Completo">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-7">
                            <label class="form-label fw-semibold">Correo Institucional</label>
                            <input type="email" class="form-control" name="correo" placeholder="correo@iestpespinar.edu.pe">
                        </div>
                        <div class="col-5">
                            <label class="form-label fw-semibold">Teléfono</label>
                            <input type="text" class="form-control" name="telefono" placeholder="084-301200">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom">Guardar Oficina</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR OFICINA -->
<div class="modal fade" id="modalEditarOficina" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 12px;">
            <form id="formEditarOficina" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="color: var(--primary, #0B4F8A);">Editar Oficina</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre de la Oficina <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nombre" id="edit_nombre" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Sigla <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="sigla" id="edit_sigla" maxlength="20" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Nº de Orden</label>
                            <input type="number" class="form-control" name="orden" id="edit_orden">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Responsable</label>
                        <input type="text" class="form-control" name="responsable" id="edit_responsable">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-7">
                            <label class="form-label fw-semibold">Correo Institucional</label>
                            <input type="email" class="form-control" name="correo" id="edit_correo">
                        </div>
                        <div class="col-5">
                            <label class="form-label fw-semibold">Teléfono</label>
                            <input type="text" class="form-control" name="telefono" id="edit_telefono">
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="activo" value="1" id="edit_activo">
                        <label class="form-check-label fw-semibold" for="edit_activo">
                            Oficina activa
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom">Actualizar Oficina</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const editButtons = document.querySelectorAll('.btn-edit-oficina');
        const form = document.getElementById('formEditarOficina');

        editButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                form.action = '<?= url("/oficinas") ?>/' + id;
                document.getElementById('edit_nombre').value = btn.dataset.nombre;
                document.getElementById('edit_sigla').value = btn.dataset.sigla;
                document.getElementById('edit_responsable').value = btn.dataset.responsable || '';
                document.getElementById('edit_correo').value = btn.dataset.correo || '';
                document.getElementById('edit_telefono').value = btn.dataset.telefono || '';
                document.getElementById('edit_orden').value = btn.dataset.orden;
                document.getElementById('edit_activo').checked = (btn.dataset.activo === '1');
            });
        });
    });
</script>
