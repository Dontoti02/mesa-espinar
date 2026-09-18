<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Panel de Control') ?> | <?= e(config('institucion_sigla', 'Mesa de Partes')) ?></title>
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
    <!-- Chart.js para gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Dynamic CSS Variables from DB -->
    <?= dynamicCssVariables() ?>
    <style>
        :root {
            --sidebar-width: 260px;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-main, #f8fafc);
            color: var(--text-main, #1e293b);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* SIDEBAR */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg, #102A43);
            color: #f1f5f9;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease-in-out;
            box-shadow: 4px 0 10px rgba(0,0,0,0.05);
        }
        .sidebar-brand {
            padding: 20px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            background: rgba(0,0,0,0.15);
        }
        .sidebar-brand h5 {
            font-weight: 800;
            margin: 0;
            font-size: 1.05rem;
            letter-spacing: -0.5px;
            color: #ffffff;
        }
        .sidebar-nav {
            padding: 16px 12px;
            overflow-y: auto;
            flex-grow: 1;
        }
        .nav-section-title {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            font-weight: 700;
            padding: 12px 14px 6px;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            color: #cbd5e1;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.9rem;
            margin-bottom: 3px;
            transition: all 0.15s ease-in-out;
        }
        .sidebar-link i {
            font-size: 1.15rem;
            width: 22px;
            text-align: center;
        }
        .sidebar-link:hover {
            color: #ffffff;
            background: rgba(255,255,255,0.08);
        }
        .sidebar-link.active {
            color: #ffffff;
            background: var(--primary, #0B4F8A);
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(11, 79, 138, 0.35);
        }

        /* HEADER / NAVBAR */
        .top-navbar {
            height: 64px;
            margin-left: var(--sidebar-width);
            background-color: var(--header-bg, #ffffff);
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            position: sticky;
            top: 0;
            z-index: 990;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            transition: margin-left 0.3s ease-in-out;
        }

        /* MAIN CONTENT */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            padding: 28px 24px 60px;
            min-height: calc(100vh - 64px);
            transition: margin-left 0.3s ease-in-out;
        }

        /* CARDS & COMPONENTS */
        .card-custom {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .btn-primary-custom {
            background-color: var(--primary, #0B4F8A);
            border: none;
            color: #ffffff;
            font-weight: 600;
            border-radius: 8px;
            padding: 8px 16px;
            transition: all 0.2s;
        }
        .btn-primary-custom:hover {
            background-color: var(--primary-hover, #083c6a);
            color: #ffffff;
            transform: translateY(-1px);
        }
        .btn-secondary-custom {
            background-color: var(--secondary, #F59E0B);
            border: none;
            color: #ffffff;
            font-weight: 600;
            border-radius: 8px;
            padding: 8px 16px;
        }

        /* RESPONSIVE TOGGLE */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .top-navbar, .main-wrapper {
                margin-left: 0;
            }
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.4);
                z-index: 999;
            }
            .sidebar-overlay.show {
                display: block;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand d-flex align-items-center gap-2">
            <?php $logoPath = config('logo_principal'); ?>
            <?php if (!empty($logoPath)): ?>
                <img src="<?= uploadUrl($logoPath) ?>" alt="Logo" style="height: 38px; max-width: 48px; object-fit: contain; background: #ffffff; padding: 2px 4px; border-radius: 6px;">
            <?php else: ?>
                <i class="bi bi-folder2-open fs-3 text-warning"></i>
            <?php endif; ?>
            <div>
                <h5 class="mb-0"><?= e(config('institucion_sigla', 'IESTP ESPINAR')) ?></h5>
                <span class="badge bg-secondary" style="font-size: 0.65rem; background-color: var(--secondary, #F59E0B) !important;">Mesa de Partes</span>
            </div>
        </div>

        <div class="sidebar-nav">
            <a href="<?= url('/dashboard') ?>" class="sidebar-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/dashboard') ? 'active' : '' ?>">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>

            <div class="nav-section-title">Expedientes y Trámites</div>

            <?php if (hasPermission('expedientes.ver')): ?>
                <a href="<?= url('/expedientes') ?>" class="sidebar-link <?= (($_SERVER['REQUEST_URI'] ?? '') === '/mesa-espinar/expedientes' || ($_SERVER['REQUEST_URI'] ?? '') === '/expedientes') ? 'active' : '' ?>">
                    <i class="bi bi-folder-fill"></i>
                    <span>Expedientes</span>
                </a>
            <?php endif; ?>

            <?php if (hasPermission('expedientes.crear')): ?>
                <a href="<?= url('/expedientes/crear') ?>" class="sidebar-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/expedientes/crear') ? 'active' : '' ?>">
                    <i class="bi bi-file-earmark-plus-fill"></i>
                    <span>Mesa de Partes</span>
                </a>
            <?php endif; ?>

            <?php if (hasRole(['superadministrador', 'direccion'])): ?>
                <a href="<?= url('/direccion') ?>" class="sidebar-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/direccion') ? 'active' : '' ?>">
                    <i class="bi bi-building-fill-check"></i>
                    <span>Dirección</span>
                </a>
            <?php endif; ?>

            <a href="<?= url('/mi-oficina') ?>" class="sidebar-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/mi-oficina') ? 'active' : '' ?>">
                <i class="bi bi-inbox-fill"></i>
                <span>Mi Oficina</span>
            </a>

            <div class="nav-section-title">Herramientas</div>

            <a href="<?= url('/notificaciones') ?>" class="sidebar-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/notificaciones') ? 'active' : '' ?>">
                <i class="bi bi-bell-fill"></i>
                <span>Notificaciones</span>
            </a>

            <?php if (hasPermission('reportes.ver')): ?>
                <a href="<?= url('/reportes') ?>" class="sidebar-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/reportes') ? 'active' : '' ?>">
                    <i class="bi bi-graph-up-arrow"></i>
                    <span>Reportes</span>
                </a>
            <?php endif; ?>

            <?php if (hasRole(['superadministrador', 'administrador'])): ?>
                <div class="nav-section-title">Administración</div>

                <?php if (hasPermission('usuarios.ver')): ?>
                    <a href="<?= url('/usuarios') ?>" class="sidebar-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/usuarios') ? 'active' : '' ?>">
                        <i class="bi bi-people-fill"></i>
                        <span>Usuarios</span>
                    </a>
                <?php endif; ?>

                <?php if (hasPermission('roles.ver')): ?>
                    <a href="<?= url('/roles') ?>" class="sidebar-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/roles') ? 'active' : '' ?>">
                        <i class="bi bi-shield-lock-fill"></i>
                        <span>Roles y Permisos</span>
                    </a>
                <?php endif; ?>

                <?php if (hasPermission('oficinas.ver')): ?>
                    <a href="<?= url('/oficinas') ?>" class="sidebar-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/oficinas') ? 'active' : '' ?>">
                        <i class="bi bi-buildings-fill"></i>
                        <span>Oficinas</span>
                    </a>
                <?php endif; ?>

                <?php if (hasPermission('tramites.ver')): ?>
                    <a href="<?= url('/tipos-tramite') ?>" class="sidebar-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/tipos-tramite') ? 'active' : '' ?>">
                        <i class="bi bi-card-checklist"></i>
                        <span>Tipos de Trámite</span>
                    </a>
                <?php endif; ?>

                <a href="<?= url('/estados') ?>" class="sidebar-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/estados') ? 'active' : '' ?>">
                    <i class="bi bi-tags-fill"></i>
                    <span>Estados</span>
                </a>

                <?php if (hasPermission('configuracion.ver')): ?>
                    <a href="<?= url('/configuracion') ?>" class="sidebar-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/configuracion') && !str_contains($_SERVER['REQUEST_URI'] ?? '', '/apariencia') ? 'active' : '' ?>">
                        <i class="bi bi-gear-fill"></i>
                        <span>Configuración</span>
                    </a>
                    <a href="<?= url('/configuracion/apariencia') ?>" class="sidebar-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/apariencia') ? 'active' : '' ?>">
                        <i class="bi bi-palette-fill"></i>
                        <span>Apariencia</span>
                    </a>
                <?php endif; ?>

                <?php if (hasPermission('auditoria.ver')): ?>
                    <a href="<?= url('/auditoria') ?>" class="sidebar-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/auditoria') ? 'active' : '' ?>">
                        <i class="bi bi-journal-text"></i>
                        <span>Auditoría</span>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </aside>

    <!-- TOP NAVBAR -->
    <header class="top-navbar">
        <div class="d-flex align-items-center gap-3">
            <button type="button" class="btn btn-light d-lg-none" id="sidebarToggleBtn">
                <i class="bi bi-list fs-5"></i>
            </button>
            <h6 class="mb-0 fw-bold d-none d-sm-block text-secondary">
                <?= e($pageTitle ?? 'Panel de Control') ?>
            </h6>
        </div>

        <div class="d-flex align-items-center gap-3">
            <!-- Enlace portal público -->
            <a href="<?= url('/consulta') ?>" target="_blank" class="btn btn-outline-secondary btn-sm d-none d-md-inline-flex align-items-center gap-1">
                <i class="bi bi-globe"></i> Consulta Pública
            </a>

            <!-- Campana de Notificaciones -->
            <?php
                $unreadCount = 0;
                try {
                    $dbNotif = \App\Core\Database::getConnection();
                    $stmtNotif = $dbNotif->prepare("SELECT COUNT(*) FROM notificaciones WHERE (usuario_id = :u OR oficina_id = :o) AND leido = 0");
                    $stmtNotif->execute([
                        ':u' => \App\Core\Auth::id(),
                        ':o' => \App\Core\Auth::officeId()
                    ]);
                    $unreadCount = (int)$stmtNotif->fetchColumn();
                } catch (\Throwable $e) {}
            ?>
            <a href="<?= url('/notificaciones') ?>" class="btn btn-light position-relative p-2" title="Notificaciones">
                <i class="bi bi-bell fs-5"></i>
                <?php if ($unreadCount > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                        <?= $unreadCount ?>
                    </span>
                <?php endif; ?>
            </a>

            <!-- Dropdown Usuario -->
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2 border-0 bg-transparent" type="button" data-bs-toggle="dropdown">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 36px; height: 36px; background-color: var(--primary, #0B4F8A);">
                        <?= strtoupper(substr(auth()['nombres'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div class="text-start d-none d-sm-block" style="line-height: 1.2;">
                        <span class="fw-semibold d-block text-dark" style="font-size: 0.85rem;"><?= e(auth()['nombres'] ?? '') ?></span>
                        <small class="text-muted" style="font-size: 0.72rem;"><?= e(auth()['rol_nombre'] ?? '') ?></small>
                    </div>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius: 10px; min-width: 220px;">
                    <li class="px-3 py-2 border-bottom">
                        <small class="text-muted d-block">Oficina Asignada</small>
                        <span class="fw-bold small text-dark"><?= e(auth()['oficina_nombre'] ?? 'Sin Oficina') ?></span>
                    </li>
                    <li>
                        <a class="dropdown-menu-item dropdown-item py-2" href="<?= url('/cambiar-password') ?>">
                            <i class="bi bi-shield-lock me-2"></i> Cambiar Contraseña
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="<?= url('/logout') ?>" method="POST">
                            <?= csrf_field() ?>
                            <button type="submit" class="dropdown-item py-2 text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- MAIN WRAPPER -->
    <main class="main-wrapper">
        <!-- Contenedor de Alertas Flash -->
        <div class="container-fluid px-0">
            <?php if (hasFlash('success')): ?>
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert" style="border-radius: 10px;">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <div><?= e(flash('success')) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (hasFlash('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert" style="border-radius: 10px;">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div><?= e(flash('error')) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (hasFlash('warning')): ?>
                <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2" role="alert" style="border-radius: 10px;">
                    <i class="bi bi-exclamation-circle-fill fs-5"></i>
                    <div><?= e(flash('warning')) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (hasFlash('info')): ?>
                <div class="alert alert-info alert-dismissible fade show d-flex align-items-center gap-2" role="alert" style="border-radius: 10px;">
                    <i class="bi bi-info-circle-fill fs-5"></i>
                    <div><?= e(flash('info')) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Contenido dinámico de la vista -->
            <?= $content ?>
        </div>
    </main>

    <!-- Scripts Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.getElementById('sidebarToggleBtn');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (toggleBtn && sidebar && overlay) {
                toggleBtn.addEventListener('click', () => {
                    sidebar.classList.toggle('show');
                    overlay.classList.toggle('show');
                });
                overlay.addEventListener('click', () => {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                });
            }
        });
    </script>
</body>
</html>
