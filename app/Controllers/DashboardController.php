<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;

class DashboardController extends Controller
{
    public function index(): void
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }

        $db = Database::getConnection();

        // 1. Métricas de tarjetas (Sección 18)
        $hoy = date('Y-m-d');
        $mes = date('Y-m');

        $stats = [
            'hoy' => (int)$db->query("SELECT COUNT(*) FROM expedientes WHERE DATE(fecha_ingreso) = '{$hoy}' AND activo = 1")->fetchColumn(),
            'mes' => (int)$db->query("SELECT COUNT(*) FROM expedientes WHERE DATE_FORMAT(fecha_ingreso, '%Y-%m') = '{$mes}' AND activo = 1")->fetchColumn(),
            'pendientes' => (int)$db->query("SELECT COUNT(*) FROM expedientes WHERE estado_id NOT IN (13, 14, 15) AND activo = 1")->fetchColumn(),
            'direccion' => (int)$db->query("SELECT COUNT(*) FROM expedientes WHERE oficina_actual_id = 1 AND estado_id NOT IN (13, 14, 15) AND activo = 1")->fetchColumn(),
            'oficinas' => (int)$db->query("SELECT COUNT(*) FROM expedientes WHERE oficina_actual_id != 1 AND oficina_actual_id != 2 AND estado_id NOT IN (13, 14, 15) AND activo = 1")->fetchColumn(),
            'observados' => (int)$db->query("SELECT COUNT(*) FROM expedientes e INNER JOIN estados_expediente est ON e.estado_id = est.id WHERE est.codigo IN ('OBSERVADO', 'DEVUELTO') AND e.activo = 1")->fetchColumn(),
            'finalizados' => (int)$db->query("SELECT COUNT(*) FROM expedientes WHERE estado_id = 13 AND activo = 1")->fetchColumn(),
            'urgentes' => (int)$db->query("SELECT COUNT(*) FROM expedientes WHERE prioridad_id IN (2, 3) AND estado_id NOT IN (13, 14, 15) AND activo = 1")->fetchColumn(),
        ];

        // 2. Gráfico por Estado
        $estadosData = $db->query("
            SELECT est.nombre, est.color, COUNT(e.id) AS total
            FROM estados_expediente est
            LEFT JOIN expedientes e ON est.id = e.estado_id AND e.activo = 1
            WHERE est.activo = 1
            GROUP BY est.id
            HAVING total > 0
            ORDER BY est.orden ASC
        ")->fetchAll();

        // 3. Gráfico por Oficina Actual (Top 7)
        $oficinasData = $db->query("
            SELECT o.sigla, COUNT(e.id) AS total
            FROM oficinas o
            INNER JOIN expedientes e ON o.id = e.oficina_actual_id AND e.activo = 1
            WHERE o.activo = 1
            GROUP BY o.id
            ORDER BY total DESC
            LIMIT 7
        ")->fetchAll();

        // 4. Últimos expedientes registrados
        $ultimosExpedientes = $db->query("
            SELECT e.*, tt.nombre AS tipo_tramite_nombre, est.nombre AS estado_nombre, est.color AS estado_color,
                   oa.sigla AS oficina_actual_sigla
            FROM expedientes e
            INNER JOIN tipos_tramite tt ON e.tipo_tramite_id = tt.id
            INNER JOIN estados_expediente est ON e.estado_id = est.id
            INNER JOIN oficinas oa ON e.oficina_actual_id = oa.id
            WHERE e.activo = 1
            ORDER BY e.id DESC
            LIMIT 8
        ")->fetchAll();

        // 5. Expedientes Urgentes
        $urgentesExpedientes = $db->query("
            SELECT e.*, tt.nombre AS tipo_tramite_nombre, est.nombre AS estado_nombre, est.color AS estado_color,
                   p.nombre AS prioridad_nombre, p.color AS prioridad_color,
                   oa.sigla AS oficina_actual_sigla
            FROM expedientes e
            INNER JOIN tipos_tramite tt ON e.tipo_tramite_id = tt.id
            INNER JOIN estados_expediente est ON e.estado_id = est.id
            INNER JOIN prioridades p ON e.prioridad_id = p.id
            INNER JOIN oficinas oa ON e.oficina_actual_id = oa.id
            WHERE e.activo = 1 AND e.prioridad_id IN (2, 3) AND e.estado_id NOT IN (13, 14, 15)
            ORDER BY e.prioridad_id DESC, e.fecha_ingreso ASC
            LIMIT 6
        ")->fetchAll();

        $this->render('dashboard.index', [
            'pageTitle' => 'Panel de Control Principal',
            'stats' => $stats,
            'estadosData' => $estadosData,
            'oficinasData' => $oficinasData,
            'ultimosExpedientes' => $ultimosExpedientes,
            'urgentesExpedientes' => $urgentesExpedientes
        ]);
    }
}
