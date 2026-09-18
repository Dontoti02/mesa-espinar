<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--primary, #0B4F8A);">Estados del Expediente</h4>
        <p class="text-muted small mb-0">Catálogo administrable de estados documentarios, colores distintivos y visibilidad ciudadana.</p>
    </div>
</div>

<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">Orden</th>
                    <th>Estado Documentario</th>
                    <th>Código del Sistema</th>
                    <th>Color Identificador</th>
                    <th>Vista Previa Badge</th>
                    <th class="text-center">Visibilidad Pública</th>
                    <th class="text-end" style="width: 100px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($estados as $e): ?>
                    <tr>
                        <td class="text-muted fw-bold"><?= $e['orden'] ?></td>
                        <td>
                            <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                <span class="rounded-circle d-inline-block" style="width: 12px; height: 12px; background-color: <?= e($e['color']) ?>;"></span>
                                <?= e($e['nombre']) ?>
                            </div>
                        </td>
                        <td>
                            <code><?= e($e['codigo']) ?></code>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="d-inline-block rounded border" style="width: 24px; height: 24px; background-color: <?= e($e['color']) ?>;"></span>
                                <small class="text-muted"><?= e($e['color']) ?></small>
                            </div>
                        </td>
                        <td>
                            <span class="badge px-3 py-1 fw-semibold text-white" style="background-color: <?= e($e['color']) ?>; border-radius: 6px;">
                                <i class="bi <?= e($e['icono'] ?: 'bi-circle') ?> me-1"></i>
                                <?= e($e['nombre']) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <?php if ((int)$e['es_publico'] === 1): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="bi bi-eye me-1"></i> Visible al Ciudadano
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                    <i class="bi bi-eye-slash me-1"></i> Solo Interno
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <?php if (hasPermission('configuracion.editar')): ?>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-primary btn-edit-estado"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEditarEstado"
                                        data-id="<?= $e['id'] ?>"
                                        data-nombre="<?= e($e['nombre']) ?>"
                                        data-color="<?= e($e['color']) ?>"
                                        data-icono="<?= e($e['icono'] ?? 'bi-circle') ?>"
                                        data-orden="<?= $e['orden'] ?>"
                                        data-publico="<?= $e['es_publico'] ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL EDITAR ESTADO -->
<div class="modal fade" id="modalEditarEstado" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 12px;">
            <form id="formEditarEstado" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="color: var(--primary, #0B4F8A);">Editar Estado de Expediente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre Visible <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nombre" id="edit_est_nombre" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Color Hexadecimal <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" id="edit_est_color_picker" style="max-width: 50px;">
                                <input type="text" class="form-control" name="color" id="edit_est_color" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Nº de Orden</label>
                            <input type="number" class="form-control" name="orden" id="edit_est_orden">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Icono Bootstrap</label>
                        <input type="text" class="form-control" name="icono" id="edit_est_icono" placeholder="bi-check2-circle">
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="es_publico" value="1" id="edit_est_publico">
                        <label class="form-check-label fw-semibold" for="edit_est_publico">
                            Visible en Consulta Pública del Ciudadano
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom">Actualizar Estado</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const editBtns = document.querySelectorAll('.btn-edit-estado');
        const form = document.getElementById('formEditarEstado');
        const colorInput = document.getElementById('edit_est_color');
        const colorPicker = document.getElementById('edit_est_color_picker');

        colorPicker?.addEventListener('input', (e) => {
            colorInput.value = e.target.value;
        });
        colorInput?.addEventListener('input', (e) => {
            if (/^#[0-9A-F]{6}$/i.test(e.target.value)) {
                colorPicker.value = e.target.value;
            }
        });

        editBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                form.action = '<?= url("/estados") ?>/' + id;
                document.getElementById('edit_est_nombre').value = btn.dataset.nombre;
                colorInput.value = btn.dataset.color;
                colorPicker.value = btn.dataset.color;
                document.getElementById('edit_est_icono').value = btn.dataset.icono || '';
                document.getElementById('edit_est_orden').value = btn.dataset.orden;
                document.getElementById('edit_est_publico').checked = (btn.dataset.publico === '1');
            });
        });
    });
</script>
