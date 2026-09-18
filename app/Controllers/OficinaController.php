<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Oficina;

class OficinaController extends Controller
{
    private Oficina $oficinaModel;

    public function __construct()
    {
        $this->oficinaModel = new Oficina();
    }

    public function index(): void
    {
        if (!Auth::can('oficinas.ver')) {
            Session::setFlash('error', 'No tienes permiso para ver oficinas.');
            $this->redirect('/dashboard');
        }

        $db = Database::getConnection();
        $sql = "SELECT o.*, 
                       (SELECT COUNT(*) FROM usuarios u WHERE u.oficina_id = o.id) AS total_usuarios,
                       (SELECT COUNT(*) FROM expedientes e WHERE e.oficina_actual_id = o.id) AS expedientes_actuales
                FROM oficinas o
                ORDER BY o.orden ASC, o.nombre ASC";
        $oficinas = $db->query($sql)->fetchAll();

        $this->render('oficinas.index', [
            'pageTitle' => 'Oficinas Institucionales',
            'oficinas' => $oficinas
        ]);
    }

    public function store(): void
    {
        if (!Auth::can('oficinas.gestionar')) {
            Session::setFlash('error', 'No tienes permiso para crear oficinas.');
            $this->redirect('/oficinas');
        }

        $this->validateCSRF();

        $validator = Validator::make($_POST, [
            'nombre' => 'required|max:150',
            'sigla' => 'required|max:20',
            'responsable' => 'max:150',
            'correo' => 'email|max:120',
            'orden' => 'integer'
        ]);

        if ($validator->fails()) {
            Session::setFlash('error', 'Por favor verifica los datos ingresados para la oficina.');
            $this->redirect('/oficinas');
        }

        $nuevoId = $this->oficinaModel->insert([
            'nombre' => trim($_POST['nombre']),
            'sigla' => strtoupper(trim($_POST['sigla'])),
            'responsable' => trim($_POST['responsable'] ?? ''),
            'correo' => trim($_POST['correo'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'orden' => (int)($_POST['orden'] ?? 0),
            'activo' => 1
        ]);

        logAudit('CREAR_OFICINA', 'Oficinas', (string)$nuevoId, "Oficina creada: {$_POST['nombre']}");
        Session::setFlash('success', 'Oficina creada exitosamente.');
        $this->redirect('/oficinas');
    }

    public function update(string $id): void
    {
        if (!Auth::can('oficinas.gestionar')) {
            Session::setFlash('error', 'No tienes permiso para editar oficinas.');
            $this->redirect('/oficinas');
        }

        $this->validateCSRF();
        $oficina = $this->oficinaModel->find((int)$id);
        if (!$oficina) {
            Session::setFlash('error', 'Oficina no encontrada.');
            $this->redirect('/oficinas');
        }

        $validator = Validator::make($_POST, [
            'nombre' => 'required|max:150',
            'sigla' => 'required|max:20',
            'responsable' => 'max:150',
            'orden' => 'integer'
        ]);

        if ($validator->fails()) {
            Session::setFlash('error', 'Corrige los errores del formulario.');
            $this->redirect('/oficinas');
        }

        $this->oficinaModel->update((int)$id, [
            'nombre' => trim($_POST['nombre']),
            'sigla' => strtoupper(trim($_POST['sigla'])),
            'responsable' => trim($_POST['responsable'] ?? ''),
            'correo' => trim($_POST['correo'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'orden' => (int)($_POST['orden'] ?? 0),
            'activo' => isset($_POST['activo']) ? 1 : 0
        ]);

        logAudit('EDITAR_OFICINA', 'Oficinas', (string)$id, "Oficina actualizada: {$_POST['nombre']}");
        Session::setFlash('success', 'Datos de la oficina actualizados correctamente.');
        $this->redirect('/oficinas');
    }

    public function toggleEstado(string $id): void
    {
        if (!Auth::can('oficinas.gestionar')) {
            Session::setFlash('error', 'No tienes permiso para modificar oficinas.');
            $this->redirect('/oficinas');
        }

        $this->validateCSRF();
        $oficina = $this->oficinaModel->find((int)$id);
        if (!$oficina) {
            Session::setFlash('error', 'Oficina no encontrada.');
            $this->redirect('/oficinas');
        }

        $nuevoEstado = ((int)$oficina['activo'] === 1) ? 0 : 1;
        $this->oficinaModel->update((int)$id, ['activo' => $nuevoEstado]);

        $accion = $nuevoEstado === 1 ? 'ACTIVAR_OFICINA' : 'DESACTIVAR_OFICINA';
        logAudit($accion, 'Oficinas', (string)$id, "Estado de oficina {$oficina['sigla']} cambiado a {$nuevoEstado}");

        $msg = $nuevoEstado === 1 ? 'Oficina reactivada correctamente.' : 'Oficina desactivada lógicamente (se preserva historial).';
        Session::setFlash('success', $msg);
        $this->redirect('/oficinas');
    }
}
