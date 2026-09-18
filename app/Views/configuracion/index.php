<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--primary, #0B4F8A);">Configuración Institucional</h4>
        <p class="text-muted small mb-0">Parámetros generales de identidad, correlativos y servidor de correo SMTP.</p>
    </div>
    <a href="<?= url('/configuracion/apariencia') ?>" class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
        <i class="bi bi-palette-fill"></i>
        <span>Personalizar Apariencia y Colores</span>
    </a>
</div>

<div class="row g-4">
    <!-- Formulario de Identidad Institucional -->
    <div class="col-12 col-lg-7">
        <div class="card card-custom p-4">
            <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom">
                <i class="bi bi-building-gear me-2"></i> Identidad y Datos Oficiales
            </h6>

            <form action="<?= url('/configuracion/general') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nombre Oficial de la Institución <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="institucion_nombre" value="<?= e($config['institucion_nombre'] ?? '') ?>" required>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold">Sigla o Nombre Corto <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="institucion_sigla" value="<?= e($config['institucion_sigla'] ?? '') ?>" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">RUC Institucional <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="institucion_ruc" value="<?= e($config['institucion_ruc'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Dirección Sede Central</label>
                    <input type="text" class="form-control" name="institucion_direccion" value="<?= e($config['institucion_direccion'] ?? '') ?>">
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold">Teléfono de Contacto</label>
                        <input type="text" class="form-control" name="institucion_telefono" value="<?= e($config['institucion_telefono'] ?? '') ?>">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Correo de Atención</label>
                        <input type="email" class="form-control" name="institucion_correo" value="<?= e($config['institucion_correo'] ?? '') ?>">
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold">Portal Web</label>
                        <input type="url" class="form-control" name="institucion_web" value="<?= e($config['institucion_web'] ?? '') ?>">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Horario de Atención</label>
                        <input type="text" class="form-control" name="institucion_horario" value="<?= e($config['institucion_horario'] ?? '') ?>">
                    </div>
                </div>

                <h6 class="fw-bold text-primary mt-4 mb-3 pb-2 border-bottom">
                    <i class="bi bi-123 me-2"></i> Numeración de Expedientes (Sección 8)
                </h6>

                <div class="row g-2 mb-3">
                    <div class="col-4">
                        <label class="form-label fw-semibold">Prefijo</label>
                        <input type="text" class="form-control" name="expedientes_prefijo" value="<?= e($config['expedientes_prefijo'] ?? 'EXP') ?>" required>
                    </div>
                    <div class="col-4">
                        <label class="form-label fw-semibold">Dígitos</label>
                        <input type="number" class="form-control" name="expedientes_digitos" value="<?= e($config['expedientes_digitos'] ?? 6) ?>" min="4" max="10" required>
                    </div>
                    <div class="col-4 d-flex align-items-end">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="expedientes_reinicio_anual" value="1" id="reinicioCheck" <?= (($config['expedientes_reinicio_anual'] ?? '1') == '1') ? 'checked' : '' ?>>
                            <label class="form-check-label small fw-semibold" for="reinicioCheck">
                                Reinicio Anual
                            </label>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-top">
                    <button type="submit" class="btn btn-primary-custom px-4">
                        <i class="bi bi-save me-1"></i> Guardar Parámetros
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Configuración del Servidor SMTP (Sección 21) -->
    <div class="col-12 col-lg-5">
        <div class="card card-custom p-4 mb-4">
            <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom">
                <i class="bi bi-envelope-at me-2"></i> Servidor de Correo SMTP
            </h6>

            <form action="<?= url('/configuracion/smtp') ?>" method="POST" autocomplete="off">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Host SMTP</label>
                    <input type="text" class="form-control" name="host" value="<?= e($smtp['host'] ?? 'smtp.gmail.com') ?>" placeholder="smtp.ejemplo.com" required>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold">Puerto</label>
                        <input type="number" class="form-control" name="puerto" value="<?= e($smtp['puerto'] ?? 587) ?>" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Cifrado</label>
                        <select name="cifrado" class="form-select">
                            <option value="tls" <?= (($smtp['cifrado'] ?? 'tls') === 'tls') ? 'selected' : '' ?>>TLS (587)</option>
                            <option value="ssl" <?= (($smtp['cifrado'] ?? '') === 'ssl') ? 'selected' : '' ?>>SSL (465)</option>
                            <option value="none" <?= (($smtp['cifrado'] ?? '') === 'none') ? 'selected' : '' ?>>Sin cifrado</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Usuario SMTP</label>
                    <input type="text" class="form-control" name="usuario" value="<?= e($smtp['usuario'] ?? '') ?>" placeholder="tu_correo@gmail.com">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Contraseña SMTP</label>
                    <input type="password" class="form-control" name="password" placeholder="Dejar en blanco para conservar actual">
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold">Correo Remitente</label>
                        <input type="email" class="form-control" name="remitente_correo" value="<?= e($smtp['remitente_correo'] ?? '') ?>" placeholder="noreply@institucion.edu.pe" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Nombre Remitente</label>
                        <input type="text" class="form-control" name="remitente_nombre" value="<?= e($smtp['remitente_nombre'] ?? '') ?>" placeholder="Mesa de Partes Virtual" required>
                    </div>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="activo" value="1" id="smtpActivoCheck" <?= (($smtp['activo'] ?? 0) == 1) ? 'checked' : '' ?>>
                    <label class="form-check-label fw-semibold" for="smtpActivoCheck">
                        Habilitar envío automático de notificaciones por correo
                    </label>
                </div>

                <button type="submit" class="btn btn-primary-custom w-100">
                    <i class="bi bi-save me-1"></i> Guardar Configuración SMTP
                </button>
            </form>
        </div>

        <!-- Prueba de Envío SMTP -->
        <div class="card card-custom p-3 bg-light">
            <h6 class="fw-bold small mb-2"><i class="bi bi-send-check me-1"></i> Probar Envío SMTP</h6>
            <form action="<?= url('/configuracion/smtp/probar') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="input-group input-group-sm">
                    <input type="email" class="form-control" name="correo_prueba" placeholder="correo_destino@test.com" required>
                    <button class="btn btn-outline-primary" type="submit">Enviar Test</button>
                </div>
            </form>
        </div>
    </div>
</div>
