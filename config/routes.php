<?php

use App\Core\App;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\PermissionMiddleware;

$router = $app->getRouter();

// -------------------------------------------------------------------
// RUTAS PÚBLICAS (Ciudadanos / Usuarios Externos)
// -------------------------------------------------------------------
$router->get('/', ['App\Controllers\ConsultaController', 'home']);
$router->get('/tramite', ['App\Controllers\TramiteController', 'index']);
$router->post('/tramite', ['App\Controllers\TramiteController', 'store']);
$router->get('/tramite/exito/{codigo}', ['App\Controllers\TramiteController', 'exito']);
$router->get('/tramite/cargo/{codigo}', ['App\Controllers\TramiteController', 'descargarCargo']);

$router->get('/consulta', ['App\Controllers\ConsultaController', 'index']);
$router->post('/consulta', ['App\Controllers\ConsultaController', 'buscar']);
$router->get('/consulta/{codigo}', ['App\Controllers\ConsultaController', 'detalle']);

// -------------------------------------------------------------------
// AUTENTICACIÓN
// -------------------------------------------------------------------
$router->get('/login', ['App\Controllers\AuthController', 'showLogin'], [GuestMiddleware::class]);
$router->post('/login', ['App\Controllers\AuthController', 'login'], [GuestMiddleware::class]);
$router->post('/logout', ['App\Controllers\AuthController', 'logout'], [AuthMiddleware::class]);

$router->get('/cambiar-password', ['App\Controllers\AuthController', 'showChangePassword'], [AuthMiddleware::class]);
$router->post('/cambiar-password', ['App\Controllers\AuthController', 'changePassword'], [AuthMiddleware::class]);

// -------------------------------------------------------------------
// PANEL ADMINISTRATIVO / INTRANET
// -------------------------------------------------------------------
$router->get('/dashboard', ['App\Controllers\DashboardController', 'index'], [AuthMiddleware::class]);

// EXPEDIENTES
$router->get('/expedientes', ['App\Controllers\ExpedienteController', 'index'], [AuthMiddleware::class]);
$router->get('/expedientes/crear', ['App\Controllers\ExpedienteController', 'create'], [AuthMiddleware::class]);
$router->post('/expedientes', ['App\Controllers\ExpedienteController', 'store'], [AuthMiddleware::class]);
$router->get('/expedientes/{id}', ['App\Controllers\ExpedienteController', 'show'], [AuthMiddleware::class]);
$router->get('/expedientes/{id}/imprimir-cargo', ['App\Controllers\ExpedienteController', 'imprimirCargo'], [AuthMiddleware::class]);
$router->post('/expedientes/{id}/enviar-direccion', ['App\Controllers\ExpedienteController', 'enviarDireccion'], [AuthMiddleware::class]);
$router->post('/expedientes/{id}/derivar', ['App\Controllers\DireccionController', 'derivar'], [AuthMiddleware::class]);
$router->post('/expedientes/{id}/recibir', ['App\Controllers\BandejaController', 'recibir'], [AuthMiddleware::class]);
$router->post('/expedientes/{id}/responder', ['App\Controllers\BandejaController', 'responder'], [AuthMiddleware::class]);
$router->post('/expedientes/{id}/solicitar-info', ['App\Controllers\BandejaController', 'solicitarInfo'], [AuthMiddleware::class]);
$router->post('/expedientes/{id}/responder-solicitud', ['App\Controllers\BandejaController', 'responderSolicitud'], [AuthMiddleware::class]);
$router->post('/expedientes/{id}/devolver', ['App\Controllers\DireccionController', 'devolver'], [AuthMiddleware::class]);
$router->post('/expedientes/{id}/finalizar', ['App\Controllers\DireccionController', 'finalizar'], [AuthMiddleware::class]);
$router->post('/expedientes/{id}/archivar', ['App\Controllers\DireccionController', 'archivar'], [AuthMiddleware::class]);
$router->get('/expedientes/documento/{id}', ['App\Controllers\ExpedienteController', 'descargarDocumento'], [AuthMiddleware::class]);
$router->get('/expedientes/documento/{id}/ver', ['App\Controllers\ExpedienteController', 'verDocumento'], [AuthMiddleware::class]);

// DIRECCIÓN (Bandeja exclusiva)
$router->get('/direccion', ['App\Controllers\DireccionController', 'index'], [AuthMiddleware::class]);

// BANDEJA DE MI OFICINA
$router->get('/mi-oficina', ['App\Controllers\BandejaController', 'index'], [AuthMiddleware::class]);

// USUARIOS
$router->get('/usuarios', ['App\Controllers\UsuarioController', 'index'], [AuthMiddleware::class]);
$router->get('/usuarios/crear', ['App\Controllers\UsuarioController', 'create'], [AuthMiddleware::class]);
$router->post('/usuarios', ['App\Controllers\UsuarioController', 'store'], [AuthMiddleware::class]);
$router->get('/usuarios/{id}/editar', ['App\Controllers\UsuarioController', 'edit'], [AuthMiddleware::class]);
$router->post('/usuarios/{id}', ['App\Controllers\UsuarioController', 'update'], [AuthMiddleware::class]);
$router->post('/usuarios/{id}/toggle-estado', ['App\Controllers\UsuarioController', 'toggleEstado'], [AuthMiddleware::class]);
$router->post('/usuarios/{id}/reset-password', ['App\Controllers\UsuarioController', 'resetPassword'], [AuthMiddleware::class]);
$router->post('/usuarios/{id}/eliminar', ['App\Controllers\UsuarioController', 'eliminar'], [AuthMiddleware::class]);

// ROLES Y PERMISOS
$router->get('/roles', ['App\Controllers\RolController', 'index'], [AuthMiddleware::class]);
$router->get('/roles/{id}/permisos', ['App\Controllers\RolController', 'permisos'], [AuthMiddleware::class]);
$router->post('/roles/{id}/permisos', ['App\Controllers\RolController', 'guardarPermisos'], [AuthMiddleware::class]);

// OFICINAS
$router->get('/oficinas', ['App\Controllers\OficinaController', 'index'], [AuthMiddleware::class]);
$router->post('/oficinas', ['App\Controllers\OficinaController', 'store'], [AuthMiddleware::class]);
$router->post('/oficinas/{id}', ['App\Controllers\OficinaController', 'update'], [AuthMiddleware::class]);
$router->post('/oficinas/{id}/toggle-estado', ['App\Controllers\OficinaController', 'toggleEstado'], [AuthMiddleware::class]);

// TIPOS DE TRÁMITE
$router->get('/tipos-tramite', ['App\Controllers\TipoTramiteController', 'index'], [AuthMiddleware::class]);
$router->post('/tipos-tramite', ['App\Controllers\TipoTramiteController', 'store'], [AuthMiddleware::class]);
$router->post('/tipos-tramite/{id}', ['App\Controllers\TipoTramiteController', 'update'], [AuthMiddleware::class]);
$router->post('/tipos-tramite/{id}/toggle-estado', ['App\Controllers\TipoTramiteController', 'toggleEstado'], [AuthMiddleware::class]);

// ESTADOS
$router->get('/estados', ['App\Controllers\EstadoController', 'index'], [AuthMiddleware::class]);
$router->post('/estados/{id}', ['App\Controllers\EstadoController', 'update'], [AuthMiddleware::class]);

// REPORTES
$router->get('/reportes', ['App\Controllers\ReporteController', 'index'], [AuthMiddleware::class]);
$router->get('/reportes/exportar-excel', ['App\Controllers\ReporteController', 'exportarExcel'], [AuthMiddleware::class]);
$router->get('/reportes/imprimir', ['App\Controllers\ReporteController', 'imprimir'], [AuthMiddleware::class]);

// NOTIFICACIONES
$router->get('/notificaciones', ['App\Controllers\NotificacionController', 'index'], [AuthMiddleware::class]);
$router->post('/notificaciones/marcar-todas', ['App\Controllers\NotificacionController', 'marcarTodas'], [AuthMiddleware::class]);
$router->post('/notificaciones/{id}/marcar', ['App\Controllers\NotificacionController', 'marcarLeida'], [AuthMiddleware::class]);

// CONFIGURACIÓN INSTITUCIONAL Y APARIENCIA
$router->get('/configuracion', ['App\Controllers\ConfiguracionController', 'index'], [AuthMiddleware::class]);
$router->post('/configuracion/general', ['App\Controllers\ConfiguracionController', 'guardarGeneral'], [AuthMiddleware::class]);
$router->get('/configuracion/apariencia', ['App\Controllers\ConfiguracionController', 'apariencia'], [AuthMiddleware::class]);
$router->post('/configuracion/apariencia', ['App\Controllers\ConfiguracionController', 'guardarApariencia'], [AuthMiddleware::class]);
$router->post('/configuracion/apariencia/restablecer', ['App\Controllers\ConfiguracionController', 'restablecerApariencia'], [AuthMiddleware::class]);
$router->post('/configuracion/smtp', ['App\Controllers\ConfiguracionController', 'guardarSmtp'], [AuthMiddleware::class]);
$router->post('/configuracion/smtp/probar', ['App\Controllers\ConfiguracionController', 'probarSmtp'], [AuthMiddleware::class]);

// AUDITORÍA
$router->get('/auditoria', ['App\Controllers\AuditoriaController', 'index'], [AuthMiddleware::class]);
