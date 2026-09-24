<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Oficina;
use App\Models\Rol;
use App\Models\Usuario;

class UsuarioController extends Controller
{
    private Usuario $usuarioModel;
    private Rol $rolModel;
    private Oficina $oficinaModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
        $this->rolModel = new Rol();
        $this->oficinaModel = new Oficina();
    }

    public function index(): void
    {
        if (!Auth::can('usuarios.ver')) {
            Session::setFlash('error', 'No tienes permiso para ver usuarios.');
            $this->redirect('/dashboard');
        }

        $page = (int)($_GET['page'] ?? 1);
        $filtros = [
            'buscar' => $_GET['buscar'] ?? '',
            'rol_id' => $_GET['rol_id'] ?? '',
            'oficina_id' => $_GET['oficina_id'] ?? '',
            'estado' => $_GET['estado'] ?? ''
        ];

        $usuariosData = $this->usuarioModel->getUsuariosFiltrados($filtros, $page, 15);
        $roles = $this->rolModel->all('nombre ASC');
        $oficinas = $this->oficinaModel->allActivas();

        $this->render('usuarios.index', [
            'pageTitle' => 'Gestión de Usuarios',
            'usuarios' => $usuariosData['data'],
            'pagination' => $usuariosData,
            'filtros' => $filtros,
            'roles' => $roles,
            'oficinas' => $oficinas
        ]);
    }

    public function create(): void
    {
        if (!Auth::can('usuarios.crear')) {
            Session::setFlash('error', 'No tienes permiso para registrar usuarios.');
            $this->redirect('/usuarios');
        }

        $roles = $this->rolModel->where('activo = 1', [], 'nombre ASC');
        $oficinas = $this->oficinaModel->allActivas();

        $this->render('usuarios.form', [
            'pageTitle' => 'Nuevo Usuario',
            'usuario' => null,
            'roles' => $roles,
            'oficinas' => $oficinas
        ]);
    }

    public function store(): void
    {
        if (!Auth::can('usuarios.crear')) {
            Session::setFlash('error', 'No tienes permiso para registrar usuarios.');
            $this->redirect('/usuarios');
        }

        $this->validateCSRF();

        $validator = Validator::make($_POST, [
            'nombres' => 'required|max:100',
            'apellidos' => 'required|max:100',
            'dni' => 'required|max:15|unique:usuarios,dni',
            'usuario' => 'required|min:4|max:50|unique:usuarios,usuario',
            'correo' => 'required|email|max:120|unique:usuarios,correo',
            'password' => 'required|min:6',
            'rol_id' => 'required|integer',
            'oficina_id' => 'required|integer',
            'telefono' => 'digits|min:6|max:15'
        ]);

        if ($validator->fails()) {
            Session::set('_old_inputs', $_POST);
            Session::setFlash('error', 'Por favor corrige los errores del formulario.');
            Session::set('_validation_errors', $validator->errors());
            $this->redirect('/usuarios/crear');
        }

        $hashed = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $debeCambiar = isset($_POST['debe_cambiar_password']) ? true : false;
        $nuevoId = $this->usuarioModel->createUser([
            'nombres' => trim($_POST['nombres']),
            'apellidos' => trim($_POST['apellidos']),
            'dni' => trim($_POST['dni']),
            'usuario' => trim($_POST['usuario']),
            'correo' => trim($_POST['correo']),
            'rol_id' => (int)$_POST['rol_id'],
            'oficina_id' => !empty($_POST['oficina_id']) ? (int)$_POST['oficina_id'] : null,
            'cargo' => trim($_POST['cargo'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'estado' => 1
        ], $hashed, $debeCambiar);

        logAudit('CREAR_USUARIO', 'Usuarios', (string)$nuevoId, "Usuario creado: {$_POST['usuario']}");
        Session::setFlash('success', 'Usuario registrado exitosamente.');
        $this->redirect('/usuarios');
    }

    public function edit(string $id): void
    {
        if (!Auth::can('usuarios.editar')) {
            Session::setFlash('error', 'No tienes permiso para editar usuarios.');
            $this->redirect('/usuarios');
        }

        $usuario = $this->usuarioModel->find((int)$id);
        if (!$usuario) {
            Session::setFlash('error', 'Usuario no encontrado.');
            $this->redirect('/usuarios');
        }

        $roles = $this->rolModel->all('nombre ASC');
        $oficinas = $this->oficinaModel->allActivas();

        $this->render('usuarios.form', [
            'pageTitle' => 'Editar Usuario: ' . $usuario['usuario'],
            'usuario' => $usuario,
            'roles' => $roles,
            'oficinas' => $oficinas
        ]);
    }

    public function update(string $id): void
    {
        if (!Auth::can('usuarios.editar')) {
            Session::setFlash('error', 'No tienes permiso para editar usuarios.');
            $this->redirect('/usuarios');
        }

        $this->validateCSRF();
        $usuario = $this->usuarioModel->find((int)$id);
        if (!$usuario) {
            Session::setFlash('error', 'Usuario no encontrado.');
            $this->redirect('/usuarios');
        }

        $validator = Validator::make($_POST, [
            'nombres' => 'required|max:100',
            'apellidos' => 'required|max:100',
            'dni' => "required|max:15|unique:usuarios,dni,{$id}",
            'usuario' => "required|min:4|max:50|unique:usuarios,usuario,{$id}",
            'correo' => "required|email|max:120|unique:usuarios,correo,{$id}",
            'rol_id' => 'required|integer',
            'telefono' => 'digits|min:6|max:15'
        ]);

        if ($validator->fails()) {
            Session::set('_old_inputs', $_POST);
            Session::setFlash('error', 'Por favor verifica los campos con errores.');
            Session::set('_validation_errors', $validator->errors());
            $this->redirect("/usuarios/{$id}/editar");
        }

        $updateData = [
            'nombres' => trim($_POST['nombres']),
            'apellidos' => trim($_POST['apellidos']),
            'dni' => trim($_POST['dni']),
            'usuario' => trim($_POST['usuario']),
            'correo' => trim($_POST['correo']),
            'rol_id' => (int)$_POST['rol_id'],
            'oficina_id' => !empty($_POST['oficina_id']) ? (int)$_POST['oficina_id'] : null,
            'cargo' => trim($_POST['cargo'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'estado' => isset($_POST['estado']) ? (int)$_POST['estado'] : $usuario['estado']
        ];

        if (!empty($_POST['password'])) {
            if (strlen($_POST['password']) < 6) {
                Session::setFlash('error', 'La nueva contraseña debe contener al menos 6 caracteres.');
                $this->redirect("/usuarios/{$id}/editar");
            }
            $pwHash = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $this->usuarioModel->resetPassword((int)$id, $pwHash);
        }

        $this->usuarioModel->update((int)$id, $updateData);
        logAudit('EDITAR_USUARIO', 'Usuarios', (string)$id, "Usuario actualizado: {$usuario['usuario']}");
        Session::setFlash('success', 'Datos del usuario actualizados correctamente.');
        $this->redirect('/usuarios');
    }

    public function toggleEstado(string $id): void
    {
        if (!Auth::can('usuarios.eliminar')) {
            Session::setFlash('error', 'No tienes permiso para modificar el estado de usuarios.');
            $this->redirect('/usuarios');
        }

        $this->validateCSRF();
        $usuario = $this->usuarioModel->find((int)$id);
        if (!$usuario) {
            Session::setFlash('error', 'Usuario no encontrado.');
            $this->redirect('/usuarios');
        }

        // Evitar desactivarse a uno mismo
        if ((int)$usuario['id'] === Auth::id()) {
            Session::setFlash('error', 'No puedes desactivar tu propia cuenta activa.');
            $this->redirect('/usuarios');
        }

        $nuevoEstado = ((int)$usuario['estado'] === 1) ? 0 : 1;

        // Proteger al último superadministrador activo
        if ($nuevoEstado === 0) {
            $db = Database::getConnection();
            $stmtRol = $db->prepare("SELECT slug FROM roles WHERE id = :rol_id");
            $stmtRol->execute([':rol_id' => $usuario['rol_id']]);
            $rolSlug = $stmtRol->fetchColumn();
            if ($rolSlug === 'superadministrador' && $this->usuarioModel->contarSuperadminsActivos() <= 1) {
                Session::setFlash('error', 'No es posible desactivar al único superadministrador activo del sistema.');
                $this->redirect('/usuarios');
            }
        }

        $this->usuarioModel->update((int)$id, ['estado' => $nuevoEstado]);

        $accion = $nuevoEstado === 1 ? 'ACTIVAR_USUARIO' : 'DESACTIVAR_USUARIO';
        logAudit($accion, 'Usuarios', (string)$id, "Estado cambiado a {$nuevoEstado} para {$usuario['usuario']}");

        $msg = $nuevoEstado === 1 ? 'Usuario activado exitosamente.' : 'Usuario desactivado exitosamente.';
        Session::setFlash('success', $msg);
        $this->redirect('/usuarios');
    }

    public function eliminar(string $id): void
    {
        if (!Auth::can('usuarios.eliminar')) {
            Session::setFlash('error', 'No tienes permiso para eliminar usuarios.');
            $this->redirect('/usuarios');
        }

        $this->validateCSRF();
        $usuario = $this->usuarioModel->find((int)$id);
        if (!$usuario) {
            Session::setFlash('error', 'Usuario no encontrado.');
            $this->redirect('/usuarios');
        }

        // Evitar eliminarse a uno mismo
        if ((int)$usuario['id'] === Auth::id()) {
            Session::setFlash('error', 'No puedes eliminar tu propia cuenta activa.');
            $this->redirect('/usuarios');
        }

        // Proteger al último superadministrador activo
        $db = Database::getConnection();
        $stmtRol = $db->prepare("SELECT slug FROM roles WHERE id = :rol_id");
        $stmtRol->execute([':rol_id' => $usuario['rol_id']]);
        $rolSlug = $stmtRol->fetchColumn();

        if ($rolSlug === 'superadministrador' && (int)$usuario['estado'] === 1) {
            $totalSuperadmins = $this->usuarioModel->contarSuperadminsActivos();
            if ($totalSuperadmins <= 1) {
                Session::setFlash('error', 'No es posible eliminar ni desactivar al único superadministrador del sistema.');
                $this->redirect('/usuarios');
            }
        }

        // Verificar si tiene dependencias / registros históricos
        $tieneHistorial = $this->usuarioModel->tieneRegistrosHistoricos((int)$id);

        if ($tieneHistorial) {
            // Baja lógica (soft delete) obligatoria para preservar integridad referencial y trazabilidad
            $this->usuarioModel->update((int)$id, ['estado' => 0]);
            logAudit('ELIMINAR_USUARIO_LOGICO', 'Usuarios', (string)$id, "Baja lógica de usuario con historial: {$usuario['usuario']}");
            Session::setFlash('success', "El usuario '{$usuario['usuario']}' ha sido dado de baja (eliminación lógica). Su acceso fue revocado y sus registros históricos en expedientes y auditoría se mantuvieron intactos.");
        } else {
            // Eliminación física segura: sin dependencias
            $db->prepare("DELETE FROM usuarios_roles WHERE usuario_id = :id")->execute([':id' => (int)$id]);
            $this->usuarioModel->delete((int)$id);
            logAudit('ELIMINAR_USUARIO_FISICO', 'Usuarios', (string)$id, "Eliminación definitiva de usuario sin dependencias: {$usuario['usuario']}");
            Session::setFlash('success', "El usuario '{$usuario['usuario']}' no registraba expedientes asociados y fue eliminado exitosamente del sistema.");
        }

        $this->redirect('/usuarios');
    }

    public function resetPassword(string $id): void
    {
        if (!Auth::can('usuarios.editar')) {
            Session::setFlash('error', 'Permiso denegado.');
            $this->redirect('/usuarios');
        }

        $this->validateCSRF();
        $usuario = $this->usuarioModel->find((int)$id);
        if (!$usuario) {
            Session::setFlash('error', 'Usuario no encontrado.');
            $this->redirect('/usuarios');
        }

        $tempPassword = bin2hex(random_bytes(8));
        $hashed = password_hash($tempPassword, PASSWORD_BCRYPT);
        $this->usuarioModel->resetPassword((int)$id, $hashed);

        logAudit('RESET_PASSWORD', 'Usuarios', (string)$id, "Contraseña restablecida para {$usuario['usuario']}");
        Session::setFlash('success', "Contraseña restablecida. Se exigirá cambio en el siguiente inicio.");
        $this->redirect('/usuarios');
    }
}
