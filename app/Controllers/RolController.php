<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Models\Permiso;
use App\Models\Rol;

class RolController extends Controller
{
    private Rol $rolModel;
    private Permiso $permisoModel;

    public function __construct()
    {
        $this->rolModel = new Rol();
        $this->permisoModel = new Permiso();
    }

    public function index(): void
    {
        if (!Auth::can('roles.ver')) {
            Session::setFlash('error', 'No tienes permiso para ver roles.');
            $this->redirect('/dashboard');
        }

        $db = Database::getConnection();
        $sql = "SELECT r.*, COUNT(u.id) AS total_usuarios
                FROM roles r
                LEFT JOIN usuarios u ON r.id = u.rol_id
                GROUP BY r.id
                ORDER BY r.id ASC";
        $roles = $db->query($sql)->fetchAll();

        $this->render('roles.index', [
            'pageTitle' => 'Roles y Permisos',
            'roles' => $roles
        ]);
    }

    public function permisos(string $id): void
    {
        if (!Auth::can('roles.gestionar')) {
            Session::setFlash('error', 'No tienes permiso para gestionar permisos de roles.');
            $this->redirect('/roles');
        }

        $rol = $this->rolModel->find((int)$id);
        if (!$rol) {
            Session::setFlash('error', 'Rol no encontrado.');
            $this->redirect('/roles');
        }

        $permisosAgrupados = $this->permisoModel->allAgrupadosPorModulo();
        $permisosAsignados = $this->rolModel->getPermisosIds((int)$id);

        $this->render('roles.permisos', [
            'pageTitle' => 'Permisos del Rol: ' . $rol['nombre'],
            'rol' => $rol,
            'permisosAgrupados' => $permisosAgrupados,
            'permisosAsignados' => $permisosAsignados
        ]);
    }

    public function guardarPermisos(string $id): void
    {
        if (!Auth::can('roles.gestionar')) {
            Session::setFlash('error', 'No tienes permiso para gestionar permisos de roles.');
            $this->redirect('/roles');
        }

        $this->validateCSRF();
        $rol = $this->rolModel->find((int)$id);
        if (!$rol) {
            Session::setFlash('error', 'Rol no encontrado.');
            $this->redirect('/roles');
        }

        $permisosSeleccionados = $_POST['permisos'] ?? [];
        $this->rolModel->sincronizarPermisos((int)$id, $permisosSeleccionados);

        logAudit('MODIFICAR_PERMISOS', 'Roles', (string)$id, "Permisos actualizados para el rol {$rol['nombre']}");
        Session::setFlash('success', "Permisos del rol '{$rol['nombre']}' actualizados correctamente.");
        $this->redirect('/roles');
    }
}
