<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--primary, #0B4F8A);">Personalización Visual y Apariencia</h4>
        <p class="text-muted small mb-0">Modifica los colores institucionales, logotipos y favicon con vista previa interactiva en tiempo real.</p>
    </div>
    <form action="<?= url('/configuracion/apariencia/restablecer') ?>" method="POST" onsubmit="return confirm('¿Restablecer los colores institucionales a los valores por defecto?');">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-outline-danger d-inline-flex align-items-center gap-2">
            <i class="bi bi-arrow-counterclockwise"></i>
            <span>Restablecer Colores</span>
        </button>
    </form>
</div>

<form action="<?= url('/configuracion/apariencia') ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- Columna Izquierda: Selectores de Colores y Logos -->
        <div class="col-12 col-lg-6">
            <!-- Logos y Favicon (Sección 22) -->
            <div class="card card-custom p-4 mb-4">
                <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom">
                    <i class="bi bi-image me-2"></i> Logotipo y Favicon
                </h6>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Logotipo Institucional Principal</label>
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="p-2 border rounded bg-light text-center" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                            <img src="<?= uploadUrl($config['logo_principal'] ?? '') ?>" id="previewLogo" alt="Logo" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                        </div>
                        <div class="flex-grow-1">
                            <input type="file" class="form-control" name="logo_principal" id="inputLogo" accept=".png,.jpg,.jpeg,.webp,.svg">
                            <small class="text-muted" style="font-size: 0.75rem;">Formatos: PNG, JPG, WEBP, SVG (Recomendado fondo transparente).</small>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="form-label fw-semibold">Favicon del Sistema</label>
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 border rounded bg-light text-center" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                            <img src="<?= uploadUrl($config['favicon'] ?? '') ?>" id="previewFavicon" alt="Favicon" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                        </div>
                        <div class="flex-grow-1">
                            <input type="file" class="form-control form-control-sm" name="favicon" id="inputFavicon" accept=".png,.ico,.svg">
                            <small class="text-muted" style="font-size: 0.75rem;">Icono de pestaña del navegador (16x16 o 32x32 px).</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paleta de Colores (Sección 22 y 57) -->
            <div class="card card-custom p-4 mb-4">
                <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom">
                    <i class="bi bi-palette me-2"></i> Paleta de Colores Institucionales
                </h6>

                <!-- 1. Color Principal -->
                <div class="mb-3">
                    <label class="form-label fw-semibold d-flex justify-content-between">
                        <span>Color Principal (Botones, Títulos, Marca)</span>
                        <code id="val_color_primario"><?= e($config['color_primario'] ?? '#0B4F8A') ?></code>
                    </label>
                    <div class="input-group">
                        <input type="color" class="form-control form-control-color" id="picker_color_primario" value="<?= e($config['color_primario'] ?? '#0B4F8A') ?>" style="max-width: 60px;">
                        <input type="text" class="form-control" name="color_primario" id="color_primario" value="<?= e($config['color_primario'] ?? '#0B4F8A') ?>" required>
                    </div>
                </div>

                <!-- 2. Color Secundario -->
                <div class="mb-3">
                    <label class="form-label fw-semibold d-flex justify-content-between">
                        <span>Color Secundario (Badges de apoyo, Avisos)</span>
                        <code id="val_color_secundario"><?= e($config['color_secundario'] ?? '#F59E0B') ?></code>
                    </label>
                    <div class="input-group">
                        <input type="color" class="form-control form-control-color" id="picker_color_secundario" value="<?= e($config['color_secundario'] ?? '#F59E0B') ?>" style="max-width: 60px;">
                        <input type="text" class="form-control" name="color_secundario" id="color_secundario" value="<?= e($config['color_secundario'] ?? '#F59E0B') ?>" required>
                    </div>
                </div>

                <!-- 3. Color Acento -->
                <div class="mb-3">
                    <label class="form-label fw-semibold d-flex justify-content-between">
                        <span>Color de Acento (Urgentes, Alertas)</span>
                        <code id="val_color_acento"><?= e($config['color_acento'] ?? '#DC2626') ?></code>
                    </label>
                    <div class="input-group">
                        <input type="color" class="form-control form-control-color" id="picker_color_acento" value="<?= e($config['color_acento'] ?? '#DC2626') ?>" style="max-width: 60px;">
                        <input type="text" class="form-control" name="color_acento" id="color_acento" value="<?= e($config['color_acento'] ?? '#DC2626') ?>" required>
                    </div>
                </div>

                <!-- 4. Color Sidebar -->
                <div class="mb-3">
                    <label class="form-label fw-semibold d-flex justify-content-between">
                        <span>Color Fondo del Sidebar Lateral</span>
                        <code id="val_color_sidebar"><?= e($config['color_sidebar'] ?? '#102A43') ?></code>
                    </label>
                    <div class="input-group">
                        <input type="color" class="form-control form-control-color" id="picker_color_sidebar" value="<?= e($config['color_sidebar'] ?? '#102A43') ?>" style="max-width: 60px;">
                        <input type="text" class="form-control" name="color_sidebar" id="color_sidebar" value="<?= e($config['color_sidebar'] ?? '#102A43') ?>" required>
                    </div>
                </div>

                <!-- 5. Color Encabezado -->
                <div class="mb-3">
                    <label class="form-label fw-semibold d-flex justify-content-between">
                        <span>Color de Fondo del Encabezado (Navbar)</span>
                        <code id="val_color_encabezado"><?= e($config['color_encabezado'] ?? '#FFFFFF') ?></code>
                    </label>
                    <div class="input-group">
                        <input type="color" class="form-control form-control-color" id="picker_color_encabezado" value="<?= e($config['color_encabezado'] ?? '#FFFFFF') ?>" style="max-width: 60px;">
                        <input type="text" class="form-control" name="color_encabezado" id="color_encabezado" value="<?= e($config['color_encabezado'] ?? '#FFFFFF') ?>" required>
                    </div>
                </div>

                <div class="pt-3 border-top">
                    <button type="submit" class="btn btn-primary-custom w-100 py-2">
                        <i class="bi bi-check2-circle me-1 fs-5"></i> Guardar Cambios Visuales
                    </button>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Vista Previa en Vivo (Sección 57) -->
        <div class="col-12 col-lg-6">
            <div class="card card-custom p-4 sticky-top" style="top: 80px; z-index: 10;">
                <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-eye-fill me-2"></i> Vista Previa en Tiempo Real</span>
                    <span class="badge bg-success-subtle text-success small">Interactivo</span>
                </h6>

                <!-- Preview 1: Header / Navbar -->
                <div class="mb-3">
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">1. ENCABEZADO SUPERIOR (NAVBAR)</small>
                    <div class="p-3 rounded border mt-1 d-flex justify-content-between align-items-center" id="previewHeaderBox" style="background-color: <?= e($config['color_encabezado'] ?? '#FFFFFF') ?>;">
                        <span class="fw-bold text-primary" id="previewNavBrand">IESTP ESPINAR</span>
                        <div class="d-flex gap-2">
                            <span class="badge bg-light text-dark border">Consulta</span>
                            <span class="badge rounded-circle bg-danger" style="width: 18px; height: 18px; display: inline-block;"></span>
                        </div>
                    </div>
                </div>

                <!-- Preview 2: Sidebar Miniatura -->
                <div class="mb-3">
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">2. BARRA LATERAL (SIDEBAR)</small>
                    <div class="p-3 rounded mt-1" id="previewSidebarBox" style="background-color: <?= e($config['color_sidebar'] ?? '#102A43') ?>; color: #ffffff;">
                        <div class="fw-bold small mb-2 text-warning d-flex align-items-center gap-2">
                            <i class="bi bi-folder-fill"></i> Panel de Trámites
                        </div>
                        <div class="p-2 rounded text-white small fw-semibold mb-1" id="previewActiveLink" style="background-color: <?= e($config['color_primario'] ?? '#0B4F8A') ?>;">
                            <i class="bi bi-grid-fill me-2"></i> Dashboard (Activo)
                        </div>
                        <div class="p-2 rounded text-light small opacity-75">
                            <i class="bi bi-inbox me-2"></i> Expedientes
                        </div>
                    </div>
                </div>

                <!-- Preview 3: Botones y Badges -->
                <div class="mb-3">
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">3. BOTONES Y BADGES</small>
                    <div class="p-3 bg-light rounded border mt-1 d-flex flex-wrap gap-2 align-items-center">
                        <button type="button" class="btn text-white fw-bold btn-sm px-3" id="previewBtnPrimary" style="background-color: <?= e($config['color_primario'] ?? '#0B4F8A') ?>;">
                            Botón Principal
                        </button>
                        <button type="button" class="btn text-white fw-bold btn-sm px-3" id="previewBtnSecondary" style="background-color: <?= e($config['color_secundario'] ?? '#F59E0B') ?>;">
                            Botón Secundario
                        </button>
                        <span class="badge text-white px-2 py-1" id="previewBadgeAccent" style="background-color: <?= e($config['color_acento'] ?? '#DC2626') ?>;">
                            Urgente
                        </span>
                    </div>
                </div>

                <!-- Preview 4: Tarjeta y Login Preview -->
                <div>
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">4. PANTALLA DE ACCESO (LOGIN PREVIEW)</small>
                    <div class="p-3 rounded border mt-1 text-center bg-white shadow-sm">
                        <h6 class="fw-bold mb-1" id="previewLoginTitle" style="color: <?= e($config['color_primario'] ?? '#0B4F8A') ?>;">Mesa de Partes</h6>
                        <div class="small text-muted mb-2">Ingreso al sistema institucional</div>
                        <div class="p-2 rounded text-white small fw-bold" id="previewLoginBtn" style="background-color: <?= e($config['color_primario'] ?? '#0B4F8A') ?>;">
                            Iniciar Sesión
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Vinculación bidireccional entre Color Pickers y campos de texto con actualización inmediata del DOM preview
        const inputs = [
            { id: 'color_primario', picker: 'picker_color_primario', label: 'val_color_primario' },
            { id: 'color_secundario', picker: 'picker_color_secundario', label: 'val_color_secundario' },
            { id: 'color_acento', picker: 'picker_color_acento', label: 'val_color_acento' },
            { id: 'color_sidebar', picker: 'picker_color_sidebar', label: 'val_color_sidebar' },
            { id: 'color_encabezado', picker: 'picker_color_encabezado', label: 'val_color_encabezado' }
        ];

        function syncPreview() {
            const primary = document.getElementById('color_primario').value;
            const secondary = document.getElementById('color_secundario').value;
            const accent = document.getElementById('color_acento').value;
            const sidebar = document.getElementById('color_sidebar').value;
            const header = document.getElementById('color_encabezado').value;

            // Actualizar elementos de preview
            document.getElementById('previewHeaderBox').style.backgroundColor = header;
            document.getElementById('previewNavBrand').style.color = primary;
            document.getElementById('previewSidebarBox').style.backgroundColor = sidebar;
            document.getElementById('previewActiveLink').style.backgroundColor = primary;
            document.getElementById('previewBtnPrimary').style.backgroundColor = primary;
            document.getElementById('previewBtnSecondary').style.backgroundColor = secondary;
            document.getElementById('previewBadgeAccent').style.backgroundColor = accent;
            document.getElementById('previewLoginTitle').style.color = primary;
            document.getElementById('previewLoginBtn').style.backgroundColor = primary;
        }

        inputs.forEach(item => {
            const txt = document.getElementById(item.id);
            const pck = document.getElementById(item.picker);
            const lbl = document.getElementById(item.label);

            pck?.addEventListener('input', (e) => {
                txt.value = e.target.value.toUpperCase();
                lbl.textContent = txt.value;
                syncPreview();
            });

            txt?.addEventListener('input', (e) => {
                if (/^#[0-9A-F]{6}$/i.test(e.target.value)) {
                    pck.value = e.target.value;
                    lbl.textContent = e.target.value.toUpperCase();
                    syncPreview();
                }
            });
        });

        // Preview de imagen de Logo
        const inputLogo = document.getElementById('inputLogo');
        const previewLogo = document.getElementById('previewLogo');
        inputLogo?.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                previewLogo.src = URL.createObjectURL(file);
            }
        });

        // Preview de Favicon
        const inputFav = document.getElementById('inputFavicon');
        const previewFav = document.getElementById('previewFavicon');
        inputFav?.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                previewFav.src = URL.createObjectURL(file);
            }
        });
    });
</script>
