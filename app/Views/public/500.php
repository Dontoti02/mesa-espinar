<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Error del Servidor | <?= e(config('institucion_sigla', 'Mesa de Partes')) ?></title>
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
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            max-width: 540px;
            text-align: center;
            border: 1px solid #e2e8f0;
        }
        .error-code {
            font-size: 6rem;
            font-weight: 800;
            line-height: 1;
            color: var(--accent, #dc2626);
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center">
        <div class="error-card">
            <div class="error-code mb-2">500</div>
            <h3 class="fw-bold mb-3">Error Interno del Sistema</h3>
            <p class="text-muted mb-4">Ha ocurrido un incidente inesperado al procesar tu solicitud. El equipo técnico ha sido notificado para solucionarlo a la brevedad.</p>
            <a href="<?= url('/') ?>" class="btn btn-primary px-4 py-2" style="background-color: var(--primary, #0B4F8A); border:none; border-radius: 8px; font-weight:600;">
                <i class="bi bi-arrow-clockwise"></i> Reintentar o volver al inicio
            </a>
        </div>
    </div>
</body>
</html>
