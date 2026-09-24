<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Documento;
use App\Models\Estado;
use App\Models\Expediente;
use App\Models\Movimiento;
use App\Models\Oficina;
use App\Models\TipoTramite;

class ExpedienteController extends Controller
{
    private Expediente $expedienteModel;
    private Documento $documentoModel;
    private Movimiento $movimientoModel;
    private TipoTramite $tipoTramiteModel;
    private Oficina $oficinaModel;
    private Estado $estadoModel;

    public function __construct()
    {
        $this->expedienteModel = new Expediente();
        $this->documentoModel = new Documento();
        $this->movimientoModel = new Movimiento();
        $this->tipoTramiteModel = new TipoTramite();
        $this->oficinaModel = new Oficina();
        $this->estadoModel = new Estado();
    }

    public function index(): void
    {
        if (!Auth::can('expedientes.ver')) {
            Session::setFlash('error', 'No tienes permiso para consultar expedientes.');
            $this->redirect('/dashboard');
        }

        $page = (int)($_GET['page'] ?? 1);
        $filtros = [
            'buscar' => $_GET['buscar'] ?? '',
            'estado_id' => $_GET['estado_id'] ?? '',
            'oficina_id' => $_GET['oficina_id'] ?? '',
            'tipo_tramite_id' => $_GET['tipo_tramite_id'] ?? '',
            'prioridad_id' => $_GET['prioridad_id'] ?? '',
            'fecha_desde' => $_GET['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? ''
        ];

        try {
            $expedientesData = $this->expedienteModel->getExpedientesFiltrados($filtros, $page, 15);
        } catch (\Throwable $e) {
            error_log("Error al consultar expedientes en Bandeja General: " . $e->getMessage());
            Session::setFlash('error', 'Ocurrió un inconveniente al procesar los filtros de búsqueda. Intente con otros criterios.');
            $expedientesData = [
                'data' => [],
                'current_page' => 1,
                'per_page' => 15,
                'total_records' => 0,
                'total_pages' => 0
            ];
        }
        $estados = $this->estadoModel->allActivos();
        $oficinas = $this->oficinaModel->allActivas();
        $tiposTramite = $this->tipoTramiteModel->allActivosVirtuales();

        $db = Database::getConnection();
        $prioridades = $db->query("SELECT * FROM prioridades WHERE activo = 1 ORDER BY orden ASC")->fetchAll();

        $this->render('expedientes.index', [
            'pageTitle' => 'Bandeja General de Expedientes',
            'expedientes' => $expedientesData['data'],
            'pagination' => $expedientesData,
            'filtros' => $filtros,
            'estados' => $estados,
            'oficinas' => $oficinas,
            'tiposTramite' => $tiposTramite,
            'prioridades' => $prioridades
        ]);
    }

    public function create(): void
    {
        if (!Auth::can('expedientes.crear')) {
            Session::setFlash('error', 'No tienes permiso para registrar expedientes presenciales.');
            $this->redirect('/expedientes');
        }

        $tiposTramite = $this->tipoTramiteModel->where('activo = 1', [], 'nombre ASC');
        $db = Database::getConnection();
        $prioridades = $db->query("SELECT * FROM prioridades WHERE activo = 1 ORDER BY orden ASC")->fetchAll();

        $this->render('expedientes.crear', [
            'pageTitle' => 'Registro de Trámite Presencial (Mesa de Partes)',
            'tiposTramite' => $tiposTramite,
            'prioridades' => $prioridades
        ]);
    }

    public function store(): void
    {
        if (!Auth::can('expedientes.crear')) {
            Session::setFlash('error', 'Permiso denegado.');
            $this->redirect('/expedientes');
        }

        $this->validateCSRF();

        $validator = Validator::make($_POST, [
            'tipo_persona' => 'required|in:NATURAL,JURIDICA',
            'numero_documento' => 'required|max:20',
            'nombres' => 'required|max:100',
            'correo' => 'required|email|max:120',
            'telefono' => 'digits|min:6|max:15',
            'tipo_tramite_id' => 'required|integer',
            'asunto' => 'required|max:255',
            'folios' => 'required|integer|min:1',
            'prioridad_id' => 'required|integer',
            'archivo_principal' => 'file_required|file_mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|file_max:25'
        ]);

        if ($validator->fails()) {
            Session::set('_old_inputs', $_POST);
            Session::set('_validation_errors', $validator->errors());
            Session::setFlash('error', 'Por favor verifica los campos obligatorios y el archivo adjunto.');
            $this->redirect('/expedientes/crear');
        }

        $userId = Auth::id();
        $this->expedienteModel->beginTransaction();

        try {
            $numeroExpediente = $this->expedienteModel->generarNumeroExpediente();
            $codigoSeguimiento = $this->expedienteModel->generarCodigoSeguimiento();

            // Oficina Mesa de Partes (ID = 2)
            $oficinaMesaPartesId = 2;
            // Estado Inicial: REGISTRADO (ID = 2)
            $estadoRegistradoId = 2;

            $expedienteId = (int)$this->expedienteModel->insert([
                'numero_expediente' => $numeroExpediente,
                'codigo_seguimiento' => $codigoSeguimiento,
                'tipo_persona' => $_POST['tipo_persona'],
                'tipo_documento' => trim($_POST['tipo_documento'] ?? 'DNI'),
                'numero_documento' => trim($_POST['numero_documento']),
                'nombres' => trim($_POST['nombres']),
                'apellidos' => trim($_POST['apellidos'] ?? ''),
                'razon_social' => trim($_POST['razon_social'] ?? ''),
                'correo' => trim($_POST['correo']),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'direccion' => trim($_POST['direccion'] ?? ''),
                'tipo_tramite_id' => (int)$_POST['tipo_tramite_id'],
                'asunto' => trim($_POST['asunto']),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'folios' => (int)$_POST['folios'],
                'estado_id' => $estadoRegistradoId,
                'prioridad_id' => (int)$_POST['prioridad_id'],
                'oficina_actual_id' => $oficinaMesaPartesId,
                'oficina_responsable_id' => null,
                'usuario_responsable_id' => $userId,
                'fecha_ingreso' => date('Y-m-d H:i:s'),
                'es_virtual' => 0,
                'activo' => 1
            ]);

            // 1. Guardar archivo principal
            if (!empty($_FILES['archivo_principal']['tmp_name'])) {
                $this->documentoModel->guardarArchivo(
                    $_FILES['archivo_principal'],
                    $expedienteId,
                    null,
                    true,
                    true,
                    $userId
                );
            }

            // 2. Guardar archivos anexos opcionales
            if (!empty($_FILES['anexos']['name'][0])) {
                $totalAnexos = count($_FILES['anexos']['name']);
                for ($i = 0; $i < $totalAnexos; $i++) {
                    if ($_FILES['anexos']['error'][$i] === UPLOAD_ERR_OK) {
                        $singleFile = [
                            'name' => $_FILES['anexos']['name'][$i],
                            'type' => $_FILES['anexos']['type'][$i],
                            'tmp_name' => $_FILES['anexos']['tmp_name'][$i],
                            'error' => $_FILES['anexos']['error'][$i],
                            'size' => $_FILES['anexos']['size'][$i]
                        ];
                        $this->documentoModel->guardarArchivo(
                            $singleFile,
                            $expedienteId,
                            null,
                            false,
                            false,
                            $userId
                        );
                    }
                }
            }

            // 3. Registrar Movimiento inicial REGISTRO
            $this->movimientoModel->registrarMovimiento(
                $expedienteId,
                'REGISTRO',
                null,
                $oficinaMesaPartesId,
                $userId,
                null,
                $estadoRegistradoId,
                'Expediente registrado en Mesa de Partes (Ventanilla Presencial).',
                true
            );

            // 4. Registrar Auditoría
            logAudit('REGISTRO_EXPEDIENTE', 'Expedientes', (string)$expedienteId, "Expediente {$numeroExpediente} registrado por operador.");

            $this->expedienteModel->commit();

            Session::setFlash('success', "¡Expediente {$numeroExpediente} registrado con éxito! Código de seguimiento: {$codigoSeguimiento}");
            $this->redirect("/expedientes/{$expedienteId}");

        } catch (\Throwable $e) {
            $this->expedienteModel->rollBack();
            Session::setFlash('error', "Error al registrar el expediente: " . $e->getMessage());
            $this->redirect('/expedientes/crear');
        }
    }

    public function show(string $id): void
    {
        if (!Auth::can('expedientes.ver')) {
            Session::setFlash('error', 'Permiso denegado.');
            $this->redirect('/expedientes');
        }

        $expediente = $this->expedienteModel->findDetallado((int)$id);
        if (!$expediente) {
            Session::setFlash('error', 'Expediente no encontrado.');
            $this->redirect('/expedientes');
        }

        $documentos = $this->documentoModel->getDocumentosExpediente((int)$id);
        $movimientos = $this->movimientoModel->getLineaDeTiempo((int)$id);
        $oficinas = $this->oficinaModel->allActivas();

        // Obtener solicitudes internas del expediente si existen
        $db = Database::getConnection();
        $solicitudes = $db->prepare("
            SELECT s.*, 
                   os.nombre AS solicitante_nombre, os.sigla AS solicitante_sigla,
                   op.nombre AS proveedora_nombre, op.sigla AS proveedora_sigla,
                   CONCAT(u.nombres, ' ', u.apellidos) AS solicitante_usuario,
                   r.respuesta, r.created_at AS fecha_respuesta_detalle
            FROM solicitudes_internas s
            INNER JOIN oficinas os ON s.oficina_solicitante_id = os.id
            INNER JOIN oficinas op ON s.oficina_proveedora_id = op.id
            INNER JOIN usuarios u ON s.usuario_solicitante_id = u.id
            LEFT JOIN solicitudes_internas_respuestas r ON s.id = r.solicitud_interna_id
            WHERE s.expediente_id = :id
            ORDER BY s.id DESC
        ");
        $solicitudes->execute([':id' => $id]);
        $solicitudesInternas = $solicitudes->fetchAll(\PDO::FETCH_ASSOC);

        $this->render('expedientes.show', [
            'pageTitle' => 'Expediente: ' . $expediente['numero_expediente'],
            'expediente' => $expediente,
            'documentos' => $documentos,
            'movimientos' => $movimientos,
            'oficinas' => $oficinas,
            'solicitudesInternas' => $solicitudesInternas
        ]);
    }

    public function enviarDireccion(string $id): void
    {
        if (!Auth::can('expedientes.crear') && !Auth::hasRole(['mesa-de-partes', 'superadministrador'])) {
            Session::setFlash('error', 'No tienes permiso para enviar expedientes a Dirección.');
            $this->redirect("/expedientes/{$id}");
        }

        $this->validateCSRF();
        $expediente = $this->expedienteModel->find((int)$id);
        if (!$expediente) {
            Session::setFlash('error', 'Expediente no encontrado.');
            $this->redirect('/expedientes');
        }

        $oficinaDireccionId = 1;
        $estadoEnviadoId = 3; // ENVIADO_DIRECCION
        $userId = Auth::id();
        $observacion = trim($_POST['observacion'] ?? 'Expediente remitido a Dirección para derivación técnica.');

        $this->expedienteModel->beginTransaction();
        try {
            $this->expedienteModel->update((int)$id, [
                'oficina_actual_id' => $oficinaDireccionId,
                'estado_id' => $estadoEnviadoId
            ]);

            $this->movimientoModel->registrarMovimiento(
                (int)$id,
                'ENVIO_DIRECCION',
                $expediente['oficina_actual_id'],
                $oficinaDireccionId,
                $userId,
                $expediente['estado_id'],
                $estadoEnviadoId,
                $observacion,
                true
            );

            // Notificación para Dirección (Oficina 1)
            $db = Database::getConnection();
            $stmtNotif = $db->prepare("
                INSERT INTO notificaciones (oficina_id, titulo, mensaje, enlace)
                VALUES (:oficina_id, :titulo, :mensaje, :enlace)
            ");
            $stmtNotif->execute([
                ':oficina_id' => $oficinaDireccionId,
                ':titulo' => "Nuevo Expediente Recibido: {$expediente['numero_expediente']}",
                ':mensaje' => "Mesa de Partes ha enviado el expediente {$expediente['numero_expediente']} a Dirección.",
                ':enlace' => url("/expedientes/{$id}")
            ]);

            logAudit('ENVIO_DIRECCION', 'Expedientes', (string)$id, "Expediente {$expediente['numero_expediente']} enviado a Dirección.");
            $this->expedienteModel->commit();

            Session::setFlash('success', "El expediente {$expediente['numero_expediente']} fue enviado a Dirección correctamente.");
            $this->redirect("/expedientes/{$id}");

        } catch (\Throwable $e) {
            $this->expedienteModel->rollBack();
            Session::setFlash('error', 'Error al enviar a Dirección: ' . $e->getMessage());
            $this->redirect("/expedientes/{$id}");
        }
    }

    public function imprimirCargo(string $id): void
    {
        if (!Auth::check()) {
            Session::setFlash('error', 'Debes iniciar sesión.');
            $this->redirect('/login');
        }

        $expediente = $this->expedienteModel->findDetallado((int)$id);
        if (!$expediente) {
            Session::setFlash('error', 'Expediente no encontrado.');
            $this->redirect('/expedientes');
        }

        $documentoPrincipal = $this->documentoModel->first("expediente_id = :id AND es_principal = 1", [':id' => $id]);

        $this->render('expedientes.cargo', [
            'pageTitle' => 'Cargo de Recepción - ' . $expediente['numero_expediente'],
            'expediente' => $expediente,
            'documento' => $documentoPrincipal
        ], 'none');
    }

    public function descargarDocumento(string $id): void
    {
        if (!Auth::check()) {
            Session::setFlash('error', 'Debes iniciar sesión para descargar documentos internos.');
            $this->redirect('/login');
        }

        $documento = $this->documentoModel->find((int)$id);
        if (!$documento) {
            http_response_code(404);
            die("Documento no encontrado.");
        }

        $expediente = $this->expedienteModel->find((int)$documento['expediente_id']);
        if (!$expediente) {
            http_response_code(404);
            die("Documento no encontrado.");
        }

        $user = Auth::user();
        $isSuperAdmin = Auth::hasRole(['superadministrador']);
        $isMesaPartes = Auth::hasRole(['mesa-de-partes']);
        $isAssigned = ($expediente['usuario_responsable_id'] == Auth::id());
        $isSameOffice = ($expediente['oficina_actual_id'] == ($user['oficina_id'] ?? 0));

        if (!$isSuperAdmin && !$isMesaPartes && !$isAssigned && !$isSameOffice) {
            http_response_code(403);
            die("No tienes permiso para descargar este documento.");
        }

        $rutaAbsoluta = dirname(__DIR__, 2) . '/' . ltrim($documento['ruta'], '/');
        $baseDir = realpath(dirname(__DIR__, 2) . '/storage/documents');
        $realFile = realpath($rutaAbsoluta);
        if (!$realFile || !file_exists($realFile) || ($baseDir && !str_starts_with($realFile, $baseDir))) {
            http_response_code(404);
            die("Archivo no disponible o ruta inválida.");
        }

        logAudit('DESCARGAR_DOCUMENTO', 'Documentos', (string)$id, "Descarga de archivo: {$documento['nombre_original']}");
        Response::download($realFile, $documento['nombre_original'], $documento['mime_type']);
    }

    public function verDocumento(string $id): void
    {
        if (!Auth::check()) {
            http_response_code(401);
            die("Debes iniciar sesión para visualizar documentos internos.");
        }

        $documento = $this->documentoModel->find((int)$id);
        if (!$documento) {
            http_response_code(404);
            die("Documento no encontrado.");
        }

        $expediente = $this->expedienteModel->find((int)$documento['expediente_id']);
        if (!$expediente) {
            http_response_code(404);
            die("Expediente no encontrado.");
        }

        $user = Auth::user();
        $isSuperAdmin = Auth::hasRole(['superadministrador']);
        $isMesaPartes = Auth::hasRole(['mesa-de-partes']);
        $isAssigned = ($expediente['usuario_responsable_id'] == Auth::id());
        $isSameOffice = ($expediente['oficina_actual_id'] == ($user['oficina_id'] ?? 0));

        if (!$isSuperAdmin && !$isMesaPartes && !$isAssigned && !$isSameOffice) {
            http_response_code(403);
            die("No tienes permiso para visualizar este documento.");
        }

        $rutaAbsoluta = dirname(__DIR__, 2) . '/' . ltrim($documento['ruta'], '/');
        $baseDir = realpath(dirname(__DIR__, 2) . '/storage/documents');
        $realFile = realpath($rutaAbsoluta);

        if (!$realFile || !file_exists($realFile) || ($baseDir && !str_starts_with($realFile, $baseDir))) {
            http_response_code(404);
            die("Archivo no disponible o ruta inválida.");
        }

        logAudit('VISUALIZAR_DOCUMENTO', 'Documentos', (string)$id, "Visualización en línea de archivo: {$documento['nombre_original']}");
        Response::inline($realFile, $documento['nombre_original'], $documento['mime_type']);
    }
}
