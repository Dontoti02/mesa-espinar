<div class="auth-card">
    <div class="text-center mb-4">
        <?php $logoPath = config('logo_login') ?: config('logo_principal'); ?>
        <?php if (!empty($logoPath)): ?>
            <div class="mb-3">
                <img src="<?= uploadUrl($logoPath) ?>" alt="<?= e(config('institucion_sigla', 'Logo')) ?>" class="auth-logo" style="max-height: 75px; max-width: 180px; object-fit: contain;">
            </div>
        <?php endif; ?>
        <div class="mb-2">
            <span class="badge px-3 py-2 text-white fw-bold" style="background-color: var(--primary, #0B4F8A); border-radius: 20px; font-size: 0.8rem; letter-spacing: 0.5px;">
                INTRANET INSTITUCIONAL
            </span>
        </div>
        <h4 class="fw-bold mb-1" style="color: var(--primary, #0B4F8A);"><?= e(config('institucion_sigla', 'IESTP ESPINAR')) ?></h4>
        <p class="text-muted small mb-0">Sistema de Trámite Documentario y Mesa de Partes</p>
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

    <?php if (hasFlash('success')): ?>
        <div class="alert alert-success d-flex align-items-center mb-3" role="alert" style="border-radius: 10px; font-size: 0.9rem;">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div><?= e(flash('success')) ?></div>
        </div>
    <?php endif; ?>

    <?php if (hasFlash('info')): ?>
        <div class="alert alert-info d-flex align-items-center mb-3" role="alert" style="border-radius: 10px; font-size: 0.9rem;">
            <i class="bi bi-info-circle me-2 fs-5"></i>
            <div><?= e(flash('info')) ?></div>
        </div>
    <?php endif; ?>

    <form action="<?= url('/login') ?>" method="POST" autocomplete="off">
        <?= csrf_field() ?>

        <div class="form-floating mb-3">
            <input type="text" 
                   class="form-control" 
                   id="identificador" 
                   name="identificador" 
                   placeholder="Usuario o Correo" 
                   value="<?= e(old('identificador')) ?>" 
                   required 
                   autofocus>
            <label for="identificador"><i class="bi bi-person me-1"></i> Usuario o Correo</label>
        </div>

        <div class="form-floating mb-4 position-relative">
            <input type="password" 
                   class="form-control" 
                   id="password" 
                   name="password" 
                   placeholder="Contraseña" 
                   required>
            <label for="password"><i class="bi bi-lock me-1"></i> Contraseña</label>
            <button type="button" class="password-toggle-btn" id="togglePasswordBtn" title="Mostrar/Ocultar contraseña" tabindex="-1">
                <i class="bi bi-eye" id="togglePasswordIcon"></i>
            </button>
        </div>

        <button type="submit" class="btn btn-primary-institutional mb-3">
            <i class="bi bi-box-arrow-in-right fs-5"></i>
            <span>Acceder al Sistema</span>
        </button>
    </form>

    <div class="text-center pt-3 border-top mt-3">
        <a href="<?= url('/tramite') ?>" class="text-decoration-none small text-muted d-inline-flex align-items-center gap-1 hover-primary">
            <i class="bi bi-arrow-left"></i> Volver a Mesa de Partes Virtual Pública
        </a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('password');
        const icon = document.getElementById('togglePasswordIcon');

        if (toggleBtn && passwordInput && icon) {
            toggleBtn.addEventListener('click', () => {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                icon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
            });
        }
    });
</script>
