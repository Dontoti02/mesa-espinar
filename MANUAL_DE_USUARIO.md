# 📖 Manual de Usuario — Sistema de Mesa de Partes Virtual
### Instituto de Educación Superior Tecnológico Público Espinar

Bienvenido al **Manual de Usuario** del Sistema Web de Mesa de Partes Virtual. Esta guía está diseñada para que cualquier persona (ciudadano, estudiante, docente o funcionario) pueda utilizar el sistema de forma rápida, sencilla y sin complicaciones.

---

## 📌 Enlaces Rápidos de Acceso

| Módulo | Enlace Web | ¿Para quién es? |
| :--- | :--- | :--- |
| 🌐 **Portal Ciudadano (Inicio)** | [http://localhost/mesa-espinar/](http://localhost/mesa-espinar/) | Público en general, estudiantes, egresados |
| 📝 **Presentar Trámite Virtual** | [http://localhost/mesa-espinar/tramite](http://localhost/mesa-espinar/tramite) | Personas que desean ingresar una solicitud |
| 🔍 **Consultar mi Expediente** | [http://localhost/mesa-espinar/consulta](http://localhost/mesa-espinar/consulta) | Consultar el avance con tu código o expediente |
| 🔐 **Acceso para Funcionarios** | [http://localhost/mesa-espinar/login](http://localhost/mesa-espinar/login) | Personal administrativo, jefes de oficina y dirección |

---

## 🧭 ¿Cómo funciona el trámite institucional? (Flujo Simple)

```text
1. Ciudadano envía solicitud (Portal Web o Ventanilla)
                 ↓
2. Mesa de Partes revisa requisitos y genera el EXPEDIENTE (EXP-2026-XXXXXX)
                 ↓
3. Dirección General revisa y deriva a la Oficina Responsable
                 ↓
4. Oficina Responsable atiende y emite su Informe Técnico
                 ↓
5. Dirección General aprueba y finaliza el trámite
                 ↓
6. Ciudadano descarga su respuesta o resolución
```

---

## 👥 Guía Paso a Paso según tu Rol

Selecciona tu rol para ver las instrucciones correspondientes:

1. [Rol 1: Ciudadano / Estudiante / Usuario Externo](#-rol-1-ciudadano--estudiante--usuario-externo)
2. [Rol 2: Operador de Mesa de Partes](#-rol-2-operador-de-mesa-de-partes)
3. [Rol 3: Dirección General (Despacho Directoral)](#-rol-3-dirección-general)
4. [Rol 4: Jefe de Oficina / Responsable de Área](#-rol-4-jefe-de-oficina--responsable-de-área)
5. [Rol 5: Administrador del Sistema](#-rol-5-administrador-del-sistema)

---

## 🎓 Rol 1: Ciudadano / Estudiante / Usuario Externo

Si necesitas solicitar un certificado, constancia, trámite de titulación, rectificación de matrícula o presentar un reclamo, sigue estos pasos:

### 1. Presentar un documento por la Web
1. Ingresa a [Presentar Trámite](http://localhost/mesa-espinar/tramite).
2. **Paso 1: Tipo de Trámite**: Selecciona el trámite que deseas realizar (por ejemplo: *Certificado de Estudios*, *Emisión de Título*, *Fut General*, etc.). Podrás ver los requisitos y el plazo estimado de atención.
3. **Paso 2: Tus Datos Personales**:
   - Selecciona tu tipo de documento (DNI, Carné de Extranjería, RUC).
   - Escribe tu número de documento, nombres y apellidos completos.
   - Escribe tu **correo electrónico** y **teléfono celular** (¡muy importante para contactarte!).
   - Si representas a una empresa o institución, marca la casilla e ingresa su nombre.
4. **Paso 3: Datos del Documento**:
   - Asunto claro (Ejemplo: *"Solicito certificado de estudios del 2024"*).
   - Número de folios (cuántas hojas tiene tu documento).
5. **Paso 4: Adjuntar Archivos**:
   - Sube tu solicitud o Formulario Único de Trámite (FUT) en formato **PDF** (máximo 15 MB por archivo).
   - Si tienes requisitos adicionales (pagos de tasa, fotos, copia de DNI), puedes adjuntarlos en la sección de anexos.
6. Haz clic en el botón verde **"Registrar Trámite"**.

### 2. Guardar tu Cargo de Recepción
- Al enviar tu trámite, la pantalla te mostrará tu **Número de Expediente** (ej. `EXP-2026-000005`) y tu **Código de Seguimiento** de 8 letras y números (ej. `TRK-A7B2C9`).
- Haz clic en **"Descargar Cargo en PDF"** o **"Imprimir Cargo"**.
- El cargo contiene un **Código QR oficial**: al escanearlo con cualquier celular te llevará directamente al estado actual de tu trámite.

### 3. Consultar cómo va tu trámite
1. Ingresa a [Consultar Trámite](http://localhost/mesa-espinar/consulta).
2. Puedes buscar de dos formas:
   - Con tu **Número de Expediente** (ej. `EXP-2026-000005`).
   - Con tu **Código de Seguimiento** (ej. `TRK-A7B2C9`).
3. Verás una **línea de tiempo interactiva** que te indicará:
   - Dónde se encuentra tu documento en este momento.
   - Si ya fue recepcionado por la oficina correspondiente.
   - Si ya fue resuelto para que recojas tu documento o descargues tu respuesta.

---

## 📥 Rol 2: Operador de Mesa de Partes

El operador de Mesa de Partes es la puerta de entrada oficial de todos los documentos del IESTP Espinar.

### 1. Iniciar Sesión
- Ingresa a [http://localhost/mesa-espinar/login](http://localhost/mesa-espinar/login).
- Usuario predeterminado: `mesapartes`
- Contraseña predeterminada: `Admin2026!` *(si es tu primera vez, el sistema te pedirá cambiarla por seguridad)*.

### 2. Registrar un Documento por Ventanilla (Presencial)
1. En el menú lateral izquierdo, haz clic en **"Expedientes"** $\rightarrow$ **"Nuevo Registro"**.
2. Completa los datos del administrado: DNI, nombres, teléfono, correo y dirección.
3. Selecciona el Tipo de Trámite y verifica físicamente que traiga los requisitos completos.
4. Escribe el Asunto, tipo de documento (Oficio, Solicitud, Carta, etc.) y número de folios.
5. Escanea y adjunta el documento en formato PDF.
6. Haz clic en **"Guardar y Generar Cargo"**.
7. El sistema generará automáticamente el número correlativo oficial (ej. `EXP-2026-000006`).
8. Haz clic en **"Imprimir Cargo"** y entrégaselo sellado al ciudadano.

### 3. Derivar el Expediente a Dirección
> **Regla de oro:** Todo expediente registrado en Mesa de Partes debe pasar primero a **Dirección General** para su providencia y asignación oficial.

1. Ve a la lista de expedientes.
2. Haz clic sobre el expediente con estado **"REGISTRADO"**.
3. En el panel de acciones a la derecha, haz clic en **"Derivar a Dirección"**.
4. Escribe un proveído corto (ej. *"Pase a Despacho Directoral para su conocimiento y derivación"*).
5. Confirma el envío. El estado cambiará a **"EN DIRECCIÓN"**.

---

## 🏛️ Rol 3: Dirección General

La Dirección General evalúa los expedientes que ingresan a la institución, determina qué oficina debe resolverlos y aprueba las respuestas finales.

### 1. Bandeja de Entrada de Dirección
Al iniciar sesión con tu usuario directoral (ej. `director`), ingresa a la opción **"Bandeja de Dirección"**. Encontrarás 4 pestañas organizadas:
- 📥 **Por Derivar**: Documentos nuevos enviados por Mesa de Partes o respuestas que regresaron de oficinas para su firma/revisión.
- ⚙️ **En Trámite**: Expedientes que están siendo trabajados actualmente en las oficinas (Secretaría Académica, Calidad, etc.).
- 👁️ **Por Aprobar**: Respuestas emitidas por los jefes de oficina listas para visto bueno.
- ✅ **Atendidos / Finalizados**: Historial de expedientes resueltos y archivados.

### 2. ¿Cómo Derivar a una Oficina Responsable?
1. En la pestaña **"Por Derivar"**, haz clic en el botón azul **"Acciones / Ver"** del expediente.
2. En el formulario de derivación:
   - **Oficina Destino**: Selecciona la oficina que atenderá el caso (ej. *Unidad de Secretaría Académica*).
   - **Prioridad**: Elige entre *Normal*, *Urgente* o *Muy Urgente*.
   - **Plazo de Atención**: Número de días hábiles que tiene la oficina para responder (ej. 5 días).
   - **Instrucción / Proveído**: Escribe la indicación clara (ej. *"Emitir informe técnico de convalidación según reglamento"*).
3. Haz clic en **"Derivar a Oficina"**. El expediente pasará inmediatamente a la bandeja de esa oficina con estado **"DERIVADO A OFICINA"**.

### 3. Devolver a Mesa de Partes (si hay errores)
Si un documento carece de sustento o fue registrado incorrectamente:
- Haz clic en **"Devolver a Mesa de Partes"**, escribe el motivo de la observación y el documento regresará para su subsanación.

### 4. Finalizar un Trámite
Cuando la oficina responsable ya emitió su informe y el trámite está listo:
1. En la pestaña **"Por Aprobar"**, revisa el informe adjuntado por la oficina.
2. Si estás conforme, haz clic en **"Finalizar Trámite"**.
3. Ingresa el número de documento de respuesta (ej. *"Resolución Directoral N° 045-2026-IESTP-E"* o *"Oficio N° 120-2026"*).
4. Adjunta el documento firmado en PDF y haz clic en **"Cerrar y Notificar"**. El expediente quedará con estado **"FINALIZADO"** y el ciudadano podrá ver su resolución.

---

## 🏢 Rol 4: Jefe de Oficina / Responsable de Área
*(Ejemplos: Secretaría Académica, Jefatura de Unidad de Calidad, Biblioteca, etc.)*

Cada oficina cuenta con su propia bandeja donde recibe únicamente los expedientes que la Dirección General le ha encomendado.

### 1. Ver tu Bandeja y Recepcionar el Documento
1. Inicia sesión con tu usuario de oficina (ej. `sec_academica`).
2. En el menú, haz clic en **"Bandeja de Oficina"**.
3. Verás los expedientes pendientes.
4. **Paso Obligatorio - Recepcionar**:
   - Todo expediente recién llegado aparece en estado **"DERIVADO A OFICINA"**.
   - Haz clic en el botón verde **"Recepcionar"**.
   - Esto colocará el **sello de tiempo electrónico**, registrando la fecha, hora exacta y tu usuario, cambiando el estado a **"RECEPCIONADO EN OFICINA"**.

### 2. Elaborar y Enviar Respuesta a Dirección
Una vez que hayas evaluado la solicitud y redactado tu informe técnico:
1. En la bandeja, haz clic en **"Responder a Dirección"**.
2. Completa los campos:
   - **Documento que emites**: (ej. *"Informe Técnico N° 012-2026-SA"*).
   - **Número de Folios**: (ej. 3).
   - **Detalle de la Respuesta**: Resumen de tu dictamen (ej. *"Se verificaron las actas de notas del alumno y procede la emisión del certificado"*).
   - **Adjuntar Archivo**: Sube tu informe firmado en formato PDF.
3. Haz clic en **"Enviar Respuesta"**. El expediente regresará automáticamente al despacho de Dirección General para su firma y finalización.

### 3. ¿Necesitas la opinión de otra oficina? (Consulta Interna)
> 💡 **Caso práctico:** Secretaría Académica necesita que la Unidad de Calidad o Biblioteca verifique si el alumno debe libros o cumple con créditos extracurriculares antes de dar una respuesta final.

- En la vista del expediente, haz clic en **"Solicitar Consulta Interna"**.
- Elige la oficina a consultar (ej. *Biblioteca*).
- Escribe tu consulta específica.
- **Importante:** Tu oficina seguirá siendo la responsable principal del expediente. La otra oficina recibirá una notificación, responderá tu consulta adjuntando su visto bueno, y tú podrás continuar con el trámite sin perder el control.

---

## ⚙️ Rol 5: Administrador del Sistema

El administrador tiene control total sobre la configuración institucional, usuarios, seguridad y personalización visual.

### 1. Gestión de Usuarios y Roles
- **Crear Usuarios**: Menú **"Usuarios"** $\rightarrow$ **"Nuevo Usuario"**. Ingresa nombre, DNI, correo, nombre de usuario, contraseña inicial y asígnale su **Oficina** y su **Rol**.
- **Roles y Permisos**: En **"Roles"** puedes ver los 6 roles del sistema y configurar qué permisos tiene cada uno de forma visual e intuitiva (24 permisos granulares).

### 2. Catálogo de Oficinas y Trámites
- **Oficinas**: En **"Mantenimiento"** $\rightarrow$ **"Oficinas"** puedes editar las 19 oficinas orgánicas del IESTP Espinar, cambiar al jefe responsable o su correo.
- **Tipos de Trámite**: En **"Tipos de Trámite"** puedes agregar nuevos trámites, definir cuántos días hábiles tienen de plazo legal y listar los requisitos que el ciudadano debe presentar.

### 3. Personalización de Apariencia (Identidad Institucional)
- Ingresa a **"Configuración"** $\rightarrow$ **"Apariencia"**.
- **Cambio de Colores en Vivo**:
  - Selecciona el **Color Primario** (por defecto Azul Marino `#0B4F8A`).
  - Selecciona el **Color Secundario** (por defecto Dorado `#F59E0B`).
  - Selecciona el color de fondo y de la barra lateral.
  - La pantalla cuenta con un **simulador en vivo** para ver cómo quedará la interfaz antes de guardar.
- **Logotipo y Favicon**: Sube el escudo o logo oficial del IESTP Espinar en formato PNG o JPG. Se actualizará instantáneamente en el portal público, barra de navegación y cargos de recepción.

### 4. Dashboard y Reportes
- **Dashboard**: Muestra estadísticas en tiempo real: total de expedientes recibidos, atendidos, pendientes, expedientes vencidos y gráficos comparativos por oficina.
- **Reportes**: Puedes generar listados filtrando por rango de fechas, oficina responsable, estado o tipo de trámite. Puedes exportar los datos a **Excel (CSV)** o generar una versión lista para **Imprimir / Guardar en PDF**.

### 5. Auditoría del Sistema (Trazabilidad Inmutable)
- En **"Auditoría"** puedes revisar la bitácora completa:
  - Quién modificó o creó un expediente.
  - La dirección IP desde donde se conectó.
  - El navegador utilizado.
  - Los datos exactos anteriores y nuevos (en formato JSON).

---

## ❓ Preguntas Frecuentes (FAQ)

### 1. ¿Qué formatos y tamaños de archivo se pueden subir?
El sistema acepta archivos en formato **PDF** para documentos oficiales y **JPG/PNG** para anexos o fotografías. El tamaño máximo recomendado es de **15 MB por archivo**.

### 2. ¿Por qué el sistema me pide cambiar la contraseña la primera vez que ingreso?
Por norma de seguridad informática del Estado Peruano, las contraseñas temporales asignadas por el administrador deben ser cambiadas por una clave personal y secreta que contenga al menos 8 caracteres, mayúsculas, minúsculas y números.

### 3. ¿Se puede borrar o eliminar un expediente?
**No**. Por ley de transparencia y trámite documentario institucional, **ningún expediente puede ser eliminado físicamente de la base de datos**. Si un documento fue anulado o rechazado, quedará registrado con ese estado y con el motivo de observación en el historial para fines de auditoría.

### 4. ¿Cómo identifico qué expedientes están por vencer?
En el Dashboard y en las bandejas de entrada, los expedientes tienen indicadores de color:
- 🟢 **Verde**: En plazo normal.
- 🟡 **Amarillo**: Por vencer (faltan 2 días o menos para cumplir el plazo).
- 🔴 **Rojo**: Vencido (ha superado los días hábiles establecidos en el TUPA institucional).

---

## 📞 Soporte Técnico
Si tienes alguna duda, problema de acceso o requieres asistencia técnica:
- **Área encargada**: Unidad de Tecnologías de la Información / Soporte IESTP Espinar.
- **Correo**: `soporte@iestpespinar.edu.pe` / `mesadepartes@iestpespinar.edu.pe`
- **Dirección**: Av. San Martín S/N, Espinar, Cusco, Perú.
