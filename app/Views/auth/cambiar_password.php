<div class="auth-card">
    <div class="text-center mb-4">
        <div class="mb-3">
            <span class="badge px-3 py-2 text-white fw-bold" style="background-color: var(--accent, #DC2626); border-radius: 20px; font-size: 0.8rem;">
                SEGURIDAD OBLIGATORIA
            </span>
        </div>
        <h4 class="fw-bold mb-1" style="color: var(--primary, #0B4F8A);">Actualizar Contraseña</h4>
        <p class="text-muted small mb-0">Por políticas institucionales, debes cambiar tu clave temporal por una contraseña segura y personal.</p>
    </div>

    <!-- Alertas Flash -->
    <?php if (hasFlash('error')): ?>
        <div class="alert alert-danger d-flex align-items-center mb-3" role="alert" style="border-radius: 10px; font-size: 0.9rem;">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div><?= e(flash('error')) ?></div>
        </div>
    <?php endif; ?>

    <?php if (hasFlash('warning')): ?>
        <div class="alert alert-warning d-flex align-items-center mb-3" role="alert" style="border-radius: 10px; font-size: 0.9rem;">
            <i class="bi bi-exclamation-circle me-2 fs-5"></i>
            <div><?= e(flash('warning')) ?></div>
        </div>
    <?php endif; ?>

    <form action="<?= url('/cambiar-password') ?>" method="POST" autocomplete="off">
        <?= csrf_field() ?>

        <div class="form-floating mb-3">
            <input type="password" 
                   class="form-control" 
                   id="password_actual" 
                   name="password_actual" 
                   placeholder="Contraseña Actual" 
                   required>
            <label for="password_actual"><i class="bi bi-key me-1"></i> Contraseña Actual (Temporal)</label>
        </div>

        <div class="form-floating mb-3">
            <input type="password" 
                   class="form-control" 
                   id="password_nuevo" 
                   name="password_nuevo" 
                   placeholder="Nueva Contraseña" 
                   minlength="8" 
                   required>
            <label for="password_nuevo"><i class="bi bi-lock me-1"></i> Nueva Contraseña (mín. 8 caracteres)</label>
        </div>

        <div class="form-floating mb-4">
            <input type="password" 
                   class="form-control" 
                   id="password_confirm" 
                   name="password_confirm" 
                   placeholder="Confirmar Contraseña" 
                   minlength="8" 
                   required>
            <label for="password_confirm"><i class="bi bi-shield-check me-1"></i> Confirmar Nueva Contraseña</label>
        </div>

        <button type="submit" class="btn btn-primary-institutional mb-3">
            <i class="bi bi-check2-circle fs-5"></i>
            <span>Guardar Nueva Contraseña</span>
        </button>
    </form>
</div>
