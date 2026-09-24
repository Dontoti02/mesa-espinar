# PROMPT MAESTRO — CORRECCIÓN DE OBSERVACIONES DEL SISTEMA DE MESA DE PARTES

## Rol

Actúa como **desarrollador senior full-stack y QA**, especializado en **PHP nativo con arquitectura MVC, MySQL, JavaScript, HTML5 y CSS3**, con enfoque en sistemas administrativos, gestión documental y Mesa de Partes.

Debes trabajar **sobre el sistema existente**, respetando su arquitectura, estilos visuales, permisos, rutas, base de datos y funcionalidades ya operativas.

> **No reconstruyas el sistema desde cero.**
> Primero analiza el código actual y luego aplica únicamente los cambios necesarios para resolver las observaciones descritas a continuación.

---

# 1. OBJETIVO GENERAL

Corregir todas las observaciones detectadas en el sistema de Mesa de Partes, tanto a nivel de:

- Frontend
- Backend
- Validaciones
- Gestión documental
- Visualización de PDF
- Búsqueda de expedientes
- Reportes
- Gestión de usuarios
- Experiencia de usuario
- Manejo de errores
- Seguridad

Las correcciones deben quedar completamente funcionales en escritorio, tablet y móvil.

---

# 2. REGLAS GENERALES DE IMPLEMENTACIÓN

Antes de modificar cualquier archivo:

1. Analiza la estructura actual del proyecto.
2. Identifica controladores, modelos, vistas, servicios, helpers, scripts JS y tablas involucradas.
3. Reutiliza funciones existentes cuando sea posible.
4. No elimines funcionalidades que ya operan correctamente.
5. No cambies rutas existentes salvo que sea estrictamente necesario.
6. Mantén compatibilidad con la base de datos actual.
7. Evita romper registros históricos.
8. Mantén el diseño visual actual del sistema.
9. Toda validación importante debe existir tanto en frontend como en backend.
10. Los errores deben mostrarse mediante mensajes claros para el usuario, sin exponer trazas, SQL, rutas internas ni información sensible.
11. Usa consultas preparadas / parámetros enlazados.
12. Valida permisos antes de ejecutar acciones administrativas.
13. Después de cada cambio, realiza pruebas funcionales.

---

# 3. OBSERVACIÓN 1 — QUITAR EL TIEMPO DE TRÁMITE DEL CAMPO “TIPO DE TRÁMITE”

## Situación actual

En el formulario de registro, el selector **Tipo de Trámite** muestra opciones similares a:

- Acceso a la Información Pública (Plazo: 10 días)
- Certificado Oficial de Estudios (Plazo: 10 días)
- Constancia de Estudios (Plazo: 5 días)
- Emisión de Título Profesional (Plazo: 30 días)
- Reclamo o Sugerencia Institucional (Plazo: 15 días)
- Rectificación de Matrícula o Calificaciones (Plazo: 7 días)
- Registro de Proyecto o Tesis en Repositorio (Plazo: 15 días)
- Solicitud General / FUT (Plazo: 15 días)
- Validación de Prácticas Preprofesionales (Plazo: 10 días)

## Cambio requerido

En el selector mostrar **únicamente el nombre del trámite**.

Ejemplo:

ANTES:

```text
Constancia de Estudios (Plazo: 5 días)
```

DESPUÉS:

```text
Constancia de Estudios
```

## Consideraciones

- No eliminar necesariamente el campo de plazo de la base de datos si es utilizado internamente.
- El plazo puede mantenerse para lógica administrativa, métricas o reportes.
- Solo debe dejar de mostrarse dentro del selector público de “Tipo de Trámite”.
- Revisar también cualquier formulario de edición o registro interno donde aparezca el mismo texto.

---

# 4. OBSERVACIÓN 2 — LISTAR LOS DOCUMENTOS ADJUNTADOS Y PERMITIR ELIMINARLOS ANTES DEL REGISTRO

## Situación actual

El campo de documentos anexos muestra algo similar a:

```text
Elegir archivos | 6 archivos
```

Esto no permite identificar claramente qué archivos fueron seleccionados.

## Cambio requerido

Después de seleccionar uno o varios archivos, mostrar una lista visual individual.

Ejemplo:

```text
Documentos seleccionados

📄 DNI.pdf                          [Eliminar]
📄 recibo_pago.pdf                  [Eliminar]
📄 solicitud_firmada.pdf            [Eliminar]
```

Cada elemento debe mostrar, como mínimo:

- Nombre del archivo.
- Extensión o tipo.
- Tamaño.
- Botón o icono de eliminar.

## Comportamiento

Al presionar **Eliminar**:

- El archivo debe retirarse de la lista visual.
- También debe retirarse realmente del conjunto de archivos que será enviado al servidor.
- No debe enviarse al backend un archivo previamente eliminado por el usuario.

## Recomendación de implementación frontend

Utilizar:

```javascript
DataTransfer
```

o una estructura equivalente para reconstruir el contenido del `input[type=file]`.

## Validaciones

Mantener las restricciones existentes:

- PDF
- DOCX
- JPG
- PNG
- Otros formatos ya autorizados por el sistema

Validar:

- Tamaño máximo individual.
- Tamaño total si aplica.
- Extensiones permitidas.
- MIME real en backend.
- Archivos duplicados.

---

# 5. OBSERVACIÓN 3 — TELÉFONO / CELULAR SOLO DEBE ACEPTAR NÚMEROS

## Situación actual

El campo permite introducir letras.

Ejemplo observado:

```text
nueve
```

## Cambio requerido

Configurar el campo para aceptar exclusivamente dígitos.

HTML sugerido:

```html
<input
    type="tel"
    inputmode="numeric"
    pattern="[0-9]*"
    autocomplete="tel"
>
```

Complementar mediante JavaScript:

```javascript
input.addEventListener('input', function () {
    this.value = this.value.replace(/\D/g, '');
});
```

## Backend

No confiar únicamente en JavaScript.

Validar nuevamente en el servidor.

Ejemplo conceptual:

```php
if (!preg_match('/^[0-9]+$/', $telefono)) {
    // devolver error de validación
}
```

## Reglas adicionales

- No permitir letras.
- No permitir caracteres especiales.
- No permitir espacios.
- Definir una longitud razonable según la lógica actual del sistema.
- Mostrar un mensaje comprensible si el valor es inválido.

---

# 6. OBSERVACIÓN 4 — INCORPORAR VISOR / LECTOR DE PDF

## Situación actual

En la vista de expediente, los documentos aparecen como una lista con botón de descarga.

Cuando existen múltiples documentos, el usuario debe descargar cada uno para revisarlo.

## Cambio requerido

Agregar un **visor de PDF integrado en el sistema**.

## Experiencia esperada

Cada documento PDF debe mostrar acciones similares a:

```text
📄 solicitud.pdf

[ Ver documento ] [ Descargar ]
```

Al presionar **Ver documento**:

- Abrir un modal, panel lateral o área de previsualización.
- Visualizar el PDF directamente.
- No obligar al usuario a descargarlo.

## Alternativas permitidas

Puede utilizarse:

### Opción A — iframe

```html
<iframe src="ruta/documento.pdf"></iframe>
```

### Opción B — PDF.js

Preferible si se necesita mayor control.

Funciones recomendadas:

- Página anterior.
- Página siguiente.
- Número de página.
- Zoom +.
- Zoom -.
- Ajustar al ancho.
- Descargar.
- Pantalla completa.

## Múltiples adjuntos

Si existen 7 archivos:

```text
Documentos Adjuntos (7)

1. documento_1.pdf     [Ver] [Descargar]
2. documento_2.pdf     [Ver] [Descargar]
3. documento_3.pdf     [Ver] [Descargar]
...
```

El visor debe abrir exactamente el documento elegido.

## Seguridad

No construir rutas públicas directamente con parámetros inseguros.

El servidor debe verificar:

- Que el archivo exista.
- Que el usuario tenga permiso para visualizar el expediente.
- Que el documento pertenezca al expediente solicitado.
- Que no exista traversal como `../`.
- Que el MIME sea correcto.

---

# 7. OBSERVACIÓN 5 — ERROR 500 AL BUSCAR EXPEDIENTES

## Situación actual

Al realizar determinadas búsquedas o filtros en la **Bandeja General de Expedientes**, el sistema devuelve:

```text
500
Error Interno del Sistema
```

## Cambio requerido

Diagnosticar y corregir el error desde su causa real.

## Procedimiento obligatorio

### Paso 1 — Revisar logs

Analizar:

- Logs de PHP.
- Logs de Apache/Nginx.
- Logs propios de la aplicación.
- Excepciones.
- Errores SQL.

### Paso 2 — Reproducir el error

Probar:

- Búsqueda vacía.
- Número de expediente completo.
- Número de expediente parcial.
- Nombre de solicitante.
- DNI.
- Asunto.
- Oficina actual.
- Estado.
- Prioridad.
- Combinación de filtros.
- Caracteres especiales.
- Valores inexistentes.

### Paso 3 — Revisar consulta SQL

Verificar posibles problemas:

- Parámetros faltantes.
- `LIKE` mal construido.
- Alias incorrectos.
- Columnas inexistentes.
- JOIN inválido.
- Valores NULL.
- Conversión de tipos.
- Paginación.
- Ordenamiento.
- Parámetros vacíos.
- Placeholders duplicados.

## Resultado esperado

Una búsqueda sin resultados debe devolver:

```text
No se encontraron expedientes con los criterios seleccionados.
```

Nunca un error 500.

## Manejo de excepciones

Implementar `try/catch` donde corresponda.

Los detalles técnicos deben quedar en el log del servidor, no mostrarse al usuario.

---

# 8. OBSERVACIÓN 6 — VALIDAR RANGO DE FECHAS EN REPORTES

## Situación actual

El módulo de reportes permite seleccionar una fecha inicial posterior a la fecha final.

Ejemplo inválido:

```text
Fecha Desde: 25/09/2026
Fecha Hasta: 20/09/2026
```

## Cambio requerido

Debe cumplirse siempre:

```text
fecha_desde <= fecha_hasta
```

## Frontend

Al cambiar cualquiera de las dos fechas:

```javascript
if (fechaDesde > fechaHasta) {
    // mostrar error
}
```

Mostrar un mensaje similar a:

```text
La fecha de inicio no puede ser posterior a la fecha final.
```

No ejecutar el filtro mientras el rango sea inválido.

## Backend

Repetir exactamente la validación en servidor.

Ejemplo conceptual:

```php
if ($fechaDesde > $fechaHasta) {
    // devolver respuesta de validación
}
```

## Mejora UX

Cuando se seleccione `Fecha Desde`, establecer dinámicamente el `min` de `Fecha Hasta`.

Ejemplo:

```javascript
fechaHasta.min = fechaDesde.value;
```

---

# 9. OBSERVACIÓN 7 — GESTIÓN DE USUARIOS: NO SE PUEDE ELIMINAR

## Situación observada

En Gestión de Usuarios aparecen acciones como:

- Editar
- Restablecer Clave
- Desactivar

No se aprecia una eliminación efectiva de usuarios.

## Objetivo

Implementar una gestión segura de eliminación/desactivación.

## IMPORTANTE

Antes de realizar cambios, revisar cómo está diseñado actualmente el modelo de usuarios y sus relaciones con:

- Expedientes.
- Derivaciones.
- Respuestas.
- Historial.
- Auditoría.
- Oficinas.
- Roles.
- Sesiones.

## Estrategia recomendada

**Preferir eliminación lógica (soft delete) o desactivación antes que eliminación física**, debido a que los usuarios pueden estar asociados con documentos históricos.

Agregar o reutilizar campos como:

```text
estado
deleted_at
activo
```

## Acción administrativa

Mostrar:

```text
Editar
Restablecer clave
Desactivar
```

y, solo si las reglas del sistema lo permiten:

```text
Eliminar
```

## Si se implementa “Eliminar”

Mostrar confirmación:

```text
¿Está seguro de eliminar este usuario?

Esta acción puede afectar el acceso del usuario.
Los registros históricos no serán eliminados.
```

## Reglas de seguridad

No permitir:

- Que un usuario se elimine a sí mismo.
- Eliminar al último administrador activo.
- Eliminar físicamente registros vinculados con expedientes históricos.
- Ejecutar la acción sin permiso administrativo.

Registrar en auditoría:

- Usuario que ejecutó la acción.
- Usuario afectado.
- Fecha.
- Hora.
- IP si el sistema ya registra este dato.
- Acción realizada.

> Si el sistema ya utiliza “Desactivar” como mecanismo deliberado de baja de usuarios, conservar esa lógica y dejar claro en interfaz que esa es la forma segura de retiro. No implementar borrado físico sin revisar dependencias.

---

# 10. MEJORAS VISUALES BASADAS EN LAS PANTALLAS OBSERVADAS

Mantener el diseño actual del sistema:

- Tarjetas blancas.
- Bordes suaves.
- Encabezados en azul.
- Botones principales azules.
- Estados mediante badges.
- Tablas limpias.
- Formularios distribuidos en columnas.
- Diseño administrativo sobrio.

No realizar un rediseño completo.

Únicamente mejorar los componentes afectados.

## Documentos adjuntos

Usar tarjetas o filas compactas:

```text
┌──────────────────────────────────────────────┐
│ 📄 solicitud.pdf                            │
│ 1.2 MB · PDF                [Ver] [Descargar]│
└──────────────────────────────────────────────┘
```

## Archivos seleccionados antes de enviar

```text
┌──────────────────────────────────────────────┐
│ 📄 documento.pdf                 1.5 MB  ✕  │
└──────────────────────────────────────────────┘
```

El botón `✕` debe retirar el archivo.

---

# 11. RESPONSIVE

Todas las modificaciones deben funcionar correctamente en:

- Desktop.
- Laptop.
- Tablet.
- Móvil.

En móvil:

- Los formularios deben pasar a una sola columna.
- Los botones deben ser fáciles de presionar.
- Las tablas pueden usar scroll horizontal.
- El visor PDF debe adaptarse al ancho disponible.
- Los modales deben ocupar casi toda la pantalla.

---

# 12. ACCESIBILIDAD

Agregar cuando corresponda:

```html
aria-label=""
title=""
```

Los botones que solo utilizan iconos deben tener descripción accesible.

Ejemplo:

```html
<button aria-label="Eliminar archivo" title="Eliminar archivo">
    ...
</button>
```

---

# 13. SEGURIDAD DE ARCHIVOS

Para cada archivo subido:

1. Validar extensión.
2. Validar MIME.
3. Renombrar internamente cuando sea necesario.
4. Evitar caracteres peligrosos.
5. Evitar ejecución de archivos.
6. Impedir traversal.
7. Verificar permisos antes de visualizar.
8. No confiar en el nombre enviado por el navegador.
9. Mantener el nombre original únicamente como metadato si corresponde.

---

# 14. MANEJO DE ERRORES

No mostrar mensajes genéricos como único resultado cuando pueda darse una explicación funcional.

Ejemplos:

```text
No se encontraron expedientes.
```

```text
No tiene permisos para visualizar este documento.
```

```text
La fecha inicial no puede ser posterior a la fecha final.
```

```text
El archivo seleccionado supera el tamaño permitido.
```

```text
El número de teléfono solo puede contener dígitos.
```

Los errores internos deben registrarse en logs.

---

# 15. PRUEBAS FUNCIONALES OBLIGATORIAS

Después de implementar los cambios, probar:

## Formulario público

- Registrar expediente correctamente.
- Seleccionar trámite.
- Verificar que no aparece “Plazo: X días”.
- Escribir letras en teléfono.
- Verificar que sean rechazadas.
- Adjuntar 1 archivo.
- Adjuntar múltiples archivos.
- Eliminar uno antes de enviar.
- Confirmar que el eliminado no llega al servidor.
- Adjuntar archivo no permitido.
- Adjuntar archivo demasiado grande.

## Expedientes

- Buscar por número.
- Buscar por solicitante.
- Buscar por documento.
- Buscar por asunto.
- Filtrar por estado.
- Filtrar por oficina.
- Filtrar por prioridad.
- Combinar filtros.
- Buscar un valor inexistente.
- Confirmar que ninguna búsqueda produce error 500.

## Documentos

- Ver PDF dentro del sistema.
- Cambiar entre múltiples documentos.
- Descargar documento.
- Intentar abrir un documento sin permiso.
- Intentar abrir una ruta inválida.

## Reportes

- Fecha inicial menor a final.
- Fechas iguales.
- Fecha inicial mayor a final.
- Rango sin resultados.
- Rango con resultados.

## Usuarios

- Editar usuario.
- Restablecer contraseña.
- Desactivar usuario.
- Reactivar usuario si existe esa función.
- Verificar restricción de autoeliminación.
- Verificar protección del último administrador.
- Revisar que el historial de expedientes no se pierda.

---

# 16. CRITERIOS DE ACEPTACIÓN

La tarea se considera terminada únicamente cuando:

- [ ] El selector de Tipo de Trámite ya no muestra plazos.
- [ ] Los archivos seleccionados se muestran individualmente.
- [ ] Cada archivo seleccionado puede eliminarse antes del envío.
- [ ] Teléfono/Celular solo acepta números.
- [ ] Existe visor PDF integrado.
- [ ] Cada adjunto puede visualizarse individualmente.
- [ ] La búsqueda de expedientes no genera error 500.
- [ ] Las búsquedas sin resultados muestran un estado vacío controlado.
- [ ] Reportes impide `fecha_desde > fecha_hasta`.
- [ ] La validación de fechas existe en frontend y backend.
- [ ] La gestión de usuarios permite una baja segura mediante desactivación o eliminación lógica.
- [ ] No se destruyen registros históricos.
- [ ] Las nuevas acciones respetan roles y permisos.
- [ ] Los cambios son responsive.
- [ ] No se rompieron funcionalidades existentes.

---

# 17. FORMA DE TRABAJO QUE DEBE SEGUIR LA IA / DESARROLLADOR

No empieces modificando archivos de forma aleatoria.

Trabaja en este orden:

### Fase 1 — Análisis

Indica:

- Archivos involucrados.
- Controladores afectados.
- Modelos afectados.
- Vistas afectadas.
- Scripts JS afectados.
- Tablas involucradas.
- Riesgos detectados.

### Fase 2 — Plan

Presenta un plan breve de implementación por observación.

### Fase 3 — Implementación

Realiza los cambios de manera incremental.

Después de cada cambio indica:

```text
Archivo modificado:
Cambio realizado:
Motivo:
Cómo probarlo:
```

### Fase 4 — Validación

Ejecuta o describe pruebas funcionales de cada observación.

### Fase 5 — Resumen

Al finalizar entregar una matriz:

| Nº | Observación | Estado | Archivos modificados | Prueba |
|---|---|---|---|---|
| 1 | Quitar plazo del trámite | Corregido | ... | OK |
| 2 | Listado de adjuntos | Corregido | ... | OK |
| 3 | Teléfono numérico | Corregido | ... | OK |
| 4 | Visor PDF | Corregido | ... | OK |
| 5 | Error 500 en búsqueda | Corregido | ... | OK |
| 6 | Validación de fechas | Corregido | ... | OK |
| 7 | Gestión de usuarios | Corregido | ... | OK |

---

# 18. INSTRUCCIÓN FINAL

Analiza primero todo el proyecto.

No supongas nombres de archivos, tablas, rutas o columnas que no existan.

Encuentra las implementaciones actuales y adapta las correcciones a ellas.

No elimines datos históricos.

No cambies la apariencia global del sistema.

Resuelve las 7 observaciones de forma completa, segura, responsive y compatible con la arquitectura existente.

Si una observación requiere una decisión de negocio —especialmente la eliminación física de usuarios— conserva los datos y aplica la alternativa más segura hasta confirmar la regla funcional.
