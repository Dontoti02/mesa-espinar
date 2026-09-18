<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Documento;
use App\Models\Estado;
use App\Models\Expediente;
use App\Models\Movimiento;
use App\Models\Oficina;

class BandejaController extends Controller
{
    private Expediente $expedienteModel;
    private Movimiento $movimientoModel;
    private Documento $documentoModel;
    private Estado $estadoModel;
    private Oficina $oficinaModel;

    public function __construct()
    {
        $this->expedienteModel = new Expediente();
        $this->movimientoModel = new Movimiento();
        $this->documentoModel = new Documento();
        $this->estadoModel = new Estado();
        $this->oficinaModel = new Oficina();
    }

    public function index(): void
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }

        $user = Auth::user();
        $oficinaId = (int)($user['oficina_id'] ?? 0);

        if ($oficinaId === 0) {
            Session::setFlash('warning', 'Tu usuario no tiene una oficina asignada para operar una bandeja.');
            $this->redirect('/dashboard');
        }

        $tab = $_GET['tab'] ?? 'pendientes';
        $db = Database::getConnection();

        // 1. Por recepcionar (estado DERIVADO y oficina actual es la del usuario)
        $sqlPorRecepcionar = "SELECT e.*, tt.nombre AS tipo_tramite_nombre, est.nombre AS estado_nombre, est.color AS estado_color,
                                     p.nombre AS prioridad_nombre, p.color AS prioridad_color
                              FROM expedientes e
                              INNER JOIN tipos_tramite tt ON e.tipo_tramite_id = tt.id
                              INNER JOIN estados_expediente est ON e.estado_id = est.id
                              INNER JOIN prioridades p ON e.prioridad_id = p.id
                              WHERE e.activo = 1 AND e.oficina_actual_id = :oficina_id AND est.codigo = 'DERIVADO'
                              ORDER BY p.orden DESC, e.updated_at ASC";
        $stmt = $db->prepare($sqlPorRecepcionar);
        $stmt->execute([':oficina_id' => $oficinaId]);
        $porRecepcionar = $stmt->fetchAll();

        // 2. En atención / recepcionados en mi oficina
        $sqlEnAtencion = "SELECT e.*, tt.nombre AS tipo_tramite_nombre, est.nombre AS estado_nombre, est.color AS estado_color,
                                 p.nombre AS prioridad_nombre, p.color AS prioridad_color
                          FROM expedientes e
                          INNER JOIN tipos_tramite tt ON e.tipo_tramite_id = tt.id
                          INNER JOIN estados_expediente est ON e.estado_id = est.id
                          INNER JOIN prioridades p ON e.prioridad_id = p.id
                          WHERE e.activo = 1 AND e.oficina_actual_id = :oficina_id 
                            AND est.codigo IN ('RECEPCIONADO_POR_OFICINA', 'EN_TRAMITE', 'PENDIENTE_INFORMACION')
                          ORDER BY p.orden DESC, e.updated_at ASC";
        $stmt = $db->prepare($sqlEnAtencion);
        $stmt->execute([':oficina_id' => $oficinaId]);
        $enAtencion = $stmt->fetchAll();

        // 3. Solicitudes de información pendientes dirigidas a mi oficina
        $sqlSolicitudes = "SELECT s.*, e.numero_expediente, e.asunto AS expediente_asunto,
                                  os.nombre AS solicitante_nombre, os.sigla AS solicitante_sigla,
                                  CONCAT(u.nombres, ' ', u.apellidos) AS solicitante_usuario
                           FROM solicitudes_internas s
                           INNER JOIN expedientes e ON s.expediente_id = e.id
                           INNER JOIN oficinas os ON s.oficina_solicitante_id = os.id
                           INNER JOIN usuarios u ON s.usuario_solicitante_id = u.id
                           WHERE s.oficina_proveedora_id = :oficina_id AND s.estado = 'PENDIENTE'
                           ORDER BY s.id DESC";
        $stmt = $db->prepare($sqlSolicitudes);
        $stmt->execute([':oficina_id' => $oficinaId]);
        $solicitudes = $stmt->fetchAll();

        // 4. Respondidos por mi oficina
        $sqlRespondidos = "SELECT e.*, tt.nombre AS tipo_tramite_nombre, est.nombre AS estado_nombre, est.color AS estado_color
                           FROM expedientes e
                           INNER JOIN tipos_tramite tt ON e.tipo_tramite_id = tt.id
                           INNER JOIN estados_expediente est ON e.estado_id = est.id
                           WHERE e.activo = 1 AND e.oficina_responsable_id = :oficina_id 
                             AND est.codigo IN ('RESPONDIDO', 'FINALIZADO', 'ARCHIVADO')
                           ORDER BY e.updated_at DESC LIMIT 25";
        $stmt = $db->prepare($sqlRespondidos);
        $stmt->execute([':oficina_id' => $oficinaId]);
        $respondidos = $stmt->fetchAll();

        $this->render('oficinas.bandeja', [
            'pageTitle' => 'Bandeja de Entrada: ' . $user['oficina_nombre'],
            'tab' => $tab,
            'porRecepcionar' => $porRecepcionar,
            'enAtencion' => $enAtencion,
            'solicitudes' => $solicitudes,
            'respondidos' => $respondidos,
            'oficina' => $user['oficina_nombre']
        ]);
    }

    public function recibir(string $id): void
    {
        $this->validateCSRF();
        $user = Auth::user();
        $oficinaId = (int)($user['oficina_id'] ?? 0);

        $expediente = $this->expedienteModel->find((int)$id);
        if (!$expediente) {
            Session::setFlash('error', 'Expediente no encontrado.');
            $this->redirect('/mi-oficina');
        }

        // Verificar que el expediente esté actualmente derivado a la oficina del usuario (o sea superadmin)
        if ((int)$expediente['oficina_actual_id'] !== $oficinaId && !Auth::hasRole('superadministrador')) {
            Session::setFlash('error', 'Solo la oficina destinataria puede recepcionar este expediente.');
            $this->redirect("/expedientes/{$id}");
        }

        // Estado: RECEPCIONADO_POR_OFICINA (ID 6)
        $estadoRecepcionado = $this->estadoModel->findByCodigo('RECEPCIONADO_POR_OFICINA');
        $estadoId = $estadoRecepcionado ? (int)$estadoRecepcionado['id'] : 6;
        $userId = Auth::id();

        $this->expedienteModel->beginTransaction();
        try {
            $this->expedienteModel->update((int)$id, [
                'estado_id' => $estadoId,
                'usuario_responsable_id' => $userId
            ]);

            $this->movimientoModel->registrarMovimiento(
                (int)$id,
                'RECEPCION',
                $expediente['oficina_actual_id'],
                $expediente['oficina_actual_id'],
                $userId,
                $expediente['estado_id'],
                $estadoId,
                "Expediente recepcionado formalmente por la oficina {$user['oficina_sigla']}.",
                true
            );

            logAudit('RECEPCIONAR_EXPEDIENTE', 'Oficinas', (string)$id, "Expediente {$expediente['numero_expediente']} recepcionado.");
            $this->expedienteModel->commit();

            Session::setFlash('success', "Expediente {$expediente['numero_expediente']} recepcionado. Ya puedes atenderlo.");
            $this->redirect("/expedientes/{$id}");

        } catch (\Throwable $e) {
            $this->expedienteModel->rollBack();
            Session::setFlash('error', "Error al recepcionar el expediente: " . $e->getMessage());
            $this->redirect("/expedientes/{$id}");
        }
    }

    public function responder(string $id): void
    {
        $this->validateCSRF();
        $user = Auth::user();
        $oficinaId = (int)($user['oficina_id'] ?? 0);

        $expediente = $this->expedienteModel->find((int)$id);
        if (!$expediente) {
            Session::setFlash('error', 'Expediente no encontrado.');
            $this->redirect('/mi-oficina');
        }

        $respuestaTexto = trim($_POST['respuesta'] ?? '');
        if (empty($respuestaTexto)) {
            Session::setFlash('error', 'El informe o detalle de la respuesta es obligatorio.');
            $this->redirect("/expedientes/{$id}");
        }

        $userId = Auth::id();
        // Al responder, el expediente regresa a Dirección (Oficina 1) con estado RESPONDIDO (ID 11)
        $oficinaDireccionId = 1;
        $estadoRespondido = $this->estadoModel->findByCodigo('RESPONDIDO');
        $estadoRespondidoId = $estadoRespondido ? (int)$estadoRespondido['id'] : 11;

        $this->expedienteModel->beginTransaction();
        try {
            $this->expedienteModel->update((int)$id, [
                'oficina_actual_id' => $oficinaDireccionId,
                'estado_id' => $estadoRespondidoId
            ]);

            $movimientoId = $this->movimientoModel->registrarMovimiento(
                (int)$id,
                'RESPUESTA',
                $expediente['oficina_actual_id'],
                $oficinaDireccionId,
                $userId,
                $expediente['estado_id'],
                $estadoRespondidoId,
                "Respuesta técnica remitida a Dirección: {$respuestaTexto}",
                true
            );

            // Subir documento de respuesta si se adjuntó
            if (!empty($_FILES['documento_respuesta']['tmp_name'])) {
                $this->documentoModel->guardarArchivo(
                    $_FILES['documento_respuesta'],
                    (int)$id,
                    $movimientoId,
                    false,
                    false,
                    $userId
                );
            }

            // Notificación para Dirección
            $db = Database::getConnection();
            $stmtNotif = $db->prepare("
                INSERT INTO notificaciones (oficina_id, titulo, mensaje, enlace)
                VALUES (:oficina_id, :titulo, :mensaje, :enlace)
            ");
            $stmtNotif->execute([
                ':oficina_id' => $oficinaDireccionId,
                ':titulo' => "Respuesta de {$user['oficina_sigla']}: {$expediente['numero_expediente']}",
                ':mensaje' => "La oficina {$user['oficina_nombre']} ha remitido su respuesta técnica.",
                ':enlace' => url("/expedientes/{$id}")
            ]);

            logAudit('RESPONDER_EXPEDIENTE', 'Oficinas', (string)$id, "Respuesta emitida a Dirección para {$expediente['numero_expediente']}");
            $this->expedienteModel->commit();

            Session::setFlash('success', "Respuesta técnica enviada a Dirección con éxito.");
            $this->redirect("/expedientes/{$id}");

        } catch (\Throwable $e) {
            $this->expedienteModel->rollBack();
            Session::setFlash('error', "Error al emitir respuesta: " . $e->getMessage());
            $this->redirect("/expedientes/{$id}");
        }
    }

    public function solicitarInfo(string $id): void
    {
        $this->validateCSRF();
        $user = Auth::user();
        $oficinaId = (int)($user['oficina_id'] ?? 0);

        $expediente = $this->expedienteModel->find((int)$id);
        if (!$expediente) {
            Session::setFlash('error', 'Expediente no encontrado.');
            $this->redirect('/mi-oficina');
        }

        $oficinaProveedoraId = (int)($_POST['oficina_proveedora_id'] ?? 0);
        $motivo = trim($_POST['motivo'] ?? '');

        if ($oficinaProveedoraId <= 0 || empty($motivo)) {
            Session::setFlash('error', 'Debes seleccionar la oficina y detallar el motivo de la consulta.');
            $this->redirect("/expedientes/{$id}");
        }

        $userId = Auth::id();
        // Estado: PENDIENTE_INFORMACION (ID 8)
        $estadoPendiente = $this->estadoModel->findByCodigo('PENDIENTE_INFORMACION');
        $estadoPendienteId = $estadoPendiente ? (int)$estadoPendiente['id'] : 8;

        $db = Database::getConnection();
        $this->expedienteModel->beginTransaction();
        try {
            // Nota importante (Regla 8): NO cambiar oficina_responsable_id
            $this->expedienteModel->update((int)$id, [
                'estado_id' => $estadoPendienteId
            ]);

            // Insertar solicitud interna
            $stmtSol = $db->prepare("
                INSERT INTO solicitudes_internas (expediente_id, oficina_solicitante_id, oficina_proveedora_id, usuario_solicitante_id, motivo)
                VALUES (:exp, :sol, :prov, :user, :motivo)
            ");
            $stmtSol->execute([
                ':exp' => $id,
                ':sol' => $oficinaId,
                ':prov' => $oficinaProveedoraId,
                ':user' => $userId,
                ':motivo' => $motivo
            ]);

            // Registrar movimiento SOLICITUD_INFORMACION
            $this->movimientoModel->registrarMovimiento(
                (int)$id,
                'SOLICITUD_INFORMACION',
                $oficinaId,
                $oficinaProveedoraId,
                $userId,
                $expediente['estado_id'],
                $estadoPendienteId,
                "Consulta interna a otra oficina: {$motivo}",
                true
            );

            // Notificación a la oficina proveedora
            $stmtNotif = $db->prepare("
                INSERT INTO notificaciones (oficina_id, titulo, mensaje, enlace)
                VALUES (:oficina_id, :titulo, :mensaje, :enlace)
            ");
            $stmtNotif->execute([
                ':oficina_id' => $oficinaProveedoraId,
                ':titulo' => "Consulta Interna de {$user['oficina_sigla']}",
                ':mensaje' => "Se ha solicitado información de apoyo para el expediente {$expediente['numero_expediente']}.",
                ':enlace' => url("/mi-oficina?tab=solicitudes")
            ]);

            logAudit('SOLICITUD_INFO_INTERNA', 'Oficinas', (string)$id, "Solicitud interna enviada a oficina ID {$oficinaProveedoraId}");
            $this->expedienteModel->commit();

            Session::setFlash('success', "Consulta interna remitida a la oficina de apoyo.");
            $this->redirect("/expedientes/{$id}");

        } catch (\Throwable $e) {
            $this->expedienteModel->rollBack();
            Session::setFlash('error', "Error al solicitar información: " . $e->getMessage());
            $this->redirect("/expedientes/{$id}");
        }
    }

    public function responderSolicitud(string $id): void
    {
        $this->validateCSRF();
        $solicitudId = (int)$id;
        $respuesta = trim($_POST['respuesta'] ?? '');

        if (empty($respuesta)) {
            Session::setFlash('error', 'Debes ingresar el texto de respuesta.');
            $this->redirect('/mi-oficina?tab=solicitudes');
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM solicitudes_internas WHERE id = :id AND estado = 'PENDIENTE'");
        $stmt->execute([':id' => $solicitudId]);
        $solicitud = $stmt->fetch();

        if (!$solicitud) {
            Session::setFlash('error', 'Solicitud no encontrada o ya respondida.');
            $this->redirect('/mi-oficina?tab=solicitudes');
        }

        $userId = Auth::id();
        $expedienteId = (int)$solicitud['expediente_id'];
        $expediente = $this->expedienteModel->find($expedienteId);

        // Estado: RECEPCIONADO_POR_OFICINA (ID 6)
        $estadoRecepcionado = $this->estadoModel->findByCodigo('RECEPCIONADO_POR_OFICINA');
        $estadoId = $estadoRecepcionado ? (int)$estadoRecepcionado['id'] : 6;

        $this->expedienteModel->beginTransaction();
        try {
            // Guardar respuesta de la solicitud interna
            $stmtResp = $db->prepare("
                INSERT INTO solicitudes_internas_respuestas (solicitud_interna_id, usuario_respuesta_id, respuesta)
                VALUES (:sol_id, :user_id, :resp)
            ");
            $stmtResp->execute([
                ':sol_id' => $solicitudId,
                ':user_id' => $userId,
                ':resp' => $respuesta
            ]);

            // Actualizar estado de la solicitud interna
            $stmtUpdateSol = $db->prepare("
                UPDATE solicitudes_internas 
                SET estado = 'RESPONDIDA', fecha_respuesta = NOW(), updated_at = NOW() 
                WHERE id = :id
            ");
            $stmtUpdateSol->execute([':id' => $solicitudId]);

            // Revertir estado del expediente para que la oficina solicitante continúe
            $this->expedienteModel->update($expedienteId, [
                'estado_id' => $estadoId
            ]);

            // Registrar movimiento RESPUESTA_INTERNA
            $this->movimientoModel->registrarMovimiento(
                $expedienteId,
                'RESPUESTA_INTERNA',
                $solicitud['oficina_proveedora_id'],
                $solicitud['oficina_solicitante_id'],
                $userId,
                $expediente['estado_id'],
                $estadoId,
                "Respuesta a consulta interna: {$respuesta}",
                true
            );

            // Notificación a la oficina solicitante
            $stmtNotif = $db->prepare("
                INSERT INTO notificaciones (oficina_id, titulo, mensaje, enlace)
                VALUES (:oficina_id, :titulo, :mensaje, :enlace)
            ");
            $stmtNotif->execute([
                ':oficina_id' => $solicitud['oficina_solicitante_id'],
                ':titulo' => "Consulta respondida para {$expediente['numero_expediente']}",
                ':mensaje' => "La oficina de apoyo ha respondido a tu solicitud interna.",
                ':enlace' => url("/expedientes/{$expedienteId}")
            ]);

            logAudit('RESPUESTA_INFO_INTERNA', 'Oficinas', (string)$expedienteId, "Consulta interna {$solicitudId} respondida.");
            $this->expedienteModel->commit();

            Session::setFlash('success', "Has respondido la consulta interna satisfactoriamente.");
            $this->redirect('/mi-oficina?tab=solicitudes');

        } catch (\Throwable $e) {
            $this->expedienteModel->rollBack();
            Session::setFlash('error', "Error al enviar la respuesta: " . $e->getMessage());
            $this->redirect('/mi-oficina?tab=solicitudes');
        }
    }
}
