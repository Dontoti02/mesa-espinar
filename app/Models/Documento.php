<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Documento extends Model
{
    protected string $table = 'expediente_documentos';
    protected array $fillable = [
        'expediente_id', 'movimiento_id', 'nombre_original', 'nombre_archivo',
        'ruta', 'extension', 'tamano_bytes', 'mime_type', 'hash_sha256',
        'es_principal', 'es_publico', 'usuario_id'
    ];

    private const MIME_MAP = [
        'pdf'  => ['application/pdf'],
        'doc'  => ['application/msword'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        'xls'  => ['application/vnd.ms-excel'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        'jpg'  => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png'  => ['image/png']
    ];

    public function guardarArchivo(
        array $fileArray,
        int $expedienteId,
        ?int $movimientoId = null,
        bool $esPrincipal = false,
        bool $esPublico = false,
        ?int $usuarioId = null
    ): ?array {
        if ($fileArray['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException("Error en la subida del archivo (código {$fileArray['error']}).");
        }

        $nombreOriginal = basename($fileArray['name']);
        $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

        // 1. Validar extensión permitida
        $permitidas = explode(',', $_ENV['ALLOWED_EXTENSIONS'] ?? 'pdf,doc,docx,xls,xlsx,jpg,jpeg,png');
        $permitidas = array_map('trim', $permitidas);
        if (!in_array($extension, $permitidas, true)) {
            throw new \RuntimeException("La extensión .{$extension} no está permitida en el sistema.");
        }

        // 2. Validar tamaño
        $maxMb = (float)($_ENV['MAX_FILE_SIZE_MB'] ?? 25);
        if ($fileArray['size'] > ($maxMb * 1024 * 1024)) {
            throw new \RuntimeException("El archivo supera el tamaño máximo permitido de {$maxMb} MB.");
        }

        // 3. Validación de MIME Real con finfo
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $realMime = $finfo->file($fileArray['tmp_name']);

        if (isset(self::MIME_MAP[$extension])) {
            if (!in_array($realMime, self::MIME_MAP[$extension], true)) {
                // Validación estricta para evitar spoofing
                throw new \RuntimeException("El contenido del archivo no coincide con su extensión declarada (.{$extension}).");
            }
        }

        // 4. Calcular Hash SHA-256
        $hashSha256 = hash_file('sha256', $fileArray['tmp_name']);

        // 5. Destino y nombre seguro en storage/documents
        $storageDir = dirname(__DIR__, 2) . '/storage/documents/' . date('Y/m');
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0775, true);
        }

        $nombreArchivoSeguro = sprintf(
            "%s_%s.%s",
            date('Ymd_His'),
            bin2hex(random_bytes(10)),
            $extension
        );

        $rutaCompleta = $storageDir . '/' . $nombreArchivoSeguro;
        $rutaRelativa = 'storage/documents/' . date('Y/m') . '/' . $nombreArchivoSeguro;

        if (!move_uploaded_file($fileArray['tmp_name'], $rutaCompleta)) {
            throw new \RuntimeException("No se pudo guardar el archivo en el almacenamiento del servidor.");
        }

        // 6. Registrar en base de datos
        $documentoId = $this->insert([
            'expediente_id' => $expedienteId,
            'movimiento_id' => $movimientoId,
            'nombre_original' => $nombreOriginal,
            'nombre_archivo' => $nombreArchivoSeguro,
            'ruta' => $rutaRelativa,
            'extension' => $extension,
            'tamano_bytes' => $fileArray['size'],
            'mime_type' => $realMime,
            'hash_sha256' => $hashSha256,
            'es_principal' => $esPrincipal ? 1 : 0,
            'es_publico' => $esPublico ? 1 : 0,
            'usuario_id' => $usuarioId
        ]);

        return $this->find($documentoId);
    }

    public function getDocumentosExpediente(int $expedienteId, bool $soloPublicos = false): array
    {
        $sql = "SELECT d.*, CONCAT(u.nombres, ' ', u.apellidos) AS subido_por
                FROM `{$this->table}` d
                LEFT JOIN usuarios u ON d.usuario_id = u.id
                WHERE d.expediente_id = :id";

        if ($soloPublicos) {
            $sql .= " AND d.es_publico = 1";
        }

        $sql .= " ORDER BY d.es_principal DESC, d.id ASC";

        $stmt = $this->db()->prepare($sql);
        $stmt->execute([':id' => $expedienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
