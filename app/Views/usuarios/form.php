<?php
$isEdit = !empty($usuario);
$actionUrl = $isEdit ? url("/usuarios/{$usuario['id']}") : url('/usuarios');
$validationErrors = \App\Core\Session::get('_validation_errors', []);
\App\Core\Session::remove('_validation_errors');
?>

<div class="mb-4">
    <a href="<?= url('/usuarios') ?>" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1 mb-2">
        <i class="bi bi-arrow-left"></i> Volver al listado
    </a>
    <h4 class="fw-bold mb-1" style="color: var(--primary, #0B4F8A);">
        <?= $isEdit ? 'Editar Usuario: ' . e($usuario['usuario']) : 'Registrar Nuevo Usuario' ?>
    </h4>
    <p class="text-muted small mb-0">Completa los datos del colaborador institucional para otorgar acceso al sistema.</p>
</div>

<div class="card card-custom p-4" style="max-width: 860px;">
    <form action="<?= $actionUrl ?>" method="POST" autocomplete="off">
        <?= csrf_field() ?>

        <div class="row g-3 mb-3">
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Nombres <span class="text-danger">*</span></label>
                <input type="text" 
                       class="form-control <?= isset($validationErrors['nombres']) ? 'is-invalid' : '' ?>" 
                       name="nombres" 
                       value="<?= e(old('nombres', $usuario['nombres'] ?? '')) ?>" 
                       required>
                <?php if (isset($validationErrors['nombres'])): ?>
                    <div class="invalid-feedback"><?= e($validationErrors['nombres'][0]) ?></div>
                <?php endif; ?>
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Apellidos <span class="text-danger">*</span></label>
                <input type="text" 
                       class="form-control <?= isset($validationErrors['apellidos']) ? 'is-invalid' : '' ?>" 
                       name="apellidos" 
                       value="<?= e(old('apellidos', $usuario['apellidos'] ?? '')) ?>" 
                       required>
                <?php if (isset($validationErrors['apellidos'])): ?>
                    <div class="invalid-feedback"><?= e($validationErrors['apellidos'][0]) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">DNI / Documento <span class="text-danger">*</span></label>
                <input type="text" 
                       class="form-control <?= isset($validationErrors['dni']) ? 'is-invalid' : '' ?>" 
                       name="dni" 
                       maxlength="15" 
                       value="<?= e(old('dni', $usuario['dni'] ?? '')) ?>" 
                       required>
                <?php if (isset($validationErrors['dni'])): ?>
                    <div class="invalid-feedback"><?= e($validationErrors['dni'][0]) ?></div>
                <?php endif; ?>
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Nombre de Usuario <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text text-muted">@</span>
                    <input type="text" 
                           class="form-control <?= isset($validationErrors['usuario']) ? 'is-invalid' : '' ?>" 
                           name="usuario" 
                           value="<?= e(old('usuario', $usuario['usuario'] ?? '')) ?>" 
                           required>
                    <?php if (isset($validationErrors['usuario'])): ?>
                        <div class="invalid-feedback"><?= e($validationErrors['usuario'][0]) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Correo Electrónico <span class="text-danger">*</span></label>
                <input type="email" 
                       class="form-control <?= isset($validationErrors['correo']) ? 'is-invalid' : '' ?>" 
                       name="correo" 
                       value="<?= e(old('correo', $usuario['correo'] ?? '')) ?>" 
                       required>
                <?php if (isset($validationErrors['correo'])): ?>
                    <div class="invalid-feedback"><?= e($validationErrors['correo'][0]) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Rol de Acceso <span class="text-danger">*</span></label>
                <select name="rol_id" class="form-select <?= isset($validationErrors['rol_id']) ? 'is-invalid' : '' ?>" required>
                    <option value="">-- Seleccionar Rol --</option>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= $r['id'] ?>" <?= (old('rol_id', $usuario['rol_id'] ?? '') == $r['id']) ? 'selected' : '' ?>>
                            <?= e($r['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($validationErrors['rol_id'])): ?>
                    <div class="invalid-feedback"><?= e($validationErrors['rol_id'][0]) ?></div>
                <?php endif; ?>
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Oficina Asignada <span class="text-danger">*</span></label>
                <select name="oficina_id" class="form-select <?= isset($validationErrors['oficina_id']) ? 'is-invalid' : '' ?>" required>
                    <option value="">-- Seleccionar Oficina --</option>
                    <?php foreach ($oficinas as $o): ?>
                        <option value="<?= $o['id'] ?>" <?= (old('oficina_id', $usuario['oficina_id'] ?? '') == $o['id']) ? 'selected' : '' ?>>
                            <?= e($o['nombre']) ?> (<?= e($o['sigla']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($validationErrors['oficina_id'])): ?>
                    <div class="invalid-feedback"><?= e($validationErrors['oficina_id'][0]) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Cargo / Función Institucional</label>
                <input type="text" class="form-control" name="cargo" value="<?= e(old('cargo', $usuario['cargo'] ?? '')) ?>" placeholder="Ej. Especialista de Trámite">
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Teléfono de Contacto</label>
                <input type="text" class="form-control" name="telefono" value="<?= e(old('telefono', $usuario['telefono'] ?? '')) ?>" placeholder="Ej. 984000000">
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">
                    <?= $isEdit ? 'Nueva Contraseña (dejar en blanco para no cambiar)' : 'Contraseña Inicial *' ?>
                </label>
                <input type="password" 
                       class="form-control <?= isset($validationErrors['password']) ? 'is-invalid' : '' ?>" 
                       name="password" 
                       minlength="6" 
                       <?= $isEdit ? '' : 'required' ?>>
                <?php if (isset($validationErrors['password'])): ?>
                    <div class="invalid-feedback"><?= e($validationErrors['password'][0]) ?></div>
                <?php endif; ?>
            </div>

            <div class="col-12 col-md-6 d-flex flex-column justify-content-end">
                <?php if (!$isEdit): ?>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="debe_cambiar_password" value="1" id="debeCambiarCheck" checked>
                        <label class="form-check-label small" for="debeCambiarCheck">
                            Obligar a cambiar la contraseña en el primer inicio de sesión
                        </label>
                    </div>
                <?php else: ?>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="estado" value="1" id="estadoCheck" <?= ($usuario['estado'] == 1) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="estadoCheck">
                            Usuario activo en el sistema
                        </label>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 pt-3 border-top">
            <button type="submit" class="btn btn-primary-custom px-4">
                <i class="bi bi-save me-1"></i> <?= $isEdit ? 'Actualizar Usuario' : 'Guardar Usuario' ?>
            </button>
            <a href="<?= url('/usuarios') ?>" class="btn btn-outline-secondary px-4">
                Cancelar
            </a>
        </div>
    </form>
</div>
