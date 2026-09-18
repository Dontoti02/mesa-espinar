<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--primary, #0B4F8A);">Tipos de Trámite</h4>
        <p class="text-muted small mb-0">Catálogo de trámites institucionales, requisitos, plazos de atención y aranceles.</p>
    </div>
    <?php if (hasPermission('tramites.gestionar')): ?>
        <button type="button" class="btn btn-primary-custom d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalNuevoTramite">
            <i class="bi bi-plus-circle-fill"></i>
            <span>Nuevo Trámite</span>
        </button>
    <?php endif; ?>
</div>

<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 90px;">Código</th>
                    <th>Nombre del Trámite</th>
                    <th>Oficina Sugerida</th>
                    <th class="text-center">Plazo Ref.</th>
                    <th class="text-center">Vía Virtual</th>
                    <th class="text-center">Costo (S/)</th>
                    <th class="text-center">Estado</th>
                    <th class="text-end" style="width: 120px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tramites as $t): ?>
                    <tr>
                        <td>
                            <code class="fw-bold text-primary"><?= e($t['codigo']) ?></code>
                        </td>
                        <td>
                            <div class="fw-bold text-dark"><?= e($t['nombre']) ?></div>
                            <small class="text-muted text-truncate d-block" style="max-width: 320px;">
                                <?= e($t['descripcion'] ?: 'Sin descripción') ?>
                            </small>
                        </td>
                        <td>
                            <?php if (!empty($t['oficina_nombre'])): ?>
                                <span class="badge bg-light text-dark border">
                                    <?= e($t['oficina_sigla']) ?> - <?= e($t['oficina_nombre']) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted small">Por derivar en Dirección</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary-subtle text-secondary px-2 py-1">
                                <i class="bi bi-clock me-1"></i> <?= (int)$t['plazo_referencial_dias'] ?> días
                            </span>
                        </td>
                        <td class="text-center">
                            <?php if ((int)$t['permite_virtual'] === 1): ?>
                                <i class="bi bi-check-circle-fill text-success fs-5" title="Permitido en portal virtual"></i>
                            <?php else: ?>
                                <i class="bi bi-x-circle-fill text-danger fs-5" title="Solo presencial"></i>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ((int)$t['requiere_pago'] === 1): ?>
                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle">
                                    S/ <?= number_format($t['monto'], 2) ?>
                                </span>
                            <?php else: ?>
                                <span class="badge bg-light text-muted border">Gratuito</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ((int)$t['activo'] === 1): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Activo</span>
                            <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <?php if (hasPermission('tramites.gestionar')): ?>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-primary btn-edit-tramite" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEditarTramite"
                                        data-id="<?= $t['id'] ?>"
                                        data-codigo="<?= e($t['codigo']) ?>"
                                        data-nombre="<?= e($t['nombre']) ?>"
                                        data-descripcion="<?= e($t['descripcion']) ?>"
                                        data-oficina-id="<?= $t['oficina_sugerida_id'] ?>"
                                        data-plazo="<?= $t['plazo_referencial_dias'] ?>"
                                        data-requisitos="<?= e($t['requisitos']) ?>"
                                        data-instrucciones="<?= e($t['instrucciones']) ?>"
                                        data-virtual="<?= $t['permite_virtual'] ?>"
                                        data-pago="<?= $t['requiere_pago'] ?>"
                                        data-monto="<?= $t['monto'] ?>"
                                        data-activo="<?= $t['activo'] ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="<?= url("/tipos-tramite/{$t['id']}/toggle-estado") ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Confirmas cambiar el estado de este trámite?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-<?= (int)$t['activo'] === 1 ? 'danger' : 'success' ?>">
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

<!-- MODAL NUEVO TRÁMITE -->
<div class="modal fade" id="modalNuevoTramite" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 12px;">
            <form action="<?= url('/tipos-tramite') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="color: var(--primary, #0B4F8A);">Registrar Tipo de Trámite</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <label class="form-label fw-semibold">Código Único <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="codigo" placeholder="Ej. CERT-MOD" maxlength="20" required>
                        </div>
                        <div class="col-8">
                            <label class="form-label fw-semibold">Nombre del Trámite <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nombre" placeholder="Ej. Certificado Modular Oficial" required>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Oficina de Destino Sugerida</label>
                            <select name="oficina_sugerida_id" class="form-select">
                                <option value="">-- Sin asignar (Decisión de Dirección) --</option>
                                <?php foreach ($oficinas as $o): ?>
                                    <option value="<?= $o['id'] ?>"><?= e($o['nombre']) ?> (<?= e($o['sigla']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Plazo Referencial (Días Hábiles) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="plazo_referencial_dias" value="15" min="1" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="2" placeholder="Resumen del trámite para orientación al usuario..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Requisitos Documentarios</label>
                        <textarea class="form-control" name="requisitos" rows="2" placeholder="Documentos que el solicitante debe adjuntar obligatoriamente..."></textarea>
                    </div>

                    <div class="row g-3 p-3 bg-light rounded mb-2">
                        <div class="col-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="permite_virtual" value="1" id="permiteVirtualCheck" checked>
                                <label class="form-check-label fw-semibold" for="permiteVirtualCheck">
                                    Disponible en Mesa de Partes Virtual
                                </label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="requiere_pago" value="1" id="requierePagoCheck">
                                <label class="form-check-label fw-semibold" for="requierePagoCheck">
                                    Requiere Pago de TUPA / Arancel
                                </label>
                            </div>
                            <div id="montoContainer" style="display: none;">
                                <label class="form-label small fw-semibold">Monto en Soles (S/)</label>
                                <input type="number" step="0.01" class="form-control form-control-sm" name="monto" value="0.00">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom">Guardar Trámite</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR TRÁMITE -->
<div class="modal fade" id="modalEditarTramite" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 12px;">
            <form id="formEditarTramite" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="color: var(--primary, #0B4F8A);">Editar Tipo de Trámite</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <label class="form-label fw-semibold">Código Único <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="codigo" id="edit_codigo" maxlength="20" required>
                        </div>
                        <div class="col-8">
                            <label class="form-label fw-semibold">Nombre del Trámite <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nombre" id="edit_nombre" required>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Oficina Sugerida</label>
                            <select name="oficina_sugerida_id" id="edit_oficina_id" class="form-select">
                                <option value="">-- Sin asignar --</option>
                                <?php foreach ($oficinas as $o): ?>
                                    <option value="<?= $o['id'] ?>"><?= e($o['nombre']) ?> (<?= e($o['sigla']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Plazo Referencial (Días)</label>
                            <input type="number" class="form-control" name="plazo_referencial_dias" id="edit_plazo" min="1" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea class="form-control" name="descripcion" id="edit_descripcion" rows="2"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Requisitos Documentarios</label>
                        <textarea class="form-control" name="requisitos" id="edit_requisitos" rows="2"></textarea>
                    </div>

                    <div class="row g-3 p-3 bg-light rounded mb-2">
                        <div class="col-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="permite_virtual" value="1" id="edit_virtual">
                                <label class="form-check-label fw-semibold" for="edit_virtual">
                                    Disponible en Mesa de Partes Virtual
                                </label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="requiere_pago" value="1" id="edit_pago">
                                <label class="form-check-label fw-semibold" for="edit_pago">
                                    Requiere Pago de Arancel
                                </label>
                            </div>
                            <div id="edit_montoContainer">
                                <label class="form-label small fw-semibold">Monto en Soles (S/)</label>
                                <input type="number" step="0.01" class="form-control form-control-sm" name="monto" id="edit_monto">
                            </div>
                        </div>
                    </div>

                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="activo" value="1" id="edit_activo">
                        <label class="form-check-label fw-semibold" for="edit_activo">
                            Tipo de trámite activo
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom">Actualizar Trámite</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const pagoCheck = document.getElementById('requierePagoCheck');
        const montoContainer = document.getElementById('montoContainer');
        if (pagoCheck && montoContainer) {
            pagoCheck.addEventListener('change', () => {
                montoContainer.style.display = pagoCheck.checked ? 'block' : 'none';
            });
        }

        const editPago = document.getElementById('edit_pago');
        const editMontoContainer = document.getElementById('edit_montoContainer');
        if (editPago && editMontoContainer) {
            editPago.addEventListener('change', () => {
                editMontoContainer.style.display = editPago.checked ? 'block' : 'none';
            });
        }

        const editBtns = document.querySelectorAll('.btn-edit-tramite');
        const editForm = document.getElementById('formEditarTramite');

        editBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                editForm.action = '<?= url("/tipos-tramite") ?>/' + id;
                document.getElementById('edit_codigo').value = btn.dataset.codigo;
                document.getElementById('edit_nombre').value = btn.dataset.nombre;
                document.getElementById('edit_descripcion').value = btn.dataset.descripcion || '';
                document.getElementById('edit_oficina_id').value = btn.dataset.oficinaId || '';
                document.getElementById('edit_plazo').value = btn.dataset.plazo;
                document.getElementById('edit_requisitos').value = btn.dataset.requisitos || '';
                document.getElementById('edit_virtual').checked = (btn.dataset.virtual === '1');
                document.getElementById('edit_pago').checked = (btn.dataset.pago === '1');
                document.getElementById('edit_monto').value = btn.dataset.monto || '0.00';
                document.getElementById('edit_montoContainer').style.display = (btn.dataset.pago === '1') ? 'block' : 'none';
                document.getElementById('edit_activo').checked = (btn.dataset.activo === '1');
            });
        });
    });
</script>
