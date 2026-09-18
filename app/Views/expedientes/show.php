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

        <!-- Documentos Adjuntos -->
        <div class="card card-custom p-4 mb-4">
            <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom">
                <i class="bi bi-paperclip me-2"></i> Documentos Adjuntos (<?= count($documentos) ?>)
            </h6>
            <?php if (empty($documentos)): ?>
                <p class="text-muted small mb-0">No se registran archivos adjuntos.</p>
            <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($documentos as $doc): ?>
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2 text-truncate" style="max-width: 260px;">
                                <i class="bi bi-file-earmark-pdf-fill text-danger fs-4"></i>
                                <div>
                                    <div class="fw-semibold small text-truncate" title="<?= e($doc['nombre_original']) ?>">
                                        <?= e($doc['nombre_original']) ?>
                                    </div>
                                    <small class="text-muted" style="font-size: 0.72rem;">
                                        <?= round($doc['tamano_bytes'] / 1024, 1) ?> KB
                                        <?= (int)$doc['es_principal'] === 1 ? ' • <span class="text-primary fw-bold">Principal</span>' : '' ?>
                                    </small>
                                </div>
                            </div>
                            <a href="<?= url("/expedientes/documento/{$doc['id']}") ?>" class="btn btn-sm btn-outline-primary" title="Descargar documento">
                                <i class="bi bi-download"></i>
                            </a>
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
