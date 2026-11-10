<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class XlsxExportService
{
    /**
     * Sanitasi formula injection pada sel spreadsheet (AC-17-2).
     */
    public function sanitizeCell(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        $str = (string) $value;
        if ($str === '') {
            return '';
        }

        $karakterBerbahaya = ['=', '+', '-', '@', "\t", "\r"];
        if (in_array($str[0], $karakterBerbahaya, true)) {
            return "'".$str;
        }

        return $str;
    }

    /**
     * Menghasilkan berkas .xlsx resmi dengan styling All Borders, Header Color yang dapat disesuaikan, dan Auto-Width kolom.
     *
     * @param  array<int, string>  $headers
     * @param  array<int, array<int, mixed>>  $rows
     */
    public function downloadXlsx(
        string $filename,
        string $sheetTitle,
        array $headers,
        array $rows,
        string $headerColor = 'FF1E3A8A'
    ): BinaryFileResponse {
        $tempPath = tempnam(sys_get_temp_dir(), 'magkom_xlsx_');
        if ($tempPath === false) {
            throw new RuntimeException('Gagal membuat berkas sementara untuk ekspor XLSX.');
        }

        $zip = new ZipArchive;
        if ($zip->open($tempPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException("Tidak dapat membuat berkas sementara XLSX di $tempPath");
        }

        // 1. [Content_Types].xml
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            .'</Types>';
        $zip->addFromString('[Content_Types].xml', $contentTypes);

        // 2. _rels/.rels
        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'</Relationships>';
        $zip->addFromString('_rels/.rels', $rels);

        // 3. xl/_rels/workbook.xml.rels
        $wbRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            .'</Relationships>';
        $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);

        // 4. xl/workbook.xml
        $safeSheetTitle = htmlspecialchars(mb_substr($sheetTitle, 0, 31), ENT_XML1, 'UTF-8');
        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<sheets>'
            .'<sheet name="'.$safeSheetTitle.'" sheetId="1" r:id="rId1"/>'
            .'</sheets>'
            .'</workbook>';
        $zip->addFromString('xl/workbook.xml', $workbook);

        // 5. xl/styles.xml
        $safeColor = htmlspecialchars(strtoupper($headerColor), ENT_XML1, 'UTF-8');
        $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<fonts count="2">'
            .'<font><sz val="11"/><color theme="1"/><name val="Calibri"/><family val="2"/></font>'
            .'<font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/><family val="2"/></font>'
            .'</fonts>'
            .'<fills count="3">'
            .'<fill><patternFill patternType="none"/></fill>'
            .'<fill><patternFill patternType="gray125"/></fill>'
            .'<fill><patternFill patternType="solid"><fgColor rgb="'.$safeColor.'"/></patternFill></fill>'
            .'</fills>'
            .'<borders count="2">'
            .'<border><left/><right/><top/><bottom/><diagonal/></border>'
            .'<border>'
            .'<left style="thin"><color rgb="FFD1D5DB"/></left>'
            .'<right style="thin"><color rgb="FFD1D5DB"/></right>'
            .'<top style="thin"><color rgb="FFD1D5DB"/></top>'
            .'<bottom style="thin"><color rgb="FFD1D5DB"/></bottom>'
            .'</border>'
            .'</borders>'
            .'<cellStyleXfs count="1">'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>'
            .'</cellStyleXfs>'
            .'<cellXfs count="3">'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            .'<xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1">'
            .'<alignment horizontal="center" vertical="center" wrapText="1"/>'
            .'</xf>'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1">'
            .'<alignment vertical="center"/>'
            .'</xf>'
            .'</cellXfs>'
            .'</styleSheet>';
        $zip->addFromString('xl/styles.xml', $styles);

        // 6. xl/worksheets/sheet1.xml
        $colWidths = [];
        foreach ($headers as $cIdx => $h) {
            $colWidths[$cIdx] = mb_strlen((string) $h);
        }
        foreach ($rows as $row) {
            foreach ($row as $cIdx => $val) {
                $len = mb_strlen((string) ($val ?? ''));
                if (! isset($colWidths[$cIdx]) || $len > $colWidths[$cIdx]) {
                    $colWidths[$cIdx] = $len;
                }
            }
        }

        $sheet = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<cols>';
        foreach ($colWidths as $cIdx => $w) {
            $colNum = $cIdx + 1;
            // Pastikan lebar kolom cukup lega (min 13, max 50) sehingga tanggal tidak muncul #####
            $width = max(13, min(50, $w + 5));
            $sheet .= '<col min="'.$colNum.'" max="'.$colNum.'" width="'.$width.'" customWidth="1"/>';
        }
        $sheet .= '</cols><sheetData>';

        $colLetter = function (int $index): string {
            $letter = '';
            while ($index >= 0) {
                $letter = chr($index % 26 + 65).$letter;
                $index = intdiv($index, 26) - 1;
            }

            return $letter;
        };

        // Header Row (tinggi 28pt, style 1)
        $sheet .= '<row r="1" ht="28" customHeight="1">';
        foreach ($headers as $cIdx => $headerText) {
            $cellRef = $colLetter($cIdx).'1';
            $escaped = htmlspecialchars((string) $headerText, ENT_XML1, 'UTF-8');
            $sheet .= '<c r="'.$cellRef.'" t="inlineStr" s="1"><is><t>'.$escaped.'</t></is></c>';
        }
        $sheet .= '</row>';

        // Data Rows (tinggi 22pt, style 2)
        $rNum = 2;
        foreach ($rows as $row) {
            $sheet .= '<row r="'.$rNum.'" ht="22" customHeight="1">';
            foreach ($row as $cIdx => $val) {
                $cellRef = $colLetter($cIdx).$rNum;
                $sanitized = $this->sanitizeCell($val);
                $escaped = htmlspecialchars($sanitized, ENT_XML1, 'UTF-8');
                $sheet .= '<c r="'.$cellRef.'" t="inlineStr" s="2"><is><t>'.$escaped.'</t></is></c>';
            }
            $sheet .= '</row>';
            $rNum++;
        }

        $sheet .= '</sheetData></worksheet>';
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheet);

        $zip->close();

        return response()->download(
            $tempPath,
            $filename,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ]
        )->deleteFileAfterSend(true);
    }
}
