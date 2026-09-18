-- ==========================================================
-- SISTEMA DE MESA DE PARTES VIRTUAL
-- Seed Inicial de Datos y Catálogos
-- ==========================================================

USE `mesa_partes_espinar`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. ROLES
INSERT INTO `roles` (`id`, `nombre`, `slug`, `descripcion`, `activo`) VALUES
(1, 'Superadministrador', 'superadministrador', 'Control total y configuración global del sistema', 1),
(2, 'Administrador', 'administrador', 'Gestión operativa, usuarios y catálogos', 1),
(3, 'Mesa de Partes', 'mesa-de-partes', 'Recepción, registro presencial y emisión de cargos', 1),
(4, 'Dirección', 'direccion', 'Revisión general, derivaciones principales y resoluciones', 1),
(5, 'Responsable de Oficina', 'responsable-oficina', 'Atención de expedientes, solicitudes internas y respuestas', 1),
(6, 'Consulta / Auditor', 'consulta-auditor', 'Solo lectura, auditoría y consulta de trazabilidad', 1)
ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`), `descripcion` = VALUES(`descripcion`);

-- 2. PERMISOS
INSERT INTO `permisos` (`id`, `clave`, `nombre`, `modulo`, `descripcion`) VALUES
(1, 'expedientes.ver', 'Ver Expedientes', 'Expedientes', 'Permite visualizar el listado de expedientes'),
(2, 'expedientes.crear', 'Registrar Expediente', 'Expedientes', 'Permite registrar expedientes presenciales o internos'),
(3, 'expedientes.derivar', 'Derivar Expediente', 'Expedientes', 'Permite transferir o derivar expediente a otra oficina'),
(4, 'expedientes.recibir', 'Recepcionar Expediente', 'Expedientes', 'Permite marcar como recepcionado un expediente'),
(5, 'expedientes.responder', 'Emitir Respuesta', 'Expedientes', 'Permite adjuntar informe de respuesta'),
(6, 'expedientes.finalizar', 'Finalizar Expediente', 'Expedientes', 'Permite dar por concluido un trámite'),
(7, 'expedientes.archivar', 'Archivar Expediente', 'Expedientes', 'Permite enviar a archivo central'),
(8, 'expedientes.devolver', 'Devolver Expediente', 'Expedientes', 'Permite retornar expediente con observación'),
(9, 'expedientes.anular', 'Anular Expediente', 'Expedientes', 'Permite anular un expediente formalmente'),
(10, 'usuarios.ver', 'Ver Usuarios', 'Usuarios', 'Permite ver lista de usuarios'),
(11, 'usuarios.crear', 'Crear Usuario', 'Usuarios', 'Permite registrar nuevos colaboradores'),
(12, 'usuarios.editar', 'Editar Usuario', 'Usuarios', 'Permite modificar roles, oficinas y datos'),
(13, 'usuarios.eliminar', 'Desactivar Usuario', 'Usuarios', 'Permite dar de baja lógica a un usuario'),
(14, 'roles.ver', 'Ver Roles', 'Roles', 'Permite listar roles y permisos'),
(15, 'roles.gestionar', 'Gestionar Roles', 'Roles', 'Permite modificar permisos de roles'),
(16, 'oficinas.ver', 'Ver Oficinas', 'Oficinas', 'Permite listar oficinas institucionales'),
(17, 'oficinas.gestionar', 'Gestionar Oficinas', 'Oficinas', 'Permite crear y editar oficinas'),
(18, 'tramites.ver', 'Ver Tipos de Trámite', 'Trámites', 'Permite listar tipos de trámite'),
(19, 'tramites.gestionar', 'Gestionar Tipos de Trámite', 'Trámites', 'Permite crear y editar tipos de trámite'),
(20, 'reportes.ver', 'Ver Reportes', 'Reportes', 'Permite generar reportes y estadísticas'),
(21, 'reportes.exportar', 'Exportar Reportes', 'Reportes', 'Permite descargar en PDF y Excel'),
(22, 'configuracion.ver', 'Ver Configuración', 'Configuración', 'Permite ver parámetros del sistema'),
(23, 'configuracion.editar', 'Editar Configuración', 'Configuración', 'Permite modificar colores, logos e identidad'),
(24, 'auditoria.ver', 'Ver Auditoría', 'Auditoría', 'Permite consultar el registro inalterable de auditoría')
ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`), `modulo` = VALUES(`modulo`), `descripcion` = VALUES(`descripcion`);

-- 3. ASIGNACIÓN INICIAL ROLES - PERMISOS
-- Superadministrador: Todos los permisos (1 a 24)
INSERT IGNORE INTO `roles_permisos` (`rol_id`, `permiso_id`)
SELECT 1, id FROM `permisos`;

-- Administrador: casi todo excepto configuraciones críticas de auditoría/roles root
INSERT IGNORE INTO `roles_permisos` (`rol_id`, `permiso_id`) VALUES
(2, 1), (2, 2), (2, 3), (2, 4), (2, 5), (2, 8), (2, 10), (2, 11), (2, 12), (2, 16), (2, 17), (2, 18), (2, 19), (2, 20), (2, 21), (2, 22), (2, 23);

-- Mesa de Partes: ver, crear, enviar a dirección, cargo
INSERT IGNORE INTO `roles_permisos` (`rol_id`, `permiso_id`) VALUES
(3, 1), (3, 2), (3, 3), (3, 20);

-- Dirección: ver, derivar, recibir, devolver, finalizar, archivar, reportes
INSERT IGNORE INTO `roles_permisos` (`rol_id`, `permiso_id`) VALUES
(4, 1), (4, 3), (4, 4), (4, 5), (4, 6), (4, 7), (4, 8), (4, 20), (4, 21);

-- Responsable de Oficina: ver, recibir, responder, solicitar info interna
INSERT IGNORE INTO `roles_permisos` (`rol_id`, `permiso_id`) VALUES
(5, 1), (5, 4), (5, 5), (5, 8);

-- Consulta / Auditor: solo ver y auditoría
INSERT IGNORE INTO `roles_permisos` (`rol_id`, `permiso_id`) VALUES
(6, 1), (6, 20), (6, 24);

-- 4. 19 OFICINAS INSTITUCIONALES (Sección 3 del Prompt)
INSERT INTO `oficinas` (`id`, `nombre`, `sigla`, `responsable`, `correo`, `telefono`, `orden`, `activo`) VALUES
(1, 'Dirección', 'DIR', 'Dr. Director General', 'direccion@iestpespinar.edu.pe', '084-301201', 1, 1),
(2, 'Mesa de Partes', 'MP', 'Lic. Encargado de Trámite', 'mesadepartes@iestpespinar.edu.pe', '084-301202', 2, 1),
(3, 'Jefatura de Unidad Académica', 'JUA', 'Mg. Jefe de Unidad Académica', 'academica@iestpespinar.edu.pe', '084-301203', 3, 1),
(4, 'Jefatura de Área de Administración', 'JAA', 'Lic. Administrador Institucional', 'administracion@iestpespinar.edu.pe', '084-301204', 4, 1),
(5, 'Secretaría Académica', 'SEC-ACAD', 'Abog. Secretaria Académica', 'secretaria@iestpespinar.edu.pe', '084-301205', 5, 1),
(6, 'Jefatura de Investigación', 'J-INV', 'Dr. Jefe de Investigación e Innovación', 'investigacion@iestpespinar.edu.pe', '084-301206', 6, 1),
(7, 'Área de Calidad', 'CALIDAD', 'Ing. Coordinador de Calidad', 'calidad@iestpespinar.edu.pe', '084-301207', 7, 1),
(8, 'Área de Empleabilidad', 'EMPLEAB', 'Lic. Coordinador de Prácticas y Empleo', 'empleabilidad@iestpespinar.edu.pe', '084-301208', 8, 1),
(9, 'Área de Formación Continua', 'FORM-CONT', 'Lic. Coordinador de Formación Continua', 'formacioncontinua@iestpespinar.edu.pe', '084-301209', 9, 1),
(10, 'Coordinación de Producción Agropecuaria', 'COORD-AGRO', 'Ing. Coordinador Agropecuaria', 'agropecuaria@iestpespinar.edu.pe', '084-301210', 10, 1),
(11, 'Coordinación APSTI', 'COORD-APSTI', 'Ing. Coordinador Sistemas e Informática', 'apsti@iestpespinar.edu.pe', '084-301211', 11, 1),
(12, 'Coordinación de Mecánica de Producción Industrial', 'COORD-MPI', 'Ing. Coordinador Mecánica', 'mecanica@iestpespinar.edu.pe', '084-301212', 12, 1),
(13, 'Coordinación de Electrónica Industrial', 'COORD-ELEC', 'Ing. Coordinador Electrónica', 'electronica@iestpespinar.edu.pe', '084-301213', 13, 1),
(14, 'Coordinación de Explotación Minera', 'COORD-MIN', 'Ing. Coordinador Minería', 'mineria@iestpespinar.edu.pe', '084-301214', 14, 1),
(15, 'Logística', 'LOG', 'Lic. Responsable de Logística y Abastecimiento', 'logistica@iestpespinar.edu.pe', '084-301215', 15, 1),
(16, 'Tesorería', 'TES', 'C.P.C. Tesorero', 'tesoreria@iestpespinar.edu.pe', '084-301216', 16, 1),
(17, 'Contabilidad', 'CONT', 'C.P.C. Contador Institucional', 'contabilidad@iestpespinar.edu.pe', '084-301217', 17, 1),
(18, 'Biblioteca', 'BIBLIO', 'Lic. Responsable de Biblioteca y Repositorio', 'biblioteca@iestpespinar.edu.pe', '084-301218', 18, 1),
(19, 'Patrimonio', 'PATRIM', 'Lic. Responsable de Control Patrimonial', 'patrimonio@iestpespinar.edu.pe', '084-301219', 19, 1)
ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`), `responsable` = VALUES(`responsable`);

-- 5. ESTADOS DE EXPEDIENTE (Sección 11)
INSERT INTO `estados_expediente` (`id`, `codigo`, `nombre`, `color`, `icono`, `orden`, `es_publico`, `activo`) VALUES
(1, 'RECIBIDO', 'Recibido', '#0284c7', 'bi-inbox', 1, 1, 1),
(2, 'REGISTRADO', 'Registrado', '#2563eb', 'bi-file-earmark-check', 2, 1, 1),
(3, 'ENVIADO_DIRECCION', 'Enviado a Dirección', '#7c3aed', 'bi-send', 3, 1, 1),
(4, 'EN_REVISION', 'En Revisión', '#9333ea', 'bi-search', 4, 1, 1),
(5, 'DERIVADO', 'Derivado', '#d97706', 'bi-arrow-right-circle', 5, 1, 1),
(6, 'RECEPCIONADO_POR_OFICINA', 'Recepcionado por Oficina', '#059669', 'bi-check2-circle', 6, 1, 1),
(7, 'EN_TRAMITE', 'En Trámite', '#0d9488', 'bi-gear-wide-connected', 7, 1, 1),
(8, 'PENDIENTE_INFORMACION', 'Pendiente de Información', '#ea580c', 'bi-clock-history', 8, 1, 1),
(9, 'OBSERVADO', 'Observado', '#dc2626', 'bi-exclamation-triangle', 9, 1, 1),
(10, 'DEVUELTO', 'Devuelto', '#b91c1c', 'bi-arrow-return-left', 10, 1, 1),
(11, 'RESPONDIDO', 'Respondido', '#16a34a', 'bi-reply-all', 11, 1, 1),
(12, 'PENDIENTE_APROBACION', 'Pendiente de Aprobación', '#4f46e5', 'bi-shield-check', 12, 1, 1),
(13, 'FINALIZADO', 'Finalizado', '#15803d', 'bi-check-all', 13, 1, 1),
(14, 'ARCHIVADO', 'Archivado', '#475569', 'bi-archive', 14, 1, 1),
(15, 'ANULADO', 'Anulado', '#991b1b', 'bi-x-circle', 15, 0, 1)
ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`);

-- 6. PRIORIDADES (Sección 12)
INSERT INTO `prioridades` (`id`, `codigo`, `nombre`, `color`, `dias_plazo`, `orden`, `activo`) VALUES
(1, 'NORMAL', 'Normal', '#0284c7', 15, 1, 1),
(2, 'URGENTE', 'Urgente', '#f59e0b', 5, 2, 1),
(3, 'MUY_URGENTE', 'Muy Urgente', '#ef4444', 2, 3, 1)
ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`);

-- 7. TIPOS DE TRÁMITE (Sección 13)
INSERT INTO `tipos_tramite` (`id`, `codigo`, `nombre`, `descripcion`, `oficina_sugerida_id`, `plazo_referencial_dias`, `requisitos`, `instrucciones`, `permite_virtual`, `requiere_pago`, `monto`, `activo`) VALUES
(1, 'SOL-GEN', 'Solicitud General / Fut', 'Peticiones generales ciudadanas y estudiantiles', 2, 15, 'Documento de identidad legible, formato FUT debidamente llenado.', 'Adjuntar fundamentación clara y documentos de sustento.', 1, 0, 0.00, 1),
(2, 'CONST-EST', 'Constancia de Estudios', 'Constancia oficial de matrícula o estudios cursados', 5, 5, 'DNI, última boleta de notas o matrícula.', 'El trámite es procesado por Secretaría Académica.', 1, 0, 0.00, 1),
(3, 'CERT-EST', 'Certificado Oficial de Estudios', 'Certificado modular oficial con calificaciones', 5, 10, 'Fotografía tamaño carné, recibo de pago de derechos.', 'Emitido con firmas y sellos oficiales institucionales.', 1, 1, 25.00, 1),
(4, 'TIT-PROF', 'Emisión de Título Profesional', 'Trámite de titulación y validación de egresado', 5, 30, 'Constancia de egresado, certificados modulares, prácticas validadas por Empleabilidad, constancia de no adeudo.', 'Requiere verificación académica, de empleabilidad y dirección.', 1, 1, 120.00, 1),
(5, 'RECT-NOT', 'Rectificación de Matrícula o Calificaciones', 'Corrección justificada de notas o asignaturas', 3, 7, 'Informe docente o justificación documentada.', 'Derivado a Unidad Académica para dictamen técnico.', 1, 0, 0.00, 1),
(6, 'PRAC-PRE', 'Validación de Prácticas Preprofesionales', 'Acreditación de horas de prácticas por programa', 8, 10, 'Plan de prácticas, informe final visado por empresa/institución.', 'Revisado por Área de Empleabilidad.', 1, 0, 0.00, 1),
(7, 'INV-REP', 'Registro de Proyecto o Tesis en Repositorio', 'Depósito y validación de trabajos de investigación', 6, 15, 'Tesis en PDF, acta de sustentación, cesión de derechos.', 'Coordinación con Biblioteca para registro en repositorio.', 1, 0, 0.00, 1),
(8, 'ACC-INFO', 'Acceso a la Información Pública', 'Solicitud bajo Ley de Transparencia y Acceso a la Información', 1, 10, 'Formato Ley de Transparencia, DNI.', 'Atendido directamente bajo supervisión de Dirección General.', 1, 0, 0.00, 1),
(9, 'REC-INST', 'Reclamo o Sugerencia Institucional', 'Presentación de inconformidad respecto a la prestación del servicio', 4, 15, 'Detalle de los hechos, pruebas anexas si correspondiera.', 'Canalizado para subsanación oportuna.', 1, 0, 0.00, 1)
ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`), `descripcion` = VALUES(`descripcion`), `requisitos` = VALUES(`requisitos`), `instrucciones` = VALUES(`instrucciones`);

-- 8. CONFIGURACIONES GENERALES (Secciones 22, 34 y 57)
INSERT INTO `configuraciones` (`clave`, `valor`, `tipo`, `descripcion`) VALUES
('institucion_nombre', 'INSTITUTO DE EDUCACIÓN SUPERIOR TECNOLÓGICO PÚBLICO ESPINAR', 'text', 'Nombre oficial de la institución'),
('institucion_sigla', 'IESTP ESPINAR', 'text', 'Sigla o nombre corto institucional'),
('institucion_ruc', '20490000001', 'text', 'Registro Único de Contribuyentes'),
('institucion_direccion', 'Av. San Martín S/N, Espinar, Cusco, Perú', 'text', 'Dirección fiscal y sede central'),
('institucion_telefono', '(084) 301234', 'text', 'Teléfono de contacto para la ciudadanía'),
('institucion_correo', 'mesadepartes@iestpespinar.edu.pe', 'text', 'Correo institucional de atención al público'),
('institucion_web', 'https://iestpespinar.edu.pe', 'text', 'Portal web institucional oficial'),
('institucion_horario', 'Lunes a Viernes de 08:00 a.m. a 04:30 p.m.', 'text', 'Horario de recepción y atención'),
('expedientes_prefijo', 'EXP', 'text', 'Prefijo para generación del correlativo'),
('expedientes_digitos', '6', 'number', 'Cantidad de dígitos con ceros a la izquierda'),
('expedientes_reinicio_anual', '1', 'boolean', 'Reiniciar secuencia correlativa cada nuevo año'),
('color_primario', '#0B4F8A', 'color', 'Color primario de la interfaz'),
('color_secundario', '#F59E0B', 'color', 'Color secundario institucional'),
('color_acento', '#DC2626', 'color', 'Color de acento y alertas'),
('color_sidebar', '#102A43', 'color', 'Color de fondo de barra lateral'),
('color_encabezado', '#FFFFFF', 'color', 'Color de encabezado superior'),
('color_texto', '#1E293B', 'color', 'Color de texto principal'),
('color_fondo', '#F8FAFC', 'color', 'Fondo general del panel'),
('logo_principal', '/assets/img/logo.png', 'image', 'Ruta del logotipo principal'),
('logo_login', '/assets/img/logo.png', 'image', 'Ruta del logotipo en pantalla de login'),
('favicon', '/assets/img/favicon.png', 'image', 'Ruta del favicon')
ON DUPLICATE KEY UPDATE `valor` = VALUES(`valor`);

-- 9. CONFIGURACIÓN SMTP (Inicial inactiva)
INSERT INTO `configuracion_smtp` (`id`, `host`, `puerto`, `usuario`, `password`, `cifrado`, `remitente_correo`, `remitente_nombre`, `activo`) VALUES
(1, 'smtp.gmail.com', 587, 'notificaciones@iestpespinar.edu.pe', '', 'tls', 'mesadepartes@iestpespinar.edu.pe', 'Mesa de Partes - IESTP Espinar', 0)
ON DUPLICATE KEY UPDATE `host` = VALUES(`host`);

-- 10. USUARIOS INICIALES
-- Superadmin: clave temporal Admin2026! (obliga a cambiar en primer acceso según sección 45)
INSERT INTO `usuarios` (`id`, `nombres`, `apellidos`, `dni`, `usuario`, `correo`, `password`, `rol_id`, `oficina_id`, `cargo`, `telefono`, `estado`, `debe_cambiar_password`) VALUES
(1, 'Administrador', 'General', '00000000', 'admin', 'admin@iestpespinar.edu.pe', '$2y$10$IKQRwLdud63dm34H7fvN3.kinwXma3G7hoHE9U4cARBIUvL4uHYgW', 1, 1, 'Superadministrador del Sistema', '984000000', 1, 1),
(2, 'Operador', 'Mesa de Partes', '11111111', 'mesapartes', 'mesadepartes@iestpespinar.edu.pe', '$2y$10$IKQRwLdud63dm34H7fvN3.kinwXma3G7hoHE9U4cARBIUvL4uHYgW', 3, 2, 'Responsable de Ventanilla', '984000001', 1, 0),
(3, 'Director', 'General', '22222222', 'director', 'direccion@iestpespinar.edu.pe', '$2y$10$IKQRwLdud63dm34H7fvN3.kinwXma3G7hoHE9U4cARBIUvL4uHYgW', 4, 1, 'Director General IESTP Espinar', '984000002', 1, 0),
(4, 'Secretaría', 'Académica', '33333333', 'sec_academica', 'secretaria@iestpespinar.edu.pe', '$2y$10$IKQRwLdud63dm34H7fvN3.kinwXma3G7hoHE9U4cARBIUvL4uHYgW', 5, 5, 'Secretaria Académica', '984000003', 1, 0)
ON DUPLICATE KEY UPDATE `usuario` = VALUES(`usuario`);

SET FOREIGN_KEY_CHECKS = 1;
