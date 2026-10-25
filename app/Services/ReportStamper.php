<?php

namespace App\Services;

use App\Models\BarangayLogDetail;
use App\Models\ReportStamp;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\PdfParserException;
use Throwable;
use ZipArchive;

/**
 * Applies the DILG Administrator's digital stamp to a submitted report.
 * The original upload is never touched: the stamped copy is written to
 * stamped-reports/{entry id}/ on the private local disk and recorded as
 * a ReportStamp only after the file has been generated and re-read
 * successfully. PDFs get the stamp on page 1 (top-right); DOCX files
 * get a bordered stamp block at the top of the document body.
 *
 * Callers must already have checked the user is an administrator (the
 * routes are role:admin) - this re-checks anyway, so it is safe to call
 * from anywhere.
 */
class ReportStamper
{
    /**
     * Returns the new stamp, or the existing one when this exact file is
     * already stamped ($stamp->wasRecentlyCreated tells them apart).
     *
     * @throws StampingException with a message safe to show the admin.
     */
    public function stamp(BarangayLogDetail $entry, User $admin): ReportStamp
    {
        if (! $admin->isAdmin() || $admin->is_active === false) {
            throw new StampingException('Only an active DILG Administrator can stamp reports.');
        }

        return DB::transaction(function () use ($entry, $admin) {
            // Row lock: a double-click or two admins at once wait here, then
            // the second one finds the first one's stamp below.
            $entry = BarangayLogDetail::whereKey($entry->id)->lockForUpdate()->firstOrFail();

            if (! $entry->isSubmittedToDilg() || ! $entry->hasReportFile()) {
                throw new StampingException('Only reports submitted to DILG with an attached file can be stamped.');
            }
            if (! $entry->isStampable()) {
                throw new StampingException('This file type cannot be stamped. Only PDF and DOCX reports are supported.');
            }

            $disk = Storage::disk('local');
            if (! $disk->exists($entry->report_file_path)) {
                throw new StampingException('The report file is missing from storage.');
            }

            // Entries submitted before stamping existed have no hash yet.
            $hash = hash_file('sha256', $disk->path($entry->report_file_path));
            if ($entry->report_file_hash !== $hash) {
                $entry->update(['report_file_hash' => $hash]);
            }

            if ($existing = $entry->stamps()->where('original_file_hash', $hash)->first()) {
                return $existing;
            }

            $code = Str::upper(Str::random(12));
            $extension = strtolower(pathinfo($entry->report_original_name, PATHINFO_EXTENSION));
            $path = 'stamped-reports/'.$entry->id.'/'.$code.'.'.$extension;
            $disk->makeDirectory(dirname($path));

            $lines = [
                'office' => config('racts.stamp.office'),
                'label' => config('racts.stamp.label'),
                'ref' => 'Ref: '.$entry->referenceNumber(),
                'date' => now()->format('M d, Y g:i A'),
                'by' => 'By: '.$admin->name,
                'code' => 'Verify: '.$code,
            ];

            try {
                $extension === 'pdf'
                    ? $this->stampPdf($disk->path($entry->report_file_path), $disk->path($path), $lines)
                    : $this->stampDocx($disk->path($entry->report_file_path), $disk->path($path), $lines, $code);

                return ReportStamp::create([
                    'barangay_log_detail_id' => $entry->id,
                    'user_id' => $admin->id,
                    'stamped_by_name' => $admin->name,
                    'verification_code' => $code,
                    'original_file_hash' => $hash,
                    'stamped_file_path' => $path,
                    'stamped_original_name' => pathinfo($entry->report_original_name, PATHINFO_FILENAME).' (stamped).'.$extension,
                    'stamped_file_hash' => hash_file('sha256', $disk->path($path)),
                ]);
            } catch (Throwable $e) {
                $disk->delete($path);

                throw $e instanceof StampingException ? $e : new StampingException(
                    $e instanceof PdfParserException
                        ? 'This PDF could not be read by the stamping engine (it may use an unsupported compression). Ask the barangay to re-save it with "Print to PDF" and resubmit.'
                        : 'The stamped document could not be generated.',
                    previous: $e
                );
            }
        });
    }

    /** @param  array<string, string>  $lines */
    private function stampPdf(string $source, string $destination, array $lines): void
    {
        $pdf = new Fpdi;
        $pages = $pdf->setSourceFile($source);

        for ($i = 1; $i <= $pages; $i++) {
            $template = $pdf->importPage($i);
            $size = $pdf->getTemplateSize($template);
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($template);

            if ($i === 1) {
                $this->drawPdfStamp($pdf, $size['width'], $lines);
            }
        }

        $pdf->Output('F', $destination);

        // Validate: the output must parse again with every page intact.
        if ((new Fpdi)->setSourceFile($destination) !== $pages) {
            throw new RuntimeException('Stamped PDF page count mismatch.');
        }
    }

    /** @param  array<string, string>  $lines */
    private function drawPdfStamp(Fpdi $pdf, float $pageWidth, array $lines): void
    {
        $w = 70;
        $h = 34;
        $x = $pageWidth - $w - 8;
        $y = 8;
        $text = fn (string $s) => iconv('UTF-8', 'windows-1252//TRANSLIT', $s);

        $pdf->SetAutoPageBreak(false);
        $pdf->SetDrawColor(31, 58, 147);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetLineWidth(0.6);
        $pdf->Rect($x, $y, $w, $h, 'DF');

        $textX = $x + 2;
        $logo = config('racts.stamp.logo');
        if ($logo && is_file(public_path($logo))) {
            $pdf->Image(public_path($logo), $x + 2, $y + 2, 12, 12);
            $textX = $x + 16;
        }

        $pdf->SetTextColor(31, 58, 147);
        $pdf->SetXY($textX, $y + 2);
        $pdf->SetFont('Helvetica', 'B', 7);
        $pdf->MultiCell($x + $w - 2 - $textX, 3.2, $text($lines['office']));
        $pdf->SetXY($textX, $pdf->GetY() + 0.5);
        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->MultiCell($x + $w - 2 - $textX, 4.5, $text($lines['label']));

        $pdf->SetFont('Helvetica', '', 7);
        $pdf->SetXY($x + 2, $y + 17);
        foreach (['ref', 'date', 'by', 'code'] as $key) {
            $pdf->Cell($w - 4, 3.6, $text($lines[$key]), 0, 2);
        }
    }

    /** @param  array<string, string>  $lines */
    private function stampDocx(string $source, string $destination, array $lines, string $code): void
    {
        if (! copy($source, $destination)) {
            throw new RuntimeException('Could not copy the DOCX for stamping.');
        }

        $zip = new ZipArchive;
        if ($zip->open($destination) !== true || ($xml = $zip->getFromName('word/document.xml')) === false) {
            throw new StampingException('This DOCX file could not be read. Ask the barangay to re-save it in Word and resubmit.');
        }

        $runs = [];
        foreach ($lines as $key => $line) {
            $bold = in_array($key, ['office', 'label'], true) ? '<w:b/>' : '';
            $size = $key === 'label' ? 24 : 16;
            $runs[] = '<w:r><w:rPr>'.$bold.'<w:color w:val="1F3A93"/><w:sz w:val="'.$size.'"/></w:rPr>'
                .'<w:t xml:space="preserve">'.htmlspecialchars($line, ENT_XML1).'</w:t></w:r>';
        }
        $border = '<w:%s w:val="single" w:sz="12" w:space="4" w:color="1F3A93"/>';
        $stamp = '<w:p><w:pPr><w:pBdr>'
            .sprintf($border, 'top').sprintf($border, 'left').sprintf($border, 'bottom').sprintf($border, 'right')
            .'</w:pBdr><w:ind w:left="5400"/></w:pPr>'
            .implode('<w:r><w:br/></w:r>', $runs).'</w:p>';

        // Insert right after the opening <w:body> tag (it may carry attributes).
        $stamped = preg_replace('/<w:body(\s[^>]*)?>/', '$0'.str_replace(['\\', '$'], ['\\\\', '\\$'], $stamp), $xml, 1, $count);
        if ($count !== 1) {
            $zip->close();
            throw new StampingException('This DOCX file has no document body to stamp.');
        }

        $zip->addFromString('word/document.xml', $stamped);
        $zip->close();

        // Validate: the saved archive must contain the stamp.
        $check = new ZipArchive;
        if ($check->open($destination) !== true || ! str_contains((string) $check->getFromName('word/document.xml'), $code)) {
            throw new RuntimeException('Stamped DOCX failed validation.');
        }
        $check->close();
    }
}
