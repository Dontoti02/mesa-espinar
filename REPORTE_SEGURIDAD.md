# Reporte de Auditoria de Seguridad — Mesa de Partes Virtual IESTP Espinar

**Fecha:** 19/09/2026
**Proyecto:** PHP MVC custom (no框架)
**Alcance:** Todos los controladores, modelos, middleware, core y configuracion

---

## Resumen Ejecutivo

| Severidad | Cantidad |
|-----------|----------|
| **CRITICA** | 4 |
| **ALTA** | 5 |
| **MEDIA** | 8 |
| **BAJA** | 7 |

El proyecto tiene una base solida (prepared statements en la mayoria de modelos, CSRF validation, sesiones seguras), pero presenta **4 vulnerabilidades criticas** que deben resolverse antes de produccion.

---

## HALLAZGOS CRITICOS

### C1. SQL Injection en Model.php — Parametros interpolados

**Archivo:** `app/Core/Model.php:47,53,64`
**Riesgo:** Cualquier controlador que pase datos de usuario a `where()`, `orderBy()` o `all()` habilita SQL injection.

```php
// VULNERABLE — lineas 47, 53
$stmt = $this->db()->query("SELECT * FROM `{$this->table}` ORDER BY {$orderBy}");
$sql = "SELECT * FROM `{$this->table}` WHERE {$condition} ORDER BY {$orderBy}";
$sql .= " LIMIT {$limit}";
```

**Remediacion:**
```php
// SEGURO — usar whitelist para ORDER BY
$allowedColumns = ['id', 'nombre', 'created_at'];
$orderBy = in_array($orderBy, $allowedColumns) ? $orderBy : 'id';

// Para WHERE, usar placeholders
public function where(string $condition, array $params = []): array
{
    $sql = "SELECT * FROM `{$this->table}` WHERE {$condition}";
    $stmt = $this->db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}
```

---

### C2. SQL Injection en Validator.php — validateUnique()

**Archivo:** `app/Core/Validator.php:217-219`
**Riesgo:** `$table`, `$column` e `$idColumn` se interpolan directamente en SQL.

```php
// VULNERABLE
$sql = "SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` = :val";
$sql .= " AND `{$idColumn}` != :except_id";
```

**Remediacion:** Validar que `$table`, `$column` e `$idColumn` existan en un whitelist de columnas de la base de datos antes de usarlos.

---

### C3. Subida de Archivos sin Validar MIME Real

**Archivo:** `app/Core/Validator.php:244-254`
**Riesgo:** Solo valida la extension del archivo, no el MIME type real. Un atacante puede subir un `.php` renombrado a `.jpg`.

```php
// VULNERABLE — solo valida extension
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $params)) { ... }
```

**Remediacion:**
```php
// SEGURO — validar MIME type real con finfo
$finfo = new \finfo(FILEINFO_MIME_TYPE);
$realMime = $finfo->file($file['tmp_name']);
$allowedMimes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'application/pdf' => 'pdf'];

if (!array_key_exists($realMime, $allowedMimes)) {
    $this->addError($field, "Tipo de archivo no permitido.");
}
// Plus: verificar que la extension coincida con el MIME
```

---

### C4. Passwords por Defecto en seed.sql — Todos Identico

**Archivo:** `database/seed.sql:169-172`
**Riesgo:** Los 4 usuarios seed comparten el mismo hash bcrypt. 3 de 4 NO tienen `debe_cambiar_password=1`.

```sql
-- VULNERABLE — mismo hash para todos
(1, 'Administrador', 'General', '00000000', 'admin', 'admin@iestpespinar.edu.pe',
 '$2y$10$IKQRwLdud63dm34H7fvN3...', 1, 1, ...),
-- Solo el usuario 1 tiene debe_cambiar_password=1
```

**Remediacion:**
- Generar hashes unicos para cada usuario
- Forzar `debe_cambiar_password=1` para TODOS
- Agregar nota en el README: "Cambiar credenciales inmediatamente tras instalacion"

---

## HALLAZGOS ALTOS

### A1. IDOR — imprimirCargo sin Autenticacion

**Archivo:** `app/Controllers/ExpedienteController.php:333-348`
**Riesgo:** Cualquier usuario no autenticado puede imprimir el cargo de recepcion de cualquier expediente adivinando el ID.

```php
// VULNERABLE — sin Auth::check()
public function imprimirCargo(string $id): void
{
    $expediente = $this->expedienteModel->findDetallado((int)$id);
    // ... genera PDF sin verificar autenticacion
}
```

**Remediacion:**
```php
public function imprimirCargo(string $id): void
{
    if (!Auth::check()) {
        $this->redirect('/login');
        return;
    }
    // ... resto del metodo
}
```

---

### A2. IDOR — descargarDocumento sin Verificar Propiedad

**Archivo:** `app/Controllers/ExpedienteController.php:350-367`
**Riesgo:** Cualquier usuario autenticado puede descargar CUALQUIER documento adivinando el ID.

```php
// VULNERABLE — solo verifica que este logueado, no que sea propietario
if (!Auth::check()) { ... }
$documento = $this->documentoModel->find((int)$id);
Response::download($rutaAbsoluta, ...);
```

**Remediacion:** Verificar que el documento pertenezca al expediente del usuario actual, o que tenga permiso de lectura sobre esa oficina.

---

### A3. IDOR — marcarLeida sin Verificar Propietario

**Archivo:** `app/Controllers/NotificacionController.php:47-55`
**Riesgo:** Cualquier usuario autenticado puede marcar como leida cualquier notificacion.

```php
// VULNERABLE
$this->notificacionModel->update((int)$id, ['leido' => 1, ...]);
```

**Remediacion:** Verificar que `$this->notificacionModel->find((int)$id)['usuario_id']` coincida con `Auth::id()`.

---

### A4. Open Redirect en validateCSRF()

**Archivo:** `app/Core/Controller.php:62-63`
**Riesgo:** Redirige a `HTTP_REFERER` sin validarlo, permitiendo phishing.

```php
// VULNERABLE
$referer = $_SERVER['HTTP_REFERER'] ?? '/';
$this->redirect($referer);
```

**Remediacion:**
```php
$referer = $_SERVER['HTTP_REFERER'] ?? '/';
// Solo permitir redirecciones internas
$parsed = parse_url($referer);
$host = $parsed['host'] ?? '';
if ($host !== parse_url($_ENV['APP_URL'], PHP_URL_HOST)) {
    $referer = '/';
}
$this->redirect($referer);
```

---

### A5. Mass Assignment en Modelos — $fillable Sobrecargado

**Archivo:** `app/Models/Usuario.php:11-15`, `app/Models/Expediente.php:12-19`
**Riesgo:** Campos sensibles como `rol_id`, `password`, `debe_cambiar_password`, `estado` estan en `$fillable`. Si algun controlador pasa `$_POST` directo, un atacante puede elevar privilegios.

```php
// VULNERABLE — Usuario.php
protected array $fillable = [
    'nombres', 'apellidos', 'dni', 'usuario', 'correo', 'password',
    'rol_id', 'oficina_id', 'cargo', 'telefono', 'estado',
    'debe_cambiar_password', 'ultimo_acceso'
];
```

**Remediacion:** Eliminar `password`, `rol_id`, `debe_cambiar_password` de `$fillable`. Manejar estos campos solo en metodos dedicados con validacion estricta.

---

## HALLAZGOS MEDIOS

### M1. CSS Injection en helpers.php — dynamicCssVariables()

**Archivo:** `app/Helpers/helpers.php:212-227`
**Riesgo:** Valores de la DB se inyectan directamente en un bloque `<style>` sin sanitizar.

```php
return "
<style id='dynamic-institutional-styles'>
    :root {
        --primary: {$primary};
```

**Remediacion:** Sanitizar valores con `preg_replace('/[^a-zA-Z0-9#%(),.\s]/', '', $value)`.

---

### M2. Host Header Injection en url()

**Archivo:** `app/Helpers/helpers.php:43-54`
**Riesgo:** `HTTP_X_FORWARDED_HOST` y `HTTP_HOST` son headers controlados por el atacante.

**Remediacion:** Validar que el host solicitado coincida con `APP_URL`.

---

### M3. SVG Upload — Stored XSS Potencial

**Archivo:** `app/Controllers/ConfiguracionController.php:106-115`
**Riesgo:** SVGs pueden contener JavaScript embebido (`<script>`, `onclick`).

**Remediacion:** Bloquear SVGs o sanitizar con `svg-sanitizer` library.

---

### M4. SMTP Password en Texto Plano

**Archivo:** `app/Controllers/ConfiguracionController.php:197-199`, `database/schema.sql:330`
**Riesgo:** Si la DB es comprometida, las credenciales SMTP quedan expuestas.

**Remediacion:** Cifrar con `openssl_encrypt()` usando APP_KEY, o usar variables de entorno.

---

### M5. Password Temporal Predecible

**Archivo:** `app/Controllers/UsuarioController.php:248`
**Riesgo:** Formula: `User2026!` — completamente predecible.

```php
// VULNERABLE
$tempPassword = 'User' . date('Y') . '!';
```

**Remediacion:** `$tempPassword = bin2hex(random_bytes(8));`

---

### M6. Password en Flash Message

**Archivo:** `app/Controllers/UsuarioController.php:256`
**Riesgo:** El password temporal se muestra en la sesion y persiste en el redirect.

**Remediacion:** Enviar por email seguro, nunca mostrar en pantalla.

---

### M7. Exception Messages Filtradas al Usuario

**Archivos:** `TramiteController.php:196`, `ExpedienteController.php:218`, `DireccionController.php:166,227,280,329`, `BandejaController.php:164,245,329,422`
**Riesgo:** `$e->getMessage()` puede filtrar estructura de la DB.

```php
// VULNERABLE
Session::setFlash('error', "Error: " . $e->getMessage());
```

**Remediacion:** Mostrar mensaje generico, logear el detalle completo.

---

### M8. Informacion Sensible en APP_DEBUG=true

**Archivo:** `.env:7`
**Riesgo:** En produccion, errores muestran stack traces completos.

**Remediacion:** `APP_DEBUG=false` en produccion.

---

## HALLAZGOS BAJOS

### B1. Contrasena de DB Root Vacia

**Archivo:** `.env:18` — `DB_PASSWORD=`

**Remediacion:** Establecer contrasena fuerte para MySQL root.

---

### B2. Directorio de Logs con Permisos 0777

**Archivo:** `app/Core/App.php:79`

**Remediacion:** Cambiar a `0750` o `0770`.

---

### B3. Falta Rate Limiting en Login

**Archivo:** `app/Middleware/AuthMiddleware.php`
**Riesgo:** La tabla `intentos_login` existe pero no se usa.

**Remediacion:** Implementar bloqueo tras 5 intentos fallidos.

---

### B4. Sin Header Content-Security-Policy

**Archivo:** `public/.htaccess`
**Riesgo:** Sin CSP, la app es vulnerable a XSS si se encuentra una入口.

**Remediacion:** Agregar headers de seguridad en `.htaccess`:
```apache
Header set Content-Security-Policy "default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'"
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "DENY"
Header set Referrer-Policy "strict-origin-when-cross-origin"
```

---

### B5. Session Fixation — Falta strict_mode

**Archivo:** `app/Core/Session.php`
**Riesgo:** No se configura `session.use_strict_mode`.

**Remediacion:** Agregar `ini_set('session.use_strict_mode', 1);` en `Session::start()`.

---

### B6. Codigo de Correlacion con Baja Entropia

**Archivo:** `app/Models/Expediente.php:48-51`
**Riesgo:** 10 caracteres hex = 48 bits de entropia (2^48).

**Remediacion:** Usar `bin2hex(random_bytes(8))` para 16 caracteres (64 bits).

---

### B7. user_agent Truncado a 255 Caracteres

**Archivo:** `database/schema.sql:223`
**Riesgo:** User agents legibles superan 255 chars. Perdida de datos forenses.

**Remediacion:** Cambiar a `TEXT` o `VARCHAR(512)`.

---

## BUENAS PRACTICAS ENCONTRADAS

- Prepared statements en la mayoria de modelos (Usuario, Expediente, etc.)
- CSRF validation con `hash_equals()` en todos los POST
- Sesiones con `httponly=true`, `samesite=Lax`
- Password hashing con `PASSWORD_BCRYPT`
- Validacion de inputs con `Validator::make()`
- `Response::redirect()` llama `exit;` correctamente
- `session_regenerate_id(true)` tras login

---

## PRIORIDAD DE REMEDIACION

| Prioridad | Hallazgo | Esfuerzo |
|-----------|----------|----------|
| 1 | C1 + C2: SQL Injection en Model/Validator | Alto |
| 2 | A1: imprimirCargo sin auth | Bajo |
| 3 | C3: MIME bypass en uploads | Medio |
| 4 | A2 + A3: IDOR en documentos y notificaciones | Medio |
| 5 | A5: Mass Assignment en modelos | Medio |
| 6 | C4: Passwords seed | Bajo |
| 7 | A4: Open Redirect | Bajo |
| 8 | M1-M8: Hallazgos medios | Variable |
| 9 | B1-B7: Hallazgos bajos | Bajo |
