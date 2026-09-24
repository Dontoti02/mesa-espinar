<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Models\Documento;
use App\Models\Expediente;
use App\Models\Movimiento;
use App\Models\TipoTramite;

class ConsultaController extends Controller
{
    private Expediente $expedienteModel;
    private Movimiento $movimientoModel;
    private Documento $documentoModel;
    private TipoTramite $tipoTramiteModel;

    public function __construct()
    {
        $this->expedienteModel = new Expediente();
        $this->movimientoModel = new Movimiento();
        $this->documentoModel = new Documento();
        $this->tipoTramiteModel = new TipoTramite();
    }

    public function home(): void
    {
        $tramitesPopulares = $this->tipoTramiteModel->masDemandadosVirtuales(6);

        $this->render('public.home', [
            'pageTitle' => 'Mesa de Partes Virtual - Inicio',
            'tramites' => $tramitesPopulares
        ], 'public');
    }

    public function index(): void
    {
        $codigoPrellenado = trim($_GET['codigo'] ?? '');
        $expedientePrellenado = trim($_GET['expediente'] ?? '');

        // Si pasaron en 'codigo' algo con formato de expediente (ej. EXP-...)
        if (!empty($codigoPrellenado) && (str_starts_with(strtoupper($codigoPrellenado), 'EXP') || str_contains($codigoPrellenado, '-'))) {
            if (empty($expedientePrellenado)) {
                $expedientePrellenado = $codigoPrellenado;
                $codigoPrellenado = '';
            }
        }

        $this->render('public.consulta', [
            'pageTitle' => 'Consulta de Trámite y Seguimiento',
            'codigoPrellenado' => $codigoPrellenado,
            'expedientePrellenado' => $expedientePrellenado
        ], 'public');
    }

    public function buscar(): void
    {
        $numeroExpediente = strtoupper(trim($_POST['numero_expediente'] ?? ''));
        $codigoSeguimiento = strtoupper(trim($_POST['codigo_seguimiento'] ?? ''));
        $criterioLegacy = strtoupper(trim($_POST['codigo'] ?? ''));
        $documentoIdentidad = trim($_POST['documento'] ?? '');

        // Soporte de compatibilidad por si se envía 'codigo'
        if (empty($numeroExpediente) && empty($codigoSeguimiento) && !empty($criterioLegacy)) {
            if (str_starts_with($criterioLegacy, 'EXP') || str_contains($criterioLegacy, '-')) {
                $numeroExpediente = $criterioLegacy;
            } else {
                $codigoSeguimiento = $criterioLegacy;
            }
        }

        if (empty($numeroExpediente) && empty($codigoSeguimiento)) {
            Session::setFlash('error', 'Por favor ingresa tu Número de Expediente o tu Código de Seguimiento.');
            $this->redirect('/consulta');
        }

        $expediente = null;

        // Si proporcionó ambos, verificar que coincidan exactamente con el mismo expediente
        if (!empty($numeroExpediente) && !empty($codigoSeguimiento)) {
            $expediente = $this->expedienteModel->findByExpedienteYCodigo($numeroExpediente, $codigoSeguimiento);
            if (!$expediente) {
                Session::setFlash('error', "El expediente '{$numeroExpediente}' y el código '{$codigoSeguimiento}' no corresponden al mismo trámite. Verifica los datos en tu cargo.");
                $this->redirect('/consulta');
            }
        } elseif (!empty($numeroExpediente)) {
            $expediente = $this->expedienteModel->findByNumeroExpediente($numeroExpediente);
        } else {
            $expediente = $this->expedienteModel->findByCodigoSeguimiento($codigoSeguimiento);
        }

        if (!$expediente) {
            $buscado = !empty($numeroExpediente) ? $numeroExpediente : $codigoSeguimiento;
            Session::setFlash('error', "No se encontró ningún expediente con los datos ingresados ('{$buscado}'). Verifica los datos e intenta nuevamente.");
            $this->redirect('/consulta');
        }

        // Si se proporcionó documento de identidad, verificar coincidencia
        if (!empty($documentoIdentidad) && trim($expediente['numero_documento']) !== $documentoIdentidad) {
            Session::setFlash('error', 'El número de documento de identidad no coincide con el titular de este expediente.');
            $this->redirect('/consulta');
        }

        $this->redirect('/consulta/' . urlencode($expediente['codigo_seguimiento']));
    }

    public function detalle(string $codigo): void
    {
        $codigoNormalizado = strtoupper(trim($codigo));
        $expediente = $this->expedienteModel->findByCodigoSeguimiento($codigoNormalizado);

        if (!$expediente) {
            Session::setFlash('error', 'Expediente no encontrado.');
            $this->redirect('/consulta');
        }

        // Trazabilidad pública (solo movimientos públicos, sin notas reservadas de funcionarios)
        $lineaTiempoPublica = $this->movimientoModel->getLineaDeTiempo((int)$expediente['id'], true);

        $this->render('public.detalle', [
            'pageTitle' => 'Estado del Expediente: ' . $expediente['numero_expediente'],
            'expediente' => $expediente,
            'movimientos' => $lineaTiempoPublica
        ], 'public');
    }
}
