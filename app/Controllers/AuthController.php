<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Usuario;

class AuthController extends Controller
{
    private Usuario $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
    }

    public function showLogin(): void
    {
        $this->render('auth.login', [
            'pageTitle' => 'Iniciar Sesión | ' . config('institucion_sigla', 'Mesa de Partes')
        ], 'auth');
    }

    public function login(): void
    {
        $this->validateCSRF();

        $identificador = trim($_POST['identificador'] ?? '');
        $password = $_POST['password'] ?? '';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        // 1. Validaciones básicas
        $validator = Validator::make($_POST, [
            'identificador' => 'required',
            'password' => 'required'
        ], [
            'identificador' => 'Ingresa tu usuario o correo institucional.',
            'password' => 'Ingresa tu contraseña.'
        ]);

        if ($validator->fails()) {
            Session::set('_old_inputs', ['identificador' => $identificador]);
            Session::setFlash('error', $validator->firstError('identificador') ?: $validator->firstError('password'));
            $this->redirect('/login');
        }

        // 2. Control de fuerza bruta (Sección 23 y 27)
        if ($this->isIpBlocked($ip, $identificador)) {
            Session::setFlash('error', 'Demasiados intentos fallidos. Tu acceso ha sido bloqueado temporalmente por 15 minutos.');
            $this->redirect('/login');
        }

        // 3. Búsqueda de usuario
        $user = $this->usuarioModel->findByLogin($identificador);

        if (!$user || !password_verify($password, $user['password'])) {
            $this->recordFailedAttempt($ip, $identificador);
            logAudit('LOGIN_FALLIDO', 'Seguridad', null, "Intento fallido para: {$identificador}");
            Session::set('_old_inputs', ['identificador' => $identificador]);
            Session::setFlash('error', 'Las credenciales ingresadas son incorrectas.');
            $this->redirect('/login');
        }

        // 4. Verificar si el usuario está activo
        if ((int)$user['estado'] !== 1) {
            logAudit('LOGIN_RECHAZADO', 'Seguridad', (string)$user['id'], 'Usuario inactivo intentó acceder.');
            Session::setFlash('error', 'Tu cuenta se encuentra inactiva. Comunícate con el Administrador.');
            $this->redirect('/login');
        }

        // 5. Login exitoso
        $this->clearFailedAttempts($ip, $identificador);
        Auth::login($user);
        logAudit('LOGIN_EXITOSO', 'Autenticación', (string)$user['id'], "Inicio de sesión de {$user['usuario']}");

        Session::setFlash('success', "¡Bienvenido/a, {$user['nombres']}!");
        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        $this->validateCSRF();
        $user = Auth::user();
        if ($user) {
            logAudit('LOGOUT', 'Autenticación', (string)$user['id'], "Cierre de sesión de {$user['usuario']}");
        }
        Auth::logout();
        Session::setFlash('info', 'Has cerrado sesión correctamente.');
        $this->redirect('/login');
    }

    private function isIpBlocked(string $ip, string $identificador): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT intentos, bloqueado_hasta
            FROM intentos_login
            WHERE ip = :ip AND identificador = :id
            LIMIT 1
        ");
        $stmt->execute([':ip' => $ip, ':id' => $identificador]);
        $row = $stmt->fetch();

        if ($row && !empty($row['bloqueado_hasta'])) {
            if (strtotime($row['bloqueado_hasta']) > time()) {
                return true;
            }
        }
        return false;
    }

    private function recordFailedAttempt(string $ip, string $identificador): void
    {
        $maxAttempts = (int)($_ENV['LOGIN_MAX_ATTEMPTS'] ?? 5);
        $lockoutMinutes = (int)($_ENV['LOGIN_LOCKOUT_MINUTES'] ?? 15);

        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT id, intentos
            FROM intentos_login
            WHERE ip = :ip AND identificador = :id
            LIMIT 1
        ");
        $stmt->execute([':ip' => $ip, ':id' => $identificador]);
        $row = $stmt->fetch();

        if ($row) {
            $newAttempts = $row['intentos'] + 1;
            $bloqueadoHasta = null;
            if ($newAttempts >= $maxAttempts) {
                $bloqueadoHasta = date('Y-m-d H:i:s', strtotime("+{$lockoutMinutes} minutes"));
            }
            $update = $db->prepare("
                UPDATE intentos_login
                SET intentos = :intentos, bloqueado_hasta = :bloqueado, updated_at = NOW()
                WHERE id = :id
            ");
            $update->execute([
                ':intentos' => $newAttempts,
                ':bloqueado' => $bloqueadoHasta,
                ':id' => $row['id']
            ]);
        } else {
            $insert = $db->prepare("
                INSERT INTO intentos_login (ip, identificador, intentos, created_at)
                VALUES (:ip, :id, 1, NOW())
            ");
            $insert->execute([':ip' => $ip, ':id' => $identificador]);
        }
    }

    private function clearFailedAttempts(string $ip, string $identificador): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM intentos_login WHERE ip = :ip AND identificador = :id");
        $stmt->execute([':ip' => $ip, ':id' => $identificador]);
    }
}
