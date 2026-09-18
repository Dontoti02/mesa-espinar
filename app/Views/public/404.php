<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada | <?= e(config('institucion_sigla', 'Mesa de Partes')) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <?= dynamicCssVariables() ?>
    <style>
        body {
            background-color: var(--bg-main, #f8fafc);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: var(--text-main, #1e293b);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .error-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 48px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            max-width: 540px;
            text-align: center;
            border: 1px solid #e2e8f0;
        }
        .error-code {
            font-size: 7rem;
            font-weight: 800;
            line-height: 1;
            color: var(--primary, #0B4F8A);
            letter-spacing: -2px;
        }
        .btn-primary-custom {
            background-color: var(--primary, #0B4F8A);
            color: #ffffff;
            border: none;
            padding: 12px 28px;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        .btn-primary-custom:hover {
            background-color: var(--primary-hover, #083c6a);
            color: #ffffff;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center">
        <div class="error-card">
            <div class="error-code mb-2">404</div>
            <h3 class="fw-bold mb-3">Página no encontrada</h3>
            <p class="text-muted mb-4">El recurso o dirección web a la que intentas acceder no existe, ha cambiado de ruta o se encuentra temporalmente inactivo.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="<?= url('/') ?>" class="btn btn-primary-custom">
                    <i class="bi bi-house-door"></i> Ir al Inicio
                </a>
                <a href="<?= url('/consulta') ?>" class="btn btn-outline-secondary px-4 py-2" style="border-radius: 8px; font-weight: 600;">
                    <i class="bi bi-search"></i> Consultar Trámite
                </a>
            </div>
        </div>
    </div>
</body>
</html>
