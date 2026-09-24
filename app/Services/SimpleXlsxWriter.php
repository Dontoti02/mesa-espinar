<?php

namespace App\Services;

use ZipArchive;

/**
 * Generador nativo y ultra liviano de hojas de cálculo Microsoft Excel (.xlsx)
 * Cumple con el estándar ECMA-376 / ISO/IEC 29500 (OpenXML).
 * No requiere librerías externas pesadas ni Composer.
 */
class SimpleXlsxWriter
{
    /**
     * Limpia caracteres de control no permitidos en XML 1.0 y escapa entidades.
     */
    public static function xmlSafe(string $str): string
    {
        $clean = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $str);
        return htmlspecialchars($clean ?? '', ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }

    /**
     * Convierte índice 0-indexed a letra de columna de Excel (0 -> A, 25 -> Z, 26 -> AA).
     */
    public static function getColLetter(int $colIdx): string
    {
        $letter = '';
        while ($colIdx >= 0) {
            $letter = chr($colIdx % 26 + 65) . $letter;
            $colIdx = intdiv($colIdx, 26) - 1;
        }
        return $letter;
    }

    /**
     * Genera un archivo .xlsx completo en la ruta de destino.
     *
     * @param string $filePath Ruta de archivo destino (.xlsx)
     * @param array $headers Encabezados de columna (ej. ['N° Expediente', 'Solicitante'])
     * @param array $rows Matriz bidimensional con los registros
     * @param string $sheetTitle Nombre de la pestaña de la hoja
     * @param array $colWidths Anchos opcionales de columna
     * @param string $headerBgColor Color hex de fondo para los encabezados (ej. '0B4F8A')
     * @return bool True si se generó con éxito
     */
    public static function create(
        string $filePath,
        array $headers,
        array $rows,
        string $sheetTitle = 'Reporte',
        array $colWidths = [],
        string $headerBgColor = '0B4F8A'
    ): bool {
        $zip = new ZipArchive();
        if ($zip->open($filePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return false;
        }

        // 1. [Content_Types].xml
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">' . "\n"
            . '  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>' . "\n"
            . '  <Default Extension="xml" ContentType="application/xml"/>' . "\n"
            . '  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>' . "\n"
            . '  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>' . "\n"
            . '  <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>' . "\n"
            . '</Types>';
        $zip->addFromString('[Content_Types].xml', $contentTypes);

        // 2. _rels/.rels
        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' . "\n"
            . '  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>' . "\n"
            . '</Relationships>';
        $zip->addFromString('_rels/.rels', $rels);

        // 3. xl/_rels/workbook.xml.rels
        $wbRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' . "\n"
            . '  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>' . "\n"
            . '  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>' . "\n"
            . '</Relationships>';
        $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);

        // 4. xl/workbook.xml
        $safeTitle = self::xmlSafe($sheetTitle);
        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">' . "\n"
            . '  <sheets>' . "\n"
            . '    <sheet name="' . $safeTitle . '" sheetId="1" r:id="rId1"/>' . "\n"
            . '  </sheets>' . "\n"
            . '</workbook>';
        $zip->addFromString('xl/workbook.xml', $workbook);

        // 5. xl/styles.xml
        $cleanColor = strtoupper(ltrim($headerBgColor, '#'));
        if (strlen($cleanColor) === 6) {
            $cleanColor = 'FF' . $cleanColor;
        } elseif (strlen($cleanColor) !== 8) {
            $cleanColor = 'FF0B4F8A';
        }

        $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' . "\n"
            . '  <numFmts count="0"/>' . "\n"
            . '  <fonts count="3">' . "\n"
            . '    <font><sz val="10"/><color rgb="FF1E293B"/><name val="Segoe UI"/><family val="2"/></font>' . "\n"
            . '    <font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Segoe UI"/><family val="2"/></font>' . "\n"
            . '    <font><sz val="10"/><color rgb="FF1E293B"/><name val="Segoe UI"/><family val="2"/></font>' . "\n"
            . '  </fonts>' . "\n"
            . '  <fills count="4">' . "\n"
            . '    <fill><patternFill patternType="none"/></fill>' . "\n"
            . '    <fill><patternFill patternType="gray125"/></fill>' . "\n"
            . '    <fill><patternFill patternType="solid"><fgColor rgb="' . $cleanColor . '"/></patternFill></fill>' . "\n"
            . '    <fill><patternFill patternType="solid"><fgColor rgb="FFF8FAFC"/></patternFill></fill>' . "\n"
            . '  </fills>' . "\n"
            . '  <borders count="2">' . "\n"
            . '    <border><left/><right/><top/><bottom/><diagonal/></border>' . "\n"
            . '    <border>' . "\n"
            . '      <left style="thin"><color rgb="FFE2E8F0"/></left>' . "\n"
            . '      <right style="thin"><color rgb="FFE2E8F0"/></right>' . "\n"
            . '      <top style="thin"><color rgb="FFE2E8F0"/></top>' . "\n"
            . '      <bottom style="thin"><color rgb="FFE2E8F0"/></bottom>' . "\n"
            . '    </border>' . "\n"
            . '  </borders>' . "\n"
            . '  <cellStyleXfs count="1">' . "\n"
            . '    <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>' . "\n"
            . '  </cellStyleXfs>' . "\n"
            . '  <cellXfs count="5">' . "\n"
            . '    <!-- 0: Default -->' . "\n"
            . '    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>' . "\n"
            . '    <!-- 1: Header (Fondo institucional, texto blanco negrita, centrado) -->' . "\n"
            . '    <xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>' . "\n"
            . '    <!-- 2: Celda de datos estándar -->' . "\n"
            . '    <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment vertical="center"/></xf>' . "\n"
            . '    <!-- 3: Celda de datos alterna (zebra) -->' . "\n"
            . '    <xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center"/></xf>' . "\n"
            . '    <!-- 4: Celda de datos centrada -->' . "\n"
            . '    <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>' . "\n"
            . '  </cellXfs>' . "\n"
            . '  <cellStyles count="1">' . "\n"
            . '    <cellStyle name="Normal" xfId="0" builtinId="0"/>' . "\n"
            . '  </cellStyles>' . "\n"
            . '</styleSheet>';
        $zip->addFromString('xl/styles.xml', $styles);

        // 6. xl/worksheets/sheet1.xml
        $colCount = count($headers);
        $colsXml = '<cols>';
        for ($i = 0; $i < $colCount; $i++) {
            $width = $colWidths[$i] ?? max(12, min(50, mb_strlen((string)$headers[$i]) + 6));
            $colNum = $i + 1;
            $colsXml .= '<col min="' . $colNum . '" max="' . $colNum . '" width="' . $width . '" customWidth="1"/>';
        }
        $colsXml .= '</cols>';

        $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' . "\n"
            . '  <sheetViews>' . "\n"
            . '    <sheetView tabSelected="1" workbookViewId="0">' . "\n"
            . '      <pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/>' . "\n"
            . '    </sheetView>' . "\n"
            . '  </sheetViews>' . "\n"
            . '  <sheetFormatPr defaultRowHeight="20"/>' . "\n"
            . $colsXml . "\n"
            . '  <sheetData>' . "\n";

        // Fila 1: Encabezados
        $sheetXml .= '    <row r="1" ht="28" customHeight="1">' . "\n";
        foreach ($headers as $colIdx => $hText) {
            $ref = self::getColLetter($colIdx) . '1';
            $safeVal = self::xmlSafe((string)$hText);
            $sheetXml .= '      <c r="' . $ref . '" t="inlineStr" s="1"><is><t>' . $safeVal . '</t></is></c>' . "\n";
        }
        $sheetXml .= '    </row>' . "\n";

        // Filas de Datos
        $rowNum = 2;
        foreach ($rows as $row) {
            $styleId = ($rowNum % 2 === 0) ? 2 : 3;
            $sheetXml .= '    <row r="' . $rowNum . '" ht="22" customHeight="1">' . "\n";
            $colIdx = 0;
            foreach ($row as $val) {
                $ref = self::getColLetter($colIdx) . $rowNum;
                $strVal = (string)($val ?? '');

                // Folios o enteros puros sin ceros a la izquierda se guardan como número
                if (is_int($val) || (is_numeric($val) && !str_starts_with($strVal, '0') && strlen($strVal) <= 6 && !str_contains($strVal, '.'))) {
                    $sheetXml .= '      <c r="' . $ref . '" s="' . $styleId . '"><v>' . $val . '</v></c>' . "\n";
                } else {
                    $safeVal = self::xmlSafe($strVal);
                    $sheetXml .= '      <c r="' . $ref . '" t="inlineStr" s="' . $styleId . '"><is><t>' . $safeVal . '</t></is></c>' . "\n";
                }
                $colIdx++;
            }
            $sheetXml .= '    </row>' . "\n";
            $rowNum++;
        }

        $lastCol = $colCount > 0 ? self::getColLetter($colCount - 1) : 'A';
        $lastRow = max(1, $rowNum - 1);

        $sheetXml .= '  </sheetData>' . "\n"
            . '  <autoFilter ref="A1:' . $lastCol . $lastRow . '"/>' . "\n"
            . '</worksheet>';
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);

        $zip->close();
        return true;
    }

    /**
     * Genera y transmite el archivo .xlsx para descarga directa al navegador.
     */
    public static function download(
        string $filename,
        array $headers,
        array $rows,
        string $sheetTitle = 'Reporte',
        array $colWidths = [],
        string $headerBgColor = '0B4F8A'
    ): void {
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx_');

        self::create($tempFile, $headers, $rows, $sheetTitle, $colWidths, $headerBgColor);

        if (!str_ends_with(strtolower($filename), '.xlsx')) {
            $filename .= '.xlsx';
        }

        // Limpiar cualquier buffer de salida previo
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($tempFile));
        header('Cache-Control: max-age=0, no-cache, no-store, must-revalidate');
        header('Pragma: public');

        readfile($tempFile);
        @unlink($tempFile);
        exit;
    }
}
