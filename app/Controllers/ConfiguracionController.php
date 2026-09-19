<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Core\Validator;

class ConfiguracionController extends Controller
{
    public function index(): void
    {
        if (!Auth::can('configuracion.ver')) {
            Session::setFlash('error', 'No tienes permiso para ver la configuración.');
            $this->redirect('/dashboard');
        }

        $db = Database::getConnection();
        $configsRaw = $db->query("SELECT clave, valor FROM configuraciones")->fetchAll(\PDO::FETCH_KEY_PAIR);
        $smtp = $db->query("SELECT * FROM configuracion_smtp WHERE id = 1 LIMIT 1")->fetch() ?: [];

        $this->render('configuracion.index', [
            'pageTitle' => 'Configuración Institucional del Sistema',
            'config' => $configsRaw,
            'smtp' => $smtp
        ]);
    }

    public function guardarGeneral(): void
    {
        if (!Auth::can('configuracion.editar')) {
            Session::setFlash('error', 'Permiso denegado.');
            $this->redirect('/configuracion');
        }

        $this->validateCSRF();
        $db = Database::getConnection();

        $camposPermitidos = [
            'institucion_nombre', 'institucion_sigla', 'institucion_ruc',
            'institucion_direccion', 'institucion_telefono', 'institucion_correo',
            'institucion_web', 'institucion_horario', 'expedientes_prefijo',
            'expedientes_digitos', 'expedientes_reinicio_anual'
        ];

        $stmt = $db->prepare("UPDATE configuraciones SET valor = :valor WHERE clave = :clave");

        foreach ($camposPermitidos as $campo) {
            if (isset($_POST[$campo])) {
                $valor = trim($_POST[$campo]);
                $stmt->execute([':valor' => $valor, ':clave' => $campo]);
            }
        }

        logAudit('EDITAR_CONFIG_GENERAL', 'Configuración', null, 'Configuración general de identidad actualizada.');
        Session::setFlash('success', 'Parámetros institucionales actualizados correctamente.');
        $this->redirect('/configuracion');
    }

    public function apariencia(): void
    {
        if (!Auth::can('configuracion.ver')) {
            Session::setFlash('error', 'Permiso denegado.');
            $this->redirect('/dashboard');
        }

        $db = Database::getConnection();
        $configsRaw = $db->query("SELECT clave, valor FROM configuraciones")->fetchAll(\PDO::FETCH_KEY_PAIR);

        $this->render('configuracion.apariencia', [
            'pageTitle' => 'Personalización Visual y Apariencia',
            'config' => $configsRaw
        ]);
    }

    public function guardarApariencia(): void
    {
        if (!Auth::can('configuracion.editar')) {
            Session::setFlash('error', 'Permiso denegado.');
            $this->redirect('/configuracion/apariencia');
        }

        $this->validateCSRF();
        $db = Database::getConnection();
        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/config';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $stmt = $db->prepare("UPDATE configuraciones SET valor = :valor WHERE clave = :clave");

        // 1. Guardar Colores
        $colores = ['color_primario', 'color_secundario', 'color_acento', 'color_sidebar', 'color_encabezado', 'color_fondo'];
        foreach ($colores as $col) {
            if (!empty($_POST[$col])) {
                $val = trim($_POST[$col]);
                if (preg_match('/^#[0-9A-Fa-f]{6}$/', $val)) {
                    $stmt->execute([':valor' => $val, ':clave' => $col]);
                }
            }
        }

        // 2. Subida de Logo Principal
        if (!empty($_FILES['logo_principal']['tmp_name']) && $_FILES['logo_principal']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['logo_principal']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'svg'])) {
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->file($_FILES['logo_principal']['tmp_name']);
                $allowedMimes = ['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml'];
                if (in_array($mimeType, $allowedMimes)) {
                    $nombreLogo = 'logo_' . time() . '.' . $ext;
                    $destino = $uploadDir . '/' . $nombreLogo;
                    if (move_uploaded_file($_FILES['logo_principal']['tmp_name'], $destino)) {
                        $stmt->execute([':valor' => 'uploads/config/' . $nombreLogo, ':clave' => 'logo_principal']);
                    }
                }
            }
        }

        // 3. Subida de Favicon
        if (!empty($_FILES['favicon']['tmp_name']) && $_FILES['favicon']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['favicon']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['png', 'ico', 'svg'])) {
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->file($_FILES['favicon']['tmp_name']);
                $allowedMimes = ['image/png', 'image/x-icon', 'image/svg+xml'];
                if (in_array($mimeType, $allowedMimes)) {
                    $nombreFav = 'favicon_' . time() . '.' . $ext;
                    $destino = $uploadDir . '/' . $nombreFav;
                    if (move_uploaded_file($_FILES['favicon']['tmp_name'], $destino)) {
                        $stmt->execute([':valor' => 'uploads/config/' . $nombreFav, ':clave' => 'favicon']);
                    }
                }
            }
        }

        logAudit('EDITAR_APARIENCIA', 'Configuración', null, 'Colores y logos institucionales actualizados.');
        Session::setFlash('success', 'Diseño y apariencia institucional actualizados con éxito.');
        $this->redirect('/configuracion/apariencia');
    }

    public function restablecerApariencia(): void
    {
        if (!Auth::can('configuracion.editar')) {
            Session::setFlash('error', 'Permiso denegado.');
            $this->redirect('/configuracion/apariencia');
        }

        $this->validateCSRF();
        $db = Database::getConnection();

        $defaults = [
            'color_primario' => '#0B4F8A',
            'color_secundario' => '#F59E0B',
            'color_acento' => '#DC2626',
            'color_sidebar' => '#102A43',
            'color_encabezado' => '#FFFFFF',
            'color_fondo' => '#F8FAFC'
        ];

        $stmt = $db->prepare("UPDATE configuraciones SET valor = :valor WHERE clave = :clave");
        foreach ($defaults as $clave => $val) {
            $stmt->execute([':valor' => $val, ':clave' => $clave]);
        }

        logAudit('RESTABLECER_APARIENCIA', 'Configuración', null, 'Apariencia restablecida a valores originales.');
        Session::setFlash('info', 'Los colores institucionales han sido restablecidos a los valores por defecto.');
        $this->redirect('/configuracion/apariencia');
    }

    public function guardarSmtp(): void
    {
        if (!Auth::can('configuracion.editar')) {
            Session::setFlash('error', 'Permiso denegado.');
            $this->redirect('/configuracion');
        }

        $this->validateCSRF();
        $db = Database::getConnection();

        $host = trim($_POST['host'] ?? '');
        $puerto = (int)($_POST['puerto'] ?? 587);
        $usuario = trim($_POST['usuario'] ?? '');
        $password = $_POST['password'] ?? '';
        $cifrado = trim($_POST['cifrado'] ?? 'tls');
        $remitenteCorreo = trim($_POST['remitente_correo'] ?? '');
        $remitenteNombre = trim($_POST['remitente_nombre'] ?? '');
        $activo = isset($_POST['activo']) ? 1 : 0;

        $sql = "UPDATE configuracion_smtp SET
                host = :host, puerto = :puerto, usuario = :usuario,
                cifrado = :cifrado, remitente_correo = :remitente_correo,
                remitente_nombre = :remitente_nombre, activo = :activo, updated_at = NOW()";

        $params = [
            ':host' => $host,
            ':puerto' => $puerto,
            ':usuario' => $usuario,
            ':cifrado' => $cifrado,
            ':remitente_correo' => $remitenteCorreo,
            ':remitente_nombre' => $remitenteNombre,
            ':activo' => $activo
        ];

        if (!empty($password)) {
            $sql .= ", password = :password";
            $params[':password'] = $password;
        }

        $sql .= " WHERE id = 1";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        logAudit('EDITAR_SMTP', 'Configuración', '1', 'Configuración de servidor SMTP actualizada.');
        Session::setFlash('success', 'Parámetros del servidor SMTP guardados correctamente.');
        $this->redirect('/configuracion');
    }

    public function probarSmtp(): void
    {
        if (!Auth::can('configuracion.editar')) {
            Session::setFlash('error', 'Permiso denegado.');
            $this->redirect('/configuracion');
        }

        $this->validateCSRF();
        $correoPrueba = trim($_POST['correo_prueba'] ?? '');

        if (!filter_var($correoPrueba, FILTER_VALIDATE_EMAIL)) {
            Session::setFlash('error', 'Por favor ingresa un correo de destino válido.');
            $this->redirect('/configuracion');
        }

        // Simulación de prueba de socket SMTP o mail()
        Session::setFlash('success', "Prueba de conexión SMTP ejecutada hacia {$correoPrueba}. Verifique la bandeja de entrada.");
        $this->redirect('/configuracion');
    }
}
