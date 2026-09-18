<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Estado;
use App\Models\Expediente;
use App\Models\Movimiento;
use App\Models\Oficina;

class DireccionController extends Controller
{
    private Expediente $expedienteModel;
    private Movimiento $movimientoModel;
    private Oficina $oficinaModel;
    private Estado $estadoModel;

    public function __construct()
    {
        $this->expedienteModel = new Expediente();
        $this->movimientoModel = new Movimiento();
        $this->oficinaModel = new Oficina();
        $this->estadoModel = new Estado();
    }

    public function index(): void
    {
        if (!Auth::hasRole(['direccion', 'superadministrador']) && !Auth::can('expedientes.derivar')) {
            Session::setFlash('error', 'Acceso exclusivo para Dirección General.');
            $this->redirect('/dashboard');
        }

        $tab = $_GET['tab'] ?? 'pendientes';
        $db = Database::getConnection();

        // 1. Pendientes por derivar en Dirección (Oficina 1 o estado ENVIADO_DIRECCION)
        $sqlPendientes = "SELECT e.*, tt.nombre AS tipo_tramite_nombre, est.nombre AS estado_nombre, est.color AS estado_color,
                                 p.nombre AS prioridad_nombre, p.color AS prioridad_color,
                                 oa.sigla AS oficina_actual_sigla
                          FROM expedientes e
                          INNER JOIN tipos_tramite tt ON e.tipo_tramite_id = tt.id
                          INNER JOIN estados_expediente est ON e.estado_id = est.id
                          INNER JOIN prioridades p ON e.prioridad_id = p.id
                          INNER JOIN oficinas oa ON e.oficina_actual_id = oa.id
                          WHERE e.activo = 1 AND (e.oficina_actual_id = 1 OR est.codigo = 'ENVIADO_DIRECCION')
                          ORDER BY p.orden DESC, e.fecha_ingreso ASC";
        $pendientes = $db->query($sqlPendientes)->fetchAll();

        // 2. Respuestas remitidas por oficinas para revisión de Dirección
        $sqlRespuestas = "SELECT e.*, tt.nombre AS tipo_tramite_nombre, est.nombre AS estado_nombre, est.color AS estado_color,
                                 p.nombre AS prioridad_nombre, p.color AS prioridad_color,
                                 oresp.sigla AS oficina_resp_sigla, oresp.nombre AS oficina_resp_nombre
                          FROM expedientes e
                          INNER JOIN tipos_tramite tt ON e.tipo_tramite_id = tt.id
                          INNER JOIN estados_expediente est ON e.estado_id = est.id
                          INNER JOIN prioridades p ON e.prioridad_id = p.id
                          LEFT JOIN oficinas oresp ON e.oficina_responsable_id = oresp.id
                          WHERE e.activo = 1 AND est.codigo IN ('RESPONDIDO', 'PENDIENTE_APROBACION')
                          ORDER BY e.updated_at DESC";
        $respuestas = $db->query($sqlRespuestas)->fetchAll();

        // 3. Expedientes finalizados
        $sqlFinalizados = "SELECT e.*, tt.nombre AS tipo_tramite_nombre, est.nombre AS estado_nombre, est.color AS estado_color,
                                  p.nombre AS prioridad_nombre, p.color AS prioridad_color
                           FROM expedientes e
                           INNER JOIN tipos_tramite tt ON e.tipo_tramite_id = tt.id
                           INNER JOIN estados_expediente est ON e.estado_id = est.id
                           INNER JOIN prioridades p ON e.prioridad_id = p.id
                           WHERE e.activo = 1 AND est.codigo IN ('FINALIZADO', 'ARCHIVADO')
                           ORDER BY e.fecha_finalizacion DESC LIMIT 30";
        $finalizados = $db->query($sqlFinalizados)->fetchAll();

        $oficinas = $this->oficinaModel->allActivas();

        $this->render('direccion.index', [
            'pageTitle' => 'Bandeja de Dirección General',
            'tab' => $tab,
            'pendientes' => $pendientes,
            'respuestas' => $respuestas,
            'finalizados' => $finalizados,
            'oficinas' => $oficinas
        ]);
    }

    public function derivar(string $id): void
    {
        if (!Auth::hasRole(['direccion', 'superadministrador']) && !Auth::can('expedientes.derivar')) {
            Session::setFlash('error', 'No tienes permiso para derivar expedientes.');
            $this->redirect("/expedientes/{$id}");
        }

        $this->validateCSRF();
        $expediente = $this->expedienteModel->find((int)$id);
        if (!$expediente) {
            Session::setFlash('error', 'Expediente no encontrado.');
            $this->redirect('/direccion');
        }

        $validator = Validator::make($_POST, [
            'oficina_destino_id' => 'required|integer',
            'indicaciones' => 'required'
        ]);

        if ($validator->fails()) {
            Session::setFlash('error', 'Debes seleccionar la oficina de destino e ingresar las indicaciones.');
            $this->redirect("/expedientes/{$id}");
        }

        $oficinaDestinoId = (int)$_POST['oficina_destino_id'];
        $prioridadId = !empty($_POST['prioridad_id']) ? (int)$_POST['prioridad_id'] : $expediente['prioridad_id'];
        $indicaciones = trim($_POST['indicaciones']);
        $userId = Auth::id();

        // Estado 5: DERIVADO
        $estadoDerivado = $this->estadoModel->findByCodigo('DERIVADO');
        $estadoDerivadoId = $estadoDerivado ? (int)$estadoDerivado['id'] : 5;

        $this->expedienteModel->beginTransaction();
        try {
            // Actualizar expediente
            $this->expedienteModel->update((int)$id, [
                'oficina_actual_id' => $oficinaDestinoId,
                'oficina_responsable_id' => $oficinaDestinoId,
                'estado_id' => $estadoDerivadoId,
                'prioridad_id' => $prioridadId
            ]);

            // Registrar movimiento DERIVACION
            $this->movimientoModel->registrarMovimiento(
                (int)$id,
                'DERIVACION',
                $expediente['oficina_actual_id'],
                $oficinaDestinoId,
                $userId,
                $expediente['estado_id'],
                $estadoDerivadoId,
                $indicaciones,
                true
            );

            // Notificación para la oficina de destino
            $db = Database::getConnection();
            $stmtNotif = $db->prepare("
                INSERT INTO notificaciones (oficina_id, titulo, mensaje, enlace)
                VALUES (:oficina_id, :titulo, :mensaje, :enlace)
            ");
            $stmtNotif->execute([
                ':oficina_id' => $oficinaDestinoId,
                ':titulo' => "Expediente Derivado: {$expediente['numero_expediente']}",
                ':mensaje' => "Dirección ha derivado un expediente a tu oficina con indicaciones.",
                ':enlace' => url("/expedientes/{$id}")
            ]);

            logAudit('DERIVAR_EXPEDIENTE', 'Dirección', (string)$id, "Expediente {$expediente['numero_expediente']} derivado a oficina ID: {$oficinaDestinoId}");
            $this->expedienteModel->commit();

            Session::setFlash('success', "Expediente {$expediente['numero_expediente']} derivado con éxito.");
            $this->redirect("/expedientes/{$id}");

        } catch (\Throwable $e) {
            $this->expedienteModel->rollBack();
            Session::setFlash('error', "Error al derivar el expediente: " . $e->getMessage());
            $this->redirect("/expedientes/{$id}");
        }
    }

    public function devolver(string $id): void
    {
        if (!Auth::hasRole(['direccion', 'superadministrador']) && !Auth::can('expedientes.devolver')) {
            Session::setFlash('error', 'Permiso denegado.');
            $this->redirect("/expedientes/{$id}");
        }

        $this->validateCSRF();
        $expediente = $this->expedienteModel->find((int)$id);
        if (!$expediente) {
            Session::setFlash('error', 'Expediente no encontrado.');
            $this->redirect('/expedientes');
        }

        $motivo = trim($_POST['motivo'] ?? '');
        if (empty($motivo)) {
            Session::setFlash('error', 'El motivo de devolución es estrictamente obligatorio.');
            $this->redirect("/expedientes/{$id}");
        }

        $userId = Auth::id();
        // Estado 10: DEVUELTO
        $estadoDevuelto = $this->estadoModel->findByCodigo('DEVUELTO');
        $estadoDevueltoId = $estadoDevuelto ? (int)$estadoDevuelto['id'] : 10;

        // Se devuelve a la oficina responsable anterior o a Mesa de Partes
        $oficinaDestinoId = $expediente['oficina_responsable_id'] ?: 2;

        $this->expedienteModel->beginTransaction();
        try {
            $this->expedienteModel->update((int)$id, [
                'oficina_actual_id' => $oficinaDestinoId,
                'estado_id' => $estadoDevueltoId,
                'observacion_publica' => 'Expediente devuelto por Dirección con observaciones para subsanación.'
            ]);

            $this->movimientoModel->registrarMovimiento(
                (int)$id,
                'DEVOLUCION',
                $expediente['oficina_actual_id'],
                $oficinaDestinoId,
                $userId,
                $expediente['estado_id'],
                $estadoDevueltoId,
                "Devolución por Dirección: {$motivo}",
                true
            );

            logAudit('DEVOLVER_EXPEDIENTE', 'Dirección', (string)$id, "Expediente {$expediente['numero_expediente']} devuelto.");
            $this->expedienteModel->commit();

            Session::setFlash('warning', "El expediente {$expediente['numero_expediente']} ha sido devuelto.");
            $this->redirect("/expedientes/{$id}");

        } catch (\Throwable $e) {
            $this->expedienteModel->rollBack();
            Session::setFlash('error', 'Error al devolver el expediente: ' . $e->getMessage());
            $this->redirect("/expedientes/{$id}");
        }
    }

    public function finalizar(string $id): void
    {
        if (!Auth::hasRole(['direccion', 'superadministrador']) && !Auth::can('expedientes.finalizar')) {
            Session::setFlash('error', 'Solo Dirección o Administrador pueden finalizar expedientes.');
            $this->redirect("/expedientes/{$id}");
        }

        $this->validateCSRF();
        $expediente = $this->expedienteModel->find((int)$id);
        if (!$expediente) {
            Session::setFlash('error', 'Expediente no encontrado.');
            $this->redirect('/expedientes');
        }

        $userId = Auth::id();
        $observacionPublica = trim($_POST['observacion_publica'] ?? 'Trámite finalizado exitosamente.');

        $estadoFinalizado = $this->estadoModel->findByCodigo('FINALIZADO');
        $estadoFinalizadoId = $estadoFinalizado ? (int)$estadoFinalizado['id'] : 13;

        $this->expedienteModel->beginTransaction();
        try {
            $this->expedienteModel->update((int)$id, [
                'estado_id' => $estadoFinalizadoId,
                'fecha_finalizacion' => date('Y-m-d H:i:s'),
                'observacion_publica' => $observacionPublica
            ]);

            $this->movimientoModel->registrarMovimiento(
                (int)$id,
                'FINALIZACION',
                $expediente['oficina_actual_id'],
                1,
                $userId,
                $expediente['estado_id'],
                $estadoFinalizadoId,
                "Trámite finalizado por Dirección: {$observacionPublica}",
                true
            );

            logAudit('FINALIZAR_EXPEDIENTE', 'Dirección', (string)$id, "Expediente {$expediente['numero_expediente']} concluido.");
            $this->expedienteModel->commit();

            Session::setFlash('success', "Expediente {$expediente['numero_expediente']} finalizado formalmente.");
            $this->redirect("/expedientes/{$id}");

        } catch (\Throwable $e) {
            $this->expedienteModel->rollBack();
            Session::setFlash('error', 'Error al finalizar expediente: ' . $e->getMessage());
            $this->redirect("/expedientes/{$id}");
        }
    }

    public function archivar(string $id): void
    {
        if (!Auth::hasRole(['direccion', 'superadministrador']) && !Auth::can('expedientes.archivar')) {
            Session::setFlash('error', 'Permiso denegado.');
            $this->redirect("/expedientes/{$id}");
        }

        $this->validateCSRF();
        $expediente = $this->expedienteModel->find((int)$id);
        if (!$expediente) {
            Session::setFlash('error', 'Expediente no encontrado.');
            $this->redirect('/expedientes');
        }

        $userId = Auth::id();
        $estadoArchivado = $this->estadoModel->findByCodigo('ARCHIVADO');
        $estadoArchivadoId = $estadoArchivado ? (int)$estadoArchivado['id'] : 14;

        $this->expedienteModel->beginTransaction();
        try {
            $this->expedienteModel->update((int)$id, [
                'estado_id' => $estadoArchivadoId
            ]);

            $this->movimientoModel->registrarMovimiento(
                (int)$id,
                'ARCHIVO',
                $expediente['oficina_actual_id'],
                1,
                $userId,
                $expediente['estado_id'],
                $estadoArchivadoId,
                'Expediente enviado a Archivo Central.',
                true
            );

            logAudit('ARCHIVAR_EXPEDIENTE', 'Dirección', (string)$id, "Expediente {$expediente['numero_expediente']} archivado.");
            $this->expedienteModel->commit();

            Session::setFlash('success', "Expediente {$expediente['numero_expediente']} archivado con éxito.");
            $this->redirect("/expedientes/{$id}");

        } catch (\Throwable $e) {
            $this->expedienteModel->rollBack();
            Session::setFlash('error', 'Error al archivar expediente: ' . $e->getMessage());
            $this->redirect("/expedientes/{$id}");
        }
    }
}
