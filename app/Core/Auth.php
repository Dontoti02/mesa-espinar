<?php

namespace App\Core;

class Auth
{
    private static ?array $cachedUser = null;
    private static ?array $cachedPermissions = null;

    public static function check(): bool
    {
        Session::start();
        return Session::has('user_id');
    }

    public static function id(): ?int
    {
        Session::start();
        return Session::get('user_id');
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }

        if (self::$cachedUser !== null) {
            return self::$cachedUser;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT u.*, r.nombre AS rol_nombre, r.slug AS rol_slug, o.nombre AS oficina_nombre, o.sigla AS oficina_sigla
            FROM usuarios u
            INNER JOIN roles r ON u.rol_id = r.id
            LEFT JOIN oficinas o ON u.oficina_id = o.id
            WHERE u.id = :id AND u.estado = 1
            LIMIT 1
        ");
        $stmt->execute([':id' => self::id()]);
        $user = $stmt->fetch();

        if (!$user) {
            self::logout();
            return null;
        }

        self::$cachedUser = $user;
        return $user;
    }

    public static function login(array $user): void
    {
        Session::start();
        Session::regenerate();
        Session::set('user_id', (int)$user['id']);
        Session::set('user_role', $user['rol_slug'] ?? '');
        self::$cachedUser = null;
        self::$cachedPermissions = null;

        // Actualizar último acceso e IP
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = :id");
        $stmt->execute([':id' => $user['id']]);
    }

    public static function logout(): void
    {
        Session::start();
        Session::destroy();
        self::$cachedUser = null;
        self::$cachedPermissions = null;
    }

    public static function role(): ?string
    {
        $user = self::user();
        return $user['rol_slug'] ?? null;
    }

    public static function hasRole(string|array $roles): bool
    {
        $currentRole = self::role();
        if (!$currentRole) {
            return false;
        }

        if ($currentRole === 'superadministrador') {
            return true;
        }

        $rolesList = is_array($roles) ? $roles : [$roles];
        return in_array($currentRole, $rolesList, true);
    }

    public static function can(string $permission): bool
    {
        $user = self::user();
        if (!$user) {
            return false;
        }

        // Superadministrador tiene todos los permisos
        if (($user['rol_slug'] ?? '') === 'superadministrador') {
            return true;
        }

        if (self::$cachedPermissions === null) {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                SELECT p.clave
                FROM permisos p
                INNER JOIN roles_permisos rp ON p.id = rp.permiso_id
                WHERE rp.rol_id = :rol_id
            ");
            $stmt->execute([':rol_id' => $user['rol_id']]);
            self::$cachedPermissions = $stmt->fetchAll(\PDO::FETCH_COLUMN) ?: [];
        }

        return in_array($permission, self::$cachedPermissions, true);
    }

    public static function officeId(): ?int
    {
        $user = self::user();
        return isset($user['oficina_id']) ? (int)$user['oficina_id'] : null;
    }
}
