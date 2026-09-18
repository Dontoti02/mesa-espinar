<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargo de Recepción - <?= e($expediente['numero_expediente']) ?></title>
    <?php $favPath = config('favicon'); ?>
    <?php if (!empty($favPath)): ?>
        <link rel="icon" type="image/png" href="<?= uploadUrl($favPath) ?>">
        <link rel="shortcut icon" href="<?= uploadUrl($favPath) ?>">
    <?php endif; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            padding: 30px 15px;
        }
        .cargo-box {
            max-width: 780px;
            margin: 0 auto;
            background: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            border: 2px dashed #cbd5e1;
        }
        .cargo-header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }
        .cargo-title {
            font-size: 1.4rem;
            font-weight: 800;
            color: #0b4f8a;
            letter-spacing: -0.5px;
        }
        .info-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            font-weight: 700;
        }
        .info-value {
            font-size: 1rem;
            font-weight: 600;
            color: #0f172a;
        }
        .qr-box {
            border: 1px solid #e2e8f0;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            background: #f8fafc;
        }
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .cargo-box {
                box-shadow: none;
                border: 1px solid #94a3b8;
                padding: 25px;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="text-center mb-3 no-print">
        <button onclick="window.print()" class="btn btn-primary px-4 py-2 fw-semibold">
            <i class="bi bi-printer-fill me-1"></i> Imprimir Cargo en PDF
        </button>
        <button onclick="window.close()" class="btn btn-outline-secondary px-3 py-2 ms-2">
            Cerrar Ventana
        </button>
    </div>

    <div class="cargo-box">
        <!-- Encabezado Institucional -->
        <div class="cargo-header d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <?php $logoPath = config('logo_principal'); ?>
                <?php if (!empty($logoPath)): ?>
                    <img src="<?= uploadUrl($logoPath) ?>" alt="Logo" style="max-height: 55px; width: auto; object-fit: contain;">
                <?php endif; ?>
                <div>
                    <h5 class="fw-bold mb-1" style="color: #0b4f8a;"><?= e(config('institucion_nombre', 'IESTP ESPINAR')) ?></h5>
                    <small class="text-muted d-block fw-semibold">MESA DE PARTES VIRTUAL Y TRÁMITE DOCUMENTARIO</small>
                    <small class="text-muted d-block">RUC: <?= e(config('institucion_ruc', '20490000001')) ?> | <?= e(config('institucion_direccion', 'Espinar, Cusco')) ?></small>
                </div>
            </div>
            <div class="text-end">
                <span class="badge bg-dark px-3 py-2 fs-6">
                    CARGO DE RECEPCIÓN
                </span>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-8">
                <div class="p-3 bg-light rounded border mb-3">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="info-label">NÚMERO DE EXPEDIENTE</div>
                            <div class="info-value text-primary fs-5"><?= e($expediente['numero_expediente']) ?></div>
                        </div>
                        <div class="col-6">
                            <div class="info-label">CÓDIGO DE SEGUIMIENTO</div>
                            <div class="info-value font-monospace"><?= e($expediente['codigo_seguimiento']) ?></div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <div class="info-label">FECHA Y HORA DE REGISTRO</div>
                        <div class="info-value"><?= formatDateTime($expediente['fecha_ingreso']) ?></div>
                    </div>
                    <div class="col-6">
                        <div class="info-label">MODALIDAD DE INGRESO</div>
                        <div class="info-value"><?= (int)$expediente['es_virtual'] === 1 ? 'Mesa Virtual (Online)' : 'Ventanilla Presencial' ?></div>
                    </div>
                    <div class="col-12">
                        <div class="info-label">SOLICITANTE</div>
                        <div class="info-value">
                            <?= e($expediente['tipo_persona'] === 'JURIDICA' ? $expediente['razon_social'] : $expediente['nombres'] . ' ' . $expediente['apellidos']) ?>
                            (<?= e($expediente['tipo_documento']) ?>: <?= e($expediente['numero_documento']) ?>)
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="info-label">CORREO DE CONTACTO</div>
                        <div class="info-value"><?= e($expediente['correo']) ?></div>
                    </div>
                    <div class="col-6">
                        <div class="info-label">TELÉFONO</div>
                        <div class="info-value"><?= e($expediente['telefono'] ?: 'No registrado') ?></div>
                    </div>
                </div>
            </div>

            <!-- Código QR para consulta -->
            <div class="col-4 d-flex flex-column align-items-center justify-content-center">
                <div class="qr-box w-100">
                    <?php
                        $consultaUrl = url('/consulta?codigo=' . urlencode($expediente['codigo_seguimiento']));
                        $qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($consultaUrl);
                    ?>
                    <img src="<?= $qrApiUrl ?>" alt="QR Seguimiento" class="img-fluid mb-2" style="max-height: 140px;">
                    <div class="small fw-bold text-muted">Escanear para Consulta</div>
                    <small class="text-muted d-block" style="font-size: 0.7rem;">Seguimiento en tiempo real</small>
                </div>
            </div>
        </div>

        <!-- Datos del Asunto -->
        <div class="border rounded p-3 mb-4">
            <div class="row g-2 mb-2">
                <div class="col-8">
                    <div class="info-label">TIPO DE TRÁMITE</div>
                    <div class="info-value"><?= e($expediente['tipo_tramite_nombre']) ?></div>
                </div>
                <div class="col-4">
                    <div class="info-label">Nº DE FOLIOS</div>
                    <div class="info-value"><?= (int)$expediente['folios'] ?> folios</div>
                </div>
            </div>
            <div class="mb-2">
                <div class="info-label">ASUNTO</div>
                <div class="info-value"><?= e($expediente['asunto']) ?></div>
            </div>
            <?php if (!empty($expediente['descripcion'])): ?>
                <div>
                    <div class="info-label">DESCRIPCIÓN / DETALLE</div>
                    <small class="text-muted"><?= nl2br(e($expediente['descripcion'])) ?></small>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pie de Cargo -->
        <div class="p-3 bg-light rounded text-center small text-muted border">
            <i class="bi bi-shield-check text-success me-1"></i>
            Conserve este comprobante. Para consultar el estado de su trámite ingrese a 
            <strong><?= e(url('/consulta')) ?></strong> e introduzca su número de expediente o código de seguimiento.
        </div>
    </div>

</body>
</html>
