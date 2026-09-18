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

        // 6. Verificar si debe cambiar contraseña
        if ((int)$user['debe_cambiar_password'] === 1) {
            Session::setFlash('warning', 'Como medida de seguridad obligatoria, debes actualizar tu contraseña antes de continuar.');
            $this->redirect('/cambiar-password');
        }

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

    public function showChangePassword(): void
    {
        $this->render('auth.cambiar_password', [
            'pageTitle' => 'Cambio Obligatorio de Contraseña'
        ], 'auth');
    }

    public function changePassword(): void
    {
        $this->validateCSRF();
        $userId = Auth::id();

        $validator = Validator::make($_POST, [
            'password_actual' => 'required',
            'password_nuevo' => 'required|min:8',
            'password_confirm' => 'required|matches:password_nuevo'
        ], [
            'password_actual' => 'Ingresa tu contraseña actual.',
            'password_nuevo' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password_confirm' => 'La confirmación de la contraseña no coincide.'
        ]);

        if ($validator->fails()) {
            Session::setFlash('error', $validator->firstError('password_actual')
                ?: ($validator->firstError('password_nuevo') ?: $validator->firstError('password_confirm')));
            $this->redirect('/cambiar-password');
        }

        $user = Auth::user();
        if (!password_verify($_POST['password_actual'], $user['password'])) {
            Session::setFlash('error', 'Tu contraseña actual es incorrecta.');
            $this->redirect('/cambiar-password');
        }

        if ($_POST['password_actual'] === $_POST['password_nuevo']) {
            Session::setFlash('error', 'La nueva contraseña debe ser distinta a la actual.');
            $this->redirect('/cambiar-password');
        }

        $hashed = password_hash($_POST['password_nuevo'], PASSWORD_BCRYPT);
        $this->usuarioModel->update($userId, [
            'password' => $hashed,
            'debe_cambiar_password' => 0
        ]);

        logAudit('CAMBIO_PASSWORD', 'Usuarios', (string)$userId, 'Actualización de contraseña exitosa.');
        Session::setFlash('success', 'Contraseña actualizada correctamente. Ya puedes usar el sistema.');
        $this->redirect('/dashboard');
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
