# PROMPT MAESTRO — SISTEMA DE MESA DE PARTES VIRTUAL

## 1. OBJETIVO GENERAL

Desarrolla un **Sistema Web de Mesa de Partes Virtual** completo para una institución educativa superior, utilizando exclusivamente:

- **PHP nativo 8.2 o superior**
- Arquitectura **MVC**
- **MySQL 8 o superior**
- HTML5
- CSS3
- JavaScript Vanilla
- Bootstrap 5 o CSS propio para la interfaz
- PDO para conexión segura a la base de datos
- Apache con `.htaccess` para URLs amigables

NO utilizar Laravel, Symfony, CodeIgniter ni otro framework PHP.

El sistema debe ser modular, seguro, responsivo, mantenible, escalable y preparado para producción.

---

# 2. CONTEXTO DEL SISTEMA

El sistema gestionará el trámite documentario institucional.

El flujo general es:

```text
USUARIO
   ↓
MESA DE PARTES
   ↓
DIRECCIÓN
   ↓
OFICINA RESPONSABLE
   ↓
DIRECCIÓN
   ↓
MESA DE PARTES / RESPUESTA
   ↓
USUARIO
```

Todos los documentos ingresan primero por **Mesa de Partes**.

Mesa de Partes registra el documento y genera automáticamente un número único de expediente.

Luego el expediente es enviado a **Dirección**.

Dirección revisa el expediente y lo deriva a una oficina responsable.

Las oficinas atienden los expedientes recibidos y remiten sus respuestas a Dirección.

Dirección puede aprobar, observar, devolver, redirigir, finalizar o disponer nuevas acciones.

El usuario externo debe poder consultar el estado de su trámite utilizando el **número de expediente**.

---

# 3. OFICINAS INSTITUCIONALES

El sistema debe permitir administrar dinámicamente las oficinas desde el panel de administración.

Crear inicialmente las siguientes oficinas:

1. Dirección
2. Mesa de Partes
3. Jefatura de Unidad Académica
4. Jefatura de Área de Administración
5. Secretaría Académica
6. Jefatura de Investigación
7. Área de Calidad
8. Área de Empleabilidad
9. Área de Formación Continua
10. Coordinación de Producción Agropecuaria
11. Coordinación APSTI
12. Coordinación de Mecánica de Producción Industrial
13. Coordinación de Electrónica Industrial
14. Coordinación de Explotación Minera
15. Logística
16. Tesorería
17. Contabilidad
18. Biblioteca
19. Patrimonio

Las oficinas deben poder:

- Crearse
- Editarse
- Activarse
- Desactivarse
- Ordenarse
- Asignarse a usuarios
- Tener responsable
- Tener correo institucional
- Tener teléfono opcional

Nunca eliminar físicamente una oficina si tiene historial de expedientes.

Usar eliminación lógica mediante campo `activo`.

---

# 4. INTERACCIONES ESPECIALES ENTRE OFICINAS

Además de las derivaciones realizadas por Dirección, deben existir interacciones internas.

## Secretaría Académica

Secretaría Académica puede solicitar información o colaboración a:

- Área de Calidad
- Área de Empleabilidad

Especialmente para trámites relacionados con:

- Títulos
- Grados
- Certificados
- Egresados
- Validaciones académicas

El expediente debe seguir teniendo como oficina responsable principal a Secretaría Académica mientras Calidad o Empleabilidad participan como oficinas de apoyo.

## Investigación

Jefatura de Investigación puede interactuar con:

- Biblioteca

Biblioteca podrá responder solicitudes internas relacionadas con documentos, repositorio, investigación, publicaciones u otros.

Estas interacciones deben quedar registradas en el historial del expediente.

---

# 5. TIPOS DE MOVIMIENTO

Diferenciar claramente:

## A. DERIVACIÓN

Una derivación cambia la oficina responsable del expediente.

Ejemplo:

```text
DIRECCIÓN → SECRETARÍA ACADÉMICA
```

## B. SOLICITUD DE INFORMACIÓN

No cambia necesariamente la oficina responsable.

Ejemplo:

```text
SECRETARÍA ACADÉMICA
        ↓
ÁREA DE CALIDAD
        ↓
SECRETARÍA ACADÉMICA
```

## C. DEVOLUCIÓN

Una oficina puede devolver un expediente indicando obligatoriamente el motivo.

## D. RESPUESTA

Una oficina registra la atención realizada y adjunta documentos.

## E. FINALIZACIÓN

Dirección puede cerrar definitivamente un expediente.

Todos estos movimientos deben quedar registrados.

---

# 6. ROLES DEL SISTEMA

Implementar control de acceso basado en roles y permisos.

Roles iniciales:

## SUPERADMINISTRADOR

Puede:

- Gestionar usuarios
- Gestionar roles
- Gestionar permisos
- Gestionar oficinas
- Gestionar configuración institucional
- Ver todos los expedientes
- Ver auditoría
- Configurar tipos de trámite
- Configurar estados
- Configurar formatos
- Configurar numeración

## ADMINISTRADOR

Puede administrar el sistema según permisos asignados.

## MESA DE PARTES

Puede:

- Registrar expedientes presenciales
- Ver expedientes ingresados
- Adjuntar documentos
- Corregir datos antes de derivación
- Enviar expedientes a Dirección
- Registrar documentos de salida
- Consultar expedientes
- Imprimir cargo

## DIRECCIÓN

Puede:

- Ver todos los expedientes enviados a Dirección
- Revisar documentos
- Derivar a oficinas
- Cambiar prioridad
- Agregar observaciones
- Devolver expedientes
- Recibir respuestas
- Reasignar expedientes
- Aprobar respuestas
- Finalizar expedientes
- Visualizar trazabilidad completa

## RESPONSABLE DE OFICINA

Puede:

- Ver expedientes de su oficina
- Recibir expedientes
- Registrar atención
- Adjuntar documentos
- Realizar observaciones
- Solicitar información a otra oficina autorizada
- Responder solicitudes internas
- Remitir respuesta a Dirección

## CONSULTA / AUDITOR

Rol opcional de solo lectura.

---

# 7. USUARIO EXTERNO

El ciudadano o usuario externo **NO requiere iniciar sesión**.

Debe poder:

## Presentar un trámite

Mediante formulario público.

Campos mínimos:

- Tipo de persona:
  - Natural
  - Jurídica
- DNI / CE / RUC
- Nombres
- Apellidos
- Razón social
- Correo electrónico
- Teléfono
- Dirección
- Tipo de trámite
- Asunto
- Descripción
- Número de folios
- Archivo principal
- Archivos anexos
- Aceptación de declaración de veracidad
- Aceptación de tratamiento de datos según corresponda
- CAPTCHA o protección anti-spam

Al registrar correctamente:

- Generar número de expediente
- Mostrar confirmación
- Permitir descargar cargo en PDF
- Mostrar código de seguimiento
- Registrar fecha y hora
- Enviar confirmación al correo si SMTP está configurado

---

# 8. NUMERACIÓN DE EXPEDIENTES

Generar automáticamente números únicos.

Formato configurable.

Formato inicial sugerido:

```text
EXP-2026-000001
EXP-2026-000002
EXP-2026-000003
```

El administrador debe poder configurar:

- Prefijo
- Año
- Número inicial
- Cantidad de dígitos
- Reinicio anual

Evitar duplicados mediante transacciones y restricciones UNIQUE.

---

# 9. CONSULTA PÚBLICA DEL TRÁMITE

Crear página pública:

```text
/consulta
```

Permitir buscar por:

- Número de expediente
- Código de seguimiento

Como seguridad adicional se puede solicitar:

- DNI/RUC del solicitante
- Código de verificación

Mostrar:

- Número de expediente
- Tipo de trámite
- Fecha de ingreso
- Estado actual
- Oficina actual
- Fecha de última actualización
- Observación pública
- Línea de tiempo del trámite

Ejemplo:

```text
EXPEDIENTE: EXP-2026-000145
ESTADO: EN TRÁMITE
OFICINA ACTUAL: SECRETARÍA ACADÉMICA
ÚLTIMA ACTUALIZACIÓN: 17/09/2026 10:35
```

No mostrar:

- Observaciones internas
- Información confidencial
- Datos de otros usuarios
- Documentos internos restringidos

---

# 10. LÍNEA DE TIEMPO DEL EXPEDIENTE

Cada expediente debe poseer trazabilidad completa.

Ejemplo:

```text
15/09/2026 09:15
Mesa de Partes
Documento recibido.

15/09/2026 10:05
Mesa de Partes
Enviado a Dirección.

15/09/2026 11:30
Dirección
Derivado a Secretaría Académica.

16/09/2026 09:40
Secretaría Académica
Expediente recibido.

17/09/2026 10:25
Secretaría Académica
Solicitud de información enviada a Calidad.
```

Registrar como mínimo:

- Expediente
- Usuario responsable
- Oficina origen
- Oficina destino
- Tipo de movimiento
- Fecha
- Hora
- Estado anterior
- Estado nuevo
- Observación
- IP
- User Agent
- Documento adjunto asociado si corresponde

El historial no debe poder eliminarse desde la interfaz normal.

---

# 11. ESTADOS DEL EXPEDIENTE

Crear catálogo administrable.

Estados iniciales:

- RECIBIDO
- REGISTRADO
- ENVIADO A DIRECCIÓN
- EN REVISIÓN
- DERIVADO
- RECEPCIONADO POR OFICINA
- EN TRÁMITE
- PENDIENTE DE INFORMACIÓN
- OBSERVADO
- DEVUELTO
- RESPONDIDO
- PENDIENTE DE APROBACIÓN
- FINALIZADO
- ARCHIVADO
- ANULADO

Cada estado debe tener:

- ID
- Nombre
- Código
- Color
- Icono opcional
- Orden
- Activo
- Visibilidad pública

---

# 12. PRIORIDAD

Cada expediente debe poseer prioridad:

- Normal
- Urgente
- Muy urgente

Permitir configurar colores.

Registrar cambios de prioridad en auditoría.

---

# 13. TIPOS DE TRÁMITE

Crear módulo administrable.

Campos:

- Código
- Nombre
- Descripción
- Oficina sugerida
- Plazo referencial
- Requisitos
- Activo
- Permite trámite virtual
- Requiere pago
- Monto opcional
- Instrucciones

Ejemplos:

- Solicitud
- Constancia
- Certificado
- Título
- Rectificación
- Reclamo
- Petición
- Acceso a información
- Otros

---

# 14. MÓDULO DE EXPEDIENTES

Pantalla principal tipo bandeja.

Filtros:

- Número de expediente
- Solicitante
- DNI/RUC
- Asunto
- Tipo de trámite
- Estado
- Oficina
- Fecha desde
- Fecha hasta
- Prioridad
- Responsable

Columnas:

- Expediente
- Fecha
- Solicitante
- Asunto
- Tipo
- Oficina actual
- Estado
- Prioridad
- Última actualización
- Acciones

Acciones posibles según permisos:

- Ver
- Derivar
- Recibir
- Responder
- Solicitar información
- Observar
- Devolver
- Adjuntar documento
- Finalizar
- Archivar
- Imprimir
- Ver historial

---

# 15. RECEPCIÓN DEL EXPEDIENTE

Cuando una oficina recibe un expediente debe existir opción:

```text
RECEPCIONAR EXPEDIENTE
```

Registrar:

- Fecha de recepción
- Hora
- Usuario
- Oficina
- IP

Esto permite diferenciar:

```text
DERIVADO
```

de:

```text
RECEPCIONADO
```

---

# 16. DOCUMENTOS Y ARCHIVOS

Permitir subir:

- PDF
- DOC
- DOCX
- XLS
- XLSX
- JPG
- JPEG
- PNG

Configurable desde administración.

Aplicar:

- Tamaño máximo configurable
- Validación de MIME real
- Renombrado interno seguro
- Evitar ejecución de archivos
- Almacenamiento fuera de `/public` si es posible
- Descarga mediante controlador con validación de permisos
- Hash SHA-256 del archivo
- Registro de nombre original
- Registro de tamaño
- Registro de usuario que subió
- Fecha y hora

Nunca confiar únicamente en la extensión.

---

# 17. RESPUESTAS

Una oficina podrá registrar:

- Tipo de respuesta
- Descripción
- Observación
- Documento principal
- Anexos
- Fecha
- Responsable

La respuesta deberá remitirse a Dirección.

Dirección podrá:

- Aprobar
- Observar
- Devolver
- Solicitar ampliación
- Derivar nuevamente
- Finalizar

---

# 18. DASHBOARD

Crear dashboard moderno y responsivo.

Mostrar tarjetas:

- Expedientes ingresados hoy
- Expedientes del mes
- Expedientes pendientes
- En Dirección
- En oficinas
- Observados
- Finalizados
- Urgentes

Gráficos:

- Expedientes por mes
- Expedientes por estado
- Expedientes por oficina
- Expedientes por tipo de trámite
- Tiempo promedio de atención

Mostrar:

- Últimos expedientes
- Expedientes urgentes
- Expedientes con mayor antigüedad

---

# 19. REPORTES

Crear módulo de reportes.

Reportes:

- Expedientes por rango de fecha
- Expedientes por oficina
- Expedientes por estado
- Expedientes por tipo
- Expedientes por usuario
- Expedientes atendidos
- Expedientes pendientes
- Expedientes finalizados
- Expedientes observados
- Tiempo promedio de atención
- Productividad por oficina

Permitir exportar:

- PDF
- Excel/CSV

Permitir imprimir.

---

# 20. NOTIFICACIONES INTERNAS

Implementar sistema de notificaciones.

Ejemplos:

- Nuevo expediente recibido
- Expediente derivado
- Solicitud de información
- Respuesta recibida
- Expediente observado
- Expediente devuelto
- Expediente urgente
- Expediente finalizado

Mostrar:

- Campana
- Contador
- Fecha
- Enlace
- Marcar como leído

---

# 21. CORREO ELECTRÓNICO

Crear configuración SMTP administrable.

Campos:

- Host
- Puerto
- Usuario
- Contraseña cifrada
- Seguridad TLS/SSL
- Correo remitente
- Nombre remitente

Notificaciones opcionales al usuario:

- Registro del expediente
- Cambio relevante de estado
- Observación
- Finalización

Crear plantillas de correo configurables.

---

# 22. CONFIGURACIÓN INSTITUCIONAL

Crear módulo:

```text
ADMINISTRACIÓN → CONFIGURACIÓN
```

Debe permitir modificar sin tocar código:

## Identidad

- Nombre de la institución
- Nombre corto
- RUC
- Dirección
- Teléfono
- Correo
- Página web

## LOGO

Permitir subir:

- PNG
- JPG
- WEBP
- SVG solo si se sanitiza de forma segura

Mostrar preview.

Permitir:

- Logo principal
- Logo para login
- Logo claro
- Logo oscuro

Guardar rutas en base de datos.

## FAVICON

Permitir subir favicon.

Formatos:

- PNG
- ICO
- SVG sanitizado

Actualizar automáticamente el favicon del sistema.

## COLORES

Crear selector de colores para:

- Color principal
- Color secundario
- Color de acento
- Color de botones
- Color del sidebar
- Color del encabezado
- Color del texto
- Fondo general
- Color de enlaces

Ejemplo:

```text
Principal: #0B4F8A
Secundario: #F59E0B
Acento: #DC2626
```

Generar variables CSS dinámicas:

```css
:root {
    --primary: #0B4F8A;
    --secondary: #F59E0B;
    --accent: #DC2626;
}
```

Los cambios deben reflejarse automáticamente en:

- Login
- Dashboard
- Sidebar
- Botones
- Badges
- Formularios
- Página pública
- Consulta pública

No requerir editar archivos CSS manualmente.

---

# 23. LOGIN

Crear página moderna.

Debe usar:

- Logo configurado
- Nombre institucional
- Colores configurados
- Usuario/correo
- Contraseña
- Mostrar/ocultar contraseña

Agregar:

- Protección CSRF
- Control de intentos fallidos
- Bloqueo temporal
- Regeneración de sesión
- `password_hash()`
- `password_verify()`

---

# 24. GESTIÓN DE USUARIOS

Campos:

- Nombres
- Apellidos
- DNI
- Usuario
- Correo
- Contraseña
- Rol
- Oficina
- Cargo
- Teléfono
- Estado
- Último acceso
- Fecha de creación

Funciones:

- Crear
- Editar
- Activar
- Desactivar
- Restablecer contraseña
- Cambiar rol
- Cambiar oficina

No eliminar físicamente usuarios con historial.

---

# 25. ROLES Y PERMISOS

No codificar todos los permisos directamente.

Implementar RBAC.

Tablas:

```text
roles
permisos
roles_permisos
usuarios_roles
```

Permisos ejemplo:

```text
expedientes.ver
expedientes.crear
expedientes.derivar
expedientes.recibir
expedientes.responder
expedientes.finalizar
expedientes.archivar

usuarios.ver
usuarios.crear
usuarios.editar

oficinas.ver
oficinas.crear
oficinas.editar

reportes.ver
configuracion.editar
auditoria.ver
```

---

# 26. AUDITORÍA

Crear módulo inalterable para usuarios comunes.

Registrar:

- Inicio de sesión
- Cierre de sesión
- Intento fallido
- Creación
- Edición
- Derivación
- Recepción
- Respuesta
- Finalización
- Descarga de archivos sensibles
- Cambio de configuración
- Cambio de permisos
- Cambio de contraseña

Campos:

- Usuario
- Acción
- Módulo
- Registro afectado
- IP
- User Agent
- Fecha
- Hora
- Datos anteriores opcionales
- Datos nuevos opcionales

---

# 27. SEGURIDAD

Aplicar obligatoriamente:

- PDO
- Prepared Statements
- CSRF Token
- Escape de salida con `htmlspecialchars`
- Validación backend
- Validación frontend
- Session fixation protection
- Regenerar session ID después del login
- Cookies HttpOnly
- Cookies Secure cuando exista HTTPS
- SameSite
- Rate limiting básico
- Protección contra fuerza bruta
- Validación de archivos
- Protección contra path traversal
- Protección XSS
- Protección SQL Injection
- Control de permisos en cada controlador
- No confiar en parámetros enviados por frontend
- Deshabilitar directory listing
- Configurar headers de seguridad
- Registrar errores sin mostrarlos al usuario en producción

Crear archivo:

```text
.env
```

o configuración equivalente fuera del directorio público.

Nunca incluir credenciales directamente en Git.

---

# 28. ARQUITECTURA MVC

Usar estructura clara.

Ejemplo:

```text
/
├── app/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── ExpedienteController.php
│   │   ├── TramiteController.php
│   │   ├── DireccionController.php
│   │   ├── OficinaController.php
│   │   ├── UsuarioController.php
│   │   ├── ReporteController.php
│   │   ├── ConfiguracionController.php
│   │   └── ConsultaController.php
│   │
│   ├── Models/
│   │   ├── Usuario.php
│   │   ├── Rol.php
│   │   ├── Permiso.php
│   │   ├── Oficina.php
│   │   ├── Expediente.php
│   │   ├── Movimiento.php
│   │   ├── Documento.php
│   │   ├── Estado.php
│   │   ├── TipoTramite.php
│   │   ├── Notificacion.php
│   │   └── Configuracion.php
│   │
│   ├── Views/
│   │   ├── layouts/
│   │   ├── auth/
│   │   ├── dashboard/
│   │   ├── expedientes/
│   │   ├── oficinas/
│   │   ├── usuarios/
│   │   ├── configuracion/
│   │   ├── reportes/
│   │   └── public/
│   │
│   ├── Core/
│   │   ├── App.php
│   │   ├── Controller.php
│   │   ├── Model.php
│   │   ├── Database.php
│   │   ├── Router.php
│   │   ├── Auth.php
│   │   ├── Session.php
│   │   ├── CSRF.php
│   │   ├── Validator.php
│   │   └── Response.php
│   │
│   ├── Middleware/
│   │   ├── AuthMiddleware.php
│   │   ├── GuestMiddleware.php
│   │   └── PermissionMiddleware.php
│   │
│   └── Helpers/
│
├── config/
│   ├── app.php
│   ├── database.php
│   ├── mail.php
│   └── routes.php
│
├── public/
│   ├── index.php
│   ├── .htaccess
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   └── img/
│   └── uploads/
│
├── storage/
│   ├── documents/
│   ├── logs/
│   └── temp/
│
├── database/
│   ├── schema.sql
│   └── seed.sql
│
├── .env
├── .env.example
└── README.md
```

---

# 29. ROUTER

Implementar router propio.

Ejemplos:

```text
GET  /login
POST /login
POST /logout

GET  /dashboard

GET  /expedientes
GET  /expedientes/{id}
POST /expedientes
POST /expedientes/{id}/derivar
POST /expedientes/{id}/recibir
POST /expedientes/{id}/responder
POST /expedientes/{id}/finalizar

GET  /consulta
POST /consulta

GET  /tramite
POST /tramite
```

URLs amigables.

---

# 30. BASE DE DATOS

Diseñar una base de datos normalizada.

Crear como mínimo las siguientes tablas:

```text
usuarios
roles
permisos
usuarios_roles
roles_permisos

oficinas

tipos_tramite
estados_expediente
prioridades

expedientes
expediente_documentos
expediente_movimientos

solicitudes_internas
solicitudes_internas_respuestas

notificaciones

configuraciones
configuracion_smtp

auditoria

intentos_login
```

---

# 31. TABLA EXPEDIENTES

Campos sugeridos:

```sql
id
numero_expediente
codigo_seguimiento
tipo_persona
tipo_documento
numero_documento
nombres
apellidos
razon_social
correo
telefono
direccion
tipo_tramite_id
asunto
descripcion
folios
estado_id
prioridad_id
oficina_actual_id
oficina_responsable_id
usuario_responsable_id
fecha_ingreso
fecha_finalizacion
observacion_publica
activo
created_at
updated_at
```

Agregar índices adecuados.

Crear UNIQUE para:

```text
numero_expediente
codigo_seguimiento
```

---

# 32. TABLA DE MOVIMIENTOS

Campos:

```sql
id
expediente_id
tipo_movimiento
oficina_origen_id
oficina_destino_id
usuario_id
estado_anterior_id
estado_nuevo_id
observacion
es_publico
ip
user_agent
created_at
```

Tipos:

```text
REGISTRO
ENVIO_DIRECCION
DERIVACION
RECEPCION
SOLICITUD_INFORMACION
RESPUESTA_INTERNA
OBSERVACION
DEVOLUCION
RESPUESTA
REASIGNACION
FINALIZACION
ARCHIVO
ANULACION
```

---

# 33. TRANSACCIONES

Usar transacciones MySQL para acciones críticas.

Ejemplo al derivar:

1. Insertar movimiento
2. Actualizar oficina actual
3. Actualizar estado
4. Crear notificación
5. Registrar auditoría

Si cualquiera falla:

```php
ROLLBACK
```

Si todo funciona:

```php
COMMIT
```

---

# 34. CONFIGURACIÓN DINÁMICA

No almacenar configuraciones visuales directamente en código.

Usar tabla:

```text
configuraciones
```

Ejemplo:

```text
clave                         valor
institucion_nombre            IESTP EJEMPLO
logo_principal                /uploads/config/logo.png
favicon                       /uploads/config/favicon.png
color_primario                #0B4F8A
color_secundario              #F59E0B
color_sidebar                 #102A43
```

Crear helper:

```php
config('color_primario');
```

Generar CSS dinámico o variables CSS en el layout principal.

---

# 35. INTERFAZ

Diseño profesional, institucional y limpio.

Desktop:

- Sidebar izquierdo
- Header superior
- Breadcrumb
- Área principal

Sidebar:

```text
Dashboard
Expedientes
Mesa de Partes
Dirección
Mi Oficina
Notificaciones
Reportes
Administración
   Usuarios
   Roles
   Oficinas
   Tipos de Trámite
   Estados
   Configuración
Auditoría
```

Ocultar elementos según permisos.

---

# 36. RESPONSIVE

Debe funcionar correctamente en:

- PC
- Laptop
- Tablet
- Smartphone

Sidebar colapsable.

Tablas deben adaptarse a móvil.

---

# 37. BÚSQUEDA

Crear buscador global.

Permitir buscar por:

- Expediente
- DNI
- RUC
- Nombre
- Asunto

Resultados respetando permisos.

---

# 38. PAGINACIÓN

Nunca cargar miles de expedientes de una sola vez.

Implementar paginación backend.

Ejemplo:

```text
20 registros por página
50 registros por página
100 registros por página
```

---

# 39. CARGO DE RECEPCIÓN

Después de registrar un trámite generar cargo imprimible.

Debe contener:

- Logo
- Institución
- Número de expediente
- Código de seguimiento
- Fecha
- Hora
- Solicitante
- Documento
- Asunto
- Número de folios
- QR opcional hacia consulta
- Texto institucional

Permitir exportar PDF.

---

# 40. QR

Opcionalmente generar código QR del expediente.

Debe enlazar a:

```text
https://dominio.com/consulta?codigo=XXXX
```

No incluir información sensible directamente dentro del QR.

---

# 41. FINALIZACIÓN

Un expediente finalizado debe mostrar:

- Fecha
- Responsable
- Documento de respuesta
- Observación pública
- Estado FINALIZADO

El historial continúa disponible.

No permitir modificar datos críticos sin permiso especial.

---

# 42. ARCHIVO

Luego de finalizar, Dirección o usuario autorizado puede archivar.

Estado:

```text
ARCHIVADO
```

No eliminar físicamente.

---

# 43. LOGS

Crear logs en:

```text
/storage/logs/
```

Separar:

```text
app.log
error.log
security.log
```

No exponer detalles internos al usuario final.

---

# 44. INSTALACIÓN

Crear `README.md` con:

- Requisitos
- Crear base de datos
- Importar `schema.sql`
- Importar `seed.sql`
- Configurar `.env`
- Configurar Apache
- Configurar permisos de carpetas
- Usuario administrador inicial
- Configurar SMTP
- Configurar URL base

---

# 45. SEED INICIAL

Crear usuario:

```text
usuario: admin
rol: SUPERADMINISTRADOR
```

No colocar contraseña insegura definitiva.

Durante instalación:

- Generar contraseña temporal
- Obligar cambio en primer ingreso

---

# 46. REGLAS DE NEGOCIO IMPORTANTES

Aplicar estas reglas obligatoriamente:

1. Todo expediente ingresa por Mesa de Partes.

2. Mesa de Partes envía los expedientes a Dirección.

3. Dirección realiza la derivación principal.

4. Las oficinas remiten sus respuestas a Dirección.

5. Dirección puede devolver, aprobar o finalizar.

6. El usuario público puede consultar en qué oficina se encuentra actualmente su expediente.

7. El historial del expediente nunca debe perderse.

8. Las solicitudes de información entre oficinas no deben destruir la responsabilidad principal del expediente.

9. Secretaría Académica puede interactuar con Calidad y Empleabilidad.

10. Investigación puede interactuar con Biblioteca.

11. Cada movimiento debe registrar usuario, fecha, hora y oficina.

12. Un usuario solo puede realizar acciones para las que tenga permiso.

13. Los documentos internos no deben ser públicos.

14. Ningún expediente debe eliminarse físicamente desde la interfaz.

---

# 47. FLUJO DE EJEMPLO

## Solicitud de título

```text
USUARIO
   ↓
MESA DE PARTES
   ↓
DIRECCIÓN
   ↓
SECRETARÍA ACADÉMICA
   ↓
Solicita información a CALIDAD
   ↓
CALIDAD responde
   ↓
SECRETARÍA ACADÉMICA
   ↓
Solicita información a EMPLEABILIDAD
   ↓
EMPLEABILIDAD responde
   ↓
SECRETARÍA ACADÉMICA
   ↓
DIRECCIÓN
   ↓
FINALIZACIÓN
   ↓
USUARIO
```

Durante todo el proceso el sistema debe conservar la trazabilidad completa.

---

# 48. FLUJO INVESTIGACIÓN

```text
DIRECCIÓN
   ↓
INVESTIGACIÓN
   ↓
Solicita información a BIBLIOTECA
   ↓
BIBLIOTECA responde
   ↓
INVESTIGACIÓN
   ↓
DIRECCIÓN
```

---

# 49. EXPERIENCIA DE USUARIO

Usar:

- Alertas claras
- Toasts
- Confirmaciones antes de acciones críticas
- Modales cuando sea apropiado
- Badges para estados
- Colores para prioridades
- Tooltips
- Empty states
- Skeleton/loading cuando corresponda

No utilizar `alert()` de JavaScript como interfaz principal.

---

# 50. VALIDACIÓN

Crear clase reutilizable:

```text
Validator
```

Reglas:

```text
required
email
numeric
integer
min
max
in
unique
date
file
mimes
```

Mostrar errores debajo de cada campo.

---

# 51. FECHAS

Guardar fechas en MySQL con formato estándar.

Mostrar al usuario:

```text
dd/mm/yyyy
```

Mostrar hora:

```text
HH:mm
```

Configurar zona horaria:

```text
America/Lima
```

---

# 52. CÓDIGO

El código debe ser:

- Organizado
- Comentado donde sea necesario
- Reutilizable
- Orientado a objetos
- Sin duplicaciones innecesarias
- Sin SQL dentro de las vistas
- Sin lógica de negocio dentro de las vistas
- Sin HTML dentro de los modelos

Mantener separación MVC estricta.

---

# 53. NO HACER

No:

- Utilizar framework PHP
- Mezclar toda la aplicación en un solo archivo
- Concatenar SQL con datos del usuario
- Guardar contraseñas sin hash
- Guardar archivos usando directamente el nombre del usuario
- Exponer rutas físicas
- Permitir acceder a documentos solo con conocer la URL
- Eliminar historial
- Dar permisos globales a todos los usuarios
- Mostrar errores SQL al usuario
- Codificar los colores institucionales directamente en múltiples archivos

---

# 54. RESULTADO ESPERADO

Generar un proyecto completamente funcional que incluya:

```text
Código fuente PHP
Arquitectura MVC
Base de datos MySQL
schema.sql
seed.sql
Sistema de autenticación
Roles y permisos
Mesa de Partes
Dirección
Oficinas
Derivaciones
Solicitudes internas
Historial
Consulta pública
Gestión documental
Dashboard
Reportes
Notificaciones
Auditoría
Configuración institucional
Personalización de colores
Subida de logo
Subida de favicon
SMTP
README de instalación
```

---

# 55. METODOLOGÍA DE DESARROLLO

No generar una maqueta estática.

Construir un sistema real.

Trabajar módulo por módulo en este orden:

```text
FASE 1
Arquitectura MVC
Configuración
Router
Base de datos

FASE 2
Autenticación
Usuarios
Roles
Permisos

FASE 3
Oficinas
Estados
Tipos de trámite

FASE 4
Mesa de Partes
Registro de expediente
Numeración
Documentos

FASE 5
Dirección
Derivaciones
Recepción

FASE 6
Bandejas de oficinas
Respuestas
Solicitudes internas

FASE 7
Consulta pública
Seguimiento

FASE 8
Dashboard
Reportes
Notificaciones

FASE 9
Configuración institucional
Logo
Favicon
Colores
SMTP

FASE 10
Auditoría
Seguridad
Optimización
Pruebas
```

Al terminar cada fase:

1. Mostrar archivos creados.
2. Indicar qué funcionalidad quedó implementada.
3. Verificar rutas.
4. Verificar permisos.
5. Verificar SQL.
6. Probar casos exitosos y errores.
7. Continuar con la siguiente fase sin destruir lo realizado.

---

# 56. PRUEBAS MÍNIMAS

Probar:

## Autenticación

- Login válido
- Login inválido
- Usuario inactivo
- Permiso denegado
- Sesión expirada

## Mesa de Partes

- Registro válido
- Número de expediente único
- Archivo inválido
- Campos incompletos

## Dirección

- Derivar expediente
- Cambiar oficina
- Devolver expediente
- Finalizar expediente

## Oficina

- Recepcionar
- Responder
- Solicitar información
- Adjuntar documento

## Consulta

- Expediente válido
- Expediente inexistente
- Código inválido

## Configuración

- Cambiar logo
- Cambiar favicon
- Cambiar colores
- Reflejar cambios inmediatamente

---

# 57. PANEL DE CONFIGURACIÓN VISUAL

Crear específicamente:

```text
Administración
   └── Apariencia
```

Vista con preview en tiempo real.

Campos:

```text
Logo institucional
[ SUBIR ]

Favicon
[ SUBIR ]

Color principal
[ selector ]

Color secundario
[ selector ]

Color acento
[ selector ]

Color sidebar
[ selector ]

Color encabezado
[ selector ]

[ RESTABLECER ]

[ GUARDAR CAMBIOS ]
```

Mostrar preview de:

- Botón
- Sidebar
- Navbar
- Badge
- Tarjeta
- Login

---

# 58. COPIAS DE SEGURIDAD DE CONFIGURACIÓN

Antes de reemplazar logo o favicon:

- Conservar únicamente archivos válidos.
- Eliminar de forma segura archivos anteriores que ya no estén en uso.
- Registrar el cambio en auditoría.

---

# 59. DISEÑO DE BASE DE DATOS

Crear claves foráneas correctamente.

Usar:

```text
InnoDB
utf8mb4
```

Agregar:

- Índices
- UNIQUE
- Foreign Keys
- `ON DELETE RESTRICT` o `SET NULL` según corresponda

No utilizar `CASCADE` si puede eliminar historial documental accidentalmente.

---

# 60. ENTREGA FINAL

Al finalizar el desarrollo entregar:

```text
1. Árbol completo de carpetas
2. Código fuente
3. schema.sql
4. seed.sql
5. .env.example
6. README.md
7. Credenciales iniciales documentadas
8. Manual rápido de instalación
9. Manual rápido de uso
10. Lista de módulos
11. Lista de roles y permisos
12. Casos de prueba realizados
```

No dejar funcionalidades importantes simuladas.

No utilizar datos hardcodeados salvo seed inicial.

Todos los módulos deben estar conectados realmente a MySQL.

---

# INSTRUCCIÓN FINAL PARA EL DESARROLLADOR / IA

Construye este sistema como un producto institucional real.

Prioriza:

1. Seguridad.
2. Trazabilidad.
3. Integridad de la información.
4. Claridad del flujo documental.
5. Arquitectura MVC limpia.
6. Facilidad de administración.
7. Experiencia de usuario.
8. Código mantenible.

Antes de programar cada módulo, analiza sus dependencias y evita duplicar lógica.

Cuando generes código, entrega archivos completos y especifica claramente su ruta.

Nunca reemplaces un archivo existente sin revisar primero su contenido y su dependencia con otros módulos.

Mantén siempre operativo el flujo principal:

```text
USUARIO
→ MESA DE PARTES
→ DIRECCIÓN
→ OFICINA
→ DIRECCIÓN
→ RESPUESTA / FINALIZACIÓN
→ USUARIO
```

Y conserva permanentemente la trazabilidad completa del expediente.
