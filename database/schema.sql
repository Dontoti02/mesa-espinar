-- ==========================================================
-- SISTEMA DE MESA DE PARTES VIRTUAL
-- Esquema de Base de Datos MySQL 8+ / MariaDB 10.4+
-- ==========================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `mesa_partes_espinar` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `mesa_partes_espinar`;

-- 1. ROLES
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(50) NOT NULL UNIQUE,
    `slug` VARCHAR(50) NOT NULL UNIQUE,
    `descripcion` VARCHAR(255) NULL,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. PERMISOS
DROP TABLE IF EXISTS `permisos`;
CREATE TABLE `permisos` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `clave` VARCHAR(100) NOT NULL UNIQUE,
    `nombre` VARCHAR(100) NOT NULL,
    `modulo` VARCHAR(50) NOT NULL,
    `descripcion` VARCHAR(255) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. ROLES - PERMISOS
DROP TABLE IF EXISTS `roles_permisos`;
CREATE TABLE `roles_permisos` (
    `rol_id` INT UNSIGNED NOT NULL,
    `permiso_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`rol_id`, `permiso_id`),
    CONSTRAINT `fk_rp_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_rp_permiso` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. OFICINAS
DROP TABLE IF EXISTS `oficinas`;
CREATE TABLE `oficinas` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(150) NOT NULL,
    `sigla` VARCHAR(20) NOT NULL,
    `responsable` VARCHAR(150) NULL,
    `correo` VARCHAR(120) NULL,
    `telefono` VARCHAR(30) NULL,
    `orden` INT NOT NULL DEFAULT 0,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_oficinas_activo` (`activo`),
    INDEX `idx_oficinas_orden` (`orden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. USUARIOS
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nombres` VARCHAR(100) NOT NULL,
    `apellidos` VARCHAR(100) NOT NULL,
    `dni` VARCHAR(15) NULL,
    `usuario` VARCHAR(50) NOT NULL UNIQUE,
    `correo` VARCHAR(120) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `rol_id` INT UNSIGNED NOT NULL,
    `oficina_id` INT UNSIGNED NULL,
    `cargo` VARCHAR(100) NULL,
    `telefono` VARCHAR(30) NULL,
    `estado` TINYINT(1) NOT NULL DEFAULT 1,
    `debe_cambiar_password` TINYINT(1) NOT NULL DEFAULT 0,
    `ultimo_acceso` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_usuario_oficina` FOREIGN KEY (`oficina_id`) REFERENCES `oficinas` (`id`) ON DELETE SET NULL,
    INDEX `idx_usuarios_usuario` (`usuario`),
    INDEX `idx_usuarios_correo` (`correo`),
    INDEX `idx_usuarios_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. USUARIOS - ROLES (Soporte multi-rol adicional)
DROP TABLE IF EXISTS `usuarios_roles`;
CREATE TABLE `usuarios_roles` (
    `usuario_id` INT UNSIGNED NOT NULL,
    `rol_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`usuario_id`, `rol_id`),
    CONSTRAINT `fk_ur_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ur_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. TIPOS DE TRÁMITE
DROP TABLE IF EXISTS `tipos_tramite`;
CREATE TABLE `tipos_tramite` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `codigo` VARCHAR(20) NOT NULL UNIQUE,
    `nombre` VARCHAR(150) NOT NULL,
    `descripcion` TEXT NULL,
    `oficina_sugerida_id` INT UNSIGNED NULL,
    `plazo_referencial_dias` INT NOT NULL DEFAULT 15,
    `requisitos` TEXT NULL,
    `instrucciones` TEXT NULL,
    `permite_virtual` TINYINT(1) NOT NULL DEFAULT 1,
    `requiere_pago` TINYINT(1) NOT NULL DEFAULT 0,
    `monto` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_tipo_tramite_oficina` FOREIGN KEY (`oficina_sugerida_id`) REFERENCES `oficinas` (`id`) ON DELETE SET NULL,
    INDEX `idx_tt_activo` (`activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. ESTADOS DE EXPEDIENTE
DROP TABLE IF EXISTS `estados_expediente`;
CREATE TABLE `estados_expediente` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `codigo` VARCHAR(30) NOT NULL UNIQUE,
    `nombre` VARCHAR(80) NOT NULL,
    `color` VARCHAR(20) NOT NULL DEFAULT '#64748b',
    `icono` VARCHAR(50) NULL,
    `orden` INT NOT NULL DEFAULT 0,
    `es_publico` TINYINT(1) NOT NULL DEFAULT 1,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_estado_codigo` (`codigo`),
    INDEX `idx_estado_activo` (`activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. PRIORIDADES
DROP TABLE IF EXISTS `prioridades`;
CREATE TABLE `prioridades` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `codigo` VARCHAR(20) NOT NULL UNIQUE,
    `nombre` VARCHAR(50) NOT NULL,
    `color` VARCHAR(20) NOT NULL DEFAULT '#3b82f6',
    `dias_plazo` INT NOT NULL DEFAULT 15,
    `orden` INT NOT NULL DEFAULT 0,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. EXPEDIENTES
DROP TABLE IF EXISTS `expedientes`;
CREATE TABLE `expedientes` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `numero_expediente` VARCHAR(50) NOT NULL UNIQUE,
    `codigo_seguimiento` VARCHAR(32) NOT NULL UNIQUE,
    `tipo_persona` ENUM('NATURAL', 'JURIDICA') NOT NULL DEFAULT 'NATURAL',
    `tipo_documento` VARCHAR(20) NOT NULL DEFAULT 'DNI',
    `numero_documento` VARCHAR(25) NOT NULL,
    `nombres` VARCHAR(100) NOT NULL,
    `apellidos` VARCHAR(100) NULL,
    `razon_social` VARCHAR(150) NULL,
    `correo` VARCHAR(120) NOT NULL,
    `telefono` VARCHAR(30) NULL,
    `direccion` VARCHAR(255) NULL,
    `tipo_tramite_id` INT UNSIGNED NOT NULL,
    `asunto` VARCHAR(255) NOT NULL,
    `descripcion` TEXT NULL,
    `folios` INT NOT NULL DEFAULT 1,
    `estado_id` INT UNSIGNED NOT NULL,
    `prioridad_id` INT UNSIGNED NOT NULL,
    `oficina_actual_id` INT UNSIGNED NOT NULL,
    `oficina_responsable_id` INT UNSIGNED NULL,
    `usuario_responsable_id` INT UNSIGNED NULL,
    `fecha_ingreso` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `fecha_finalizacion` DATETIME NULL,
    `observacion_publica` TEXT NULL,
    `es_virtual` TINYINT(1) NOT NULL DEFAULT 1,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_exp_tipo_tramite` FOREIGN KEY (`tipo_tramite_id`) REFERENCES `tipos_tramite` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_exp_estado` FOREIGN KEY (`estado_id`) REFERENCES `estados_expediente` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_exp_prioridad` FOREIGN KEY (`prioridad_id`) REFERENCES `prioridades` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_exp_oficina_actual` FOREIGN KEY (`oficina_actual_id`) REFERENCES `oficinas` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_exp_oficina_resp` FOREIGN KEY (`oficina_responsable_id`) REFERENCES `oficinas` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_exp_usuario_resp` FOREIGN KEY (`usuario_responsable_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
    INDEX `idx_exp_numero` (`numero_expediente`),
    INDEX `idx_exp_codigo_seg` (`codigo_seguimiento`),
    INDEX `idx_exp_documento` (`numero_documento`),
    INDEX `idx_exp_correo` (`correo`),
    INDEX `idx_exp_fecha_ingreso` (`fecha_ingreso`),
    INDEX `idx_exp_activo` (`activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. MOVIMIENTOS DE EXPEDIENTE
DROP TABLE IF EXISTS `expediente_movimientos`;
CREATE TABLE `expediente_movimientos` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `expediente_id` INT UNSIGNED NOT NULL,
    `tipo_movimiento` ENUM(
        'REGISTRO',
        'ENVIO_DIRECCION',
        'DERIVACION',
        'RECEPCION',
        'SOLICITUD_INFORMACION',
        'RESPUESTA_INTERNA',
        'OBSERVACION',
        'DEVOLUCION',
        'RESPUESTA',
        'REASIGNACION',
        'FINALIZACION',
        'ARCHIVO',
        'ANULACION'
    ) NOT NULL,
    `oficina_origen_id` INT UNSIGNED NULL,
    `oficina_destino_id` INT UNSIGNED NULL,
    `usuario_id` INT UNSIGNED NULL,
    `estado_anterior_id` INT UNSIGNED NULL,
    `estado_nuevo_id` INT UNSIGNED NOT NULL,
    `observacion` TEXT NULL,
    `es_publico` TINYINT(1) NOT NULL DEFAULT 1,
    `ip` VARCHAR(45) NULL,
    `user_agent` VARCHAR(255) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_mov_expediente` FOREIGN KEY (`expediente_id`) REFERENCES `expedientes` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_mov_origen` FOREIGN KEY (`oficina_origen_id`) REFERENCES `oficinas` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_mov_destino` FOREIGN KEY (`oficina_destino_id`) REFERENCES `oficinas` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_mov_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_mov_estado_ant` FOREIGN KEY (`estado_anterior_id`) REFERENCES `estados_expediente` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_mov_estado_nvo` FOREIGN KEY (`estado_nuevo_id`) REFERENCES `estados_expediente` (`id`) ON DELETE RESTRICT,
    INDEX `idx_mov_exp` (`expediente_id`),
    INDEX `idx_mov_fecha` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. DOCUMENTOS ADJUNTOS DE EXPEDIENTE
DROP TABLE IF EXISTS `expediente_documentos`;
CREATE TABLE `expediente_documentos` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `expediente_id` INT UNSIGNED NOT NULL,
    `movimiento_id` INT UNSIGNED NULL,
    `nombre_original` VARCHAR(255) NOT NULL,
    `nombre_archivo` VARCHAR(255) NOT NULL,
    `ruta` VARCHAR(255) NOT NULL,
    `extension` VARCHAR(20) NOT NULL,
    `tamano_bytes` BIGINT UNSIGNED NOT NULL,
    `mime_type` VARCHAR(100) NOT NULL,
    `hash_sha256` VARCHAR(64) NOT NULL,
    `es_principal` TINYINT(1) NOT NULL DEFAULT 0,
    `es_publico` TINYINT(1) NOT NULL DEFAULT 0,
    `usuario_id` INT UNSIGNED NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_doc_expediente` FOREIGN KEY (`expediente_id`) REFERENCES `expedientes` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_doc_movimiento` FOREIGN KEY (`movimiento_id`) REFERENCES `expediente_movimientos` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_doc_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
    INDEX `idx_doc_exp` (`expediente_id`),
    INDEX `idx_doc_hash` (`hash_sha256`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. SOLICITUDES DE INFORMACIÓN INTERNAS (Colaboración entre oficinas)
DROP TABLE IF EXISTS `solicitudes_internas`;
CREATE TABLE `solicitudes_internas` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `expediente_id` INT UNSIGNED NOT NULL,
    `oficina_solicitante_id` INT UNSIGNED NOT NULL,
    `oficina_proveedora_id` INT UNSIGNED NOT NULL,
    `usuario_solicitante_id` INT UNSIGNED NOT NULL,
    `motivo` TEXT NOT NULL,
    `estado` ENUM('PENDIENTE', 'RESPONDIDA', 'CANCELADA') NOT NULL DEFAULT 'PENDIENTE',
    `fecha_solicitud` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `fecha_respuesta` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_sol_exp` FOREIGN KEY (`expediente_id`) REFERENCES `expedientes` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_sol_of_sol` FOREIGN KEY (`oficina_solicitante_id`) REFERENCES `oficinas` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_sol_of_prov` FOREIGN KEY (`oficina_proveedora_id`) REFERENCES `oficinas` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_sol_user_sol` FOREIGN KEY (`usuario_solicitante_id`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT,
    INDEX `idx_sol_estado` (`estado`),
    INDEX `idx_sol_exp` (`expediente_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. RESPUESTAS A SOLICITUDES INTERNAS
DROP TABLE IF EXISTS `solicitudes_internas_respuestas`;
CREATE TABLE `solicitudes_internas_respuestas` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `solicitud_interna_id` INT UNSIGNED NOT NULL,
    `usuario_respuesta_id` INT UNSIGNED NOT NULL,
    `respuesta` TEXT NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_sir_sol` FOREIGN KEY (`solicitud_interna_id`) REFERENCES `solicitudes_internas` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_sir_user` FOREIGN KEY (`usuario_respuesta_id`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. NOTIFICACIONES
DROP TABLE IF EXISTS `notificaciones`;
CREATE TABLE `notificaciones` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT UNSIGNED NULL,
    `oficina_id` INT UNSIGNED NULL,
    `titulo` VARCHAR(150) NOT NULL,
    `mensaje` TEXT NOT NULL,
    `enlace` VARCHAR(255) NULL,
    `leido` TINYINT(1) NOT NULL DEFAULT 0,
    `fecha_leido` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_notif_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_notif_oficina` FOREIGN KEY (`oficina_id`) REFERENCES `oficinas` (`id`) ON DELETE CASCADE,
    INDEX `idx_notif_leido` (`leido`),
    INDEX `idx_notif_user` (`usuario_id`),
    INDEX `idx_notif_oficina` (`oficina_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 16. CONFIGURACIONES GENERALES DEL SISTEMA
DROP TABLE IF EXISTS `configuraciones`;
CREATE TABLE `configuraciones` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `clave` VARCHAR(80) NOT NULL UNIQUE,
    `valor` TEXT NULL,
    `tipo` VARCHAR(20) NOT NULL DEFAULT 'text',
    `descripcion` VARCHAR(255) NULL,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 17. CONFIGURACIÓN SMTP
DROP TABLE IF EXISTS `configuracion_smtp`;
CREATE TABLE `configuracion_smtp` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `host` VARCHAR(120) NOT NULL,
    `puerto` INT NOT NULL DEFAULT 587,
    `usuario` VARCHAR(120) NULL,
    `password` VARCHAR(255) NULL,
    `cifrado` VARCHAR(20) NOT NULL DEFAULT 'tls',
    `remitente_correo` VARCHAR(120) NOT NULL,
    `remitente_nombre` VARCHAR(120) NOT NULL,
    `activo` TINYINT(1) NOT NULL DEFAULT 0,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 18. AUDITORÍA INALTERABLE
DROP TABLE IF EXISTS `auditoria`;
CREATE TABLE `auditoria` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT UNSIGNED NULL,
    `accion` VARCHAR(100) NOT NULL,
    `modulo` VARCHAR(60) NOT NULL,
    `registro_id` VARCHAR(50) NULL,
    `detalles` TEXT NULL,
    `ip` VARCHAR(45) NULL,
    `user_agent` VARCHAR(255) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_auditoria_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
    INDEX `idx_auditoria_accion` (`accion`),
    INDEX `idx_auditoria_modulo` (`modulo`),
    INDEX `idx_auditoria_fecha` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 19. INTENTOS DE LOGIN Y PROTECCIÓN FUERZA BRUTA
DROP TABLE IF EXISTS `intentos_login`;
CREATE TABLE `intentos_login` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `ip` VARCHAR(45) NOT NULL,
    `identificador` VARCHAR(120) NOT NULL,
    `intentos` INT NOT NULL DEFAULT 1,
    `bloqueado_hasta` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_intentos_ip_identificador` (`ip`, `identificador`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
