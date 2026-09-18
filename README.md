# Sistema Web de Mesa de Partes Virtual — IESTP Espinar

Sistema integral y modular de **Mesa de Partes Virtual y Trámite Documentario Institucional** desarrollado exclusivamente con **PHP nativo 8.2+**, arquitectura **MVC**, base de datos relacional **MySQL 8+ / MariaDB**, **PDO**, **Bootstrap 5**, **CSS dinámico** y **JavaScript Vanilla**, sin dependencias de frameworks externos.

> 📘 **¿Buscas el manual paso a paso por roles?** Consulta el [Manual de Usuario (MANUAL_DE_USUARIO.md)](file:///c:/xampp/htdocs/mesa-espinar/MANUAL_DE_USUARIO.md) con guías prácticas para Ciudadanos, Mesa de Partes, Dirección, Oficinas y Administrador.

---

## 1. Características Principales

- **Arquitectura MVC Pura**: Estricta separación entre Controladores, Modelos, Vistas y Helpers, sin frameworks PHP.
- **Flujo Documental Completo**:
  ```text
  Ciudadano / Ventanilla → Mesa de Partes → Dirección General → Oficina Responsable → Dirección General → Respuesta / Finalización → Usuario Externo
  ```
- **19 Oficinas Institucionales**: Dirección, Mesa de Partes, Jefatura de Unidad Académica, Secretaría Académica, Investigación, Calidad, Empleabilidad, Biblioteca, Logística, Contabilidad, Tesorería, etc.
- **Interacciones Especiales y Colaboración Interna**:
  - Secretaría Académica consulta a Calidad y Empleabilidad (grados, títulos, validaciones).
  - Jefatura de Investigación consulta a Biblioteca (tesis, repositorio).
  - Preservación estricta de la oficina responsable principal sin transferir titularidad accidental.
- **Control de Acceso RBAC Granular**: Roles (Superadministrador, Administrador, Mesa de Partes, Dirección, Responsable de Oficina, Consulta) y matriz administrable de permisos por módulo.
- **Numeración Correlativa Atómica**: Formato `EXP-YYYY-NNNNNN` con bloqueos transaccionales contra condiciones de carrera y código alfanumérico único de seguimiento.
- **Portal Ciudadano Público**:
  - Presentación de trámites en línea (`/tramite`) para personas naturales o jurídicas.
  - Validación anti-spam mediante captcha matemático.
  - Consulta pública de estado en tiempo real (`/consulta`) con línea de tiempo protegida (sin fugas de notas o archivos internos).
  - Emisión de **Cargo Oficial de Recepción** en PDF con código QR de verificación.
- **Almacenamiento Documental Seguro**:
  - Archivos guardados fuera del web root en `storage/documents/` con nombres aleatorios y hash SHA-256.
  - Validación estricta de MIME real mediante `finfo`.
  - Descargas controladas por controlador con verificación de autenticación y auditoría.
- **Personalización Visual Dinámica (Sección 57)**:
  - Selector en vivo de colores (primario, secundario, acento, sidebar, header).
  - Generador de variables CSS `:root` dinámicas.
  - Subida de logotipos y favicon con vista previa instantánea y botón para restablecer valores originales.
- **Bitácora Inalterable de Auditoría**: Registro de IP, fecha, usuario, módulo y acciones críticas.

---

## 2. Árbol de Carpetas del Proyecto

```text
mesa-espinar/
├── app/
│   ├── Controllers/
│   │   ├── AuditoriaController.php
│   │   ├── AuthController.php
│   │   ├── BandejaController.php
│   │   ├── ConfiguracionController.php
│   │   ├── ConsultaController.php
│   │   ├── DashboardController.php
│   │   ├── DireccionController.php
│   │   ├── EstadoController.php
│   │   ├── ExpedienteController.php
│   │   ├── NotificacionController.php
│   │   ├── OficinaController.php
│   │   ├── ReporteController.php
│   │   ├── RolController.php
│   │   ├── TipoTramiteController.php
│   │   ├── TramiteController.php
│   │   └── UsuarioController.php
│   ├── Core/
│   │   ├── App.php
│   │   ├── Auth.php
│   │   ├── Controller.php
│   │   ├── CSRF.php
│   │   ├── Database.php
│   │   ├── Model.php
│   │   ├── Response.php
│   │   ├── Router.php
│   │   ├── Session.php
│   │   └── Validator.php
│   ├── Helpers/
│   │   └── helpers.php
│   ├── Middleware/
│   │   ├── AuthMiddleware.php
│   │   ├── GuestMiddleware.php
│   │   └── PermissionMiddleware.php
│   ├── Models/
│   │   ├── Documento.php
│   │   ├── Estado.php
│   │   ├── Expediente.php
│   │   ├── Movimiento.php
│   │   ├── Notificacion.php
│   │   ├── Oficina.php
│   │   ├── Permiso.php
│   │   ├── Rol.php
│   │   ├── TipoTramite.php
│   │   └── Usuario.php
│   └── Views/
│       ├── auth/
│       ├── auditoria/
│       ├── configuracion/
│       ├── dashboard/
│       ├── direccion/
│       ├── estados/
│       ├── expedientes/
│       ├── layouts/
│       ├── notificaciones/
│       ├── oficinas/
│       ├── public/
│       ├── reportes/
│       ├── roles/
│       ├── tipos_tramite/
│       └── usuarios/
├── config/
│   ├── app.php
│   ├── database.php
│   ├── mail.php
│   └── routes.php
├── database/
│   ├── schema.sql
│   └── seed.sql
├── public/
│   ├── assets/ (css, js, img)
│   ├── uploads/ (config, logos, favicon)
│   ├── index.php
│   └── .htaccess
├── storage/
│   ├── documents/ (archivos de expedientes protegidos)
│   ├── logs/ (error.log, app.log)
│   └── temp/
├── .env
├── .env.example
├── .htaccess (redirección a public)
└── README.md
```

---

## 3. Requisitos del Sistema

- **Servidor Web**: Apache 2.4 o superior con módulo `mod_rewrite` habilitado.
- **Lenguaje**: PHP 8.2 o superior (extensiones activas: `pdo_mysql`, `fileinfo`, `mbstring`, `openssl`).
- **Base de Datos**: MySQL 8.0+ o MariaDB 10.4+.
- **Entorno Local**: XAMPP, WampServer o Laragon en Windows / Linux.

---

## 4. Guía de Instalación Rápida en XAMPP

1. **Ubicación del Proyecto**:
   Ubicar la carpeta del proyecto en el directorio `htdocs` de XAMPP:
   ```text
   C:\xampp\htdocs\mesa-espinar\
   ```

2. **Crear e Importar la Base de Datos**:
   - Abrir una consola o phpMyAdmin e importar el esquema e inserciones iniciales:
   ```powershell
   Get-Content "C:\xampp\htdocs\mesa-espinar\database\schema.sql" -Raw | & "C:\xampp\mysql\bin\mysql.exe" -u root
   Get-Content "C:\xampp\htdocs\mesa-espinar\database\seed.sql" -Raw | & "C:\xampp\mysql\bin\mysql.exe" -u root
   ```

3. **Configuración de Variables de Entorno (`.env`)**:
   - El archivo `.env` ya se encuentra configurado para la base de datos `mesa_partes_espinar`:
   ```ini
   APP_NAME="Mesa de Partes Virtual - IESTP Espinar"
   APP_ENV=local
   APP_DEBUG=true
   APP_URL=http://localhost/mesa-espinar
   APP_TIMEZONE=America/Lima

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=mesa_partes_espinar
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Acceso al Sistema**:
   - **Portal Ciudadano**: [http://localhost/mesa-espinar/](http://localhost/mesa-espinar/)
   - **Presentación de Trámite**: [http://localhost/mesa-espinar/tramite](http://localhost/mesa-espinar/tramite)
   - **Consulta Pública**: [http://localhost/mesa-espinar/consulta](http://localhost/mesa-espinar/consulta)
   - **Acceso a Intranet**: [http://localhost/mesa-espinar/login](http://localhost/mesa-espinar/login)

---

## 5. Credenciales Iniciales de Prueba

| Usuario | Rol Institucional | Oficina Asignada | Contraseña Inicial |
|---|---|---|---|
| **admin** | Superadministrador | Dirección General | `Admin2026!` *(exige cambio en 1er login)* |
| **mesapartes** | Mesa de Partes | Mesa de Partes | `Admin2026!` |
| **director** | Dirección General | Dirección | `Admin2026!` |
| **sec_academica** | Responsable de Oficina | Secretaría Académica | `Admin2026!` |

---

## 6. Módulos y Funcionalidades Disponibles

1. **Portal Ciudadano**: Registro de trámites virtuales, selección de tipo de trámite con visualización de requisitos y costos TUPA, adjuntos y emisión de cargo imprimible con QR.
2. **Consulta Pública**: Búsqueda por número o código con trazabilidad de movimientos públicos.
3. **Mesa de Partes**: Registro presencial en ventanilla, corrección de datos y envío a Dirección.
4. **Dirección General**: Bandeja de entrada especializada, derivación técnica a oficinas, asignación de prioridades, devolución con observaciones y finalización formal.
5. **Bandeja de Oficinas**: Recepción formal con sello de tiempo e IP, atención de trámites, consultas internas interoficinas (Secretaría Académica <-> Calidad/Empleabilidad, Investigación <-> Biblioteca) y emisión de informes de respuesta a Dirección.
6. **Dashboard y Reportes**: KPIs en tiempo real, gráficos circulares y de barras con Chart.js, filtros multidimensionales y exportación en CSV/Excel e impresión en PDF.
7. **Personalización Visual**: Panel dinámico con selectores de color, previsualización interactiva de componentes, carga de logotipos y favicon.
8. **Seguridad y Auditoría**: Bitácora inalterable con registro de IP, agente de usuario, prevención de ataques de fuerza bruta con bloqueo temporal y token anti-CSRF en todos los formularios.
