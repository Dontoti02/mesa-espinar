<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Oficina;
use App\Models\TipoTramite;

class TipoTramiteController extends Controller
{
    private TipoTramite $tipoTramiteModel;
    private Oficina $oficinaModel;

    public function __construct()
    {
        $this->tipoTramiteModel = new TipoTramite();
        $this->oficinaModel = new Oficina();
    }

    public function index(): void
    {
        if (!Auth::can('tramites.ver')) {
            Session::setFlash('error', 'No tienes permiso para ver los tipos de trámite.');
            $this->redirect('/dashboard');
        }

        $tramites = $this->tipoTramiteModel->allConOficina();
        $oficinas = $this->oficinaModel->allActivas();

        $this->render('tipos_tramite.index', [
            'pageTitle' => 'Tipos de Trámite Institucionales',
            'tramites' => $tramites,
            'oficinas' => $oficinas
        ]);
    }

    public function store(): void
    {
        if (!Auth::can('tramites.gestionar')) {
            Session::setFlash('error', 'No tienes permiso para crear tipos de trámite.');
            $this->redirect('/tipos-tramite');
        }

        $this->validateCSRF();

        $validator = Validator::make($_POST, [
            'codigo' => 'required|max:20|unique:tipos_tramite,codigo',
            'nombre' => 'required|max:150',
            'plazo_referencial_dias' => 'required|integer',
            'monto' => 'numeric'
        ]);

        if ($validator->fails()) {
            Session::setFlash('error', 'Por favor verifica los campos obligatorios del trámite.');
            $this->redirect('/tipos-tramite');
        }

        $nuevoId = $this->tipoTramiteModel->insert([
            'codigo' => strtoupper(trim($_POST['codigo'])),
            'nombre' => trim($_POST['nombre']),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'oficina_sugerida_id' => !empty($_POST['oficina_sugerida_id']) ? (int)$_POST['oficina_sugerida_id'] : null,
            'plazo_referencial_dias' => (int)$_POST['plazo_referencial_dias'],
            'requisitos' => trim($_POST['requisitos'] ?? ''),
            'instrucciones' => trim($_POST['instrucciones'] ?? ''),
            'permite_virtual' => isset($_POST['permite_virtual']) ? 1 : 0,
            'requiere_pago' => isset($_POST['requiere_pago']) ? 1 : 0,
            'monto' => isset($_POST['requiere_pago']) ? (float)($_POST['monto'] ?? 0) : 0.00,
            'activo' => 1
        ]);

        logAudit('CREAR_TIPO_TRAMITE', 'Trámites', (string)$nuevoId, "Tipo de trámite creado: {$_POST['nombre']}");
        Session::setFlash('success', 'Tipo de trámite registrado con éxito.');
        $this->redirect('/tipos-tramite');
    }

    public function update(string $id): void
    {
        if (!Auth::can('tramites.gestionar')) {
            Session::setFlash('error', 'No tienes permiso para editar tipos de trámite.');
            $this->redirect('/tipos-tramite');
        }

        $this->validateCSRF();
        $tramite = $this->tipoTramiteModel->find((int)$id);
        if (!$tramite) {
            Session::setFlash('error', 'Trámite no encontrado.');
            $this->redirect('/tipos-tramite');
        }

        $validator = Validator::make($_POST, [
            'codigo' => "required|max:20|unique:tipos_tramite,codigo,{$id}",
            'nombre' => 'required|max:150',
            'plazo_referencial_dias' => 'required|integer',
            'monto' => 'numeric'
        ]);

        if ($validator->fails()) {
            Session::setFlash('error', 'Verifica los campos ingresados.');
            $this->redirect('/tipos-tramite');
        }

        $this->tipoTramiteModel->update((int)$id, [
            'codigo' => strtoupper(trim($_POST['codigo'])),
            'nombre' => trim($_POST['nombre']),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'oficina_sugerida_id' => !empty($_POST['oficina_sugerida_id']) ? (int)$_POST['oficina_sugerida_id'] : null,
            'plazo_referencial_dias' => (int)$_POST['plazo_referencial_dias'],
            'requisitos' => trim($_POST['requisitos'] ?? ''),
            'instrucciones' => trim($_POST['instrucciones'] ?? ''),
            'permite_virtual' => isset($_POST['permite_virtual']) ? 1 : 0,
            'requiere_pago' => isset($_POST['requiere_pago']) ? 1 : 0,
            'monto' => isset($_POST['requiere_pago']) ? (float)($_POST['monto'] ?? 0) : 0.00,
            'activo' => isset($_POST['activo']) ? 1 : 0
        ]);

        logAudit('EDITAR_TIPO_TRAMITE', 'Trámites', (string)$id, "Tipo de trámite modificado: {$_POST['nombre']}");
        Session::setFlash('success', 'Tipo de trámite actualizado correctamente.');
        $this->redirect('/tipos-tramite');
    }

    public function toggleEstado(string $id): void
    {
        if (!Auth::can('tramites.gestionar')) {
            Session::setFlash('error', 'Permiso denegado.');
            $this->redirect('/tipos-tramite');
        }

        $this->validateCSRF();
        $tramite = $this->tipoTramiteModel->find((int)$id);
        if (!$tramite) {
            Session::setFlash('error', 'Trámite no encontrado.');
            $this->redirect('/tipos-tramite');
        }

        $nuevo = ((int)$tramite['activo'] === 1) ? 0 : 1;
        $this->tipoTramiteModel->update((int)$id, ['activo' => $nuevo]);

        logAudit('ESTADO_TIPO_TRAMITE', 'Trámites', (string)$id, "Estado cambiado a {$nuevo}");
        Session::setFlash('success', 'Estado del tipo de trámite modificado.');
        $this->redirect('/tipos-tramite');
    }
}
