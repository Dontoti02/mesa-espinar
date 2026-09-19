<?php

namespace App\Core;

use PDO;

class Validator
{
    private array $data;
    private array $rules;
    private array $errors = [];
    private array $customMessages = [];

    public function __construct(array $data, array $rules, array $customMessages = [])
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->customMessages = $customMessages;
    }

    public static function make(array $data, array $rules, array $customMessages = []): self
    {
        $validator = new self($data, $rules, $customMessages);
        $validator->validate();
        return $validator;
    }

    public function validate(): bool
    {
        foreach ($this->rules as $field => $rulesString) {
            $rulesList = is_array($rulesString) ? $rulesString : explode('|', $rulesString);
            $value = $this->data[$field] ?? null;

            foreach ($rulesList as $ruleItem) {
                $params = [];
                if (str_contains($ruleItem, ':')) {
                    [$ruleName, $paramStr] = explode(':', $ruleItem, 2);
                    $params = explode(',', $paramStr);
                } else {
                    $ruleName = $ruleItem;
                }

                $ruleName = trim($ruleName);

                // Si no es required y el campo está vacío, saltar otras validaciones (excepto reglas de archivos)
                if ($ruleName !== 'required' && ($value === null || $value === '' || (is_array($value) && empty($value)))) {
                    if (!in_array($ruleName, ['file_required', 'file_mimes', 'file_max'])) {
                        continue;
                    }
                }

                $method = 'validate' . str_replace('_', '', ucwords($ruleName, '_'));
                if (method_exists($this, $method)) {
                    $this->$method($field, $value, $params);
                }
            }
        }

        return empty($this->errors);
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    private function addError(string $field, string $defaultMessage): void
    {
        $message = $this->customMessages[$field] ?? $defaultMessage;
        $this->errors[$field][] = $message;
    }

    private function isNumericField(string $field): bool
    {
        $ruleString = $this->rules[$field] ?? '';
        $rules = is_array($ruleString) ? $ruleString : explode('|', $ruleString);
        foreach ($rules as $r) {
            $ruleName = explode(':', $r, 2)[0];
            if (in_array($ruleName, ['numeric', 'integer'], true)) {
                return true;
            }
        }
        return false;
    }

    private function getFieldLabel(string $field): string
    {
        $labels = [
            'tipo_persona' => 'tipo de persona',
            'tipo_documento' => 'tipo de documento',
            'numero_documento' => 'número de documento',
            'nombres' => 'nombres',
            'apellidos' => 'apellidos',
            'razon_social' => 'razón social',
            'correo' => 'correo electrónico',
            'telefono' => 'teléfono',
            'direccion' => 'dirección',
            'asunto' => 'asunto',
            'descripcion' => 'descripción',
            'folios' => 'número de folios',
            'tipo_tramite_id' => 'tipo de trámite',
            'archivo_principal' => 'archivo principal',
            'dni' => 'DNI',
            'usuario' => 'nombre de usuario',
            'password' => 'contraseña',
        ];
        return $labels[$field] ?? str_replace('_', ' ', $field);
    }

    private function validateRequired(string $field, mixed $value, array $params): void
    {
        if ($value === null || trim((string)$value) === '') {
            $label = $this->getFieldLabel($field);
            $this->addError($field, "El campo {$label} es obligatorio.");
        }
    }

    private function validateEmail(string $field, mixed $value, array $params): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $label = $this->getFieldLabel($field);
            $this->addError($field, "El campo {$label} debe ser un correo electrónico válido.");
        }
    }

    private function validateNumeric(string $field, mixed $value, array $params): void
    {
        if (!is_numeric($value)) {
            $label = $this->getFieldLabel($field);
            $this->addError($field, "El campo {$label} debe ser numérico.");
        }
    }

    private function validateInteger(string $field, mixed $value, array $params): void
    {
        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            $label = $this->getFieldLabel($field);
            $this->addError($field, "El campo {$label} debe ser un número entero.");
        }
    }

    private function validateMin(string $field, mixed $value, array $params): void
    {
        $min = (int)($params[0] ?? 0);
        $label = $this->getFieldLabel($field);
        if ($this->isNumericField($field)) {
            if ((float)$value < $min) {
                $this->addError($field, "El valor de {$label} debe ser como mínimo {$min}.");
            }
        } else {
            if (mb_strlen(trim((string)$value)) < $min) {
                $this->addError($field, "El campo {$label} debe tener al menos {$min} caracteres.");
            }
        }
    }

    private function validateMax(string $field, mixed $value, array $params): void
    {
        $max = (int)($params[0] ?? 255);
        $label = $this->getFieldLabel($field);
        if ($this->isNumericField($field)) {
            if ((float)$value > $max) {
                $this->addError($field, "El valor de {$label} no debe ser mayor a {$max}.");
            }
        } else {
            if (mb_strlen(trim((string)$value)) > $max) {
                $this->addError($field, "El campo {$label} no debe superar los {$max} caracteres.");
            }
        }
    }

    private function validateIn(string $field, mixed $value, array $params): void
    {
        if (!in_array((string)$value, $params, true)) {
            $this->addError($field, "El valor seleccionado para {$field} no es válido.");
        }
    }

    private function validateMatches(string $field, mixed $value, array $params): void
    {
        $otherField = $params[0] ?? '';
        $otherVal = $this->data[$otherField] ?? null;
        if ($value !== $otherVal) {
            $this->addError($field, "El campo {$field} no coincide con {$otherField}.");
        }
    }

    private function validateDate(string $field, mixed $value, array $params): void
    {
        $d = \DateTime::createFromFormat('Y-m-d', (string)$value);
        if (!$d || $d->format('Y-m-d') !== $value) {
            $this->addError($field, "El campo {$field} debe ser una fecha válida (AAAA-MM-DD).");
        }
    }

    private function validateIdentifier(string $identifier): bool
    {
        return preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $identifier) === 1;
    }

    private function validateUnique(string $field, mixed $value, array $params): void
    {
        $table = $params[0] ?? '';
        $column = $params[1] ?? $field;
        $exceptId = $params[2] ?? null;
        $idColumn = $params[3] ?? 'id';

        if (!$table) {
            return;
        }

        if (!$this->validateIdentifier($table) || !$this->validateIdentifier($column) || !$this->validateIdentifier($idColumn)) {
            return;
        }

        $sql = "SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` = :val";
        if ($exceptId !== null && $exceptId !== '') {
            $sql .= " AND `{$idColumn}` != :except_id";
        }

        $db = Database::getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':val', $value);
        if ($exceptId !== null && $exceptId !== '') {
            $stmt->bindValue(':except_id', $exceptId);
        }
        $stmt->execute();
        $count = (int)$stmt->fetchColumn();

        if ($count > 0) {
            $this->addError($field, "El valor ingresado para {$field} ya está registrado.");
        }
    }

    private function validateFileRequired(string $field, mixed $value, array $params): void
    {
        $file = $_FILES[$field] ?? null;
        if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
            $this->addError($field, "El archivo {$field} es obligatorio.");
        }
    }

    private function validateFileMimes(string $field, mixed $value, array $params): void
    {
        $file = $_FILES[$field] ?? null;
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $params)) {
                $allowed = implode(', ', $params);
                $this->addError($field, "El archivo debe tener una de las siguientes extensiones: {$allowed}.");
                return;
            }

            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file['tmp_name']);
            $extToMime = [
                'pdf' => 'application/pdf',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'xls' => 'application/vnd.ms-excel',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'csv' => 'text/csv',
                'txt' => 'text/plain',
                'zip' => 'application/zip',
            ];
            $expectedMime = $extToMime[$ext] ?? null;
            if ($expectedMime !== null && $mimeType !== $expectedMime) {
                $this->addError($field, "El tipo MIME del archivo no coincide con la extensión detectada.");
            }
        }
    }

    private function validateFileMax(string $field, mixed $value, array $params): void
    {
        $file = $_FILES[$field] ?? null;
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $maxMb = (float)($params[0] ?? 25);
            $maxBytes = $maxMb * 1024 * 1024;
            if ($file['size'] > $maxBytes) {
                $this->addError($field, "El archivo no debe superar los {$maxMb} MB.");
            }
        }
    }
}
