<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Response;
use App\Core\Session;

class PermissionMiddleware
{
    private string $permission;

    public function __construct(string $permission = '')
    {
        $this->permission = $permission;
    }

    public static function require(string $permission): self
    {
        return new self($permission);
    }

    public function handle(): void
    {
        if (!Auth::check()) {
            Response::redirect('/login');
        }

        if ($this->permission && !Auth::can($this->permission)) {
            Session::setFlash('error', 'No tienes permisos suficientes para realizar esta acción.');
            Response::redirect('/dashboard');
        }
    }
}
