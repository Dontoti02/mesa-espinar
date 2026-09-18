<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Response;
use App\Core\Session;

class AuthMiddleware
{
    public function handle(): void
    {
        if (!Auth::check()) {
            Session::setFlash('warning', 'Debes iniciar sesión para acceder al sistema.');
            Response::redirect('/login');
        }

        $user = Auth::user();
        if ($user && ($user['debe_cambiar_password'] ?? 0) == 1) {
            $currentUri = $_SERVER['REQUEST_URI'] ?? '';
            if (!str_contains($currentUri, '/cambiar-password') && !str_contains($currentUri, '/logout')) {
                Session::setFlash('warning', 'Por motivos de seguridad, debes actualizar tu contraseña antes de continuar.');
                Response::redirect('/cambiar-password');
            }
        }
    }
}
