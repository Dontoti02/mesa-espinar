<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Autenticación') ?> | <?= e(config('institucion_sigla', 'Mesa de Partes')) ?></title>
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
            background: linear-gradient(135deg, var(--bg-main, #f8fafc) 0%, #e2e8f0 100%);
            color: var(--text-main, #1e293b);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .auth-container {
            width: 100%;
            max-width: 440px;
        }
        .auth-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 40px 32px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
        .auth-logo {
            max-height: 64px;
            object-fit: contain;
        }
        .btn-primary-institutional {
            background-color: var(--primary, #0B4F8A);
            border: none;
            color: #ffffff;
            font-weight: 600;
            padding: 12px 20px;
            border-radius: 10px;
            transition: all 0.2s ease-in-out;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
        }
        .btn-primary-institutional:hover {
            background-color: var(--primary-hover, #083c6a);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(11, 79, 138, 0.25);
        }
        .form-control:focus {
            border-color: var(--primary, #0B4F8A);
            box-shadow: 0 0 0 0.25rem var(--primary-subtle, rgba(11, 79, 138, 0.15));
        }
        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label {
            color: var(--primary, #0B4F8A);
        }
        .password-toggle-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #64748b;
            cursor: pointer;
            z-index: 10;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <?= $content ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
