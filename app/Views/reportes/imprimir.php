<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Oficial de Expedientes - <?= e(config('institucion_sigla', 'IESTP ESPINAR')) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', sans-serif; padding: 20px; color: #1e293b; }
        .table-custom th { background-color: #f1f5f9; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="text-center mb-3 no-print">
        <button onclick="window.print()" class="btn btn-primary px-4 py-2 fw-bold">Imprimir Reporte</button>
        <button onclick="window.close()" class="btn btn-outline-secondary px-3 py-2 ms-2">Cerrar</button>
    </div>

    <div class="border-bottom pb-3 mb-3 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-0" style="color: #0b4f8a;"><?= e(config('institucion_nombre', 'IESTP ESPINAR')) ?></h4>
            <small class="text-muted">REPORTE CONSOLIDADO DE TRÁMITES Y EXPEDIENTES</small>
        </div>
        <div class="text-end small text-muted">
            <div>Fecha de Emisión: <?= date('d/m/Y H:i') ?></div>
            <div>Usuario: <?= e(auth()['nombres'] ?? 'Sistema') ?></div>
        </div>
    </div>

    <div class="row g-2 mb-3 small bg-light p-2 rounded">
        <div class="col-4">Periodo: <strong><?= e($filtros['fecha_desde'] ?: 'Inicio') ?> al <?= e($filtros['fecha_hasta'] ?: 'Hoy') ?></strong></div>
        <div class="col-4">Total Registros: <strong><?= $resumen['total'] ?></strong></div>
        <div class="col-4">Finalizados: <strong><?= $resumen['finalizados'] ?></strong> | Pendientes: <strong><?= $resumen['pendientes'] ?></strong></div>
    </div>

    <table class="table table-bordered table-sm table-custom align-middle" style="font-size: 0.8rem;">
        <thead>
            <tr>
                <th>Nº Expediente</th>
                <th>Fecha Ingreso</th>
                <th>Solicitante</th>
                <th>Tipo Trámite</th>
                <th>Asunto</th>
                <th>Oficina Actual</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($expedientes as $exp): ?>
                <tr>
                    <td class="fw-bold"><?= e($exp['numero_expediente']) ?></td>
                    <td><?= formatDateTime($exp['fecha_ingreso']) ?></td>
                    <td><?= e($exp['tipo_persona'] === 'JURIDICA' ? $exp['razon_social'] : $exp['nombres'] . ' ' . $exp['apellidos']) ?></td>
                    <td><?= e($exp['tipo_tramite_nombre']) ?></td>
                    <td><?= e($exp['asunto']) ?></td>
                    <td><?= e($exp['oficina_actual_sigla']) ?></td>
                    <td><?= e($exp['estado_nombre']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>
