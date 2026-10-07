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
    }
}
