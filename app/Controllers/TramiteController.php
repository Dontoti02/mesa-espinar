<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Documento;
use App\Models\Estado;
use App\Models\Expediente;
use App\Models\Movimiento;
use App\Models\TipoTramite;

class TramiteController extends Controller
{
    private Expediente $expedienteModel;
    private Documento $documentoModel;
    private Movimiento $movimientoModel;
    private TipoTramite $tipoTramiteModel;
    private Estado $estadoModel;

    public function __construct()
    {
        $this->expedienteModel = new Expediente();
        $this->documentoModel = new Documento();
        $this->movimientoModel = new Movimiento();
        $this->tipoTramiteModel = new TipoTramite();
        $this->estadoModel = new Estado();
    }

    public function index(): void
    {
        // Generar captcha matemático anti-spam
        $n1 = rand(2, 9);
        $n2 = rand(1, 8);
        Session::set('_captcha_result', $n1 + $n2);

        $tramites = $this->tipoTramiteModel->allActivosVirtuales();

        $this->render('public.tramite', [
            'pageTitle' => 'Presentación de Trámite Virtual',
            'tramites' => $tramites,
            'captchaQuestion' => "¿Cuánto es {$n1} + {$n2}?"
        ], 'public');
    }

    public function store(): void
    {
        $this->validateCSRF();

        // 1. Validar Captcha
        $captchaIngresado = (int)($_POST['captcha'] ?? 0);
        $captchaEsperado = (int)Session::get('_captcha_result', -1);
        if ($captchaIngresado !== $captchaEsperado) {
            Session::set('_old_inputs', $_POST);
            Session::setFlash('error', 'El código de seguridad anti-spam (captcha) es incorrecto. Por favor intenta de nuevo.');
            $this->redirect('/tramite');
        }

        // 2. Validar Declaración Jurada
        if (!isset($_POST['declaracion_veracidad'])) {
            Session::set('_old_inputs', $_POST);
            Session::setFlash('error', 'Debes aceptar la declaración jurada de veracidad de la información.');
            $this->redirect('/tramite');
        }

        // 3. Validaciones de formulario
        $validator = Validator::make($_POST, [
            'tipo_persona' => 'required|in:NATURAL,JURIDICA',
            'numero_documento' => 'required|max:20',
            'nombres' => 'required|max:100',
            'correo' => 'required|email|max:120',
            'telefono' => 'required|digits|min:6|max:15',
            'tipo_tramite_id' => 'required|integer',
            'asunto' => 'required|max:255',
            'folios' => 'required|integer|min:1',
            'archivo_principal' => 'file_required|file_mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|file_max:25'
        ]);

        if ($validator->fails()) {
            Session::set('_old_inputs', $_POST);
            Session::set('_validation_errors', $validator->errors());
            Session::setFlash('error', 'Corrige los errores señalados en el formulario.');
            $this->redirect('/tramite');
        }

        $this->expedienteModel->beginTransaction();

        try {
            $numeroExpediente = $this->expedienteModel->generarNumeroExpediente();
            $codigoSeguimiento = $this->expedienteModel->generarCodigoSeguimiento();

            // Estado Inicial: RECIBIDO (ID 1)
            $estadoRecibido = $this->estadoModel->findByCodigo('RECIBIDO');
            $estadoRecibidoId = $estadoRecibido ? (int)$estadoRecibido['id'] : 1;

            // Oficina inicial: Mesa de Partes (ID 2)
            $oficinaMesaPartesId = 2;

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
                'estado_id' => $estadoRecibidoId,
                'prioridad_id' => 1, // NORMAL por defecto para público
                'oficina_actual_id' => $oficinaMesaPartesId,
                'oficina_responsable_id' => null,
                'usuario_responsable_id' => null,
                'fecha_ingreso' => date('Y-m-d H:i:s'),
                'es_virtual' => 1,
                'activo' => 1
            ]);

            // Guardar archivo principal (es_publico = 1 para que el ciudadano pueda acceder a su propio cargo)
            if (!empty($_FILES['archivo_principal']['tmp_name'])) {
                $this->documentoModel->guardarArchivo(
                    $_FILES['archivo_principal'],
                    $expedienteId,
                    null,
                    true,
                    true,
                    null
                );
            }

            // Guardar anexos
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
                            null
                        );
                    }
                }
            }

            // Registrar movimiento REGISTRO
            $this->movimientoModel->registrarMovimiento(
                $expedienteId,
                'REGISTRO',
                null,
                $oficinaMesaPartesId,
                null,
                null,
                $estadoRecibidoId,
                'Trámite ingresado virtualmente por el ciudadano a través del portal institucional.',
                true
            );

            // Notificación interna para Mesa de Partes
            $db = Database::getConnection();
            $stmtNotif = $db->prepare("
                INSERT INTO notificaciones (oficina_id, titulo, mensaje, enlace)
                VALUES (:oficina_id, :titulo, :mensaje, :enlace)
            ");
            $stmtNotif->execute([
                ':oficina_id' => $oficinaMesaPartesId,
                ':titulo' => "Nuevo Trámite Virtual: {$numeroExpediente}",
                ':mensaje' => "El solicitante {$_POST['nombres']} ha presentado una solicitud virtual.",
                ':enlace' => url("/expedientes/{$expedienteId}")
            ]);

            logAudit('TRAMITE_VIRTUAL_REGISTRADO', 'Portal Público', (string)$expedienteId, "Expediente {$numeroExpediente} ingresado por la web.");

            $this->expedienteModel->commit();

            $this->redirect('/tramite/exito/' . urlencode($codigoSeguimiento));

        } catch (\Throwable $e) {
            $this->expedienteModel->rollBack();
            Session::setFlash('error', "Ocurrió un problema al procesar tu trámite: " . $e->getMessage());
            $this->redirect('/tramite');
        }
    }

    public function exito(string $codigo): void
    {
        $expediente = $this->expedienteModel->findByCodigoSeguimiento($codigo);

        if (!$expediente) {
            Session::setFlash('error', 'Expediente no encontrado.');
            $this->redirect('/tramite');
        }

        $this->render('public.exito', [
            'pageTitle' => 'Trámite Registrado Exitosamente',
            'expediente' => $expediente
        ], 'public');
    }

    public function descargarCargo(string $codigo): void
    {
        $expediente = $this->expedienteModel->findByCodigoSeguimiento($codigo);

        if (!$expediente) {
            die("Expediente no encontrado.");
        }

        $expedienteDetallado = $this->expedienteModel->findDetallado((int)$expediente['id']);

        $this->render('expedientes.cargo', [
            'pageTitle' => 'Cargo de Recepción - ' . $expediente['numero_expediente'],
            'expediente' => $expedienteDetallado
        ], 'none');
    }
}
