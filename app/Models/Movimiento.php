<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Movimiento extends Model
{
    protected string $table = 'expediente_movimientos';
    protected array $fillable = [
        'expediente_id', 'tipo_movimiento', 'oficina_origen_id',
        'oficina_destino_id', 'usuario_id', 'estado_anterior_id',
        'estado_nuevo_id', 'observacion', 'es_publico', 'ip', 'user_agent'
    ];

    public function registrarMovimiento(
        int $expedienteId,
        string $tipoMovimiento,
        ?int $origenId,
        ?int $destinoId,
        ?int $usuarioId,
        ?int $estadoAnteriorId,
        int $estadoNuevoId,
        ?string $observacion = null,
        bool $esPublico = true
    ): int {
        return (int)$this->insert([
            'expediente_id' => $expedienteId,
            'tipo_movimiento' => $tipoMovimiento,
            'oficina_origen_id' => $origenId,
            'oficina_destino_id' => $destinoId,
            'usuario_id' => $usuarioId,
            'estado_anterior_id' => $estadoAnteriorId,
            'estado_nuevo_id' => $estadoNuevoId,
            'observacion' => $observacion ? trim($observacion) : null,
            'es_publico' => $esPublico ? 1 : 0,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido', 0, 255)
        ]);
    }

    public function getLineaDeTiempo(int $expedienteId, bool $soloPublico = false): array
    {
        $sql = "SELECT m.*,
                       oo.nombre AS oficina_origen_nombre, oo.sigla AS oficina_origen_sigla,
                       od.nombre AS oficina_destino_nombre, od.sigla AS oficina_destino_sigla,
                       CONCAT(u.nombres, ' ', u.apellidos) AS usuario_nombre,
                       ea.nombre AS estado_ant_nombre,
                       en.nombre AS estado_nvo_nombre, en.color AS estado_nvo_color, en.icono AS estado_nvo_icono
                FROM `{$this->table}` m
                LEFT JOIN oficinas oo ON m.oficina_origen_id = oo.id
                LEFT JOIN oficinas od ON m.oficina_destino_id = od.id
                LEFT JOIN usuarios u ON m.usuario_id = u.id
                LEFT JOIN estados_expediente ea ON m.estado_anterior_id = ea.id
                INNER JOIN estados_expediente en ON m.estado_nuevo_id = en.id
                WHERE m.expediente_id = :id";

        if ($soloPublico) {
            $sql .= " AND m.es_publico = 1";
        }

        $sql .= " ORDER BY m.created_at ASC, m.id ASC";

        $stmt = $this->db()->prepare($sql);
        $stmt->execute([':id' => $expedienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
