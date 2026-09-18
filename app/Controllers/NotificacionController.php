<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Models\Notificacion;

class NotificacionController extends Controller
{
    private Notificacion $notificacionModel;

    public function __construct()
    {
        $this->notificacionModel = new Notificacion();
    }

    public function index(): void
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }

        $userId = Auth::id();
        $oficinaId = Auth::officeId();

        $notificaciones = $this->notificacionModel->getNotificacionesUsuario($userId, $oficinaId, 50);

        $this->render('notificaciones.index', [
            'pageTitle' => 'Centro de Notificaciones',
            'notificaciones' => $notificaciones
        ]);
    }

    public function marcarTodas(): void
    {
        $this->validateCSRF();
        $userId = Auth::id();
        $oficinaId = Auth::officeId();

        $this->notificacionModel->marcarTodasLeidas($userId, $oficinaId);
        Session::setFlash('success', 'Todas las notificaciones han sido marcadas como leídas.');
        $this->redirect('/notificaciones');
    }

    public function marcarLeida(string $id): void
    {
        $this->validateCSRF();
        $this->notificacionModel->update((int)$id, [
            'leido' => 1,
            'fecha_leido' => date('Y-m-d H:i:s')
        ]);
        $this->redirect('/notificaciones');
    }
}
