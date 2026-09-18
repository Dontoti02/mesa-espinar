<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Mesa de Partes Virtual') ?> | <?= e(config('institucion_sigla', 'IESTP ESPINAR')) ?></title>
    <?php $favPath = config('favicon'); ?>
    <?php if (!empty($favPath)): ?>
        <link rel="icon" type="image/png" href="<?= uploadUrl($favPath) ?>">
        <link rel="shortcut icon" href="<?= uploadUrl($favPath) ?>">
        <link rel="apple-touch-icon" href="<?= uploadUrl($favPath) ?>">
    <?php endif; ?>
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Dynamic CSS Variables from DB -->
    <?= dynamicCssVariables() ?>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-main, #f8fafc);
            color: var(--text-main, #1e293b);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }
        .public-navbar {
            background-color: var(--header-bg, #ffffff);
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            padding: 12px 0;
        }
        .navbar-brand-title {
            font-weight: 800;
            color: var(--primary, #0B4F8A);
            font-size: 1.25rem;
            letter-spacing: -0.5px;
            margin: 0;
        }
        .nav-link-custom {
            font-weight: 600;
            color: #475569;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .nav-link-custom:hover {
            color: var(--primary, #0B4F8A);
            background: rgba(11, 79, 138, 0.05);
        }
        .nav-link-custom.active {
            color: var(--primary, #0B4F8A);
            background: var(--primary-subtle, rgba(11, 79, 138, 0.1));
        }
        .btn-primary-portal {
            background-color: var(--primary, #0B4F8A);
            color: #ffffff;
            border: none;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.2s;
        }
        .btn-primary-portal:hover {
            background-color: var(--primary-hover, #083c6a);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(11, 79, 138, 0.25);
        }
        .btn-secondary-portal {
            background-color: var(--secondary, #F59E0B);
            color: #ffffff;
            border: none;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px 20px;
        }
        .card-portal {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .public-footer {
            background: #0f172a;
            color: #94a3b8;
            padding: 40px 0 20px;
            margin-top: auto;
        }
    </style>
</head>
<body>

    <!-- NAVBAR PÚBLICA -->
    <header class="public-navbar sticky-top">
        <div class="container d-flex justify-content-between align-items-center flex-wrap gap-2">
            <?php $logoPath = config('logo_principal'); ?>
            <a href="<?= url('/') ?>" class="text-decoration-none d-flex align-items-center gap-2">
                <?php if (!empty($logoPath)): ?>
                    <img src="<?= uploadUrl($logoPath) ?>" alt="<?= e(config('institucion_sigla', 'Logo')) ?>" style="height: 48px; max-width: 150px; object-fit: contain;">
                <?php else: ?>
                    <i class="bi bi-folder-symlink-fill fs-2" style="color: var(--primary, #0B4F8A);"></i>
                <?php endif; ?>
                <div>
                    <h5 class="navbar-brand-title"><?= e(config('institucion_sigla', 'IESTP ESPINAR')) ?></h5>
                    <small class="text-muted" style="font-size: 0.75rem;">Mesa de Partes Virtual Institucional</small>
                </div>
            </a>

            <nav class="d-flex align-items-center gap-2">
                <a href="<?= url('/') ?>" class="nav-link-custom <?= (($_SERVER['REQUEST_URI'] ?? '') === '/mesa-espinar/' || ($_SERVER['REQUEST_URI'] ?? '') === '/' || ($_SERVER['REQUEST_URI'] ?? '') === '/mesa-espinar') ? 'active' : '' ?>">
                    <i class="bi bi-house me-1"></i> Inicio
                </a>
                <a href="<?= url('/tramite') ?>" class="nav-link-custom <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/tramite') ? 'active' : '' ?>">
                    <i class="bi bi-file-earmark-plus me-1"></i> Presentar Trámite
                </a>
                <a href="<?= url('/consulta') ?>" class="nav-link-custom <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/consulta') ? 'active' : '' ?>">
                    <i class="bi bi-search me-1"></i> Consultar Estado
                </a>
                <a href="<?= url('/login') ?>" class="btn btn-outline-primary btn-sm ms-2" style="border-radius: 8px; font-weight: 600;">
                    <i class="bi bi-person-lock me-1"></i> Intranet
                </a>
            </nav>
        </div>
    </header>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="flex-grow-1 py-4">
        <div class="container">
            <?php if (hasFlash('success')): ?>
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert" style="border-radius: 10px;">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <div><?= e(flash('success')) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (hasFlash('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert" style="border-radius: 10px;">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div><?= e(flash('error')) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (hasFlash('warning')): ?>
                <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert" style="border-radius: 10px;">
                    <i class="bi bi-exclamation-circle-fill fs-5"></i>
                    <div><?= e(flash('warning')) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?= $content ?>
        </div>
    </main>

    <!-- FOOTER PÚBLICO -->
    <footer class="public-footer">
        <div class="container">
            <div class="row g-4 pb-4 border-bottom border-secondary border-opacity-25">
                <div class="col-12 col-md-5">
                    <?php if (!empty($logoPath)): ?>
                        <div class="mb-3">
                            <img src="<?= uploadUrl($logoPath) ?>" alt="<?= e(config('institucion_sigla', 'Logo')) ?>" style="max-height: 48px; width: auto; background: rgba(255,255,255,0.95); padding: 4px 10px; border-radius: 8px;">
                        </div>
                    <?php endif; ?>
                    <h6 class="text-white fw-bold mb-2"><?= e(config('institucion_nombre', 'IESTP ESPINAR')) ?></h6>
                    <p class="small mb-3"><?= e(config('institucion_direccion', 'Espinar, Cusco, Perú')) ?></p>
                    <div class="small">
                        <div class="mb-1"><i class="bi bi-clock me-2 text-warning"></i>Horario de Atención: <?= e(config('institucion_horario', 'Lunes a Viernes 08:00 AM - 04:30 PM')) ?></div>
                        <div><i class="bi bi-envelope me-2 text-warning"></i><?= e(config('institucion_correo', 'mesadepartes@iestpespinar.edu.pe')) ?></div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <h6 class="text-white fw-bold mb-2">Servicios en Línea</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-1"><a href="<?= url('/tramite') ?>" class="text-decoration-none text-muted hover-white">Presentación de Documentos</a></li>
                        <li class="mb-1"><a href="<?= url('/consulta') ?>" class="text-decoration-none text-muted hover-white">Seguimiento de Expedientes</a></li>
                        <li class="mb-1"><a href="<?= url('/login') ?>" class="text-decoration-none text-muted hover-white">Acceso para Funcionarios</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-4 text-md-end">
                    <div class="badge px-3 py-2 text-white mb-2" style="background-color: var(--primary, #0B4F8A);">
                        Trámite Documentario Virtual
                    </div>
                    <p class="small mb-0">Sistema oficial seguro con verificación por código de seguimiento.</p>
                </div>
            </div>
            <div class="pt-3 text-center small text-muted">
                &copy; <?= date('Y') ?> <?= e(config('institucion_sigla', 'IESTP ESPINAR')) ?>. Todos los derechos reservados.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
