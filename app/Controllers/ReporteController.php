<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Models\Estado;
use App\Models\Oficina;
use App\Models\TipoTramite;

class ReporteController extends Controller
{
    private Oficina $oficinaModel;
    private Estado $estadoModel;
    private TipoTramite $tipoTramiteModel;

    public function __construct()
    {
        $this->oficinaModel = new Oficina();
        $this->estadoModel = new Estado();
        $this->tipoTramiteModel = new TipoTramite();
    }

    public function index(): void
    {
        if (!Auth::can('reportes.ver')) {
            Session::setFlash('error', 'No tienes permiso para consultar reportes.');
            $this->redirect('/dashboard');
        }

        $filtros = [
            'fecha_desde' => $_GET['fecha_desde'] ?? date('Y-m-01'),
            'fecha_hasta' => $_GET['fecha_hasta'] ?? date('Y-m-d'),
            'oficina_id' => $_GET['oficina_id'] ?? '',
            'estado_id' => $_GET['estado_id'] ?? '',
            'tipo_tramite_id' => $_GET['tipo_tramite_id'] ?? ''
        ];

        $dataReporte = $this->obtenerDatosReporte($filtros);

        $oficinas = $this->oficinaModel->allActivas();
        $estados = $this->estadoModel->allActivos();
        $tiposTramite = $this->tipoTramiteModel->allActivosVirtuales();

        $this->render('reportes.index', [
            'pageTitle' => 'Módulo de Reportes y Estadísticas',
            'filtros' => $filtros,
            'expedientes' => $dataReporte['expedientes'],
            'resumen' => $dataReporte['resumen'],
            'oficinas' => $oficinas,
            'estados' => $estados,
            'tiposTramite' => $tiposTramite
        ]);
    }

    public function exportarExcel(): void
    {
        if (!Auth::can('reportes.exportar')) {
            Session::setFlash('error', 'Permiso denegado.');
            $this->redirect('/reportes');
        }

        $filtros = [
            'fecha_desde' => $_GET['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? '',
            'oficina_id' => $_GET['oficina_id'] ?? '',
            'estado_id' => $_GET['estado_id'] ?? '',
            'tipo_tramite_id' => $_GET['tipo_tramite_id'] ?? ''
        ];

        $dataReporte = $this->obtenerDatosReporte($filtros);
        $expedientes = $dataReporte['expedientes'];

        $filename = "reporte_expedientes_" . date('Ymd_His') . ".csv";

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        // Agregar BOM para compatibilidad con Excel (acentos y ñ)
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, [
            'Numero Expediente',
            'Codigo Seguimiento',
            'Fecha Ingreso',
            'Modalidad',
            'Tipo Documento',
            'Numero Documento',
            'Solicitante',
            'Correo',
            'Telefono',
            'Tipo Tramite',
            'Asunto',
            'Folios',
            'Oficina Actual',
            'Estado',
            'Prioridad',
            'Fecha Finalizacion'
        ]);

        foreach ($expedientes as $exp) {
            fputcsv($output, [
                $exp['numero_expediente'],
                $exp['codigo_seguimiento'],
                $exp['fecha_ingreso'],
                (int)$exp['es_virtual'] === 1 ? 'Virtual' : 'Presencial',
                $exp['tipo_documento'],
                $exp['numero_documento'],
                $exp['tipo_persona'] === 'JURIDICA' ? $exp['razon_social'] : $exp['nombres'] . ' ' . $exp['apellidos'],
                $exp['correo'],
                $exp['telefono'],
                $exp['tipo_tramite_nombre'],
                $exp['asunto'],
                $exp['folios'],
                $exp['oficina_actual_nombre'],
                $exp['estado_nombre'],
                $exp['prioridad_nombre'],
                $exp['fecha_finalizacion'] ?: '-'
            ]);
        }

        fclose($output);
        exit;
    }

    public function imprimir(): void
    {
        if (!Auth::can('reportes.ver')) {
            die("Permiso denegado.");
        }

        $filtros = [
            'fecha_desde' => $_GET['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? '',
            'oficina_id' => $_GET['oficina_id'] ?? '',
            'estado_id' => $_GET['estado_id'] ?? '',
            'tipo_tramite_id' => $_GET['tipo_tramite_id'] ?? ''
        ];

        $dataReporte = $this->obtenerDatosReporte($filtros);

        $this->render('reportes.imprimir', [
            'pageTitle' => 'Reporte Oficial de Trámites',
            'filtros' => $filtros,
            'expedientes' => $dataReporte['expedientes'],
            'resumen' => $dataReporte['resumen']
        ], 'none');
    }

    private function obtenerDatosReporte(array $filtros): array
    {
        $db = Database::getConnection();
        $conditions = ["e.activo = 1"];
        $params = [];

        if (!empty($filtros['fecha_desde'])) {
            $conditions[] = "DATE(e.fecha_ingreso) >= :desde";
            $params[':desde'] = $filtros['fecha_desde'];
        }

        if (!empty($filtros['fecha_hasta'])) {
            $conditions[] = "DATE(e.fecha_ingreso) <= :hasta";
            $params[':hasta'] = $filtros['fecha_hasta'];
        }

        if (!empty($filtros['oficina_id'])) {
            $conditions[] = "e.oficina_actual_id = :oficina_id";
            $params[':oficina_id'] = (int)$filtros['oficina_id'];
        }

        if (!empty($filtros['estado_id'])) {
            $conditions[] = "e.estado_id = :estado_id";
            $params[':estado_id'] = (int)$filtros['estado_id'];
        }

        if (!empty($filtros['tipo_tramite_id'])) {
            $conditions[] = "e.tipo_tramite_id = :tipo_tramite_id";
            $params[':tipo_tramite_id'] = (int)$filtros['tipo_tramite_id'];
        }

        $whereClause = implode(' AND ', $conditions);

        $sql = "SELECT e.*, 
                       tt.nombre AS tipo_tramite_nombre,
                       est.nombre AS estado_nombre, est.color AS estado_color, est.codigo AS estado_codigo,
                       p.nombre AS prioridad_nombre,
                       oa.nombre AS oficina_actual_nombre, oa.sigla AS oficina_actual_sigla
                FROM expedientes e
                INNER JOIN tipos_tramite tt ON e.tipo_tramite_id = tt.id
                INNER JOIN estados_expediente est ON e.estado_id = est.id
                INNER JOIN prioridades p ON e.prioridad_id = p.id
                INNER JOIN oficinas oa ON e.oficina_actual_id = oa.id
                WHERE {$whereClause}
                ORDER BY e.id DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $expedientes = $stmt->fetchAll();

        // Resumen
        $total = count($expedientes);
        $finalizados = count(array_filter($expedientes, fn($x) => $x['estado_codigo'] === 'FINALIZADO'));
        $pendientes = $total - $finalizados;

        return [
            'expedientes' => $expedientes,
            'resumen' => [
                'total' => $total,
                'finalizados' => $finalizados,
                'pendientes' => $pendientes
            ]
        ];
    }
}
