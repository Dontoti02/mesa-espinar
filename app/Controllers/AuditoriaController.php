<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;

class AuditoriaController extends Controller
{
    public function index(): void
    {
        if (!Auth::can('auditoria.ver')) {
            Session::setFlash('error', 'No tienes permiso para consultar el registro de auditoría.');
            $this->redirect('/dashboard');
        }

        $page = (int)($_GET['page'] ?? 1);
        $perPage = 25;
        $offset = ($page - 1) * $perPage;

        $buscar = trim($_GET['buscar'] ?? '');
        $modulo = trim($_GET['modulo'] ?? '');

        $conditions = ["1=1"];
        $params = [];

        if (!empty($buscar)) {
            $term = "%{$buscar}%";
            $conditions[] = "(a.accion LIKE :b1 OR a.detalles LIKE :b2 OR a.ip LIKE :b3 OR u.usuario LIKE :b4)";
            $params[':b1'] = $term;
            $params[':b2'] = $term;
            $params[':b3'] = $term;
            $params[':b4'] = $term;
        }

        if (!empty($modulo)) {
            $conditions[] = "a.modulo = :mod";
            $params[':mod'] = $modulo;
        }

        $whereClause = implode(' AND ', $conditions);

        $db = Database::getConnection();

        // Conteo total
        $stmtCount = $db->prepare("SELECT COUNT(*) FROM auditoria a LEFT JOIN usuarios u ON a.usuario_id = u.id WHERE {$whereClause}");
        $stmtCount->execute($params);
        $totalRecords = (int)$stmtCount->fetchColumn();

        // Registros paginados
        $sql = "SELECT a.*, u.usuario AS usuario_login, CONCAT(u.nombres, ' ', u.apellidos) AS usuario_nombre
                FROM auditoria a
                LEFT JOIN usuarios u ON a.usuario_id = u.id
                WHERE {$whereClause}
                ORDER BY a.id DESC
                LIMIT {$perPage} OFFSET {$offset}";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $registros = $stmt->fetchAll();

        // Módulos para filtro
        $modulos = $db->query("SELECT DISTINCT modulo FROM auditoria ORDER BY modulo ASC")->fetchAll(\PDO::FETCH_COLUMN);

        $this->render('auditoria.index', [
            'pageTitle' => 'Registro de Auditoría y Trazabilidad',
            'registros' => $registros,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total_records' => $totalRecords,
                'total_pages' => (int)ceil($totalRecords / $perPage)
            ],
            'buscar' => $buscar,
            'modulo' => $modulo,
            'modulos' => $modulos
        ]);
    }
}
