<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Model;
use PDO;

class Expediente extends Model
{
    protected string $table = 'expedientes';
    protected array $fillable = [
        'numero_expediente', 'codigo_seguimiento', 'tipo_persona', 'tipo_documento',
        'numero_documento', 'nombres', 'apellidos', 'razon_social', 'correo',
        'telefono', 'direccion', 'tipo_tramite_id', 'asunto', 'descripcion',
        'folios', 'estado_id', 'prioridad_id', 'oficina_actual_id',
        'oficina_responsable_id', 'usuario_responsable_id', 'fecha_ingreso',
        'fecha_finalizacion', 'observacion_publica', 'es_virtual', 'activo'
    ];

    public function generarNumeroExpediente(): string
    {
        $prefijo = config('expedientes_prefijo', 'EXP');
        $digitos = (int)config('expedientes_digitos', 6);
        $reinicioAnual = (int)config('expedientes_reinicio_anual', 1);
        $anio = date('Y');

        $filtroAnio = $reinicioAnual ? "WHERE YEAR(fecha_ingreso) = {$anio}" : "";
        $sql = "SELECT numero_expediente FROM `{$this->table}` {$filtroAnio} ORDER BY id DESC LIMIT 1 FOR UPDATE";
        
        $stmt = $this->db()->query($sql);
        $ultimo = $stmt->fetchColumn();

        $siguiente = 1;
        if ($ultimo) {
            // Extraer parte numérica al final
            if (preg_match('/(\d+)$/', $ultimo, $matches)) {
                $siguiente = (int)$matches[1] + 1;
            }
        }

        $formatoNumero = str_pad((string)$siguiente, $digitos, '0', STR_PAD_LEFT);
        return "{$prefijo}-{$anio}-{$formatoNumero}";
    }

    public function generarCodigoSeguimiento(): string
    {
        do {
            $codigo = strtoupper(substr(bin2hex(random_bytes(8)), 0, 12));
            $existe = $this->first("codigo_seguimiento = :c", [':c' => $codigo]);
        } while ($existe !== null);

        return $codigo;
    }

    public function findDetallado(int $id): ?array
    {
        $sql = "SELECT e.*, 
                       tt.nombre AS tipo_tramite_nombre, tt.codigo AS tipo_tramite_codigo,
                       est.nombre AS estado_nombre, est.color AS estado_color, est.codigo AS estado_codigo, est.icono AS estado_icono,
                       p.nombre AS prioridad_nombre, p.color AS prioridad_color, p.codigo AS prioridad_codigo,
                       oa.nombre AS oficina_actual_nombre, oa.sigla AS oficina_actual_sigla,
                       oresp.nombre AS oficina_resp_nombre, oresp.sigla AS oficina_resp_sigla,
                       CONCAT(u.nombres, ' ', u.apellidos) AS usuario_resp_nombre
                FROM `{$this->table}` e
                INNER JOIN tipos_tramite tt ON e.tipo_tramite_id = tt.id
                INNER JOIN estados_expediente est ON e.estado_id = est.id
                INNER JOIN prioridades p ON e.prioridad_id = p.id
                INNER JOIN oficinas oa ON e.oficina_actual_id = oa.id
                LEFT JOIN oficinas oresp ON e.oficina_responsable_id = oresp.id
                LEFT JOIN usuarios u ON e.usuario_responsable_id = u.id
                WHERE e.id = :id AND e.activo = 1
                LIMIT 1";

        $stmt = $this->db()->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByCodigoSeguimiento(string $codigo): ?array
    {
        $sql = "SELECT e.*, 
                       tt.nombre AS tipo_tramite_nombre,
                       est.nombre AS estado_nombre, est.color AS estado_color, est.codigo AS estado_codigo, est.icono AS estado_icono, est.es_publico AS estado_es_publico,
                       oa.nombre AS oficina_actual_nombre, oa.sigla AS oficina_actual_sigla
                FROM `{$this->table}` e
                INNER JOIN tipos_tramite tt ON e.tipo_tramite_id = tt.id
                INNER JOIN estados_expediente est ON e.estado_id = est.id
                INNER JOIN oficinas oa ON e.oficina_actual_id = oa.id
                WHERE (e.codigo_seguimiento = :c1 OR e.numero_expediente = :c2) AND e.activo = 1
                LIMIT 1";

        $stmt = $this->db()->prepare($sql);
        $stmt->execute([':c1' => $codigo, ':c2' => $codigo]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByNumeroExpediente(string $numero): ?array
    {
        $sql = "SELECT e.*, 
                       tt.nombre AS tipo_tramite_nombre,
                       est.nombre AS estado_nombre, est.color AS estado_color, est.codigo AS estado_codigo, est.icono AS estado_icono, est.es_publico AS estado_es_publico,
                       oa.nombre AS oficina_actual_nombre, oa.sigla AS oficina_actual_sigla
                FROM `{$this->table}` e
                INNER JOIN tipos_tramite tt ON e.tipo_tramite_id = tt.id
                INNER JOIN estados_expediente est ON e.estado_id = est.id
                INNER JOIN oficinas oa ON e.oficina_actual_id = oa.id
                WHERE e.numero_expediente = :n AND e.activo = 1
                LIMIT 1";

        $stmt = $this->db()->prepare($sql);
        $stmt->execute([':n' => $numero]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByExpedienteYCodigo(string $numeroExpediente, string $codigoSeguimiento): ?array
    {
        $sql = "SELECT e.*, 
                       tt.nombre AS tipo_tramite_nombre,
                       est.nombre AS estado_nombre, est.color AS estado_color, est.codigo AS estado_codigo, est.icono AS estado_icono, est.es_publico AS estado_es_publico,
                       oa.nombre AS oficina_actual_nombre, oa.sigla AS oficina_actual_sigla
                FROM `{$this->table}` e
                INNER JOIN tipos_tramite tt ON e.tipo_tramite_id = tt.id
                INNER JOIN estados_expediente est ON e.estado_id = est.id
                INNER JOIN oficinas oa ON e.oficina_actual_id = oa.id
                WHERE e.numero_expediente = :n AND e.codigo_seguimiento = :c AND e.activo = 1
                LIMIT 1";

        $stmt = $this->db()->prepare($sql);
        $stmt->execute([':n' => $numeroExpediente, ':c' => $codigoSeguimiento]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getExpedientesFiltrados(array $filtros = [], int $page = 1, int $perPage = 20): array
    {
        $conditions = ["e.activo = 1"];
        $params = [];

        if (!empty($filtros['buscar'])) {
            $conditions[] = "(e.numero_expediente LIKE :b OR e.codigo_seguimiento LIKE :b OR e.numero_documento LIKE :b OR e.nombres LIKE :b OR e.apellidos LIKE :b OR e.razon_social LIKE :b OR e.asunto LIKE :b)";
            $params[':b'] = '%' . trim($filtros['buscar']) . '%';
        }

        if (!empty($filtros['estado_id'])) {
            $conditions[] = "e.estado_id = :estado_id";
            $params[':estado_id'] = (int)$filtros['estado_id'];
        }

        if (!empty($filtros['oficina_id'])) {
            $conditions[] = "e.oficina_actual_id = :oficina_id";
            $params[':oficina_id'] = (int)$filtros['oficina_id'];
        }

        if (!empty($filtros['tipo_tramite_id'])) {
            $conditions[] = "e.tipo_tramite_id = :tipo_tramite_id";
            $params[':tipo_tramite_id'] = (int)$filtros['tipo_tramite_id'];
        }

        if (!empty($filtros['prioridad_id'])) {
            $conditions[] = "e.prioridad_id = :prioridad_id";
            $params[':prioridad_id'] = (int)$filtros['prioridad_id'];
        }

        if (!empty($filtros['fecha_desde'])) {
            $conditions[] = "DATE(e.fecha_ingreso) >= :fecha_desde";
            $params[':fecha_desde'] = $filtros['fecha_desde'];
        }

        if (!empty($filtros['fecha_hasta'])) {
            $conditions[] = "DATE(e.fecha_ingreso) <= :fecha_hasta";
            $params[':fecha_hasta'] = $filtros['fecha_hasta'];
        }

        $whereClause = implode(' AND ', $conditions);

        $countSql = "SELECT COUNT(*) FROM `{$this->table}` e WHERE {$whereClause}";
        $stmt = $this->db()->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $offset = ($page - 1) * $perPage;
        $sql = "SELECT e.*, 
                       tt.nombre AS tipo_tramite_nombre,
                       est.nombre AS estado_nombre, est.color AS estado_color, est.codigo AS estado_codigo,
                       p.nombre AS prioridad_nombre, p.color AS prioridad_color,
                       oa.nombre AS oficina_actual_nombre, oa.sigla AS oficina_actual_sigla
                FROM `{$this->table}` e
                INNER JOIN tipos_tramite tt ON e.tipo_tramite_id = tt.id
                INNER JOIN estados_expediente est ON e.estado_id = est.id
                INNER JOIN prioridades p ON e.prioridad_id = p.id
                INNER JOIN oficinas oa ON e.oficina_actual_id = oa.id
                WHERE {$whereClause}
                ORDER BY e.id DESC
                LIMIT {$perPage} OFFSET {$offset}";

        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll();

        return [
            'data' => $data,
            'current_page' => $page,
            'per_page' => $perPage,
            'total_records' => $total,
            'total_pages' => (int)ceil($total / $perPage)
        ];
    }
}
