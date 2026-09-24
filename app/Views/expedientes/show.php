<div class="mb-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
        <a href="<?= url('/expedientes') ?>" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Volver a la bandeja
        </a>
        <div class="d-flex gap-2">
            <a href="<?= url("/expedientes/{$expediente['id']}/imprimir-cargo") ?>" target="_blank" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-printer-fill me-1"></i> Imprimir Cargo
            </a>
        </div>
    </div>

    <!-- Encabezado del Expediente -->
    <div class="card card-custom p-4 mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <h3 class="fw-bold mb-0 text-primary"><?= e($expediente['numero_expediente']) ?></h3>
                    <span class="badge px-3 py-1 text-white fw-semibold" style="background-color: <?= e($expediente['estado_color']) ?>; font-size: 0.85rem;">
                        <i class="bi <?= e($expediente['estado_icono'] ?: 'bi-circle') ?> me-1"></i>
                        <?= e($expediente['estado_nombre']) ?>
                    </span>
                    <span class="badge px-3 py-1 text-white fw-semibold" style="background-color: <?= e($expediente['prioridad_color']) ?>; font-size: 0.85rem;">
                        Prioridad <?= e($expediente['prioridad_nombre']) ?>
                    </span>
                </div>
                <div class="text-muted small">
                    <span class="me-3"><i class="bi bi-key me-1"></i> Código: <code><?= e($expediente['codigo_seguimiento']) ?></code></span>
                    <span class="me-3"><i class="bi bi-calendar-event me-1"></i> Ingreso: <?= formatDateTime($expediente['fecha_ingreso']) ?></span>
                    <span><i class="bi bi-building me-1 text-primary"></i> Oficina Actual: <strong><?= e($expediente['oficina_actual_nombre']) ?> (<?= e($expediente['oficina_actual_sigla']) ?>)</strong></span>
                </div>
            </div>

            <!-- Botonera de Acciones según rol / oficina -->
            <div class="d-flex flex-wrap gap-2">
                <!-- 1. Enviar a Dirección (si está en Mesa de Partes y estado inicial) -->
                <?php if (in_array($expediente['estado_codigo'], ['RECIBIDO', 'REGISTRADO']) && (int)$expediente['oficina_actual_id'] === 2 && (hasRole(['mesa-de-partes', 'superadministrador']) || hasPermission('expedientes.crear'))): ?>
                    <button type="button" class="btn btn-warning text-dark fw-bold btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalEnviarDireccion">
                        <i class="bi bi-send-fill me-1"></i> Enviar a Dirección
                    </button>
                <?php endif; ?>

                <!-- 2. Derivar (si está en Dirección y usuario tiene rol Dirección / Superadmin) -->
                <?php if (hasRole(['direccion', 'superadministrador']) && ((int)$expediente['oficina_actual_id'] === 1 || in_array($expediente['estado_codigo'], ['ENVIADO_DIRECCION', 'RESPONDIDO', 'EN_REVISION']))): ?>
                    <button type="button" class="btn btn-primary-custom btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalDerivar">
                        <i class="bi bi-arrow-right-circle-fill me-1"></i> Derivar a Oficina
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalDevolver">
                        <i class="bi bi-arrow-return-left me-1"></i> Devolver
                    </button>
                    <button type="button" class="btn btn-success btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalFinalizar">
                        <i class="bi bi-check-circle-fill me-1"></i> Finalizar
                    </button>
                <?php endif; ?>

                <!-- 3. Recepcionar (si el usuario pertenece a la oficina actual y el estado es DERIVADO) -->
                <?php if ($expediente['estado_codigo'] === 'DERIVADO' && (int)$expediente['oficina_actual_id'] === (int)auth()['oficina_id']): ?>
                    <form action="<?= url("/expedientes/{$expediente['id']}/recibir") ?>" method="POST" class="d-inline">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-success fw-bold btn-sm px-3">
                            <i class="bi bi-inbox-fill me-1"></i> Recepcionar Expediente
                        </button>
                    </form>
                <?php endif; ?>

                <!-- 4. Responder o Solicitar Apoyo Interno (si la oficina recepcionó el trámite) -->
                <?php if (in_array($expediente['estado_codigo'], ['RECEPCIONADO_POR_OFICINA', 'EN_TRAMITE', 'PENDIENTE_INFORMACION']) && ((int)$expediente['oficina_actual_id'] === (int)auth()['oficina_id'] || hasRole('superadministrador'))): ?>
                    <button type="button" class="btn btn-primary-custom btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalResponder">
                        <i class="bi bi-reply-fill me-1"></i> Emitir Respuesta a Dirección
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalSolicitudInterna">
                        <i class="bi bi-chat-left-dots-fill me-1"></i> Consulta Interna
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Columna Izquierda: Información del Solicitante y Trámite -->
    <div class="col-12 col-lg-5">
        <div class="card card-custom p-4 mb-4">
            <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom">
                <i class="bi bi-person-bounding-box me-2"></i> Datos del Solicitante
            </h6>
            <div class="mb-3">
                <div class="text-muted small">Nombre / Razón Social</div>
                <div class="fw-bold fs-6 text-dark">
                    <?= e($expediente['tipo_persona'] === 'JURIDICA' ? $expediente['razon_social'] : $expediente['nombres'] . ' ' . $expediente['apellidos']) ?>
                </div>
            </div>
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <div class="text-muted small">Tipo / Documento</div>
                    <div class="fw-semibold"><?= e($expediente['tipo_documento']) ?>: <?= e($expediente['numero_documento']) ?></div>
                </div>
                <div class="col-6">
                    <div class="text-muted small">Tipo Persona</div>
                    <div class="fw-semibold"><?= e($expediente['tipo_persona']) ?></div>
                </div>
            </div>
            <div class="row g-2 mb-2">
                <div class="col-12">
                    <div class="text-muted small">Correo Electrónico</div>
                    <div class="fw-semibold"><?= e($expediente['correo']) ?></div>
                </div>
                <div class="col-12">
                    <div class="text-muted small">Teléfono / Celular</div>
                    <div class="fw-semibold"><?= e($expediente['telefono'] ?: 'No registrado') ?></div>
                </div>
            </div>
        </div>

        <div class="card card-custom p-4 mb-4">
            <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom">
                <i class="bi bi-file-earmark-text me-2"></i> Datos del Trámite
            </h6>
            <div class="mb-3">
                <div class="text-muted small">Tipo de Trámite</div>
                <div class="fw-bold text-dark"><?= e($expediente['tipo_tramite_nombre']) ?></div>
            </div>
            <div class="mb-3">
                <div class="text-muted small">Asunto</div>
                <div class="fw-semibold text-dark"><?= e($expediente['asunto']) ?></div>
            </div>
            <?php if (!empty($expediente['descripcion'])): ?>
                <div class="mb-3">
                    <div class="text-muted small">Descripción o Fundamentación</div>
                    <div class="small text-muted p-2 bg-light rounded border"><?= nl2br(e($expediente['descripcion'])) ?></div>
                </div>
            <?php endif; ?>
            <div class="row g-2">
                <div class="col-6">
                    <div class="text-muted small">Folios Declarados</div>
                    <div class="fw-bold"><?= (int)$expediente['folios'] ?> folios</div>
                </div>
                <div class="col-6">
                    <div class="text-muted small">Modalidad</div>
                    <div class="fw-bold"><?= (int)$expediente['es_virtual'] === 1 ? 'Virtual (Web)' : 'Presencial' ?></div>
                </div>
            </div>
        </div>

        <!-- Documentos Adjuntos (Observación 4) -->
        <div class="card card-custom p-4 mb-4">
            <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom d-flex align-items-center justify-content-between">
                <span><i class="bi bi-paperclip me-2"></i> Documentos Adjuntos (<?= count($documentos) ?>)</span>
                <?php if (!empty($documentos)): ?>
                    <span class="badge bg-primary-subtle text-primary small"><?= count($documentos) ?> <?= count($documentos) === 1 ? 'archivo' : 'archivos' ?></span>
                <?php endif; ?>
            </h6>
            <?php if (empty($documentos)): ?>
                <p class="text-muted small mb-0">No se registran archivos adjuntos.</p>
            <?php else: ?>
                <div class="d-flex flex-column gap-2">
                    <?php foreach ($documentos as $num => $doc): ?>
                        <?php 
                            $ext = strtolower($doc['extension'] ?? pathinfo($doc['nombre_original'], PATHINFO_EXTENSION));
                            $esPdf = ($ext === 'pdf');
                            $esImagen = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg']);
                            $esWord = in_array($ext, ['doc', 'docx']);
                            $esExcel = in_array($ext, ['xls', 'xlsx', 'csv']);
                            $peso = ($doc['tamano_bytes'] >= 1048576) 
                                ? round($doc['tamano_bytes'] / 1048576, 2) . ' MB' 
                                : round($doc['tamano_bytes'] / 1024, 1) . ' KB';
                            $iconoClass = 'bi-file-earmark-fill text-secondary';
                            if ($esPdf) $iconoClass = 'bi-file-earmark-pdf-fill text-danger';
                            elseif ($esWord) $iconoClass = 'bi-file-earmark-word-fill text-primary';
                            elseif ($esExcel) $iconoClass = 'bi-file-earmark-excel-fill text-success';
                            elseif ($esImagen) $iconoClass = 'bi-file-earmark-image-fill text-info';
                        ?>
                        <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between gap-2 shadow-sm">
                            <div class="d-flex align-items-center gap-2 text-truncate" style="min-width: 0;">
                                <i class="bi <?= $iconoClass ?> fs-4 flex-shrink-0"></i>
                                <div class="text-truncate">
                                    <div class="fw-semibold small text-truncate text-dark" title="<?= e($doc['nombre_original']) ?>">
                                        <?= ($num + 1) . '. ' . e($doc['nombre_original']) ?>
                                    </div>
                                    <small class="text-muted" style="font-size: 0.72rem;">
                                        <span class="badge bg-light text-dark border me-1"><?= strtoupper($ext) ?></span>
                                        <?= $peso ?>
                                        <?= (int)$doc['es_principal'] === 1 ? ' • <span class="badge bg-primary text-white" style="font-size: 0.65rem;">Principal</span>' : '' ?>
                                    </small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                <button type="button" 
                                        class="btn btn-sm btn-outline-primary btn-ver-documento px-2 py-1" 
                                        data-id="<?= $doc['id'] ?>"
                                        data-nombre="<?= e($doc['nombre_original']) ?>"
                                        data-url-ver="<?= url("/expedientes/documento/{$doc['id']}/ver") ?>"
                                        data-url-descarga="<?= url("/expedientes/documento/{$doc['id']}") ?>"
                                        data-ext="<?= e($ext) ?>"
                                        title="Ver documento en visor integrado"
                                        aria-label="Ver <?= e($doc['nombre_original']) ?>">
                                    <i class="bi bi-eye"></i> <span class="d-none d-sm-inline ms-1">Ver</span>
                                </button>
                                <a href="<?= url("/expedientes/documento/{$doc['id']}") ?>" 
                                   class="btn btn-sm btn-outline-secondary px-2 py-1" 
                                   title="Descargar archivo"
                                   aria-label="Descargar <?= e($doc['nombre_original']) ?>">
                                    <i class="bi bi-download"></i> <span class="d-none d-sm-inline ms-1">Descargar</span>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Columna Derecha: Trazabilidad y Línea de Tiempo (Sección 10) -->
    <div class="col-12 col-lg-7">
        <div class="card card-custom p-4 mb-4">
            <h6 class="fw-bold text-primary mb-4 pb-2 border-bottom d-flex align-items-center justify-content-between">
                <span><i class="bi bi-clock-history me-2"></i> Línea de Tiempo y Trazabilidad</span>
                <span class="badge bg-secondary-subtle text-secondary small"><?= count($movimientos) ?> movimientos</span>
            </h6>

            <div class="timeline position-relative ps-4" style="border-left: 2px solid #e2e8f0; margin-left: 10px;">
                <?php foreach ($movimientos as $mov): ?>
                    <div class="timeline-item mb-4 position-relative">
                        <!-- Punto conector -->
                        <span class="position-absolute" style="left: -29px; top: 2px; width: 14px; height: 14px; border-radius: 50%; background-color: <?= e($mov['estado_nvo_color'] ?? '#0B4F8A') ?>; border: 2px solid #ffffff; box-shadow: 0 0 0 2px rgba(11,79,138,0.2);"></span>
                        
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1">
                            <span class="badge text-white px-2 py-1" style="background-color: <?= e($mov['estado_nvo_color'] ?? '#0B4F8A') ?>; font-size: 0.72rem;">
                                <?= e($mov['tipo_movimiento']) ?> • <?= e($mov['estado_nvo_nombre']) ?>
                            </span>
                            <small class="text-muted" style="font-size: 0.75rem;">
                                <i class="bi bi-calendar3 me-1"></i><?= formatDateTime($mov['created_at']) ?>
                            </small>
                        </div>

                        <div class="p-3 bg-light rounded border mt-2">
                            <div class="fw-semibold text-dark small mb-1">
                                <?php if (!empty($mov['oficina_origen_sigla'])): ?>
                                    <span><?= e($mov['oficina_origen_sigla']) ?></span>
                                    <i class="bi bi-arrow-right mx-1 text-muted"></i>
                                <?php endif; ?>
                                <span><?= e($mov['oficina_destino_sigla'] ?? 'Mesa de Partes') ?></span>
                                <?php if (!empty($mov['usuario_nombre'])): ?>
                                    <span class="text-muted fw-normal"> (por <?= e($mov['usuario_nombre']) ?>)</span>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($mov['observacion'])): ?>
                                <div class="small text-secondary mt-2 pt-2 border-top">
                                    <i class="bi bi-chat-quote me-1"></i><?= nl2br(e($mov['observacion'])) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Solicitudes Internas entre Oficinas (Sección 4) -->
        <?php if (!empty($solicitudesInternas)): ?>
            <div class="card card-custom p-4 mb-4">
                <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom">
                    <i class="bi bi-chat-square-text me-2"></i> Solicitudes Internas de Información
                </h6>
                <?php foreach ($solicitudesInternas as $sol): ?>
                    <div class="p-3 bg-light rounded border mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                <?= e($sol['solicitante_sigla']) ?> <i class="bi bi-arrow-right"></i> <?= e($sol['proveedora_sigla']) ?>
                            </span>
                            <span class="badge bg-<?= $sol['estado'] === 'RESPONDIDA' ? 'success' : 'warning text-dark' ?>">
                                <?= e($sol['estado']) ?>
                            </span>
                        </div>
                        <div class="small text-dark mt-2">
                            <strong>Motivo / Consulta:</strong> <?= e($sol['motivo']) ?>
                        </div>
                        <?php if (!empty($sol['respuesta'])): ?>
                            <div class="mt-2 p-2 bg-white rounded border small text-success">
                                <strong>Respuesta:</strong> <?= e($sol['respuesta']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- =================================================================== -->
<!-- MODALES DE ACCIÓN DOCUMENTARIA                                      -->
<!-- =================================================================== -->

<!-- MODAL ENVIAR A DIRECCIÓN -->
<div class="modal fade" id="modalEnviarDireccion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 12px;">
            <form action="<?= url("/expedientes/{$expediente['id']}/enviar-direccion") ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="color: var(--primary, #0B4F8A);">Remitir Expediente a Dirección</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-3">
                        El expediente pasará a la bandeja de Dirección General para su revisión y derivación a la oficina técnica correspondiente.
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Observación / Proveído de Envío</label>
                        <textarea class="form-control" name="observacion" rows="3" placeholder="Observaciones de Mesa de Partes para Dirección..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning text-dark fw-bold">Confirmar Envío</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL DERIVAR A OFICINA (DIRECCIÓN) -->
<div class="modal fade" id="modalDerivar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 12px;">
            <form action="<?= url("/expedientes/{$expediente['id']}/derivar") ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="color: var(--primary, #0B4F8A);">Derivar a Oficina Responsable</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Oficina de Destino <span class="text-danger">*</span></label>
                        <select name="oficina_destino_id" class="form-select" required>
                            <option value="">-- Seleccionar Oficina --</option>
                            <?php foreach ($oficinas as $o): ?>
                                <?php if ($o['id'] != 1): // No auto-derivar a dirección ?>
                                    <option value="<?= $o['id'] ?>"><?= e($o['nombre']) ?> (<?= e($o['sigla']) ?>)</option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Prioridad</label>
                        <select name="prioridad_id" class="form-select">
                            <option value="1" <?= ($expediente['prioridad_id'] == 1) ? 'selected' : '' ?>>Normal (15 días)</option>
                            <option value="2" <?= ($expediente['prioridad_id'] == 2) ? 'selected' : '' ?>>Urgente (5 días)</option>
                            <option value="3" <?= ($expediente['prioridad_id'] == 3) ? 'selected' : '' ?>>Muy Urgente (2 días)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Indicaciones / Proveído de Derivación <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="indicaciones" rows="3" placeholder="Disposiciones de Dirección para la atención del trámite..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom">Ejecutar Derivación</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL RESPONDER A DIRECCIÓN (OFICINA) -->
<div class="modal fade" id="modalResponder" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 12px;">
            <form action="<?= url("/expedientes/{$expediente['id']}/responder") ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="color: var(--primary, #0B4F8A);">Emitir Respuesta Técnica a Dirección</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Detalle de la Atención / Informe <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="respuesta" rows="4" placeholder="Describe los actos administrativos realizados, dictamen o resultado..." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Documento de Respuesta (Oficio / Informe / Resolución)</label>
                        <input type="file" class="form-control" name="documento_respuesta" accept=".pdf,.doc,.docx,.jpg,.png">
                        <small class="text-muted">Adjunta el archivo firmado o informe técnico (Máx. 25 MB).</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success fw-bold">Remitir Respuesta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL SOLICITUD INTERNA ENTRE OFICINAS (Sección 4) -->
<div class="modal fade" id="modalSolicitudInterna" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 12px;">
            <form action="<?= url("/expedientes/{$expediente['id']}/solicitar-info") ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="color: var(--primary, #0B4F8A);">Consulta / Colaboración Interna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-3">
                        Solicita información complementaria a otra oficina (ej. Secretaría Académica a Calidad/Empleabilidad o Investigación a Biblioteca) sin transferir la responsabilidad principal del expediente.
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Oficina de Apoyo <span class="text-danger">*</span></label>
                        <select name="oficina_proveedora_id" class="form-select" required>
                            <option value="">-- Seleccionar Oficina --</option>
                            <?php foreach ($oficinas as $o): ?>
                                <?php if ($o['id'] != auth()['oficina_id']): ?>
                                    <option value="<?= $o['id'] ?>"><?= e($o['nombre']) ?> (<?= e($o['sigla']) ?>)</option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Motivo de la Solicitud <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="motivo" rows="3" placeholder="Información requerida para dictaminar..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom">Enviar Consulta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL DEVOLVER (DIRECCIÓN) -->
<div class="modal fade" id="modalDevolver" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 12px;">
            <form action="<?= url("/expedientes/{$expediente['id']}/devolver") ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-danger">Devolver Expediente con Observación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Motivo Obligatorio de Devolución <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="motivo" rows="3" placeholder="Indica detalladamente los motivos u observaciones para la devolución..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Devolución</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL FINALIZAR (DIRECCIÓN) -->
<div class="modal fade" id="modalFinalizar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 12px;">
            <form action="<?= url("/expedientes/{$expediente['id']}/finalizar") ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-success">Dar por Finalizado el Trámite</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-3">
                        El expediente se marcará como <strong>FINALIZADO</strong>. Esta resolución se reflejará en la consulta pública del usuario externo.
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Observación o Notificación Pública para el Solicitante</label>
                        <textarea class="form-control" name="observacion_publica" rows="3" placeholder="Ej. Trámite atendido favorablemente. Puede recoger su documento en Secretaría..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Finalizar Trámite</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL VISOR INTEGRADO MULTIFORMATO (PDF, WORD, EXCEL, IMÁGENES, TEXTO) -->
<div class="modal fade" id="modalVisorDocumento" tabindex="-1" aria-labelledby="modalVisorTitulo" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" id="visorModalDialog" style="max-width: 94vw; transition: all 0.25s ease;">
        <div class="modal-content shadow-lg border-0" style="border-radius: 12px; overflow: hidden; height: 90vh;">
            <div class="modal-header text-white py-2 px-3" style="background-color: #1e293b;">
                <div class="d-flex align-items-center gap-2 text-truncate me-2" style="min-width: 0;">
                    <i class="bi bi-file-earmark-fill fs-4 flex-shrink-0" id="visorIcono"></i>
                    <div class="text-truncate">
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="modal-title text-truncate fw-semibold mb-0 text-white" id="modalVisorTitulo">Visualizador de Documento</h6>
                            <span class="badge bg-secondary" id="visorBadgeExt" style="font-size: 0.68rem;"></span>
                        </div>
                        <small class="text-light opacity-75" style="font-size: 0.72rem;">Mesa de Partes • Visualización Segura Integral</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <button type="button" id="visorBtnFullscreen" class="btn btn-sm btn-outline-light" title="Alternar pantalla completa">
                        <i class="bi bi-arrows-fullscreen"></i>
                    </button>
                    <a href="#" id="visorBtnNuevaPestana" target="_blank" class="btn btn-sm btn-outline-light" title="Abrir en pestaña nueva">
                        <i class="bi bi-box-arrow-up-right me-1"></i> <span class="d-none d-md-inline">Pestaña nueva</span>
                    </a>
                    <a href="#" id="visorBtnDescargar" class="btn btn-sm btn-primary-custom" title="Descargar documento">
                        <i class="bi bi-download me-1"></i> <span class="d-none d-md-inline">Descargar</span>
                    </a>
                    <button type="button" class="btn-close btn-close-white ms-1" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
            </div>
            
            <div class="modal-body p-0 position-relative d-flex flex-column" style="height: calc(90vh - 58px); overflow: hidden; background-color: #1e293b;">
                <!-- Spinner de carga -->
                <div id="visorLoading" class="position-absolute top-50 start-50 translate-middle text-center text-white" style="z-index: 20;">
                    <div class="spinner-border text-primary mb-2" role="status"></div>
                    <div class="small fw-semibold" id="visorLoadingTexto">Cargando documento...</div>
                </div>

                <!-- 1. Visor de PDF (Iframe nativo) -->
                <div id="visorPdfWrapper" class="w-100 h-100 d-none" style="position: relative;">
                    <iframe id="visorIframe" src="about:blank" style="width: 100%; height: 100%; border: none; display: block;" title="Visor de PDF"></iframe>
                </div>

                <!-- 2. Visor de Imágenes (JPG, PNG, WEBP, etc.) -->
                <div id="visorImgWrapper" class="w-100 h-100 d-none d-flex flex-column align-items-center justify-content-center p-3" style="background: #0f172a; position: relative;">
                    <div class="position-absolute top-0 end-0 p-3 d-flex gap-2" style="z-index: 15;">
                        <button type="button" class="btn btn-sm btn-dark bg-opacity-75 border-secondary text-white" id="btnImgZoomIn" title="Acercar"><i class="bi bi-zoom-in"></i></button>
                        <button type="button" class="btn btn-sm btn-dark bg-opacity-75 border-secondary text-white" id="btnImgZoomOut" title="Alejar"><i class="bi bi-zoom-out"></i></button>
                        <button type="button" class="btn btn-sm btn-dark bg-opacity-75 border-secondary text-white" id="btnImgRotate" title="Rotar 90°"><i class="bi bi-arrow-clockwise"></i></button>
                        <button type="button" class="btn btn-sm btn-dark bg-opacity-75 border-secondary text-white" id="btnImgReset" title="Ajustar a pantalla"><i class="bi bi-arrows-angle-contract"></i></button>
                    </div>
                    <div class="overflow-auto w-100 h-100 d-flex align-items-center justify-content-center">
                        <img id="visorImgElement" src="" alt="Previsualización de imagen" class="img-fluid rounded shadow-lg" style="max-height: 80vh; max-width: 90%; object-fit: contain; transition: transform 0.2s ease;">
                    </div>
                </div>

                <!-- 3. Visor de Word (.docx / .doc) -->
                <div id="visorDocxWrapper" class="w-100 h-100 d-none overflow-auto" style="background-color: #525659; padding: 24px 12px;">
                    <div id="visorDocxContainer" class="mx-auto shadow-lg" style="max-width: 880px; min-height: 100%; background: #ffffff; padding: 40px; border-radius: 4px;"></div>
                </div>

                <!-- 4. Visor de Excel (.xlsx / .xls / .csv) -->
                <div id="visorExcelWrapper" class="w-100 h-100 d-none d-flex flex-column bg-white">
                    <div class="p-2 border-bottom bg-light d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <ul class="nav nav-pills nav-fill small gap-1 mb-0" id="visorExcelTabs"></ul>
                        <div class="input-group input-group-sm" style="max-width: 250px;">
                            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                            <input type="text" id="visorExcelFilter" class="form-control" placeholder="Buscar en hoja...">
                        </div>
                    </div>
                    <div id="visorExcelContainer" class="table-responsive flex-grow-1 p-3" style="overflow: auto;"></div>
                </div>

                <!-- 5. Visor de Texto Plano (.txt) -->
                <div id="visorTextWrapper" class="w-100 h-100 d-none p-4 overflow-auto" style="background-color: #0f172a;">
                    <pre id="visorTextContent" class="text-light font-monospace small mb-0" style="white-space: pre-wrap; word-break: break-word; line-height: 1.5;"></pre>
                </div>

                <!-- 6. Fallback si no es parseable o error -->
                <div id="visorFallbackWrapper" class="w-100 h-100 d-none d-flex flex-column align-items-center justify-content-center p-4 text-center text-white" style="background: #1e293b;">
                    <div class="rounded-circle p-4 mb-3" style="background: rgba(255,255,255,0.08);">
                        <i class="bi bi-file-earmark-text text-warning" style="font-size: 3.5rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-2" id="visorFallbackTitulo">Visualización en aplicación externa</h5>
                    <p class="text-muted small mb-4" style="max-width: 480px;" id="visorFallbackMensaje">
                        Puedes descargar este archivo para abrirlo con su programa oficial (Word, Excel o visor de sistema).
                    </p>
                    <a href="#" id="visorFallbackBtnDescarga" class="btn btn-primary-custom px-4 py-2">
                        <i class="bi bi-download me-2"></i> Descargar Documento
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Librerías de renderizado en cliente para Word y Excel -->
<script src="<?= url('assets/js/vendor/jszip.min.js') ?>"></script>
<script src="<?= url('assets/js/vendor/docx-preview.min.js') ?>"></script>
<script src="<?= url('assets/js/vendor/xlsx.full.min.js') ?>"></script>
<script>
    if (typeof JSZip === 'undefined') {
        document.write('<script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"><\/script>');
    }
</script>
<script>
    if (typeof docx === 'undefined') {
        document.write('<script src="https://cdn.jsdelivr.net/npm/docx-preview@0.3.4/dist/docx-preview.min.js"><\/script>');
    }
</script>
<script>
    if (typeof XLSX === 'undefined') {
        document.write('<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"><\/script>');
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const visorModalEl = document.getElementById('modalVisorDocumento');
        const visorModalDialog = document.getElementById('visorModalDialog');
        const visorTitulo = document.getElementById('modalVisorTitulo');
        const visorBadgeExt = document.getElementById('visorBadgeExt');
        const visorIcono = document.getElementById('visorIcono');
        const visorBtnFullscreen = document.getElementById('visorBtnFullscreen');
        const visorBtnNuevaPestana = document.getElementById('visorBtnNuevaPestana');
        const visorBtnDescargar = document.getElementById('visorBtnDescargar');
        const visorLoading = document.getElementById('visorLoading');
        const visorLoadingTexto = document.getElementById('visorLoadingTexto');

        // Contenedores individuales
        const visorPdfWrapper = document.getElementById('visorPdfWrapper');
        const visorIframe = document.getElementById('visorIframe');
        const visorImgWrapper = document.getElementById('visorImgWrapper');
        const visorImgElement = document.getElementById('visorImgElement');
        const visorDocxWrapper = document.getElementById('visorDocxWrapper');
        const visorDocxContainer = document.getElementById('visorDocxContainer');
        const visorExcelWrapper = document.getElementById('visorExcelWrapper');
        const visorExcelTabs = document.getElementById('visorExcelTabs');
        const visorExcelContainer = document.getElementById('visorExcelContainer');
        const visorExcelFilter = document.getElementById('visorExcelFilter');
        const visorTextWrapper = document.getElementById('visorTextWrapper');
        const visorTextContent = document.getElementById('visorTextContent');
        const visorFallbackWrapper = document.getElementById('visorFallbackWrapper');
        const visorFallbackTitulo = document.getElementById('visorFallbackTitulo');
        const visorFallbackMensaje = document.getElementById('visorFallbackMensaje');
        const visorFallbackBtnDescarga = document.getElementById('visorFallbackBtnDescarga');

        // Controles de imagen
        let imgZoom = 1;
        let imgRotation = 0;
        function actualizarTransformImagen() {
            visorImgElement.style.transform = `scale(${imgZoom}) rotate(${imgRotation}deg)`;
        }
        document.getElementById('btnImgZoomIn')?.addEventListener('click', () => {
            imgZoom = Math.min(3.5, imgZoom + 0.25);
            actualizarTransformImagen();
        });
        document.getElementById('btnImgZoomOut')?.addEventListener('click', () => {
            imgZoom = Math.max(0.4, imgZoom - 0.25);
            actualizarTransformImagen();
        });
        document.getElementById('btnImgRotate')?.addEventListener('click', () => {
            imgRotation = (imgRotation + 90) % 360;
            actualizarTransformImagen();
        });
        document.getElementById('btnImgReset')?.addEventListener('click', () => {
            imgZoom = 1;
            imgRotation = 0;
            actualizarTransformImagen();
        });

        // Alternar pantalla completa
        if (visorBtnFullscreen && visorModalDialog) {
            visorBtnFullscreen.addEventListener('click', () => {
                visorModalDialog.classList.toggle('modal-fullscreen');
                const isFull = visorModalDialog.classList.contains('modal-fullscreen');
                visorBtnFullscreen.innerHTML = isFull 
                    ? '<i class="bi bi-fullscreen-exit"></i>' 
                    : '<i class="bi bi-arrows-fullscreen"></i>';
                const modalContent = visorModalDialog.querySelector('.modal-content');
                if (modalContent) {
                    modalContent.style.height = isFull ? '100vh' : '90vh';
                }
            });
        }

        function ocultarTodosLosVisores() {
            visorPdfWrapper.classList.add('d-none');
            visorImgWrapper.classList.add('d-none');
            visorDocxWrapper.classList.add('d-none');
            visorExcelWrapper.classList.add('d-none');
            visorTextWrapper.classList.add('d-none');
            visorFallbackWrapper.classList.add('d-none');
            visorIframe.src = 'about:blank';
            visorImgElement.src = '';
            visorDocxContainer.innerHTML = '';
            visorExcelTabs.innerHTML = '';
            visorExcelContainer.innerHTML = '';
            visorTextContent.textContent = '';
        }

        function mostrarFallback(mensaje = '', titulo = 'Visualización en aplicación externa') {
            ocultarTodosLosVisores();
            visorLoading.classList.add('d-none');
            visorFallbackTitulo.textContent = titulo;
            if (mensaje) visorFallbackMensaje.textContent = mensaje;
            visorFallbackWrapper.classList.remove('d-none');
        }

        function renderizarExcel(workbook) {
            visorExcelTabs.innerHTML = '';
            visorExcelContainer.innerHTML = '';

            const sheetNames = workbook.SheetNames || [];
            if (sheetNames.length === 0) {
                visorExcelContainer.innerHTML = '<div class="alert alert-warning m-3">El archivo de cálculo no contiene hojas disponibles.</div>';
                return;
            }

            sheetNames.forEach((sheetName, index) => {
                const li = document.createElement('li');
                li.className = 'nav-item';
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'nav-link py-1 px-3 fw-semibold ' + (index === 0 ? 'active' : '');
                btn.textContent = sheetName;
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    visorExcelTabs.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                    btn.classList.add('active');
                    renderizarHojaExcel(workbook.Sheets[sheetName]);
                });
                li.appendChild(btn);
                visorExcelTabs.appendChild(li);
            });

            function renderizarHojaExcel(sheet) {
                const html = XLSX.utils.sheet_to_html(sheet, { id: 'tablaExcelRenderizada', editable: false });
                visorExcelContainer.innerHTML = html;
                const table = visorExcelContainer.querySelector('table');
                if (table) {
                    table.className = 'table table-bordered table-hover table-sm align-middle text-nowrap mb-0';
                    table.style.fontSize = '0.84rem';
                    const firstRow = table.querySelector('tr');
                    if (firstRow) {
                        firstRow.style.backgroundColor = '#f1f5f9';
                        firstRow.style.fontWeight = '700';
                        firstRow.style.position = 'sticky';
                        firstRow.style.top = '0';
                        firstRow.style.zIndex = '3';
                    }
                }
            }

            renderizarHojaExcel(workbook.Sheets[sheetNames[0]]);

            if (visorExcelFilter) {
                visorExcelFilter.value = '';
                visorExcelFilter.oninput = function() {
                    const q = this.value.toLowerCase().trim();
                    const table = visorExcelContainer.querySelector('table');
                    if (!table) return;
                    const rows = table.querySelectorAll('tr');
                    rows.forEach((r, idx) => {
                        if (idx === 0) return; // Mantener encabezado
                        const match = !q || r.textContent.toLowerCase().includes(q);
                        r.style.display = match ? '' : 'none';
                    });
                };
            }
        }

        if (visorModalEl) {
            const bsVisorModal = new bootstrap.Modal(visorModalEl);

            document.querySelectorAll('.btn-ver-documento').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const nombre = this.dataset.nombre || 'Documento';
                    const urlVer = this.dataset.urlVer;
                    const urlDescarga = this.dataset.urlDescarga;
                    const ext = (this.dataset.ext || '').toLowerCase();

                    const esPdf = ext === 'pdf';
                    const esImagen = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'].includes(ext);
                    const esDocx = ext === 'docx';
                    const esDoc = ext === 'doc';
                    const esExcel = ['xlsx', 'xls', 'csv'].includes(ext);
                    const esTexto = ['txt', 'log', 'json', 'xml'].includes(ext);

                    ocultarTodosLosVisores();

                    visorTitulo.textContent = nombre;
                    visorBadgeExt.textContent = ext.toUpperCase();
                    visorBtnNuevaPestana.href = urlVer;
                    visorBtnDescargar.href = urlDescarga;
                    visorFallbackBtnDescarga.href = urlDescarga;

                    // Asignar icono dinámico según formato
                    if (esPdf) {
                        visorIcono.className = 'bi bi-file-earmark-pdf-fill text-danger fs-4 flex-shrink-0';
                    } else if (esDocx || esDoc) {
                        visorIcono.className = 'bi bi-file-earmark-word-fill text-primary fs-4 flex-shrink-0';
                    } else if (esExcel) {
                        visorIcono.className = 'bi bi-file-earmark-excel-fill text-success fs-4 flex-shrink-0';
                    } else if (esImagen) {
                        visorIcono.className = 'bi bi-file-earmark-image-fill text-info fs-4 flex-shrink-0';
                    } else {
                        visorIcono.className = 'bi bi-file-earmark-text-fill text-warning fs-4 flex-shrink-0';
                    }

                    visorLoadingTexto.textContent = 'Cargando ' + nombre + '...';
                    visorLoading.classList.remove('d-none');
                    bsVisorModal.show();

                    try {
                        if (esPdf) {
                            // 1. PDF nativo
                            visorPdfWrapper.classList.remove('d-none');
                            visorIframe.src = urlVer;
                            visorIframe.onload = () => visorLoading.classList.add('d-none');
                        } else if (esImagen) {
                            // 2. Imagen con zoom y rotación
                            imgZoom = 1;
                            imgRotation = 0;
                            actualizarTransformImagen();
                            visorImgElement.onload = () => {
                                visorLoading.classList.add('d-none');
                                visorImgWrapper.classList.remove('d-none');
                            };
                            visorImgElement.onerror = () => {
                                mostrarFallback("No se pudo cargar la imagen seleccionada.");
                            };
                            visorImgElement.src = urlVer;
                        } else if (esDocx) {
                            // 3. Documento Word (.docx)
                            const resp = await fetch(urlVer);
                            if (!resp.ok) throw new Error('HTTP ' + resp.status);
                            const blob = await resp.blob();

                            if (typeof docx !== 'undefined' && docx.renderAsync) {
                                visorLoadingTexto.textContent = 'Renderizando documento Word...';
                                await docx.renderAsync(blob, visorDocxContainer, null, {
                                    className: "docx-render-page",
                                    inWrapper: true,
                                    ignoreWidth: false,
                                    ignoreHeight: false,
                                    breakPages: true
                                });
                                visorLoading.classList.add('d-none');
                                visorDocxWrapper.classList.remove('d-none');
                            } else {
                                throw new Error('Motor docx-preview no cargado');
                            }
                        } else if (esExcel) {
                            // 4. Excel (.xlsx / .xls / .csv)
                            const resp = await fetch(urlVer);
                            if (!resp.ok) throw new Error('HTTP ' + resp.status);
                            const arrayBuffer = await resp.arrayBuffer();

                            if (typeof XLSX !== 'undefined') {
                                visorLoadingTexto.textContent = 'Procesando hojas de cálculo...';
                                const workbook = XLSX.read(arrayBuffer, { type: 'array' });
                                renderizarExcel(workbook);
                                visorLoading.classList.add('d-none');
                                visorExcelWrapper.classList.remove('d-none');
                            } else {
                                throw new Error('Motor SheetJS no cargado');
                            }
                        } else if (esTexto) {
                            // 5. Texto plano
                            const resp = await fetch(urlVer);
                            if (!resp.ok) throw new Error('HTTP ' + resp.status);
                            const text = await resp.text();
                            visorTextContent.textContent = text;
                            visorLoading.classList.add('d-none');
                            visorTextWrapper.classList.remove('d-none');
                        } else if (esDoc) {
                            // Formato Word 97-2003 (.doc binario)
                            mostrarFallback(
                                "El archivo está en formato Word 97-2003 (.doc binario antiguo). Para visualizarlo con total precisión, descárguelo para abrirlo con Microsoft Word.",
                                "Documento Word (.doc)"
                            );
                        } else {
                            mostrarFallback("Formato no soportado para previsualización directa.");
                        }
                    } catch (err) {
                        console.warn("Fallo en visor directo:", err);
                        mostrarFallback(
                            "No se pudo cargar la vista previa directa (" + (err.message || 'Error de lectura') + "). Puede descargar el archivo para abrirlo con su aplicación local.",
                            "Descarga disponible"
                        );
                    }
                });
            });

            // Al cerrar el modal, liberar memoria y restablecer tamaño
            visorModalEl.addEventListener('hidden.bs.modal', () => {
                ocultarTodosLosVisores();
                if (visorModalDialog.classList.contains('modal-fullscreen')) {
                    visorModalDialog.classList.remove('modal-fullscreen');
                    if (visorBtnFullscreen) {
                        visorBtnFullscreen.innerHTML = '<i class="bi bi-arrows-fullscreen"></i>';
                    }
                }
            });
        }
    });
</script>
