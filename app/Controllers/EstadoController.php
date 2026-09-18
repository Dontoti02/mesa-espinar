<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Estado;

class EstadoController extends Controller
{
    private Estado $estadoModel;

    public function __construct()
    {
        $this->estadoModel = new Estado();
    }

    public function index(): void
    {
        if (!Auth::can('configuracion.ver')) {
            Session::setFlash('error', 'No tienes permiso para ver los estados.');
            $this->redirect('/dashboard');
        }

        $estados = $this->estadoModel->all('orden ASC, id ASC');

        $this->render('estados.index', [
            'pageTitle' => 'Estados de Expediente',
            'estados' => $estados
        ]);
    }

    public function update(string $id): void
    {
        if (!Auth::can('configuracion.editar')) {
            Session::setFlash('error', 'No tienes permiso para modificar estados.');
            $this->redirect('/estados');
        }

        $this->validateCSRF();
        $estado = $this->estadoModel->find((int)$id);
        if (!$estado) {
            Session::setFlash('error', 'Estado no encontrado.');
            $this->redirect('/estados');
        }

        $validator = Validator::make($_POST, [
            'nombre' => 'required|max:80',
            'color' => 'required|max:20',
            'orden' => 'integer'
        ]);

        if ($validator->fails()) {
            Session::setFlash('error', 'Verifica los valores ingresados.');
            $this->redirect('/estados');
        }

        $this->estadoModel->update((int)$id, [
            'nombre' => trim($_POST['nombre']),
            'color' => trim($_POST['color']),
            'icono' => trim($_POST['icono'] ?? 'bi-circle'),
            'orden' => (int)($_POST['orden'] ?? 0),
            'es_publico' => isset($_POST['es_publico']) ? 1 : 0
        ]);

        logAudit('EDITAR_ESTADO', 'Estados', (string)$id, "Estado {$estado['codigo']} actualizado");
        Session::setFlash('success', 'Estado de expediente actualizado correctamente.');
        $this->redirect('/estados');
    }
}
