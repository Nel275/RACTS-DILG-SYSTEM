<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Barangay;
use App\Models\BarangayLog;
use App\Models\BarangayLogDetail;
use App\Models\ReportRequirement;
use App\Models\ReportStamp;
use App\Models\User;
use FPDF;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use ZipArchive;

/**
 * Digital stamping: admin-only stamping of reports submitted to DILG,
 * PDF/DOCX output, history across resubmissions, and access limits.
 */
class DigitalStampingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    private function barangay(string $name = 'Centro East'): Barangay
    {
        return Barangay::create(['barangay_name' => $name, 'status' => Barangay::STATUS_ACTIVE]);
    }

    private function user(string $role, ?Barangay $barangay = null): User
    {
        return User::create([
            'name' => ucfirst($role).' User',
            'username' => $role.'_'.substr(uniqid(), -6),
            'password' => 'password123',
            'role' => $role,
            'barangay_id' => $barangay?->id,
        ]);
    }

    private function entry(Barangay $barangay): BarangayLogDetail
    {
        $log = BarangayLog::create([
            'item_description' => 'Monthly Report',
            'council_type' => ReportRequirement::COUNCIL_BARANGAY,
            'deadline_date' => now()->addDays(10)->toDateString(),
            'status' => BarangayLog::STATUS_ACTIVE,
        ]);

        return BarangayLogDetail::create([
            'barangay_log_id' => $log->id,
            'barangay_id' => $barangay->id,
            'item_description' => $log->item_description,
            'deadline_date' => $log->deadline_date,
            'status' => BarangayLogDetail::STATUS_PENDING,
        ]);
    }

    private function pdfBytes(string $text = 'Barangay report body'): string
    {
        $pdf = new FPDF;
        $pdf->AddPage();
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->Cell(0, 10, $text);
        $pdf->AddPage();

        return $pdf->Output('S');
    }

    private function docxBytes(): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'docx');
        $zip = new ZipArchive;
        $zip->open($tmp, ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/></Types>');
        $zip->addFromString('_rels/.rels', '<?xml version="1.0"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/></Relationships>');
        $zip->addFromString('word/document.xml', '<?xml version="1.0"?><w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:body><w:p><w:r><w:t>Original body</w:t></w:r></w:p></w:body></w:document>');
        $zip->close();
        $bytes = file_get_contents($tmp);
        unlink($tmp);

        return $bytes;
    }

    /** Submits through the model directly (bypasses upload MIME sniffing). */
    private function submitted(Barangay $barangay, string $name, string $bytes): BarangayLogDetail
    {
        $entry = $this->entry($barangay);
        Storage::disk('local')->put('incoming/'.$name, $bytes);
        $entry->markSubmitted(now(), 'Test', 'incoming/'.$name, $name);

        return $entry->refresh();
    }

    /** All PDF stream contents, inflated, so drawn text can be searched. */
    private function pdfText(string $bytes): string
    {
        preg_match_all('/stream\r?\n(.*?)\r?\nendstream/s', $bytes, $m);

        return implode("\n", array_map(fn ($s) => @gzuncompress($s) ?: $s, $m[1]));
    }

    public function test_unsubmitted_report_is_not_stamped_and_cannot_be_stamped(): void
    {
        $barangay = $this->barangay();
        $entry = $this->entry($barangay);

        $this->actingAs($this->user(User::ROLE_BARANGAY, $barangay))
            ->get('/my-reports')->assertOk()->assertSee('Not submitted')->assertDontSee('Stamped copy');

        $this->actingAs($this->user(User::ROLE_ADMIN))
            ->post("/digital-stamping/{$entry->id}/stamp")
            ->assertSessionHas('error');

        $this->assertSame(0, ReportStamp::count());
    }

    public function test_barangay_submission_makes_report_eligible_but_not_stamped(): void
    {
        $barangay = $this->barangay();
        $entry = $this->entry($barangay);
        $user = $this->user(User::ROLE_BARANGAY, $barangay);

        $this->actingAs($user)->patch("/my-reports/{$entry->id}/submit", [
            'report_file' => UploadedFile::fake()->createWithContent('report.pdf', $this->pdfBytes()),
        ])->assertSessionHasNoErrors();

        $entry->refresh();
        $this->assertTrue($entry->isSubmittedToDilg());
        $this->assertNotNull($entry->report_file_hash);
        $this->assertSame('Awaiting stamp', $entry->stampingStatusLabel());

        $this->actingAs($user)->get('/my-reports')->assertSee('Awaiting stamp')->assertDontSee('Stamped copy');
        $this->actingAs($user)->get("/my-reports/{$entry->id}/stamped")->assertNotFound();

        $this->actingAs($this->user(User::ROLE_ADMIN))
            ->get('/digital-stamping')->assertOk()->assertSee($entry->referenceNumber())->assertSee('Stamp Report');
    }

    public function test_admin_stamps_pdf_and_downloaded_file_contains_the_stamp(): void
    {
        $barangay = $this->barangay();
        $entry = $this->submitted($barangay, 'report.pdf', $original = $this->pdfBytes());
        $admin = $this->user(User::ROLE_ADMIN);

        $this->actingAs($admin)->post("/digital-stamping/{$entry->id}/stamp")
            ->assertRedirect(route('digital-stamping.show', $entry))
            ->assertSessionHas('success');

        $stamp = ReportStamp::sole();
        $this->assertSame($admin->id, $stamp->user_id);
        $this->assertSame(hash('sha256', $original), $stamp->original_file_hash);
        // Original upload untouched.
        $this->assertSame($original, Storage::disk('local')->get($entry->refresh()->report_file_path));

        $download = $this->actingAs($this->user(User::ROLE_BARANGAY, $barangay))->get("/my-reports/{$entry->id}/stamped");
        $download->assertOk();
        $text = $this->pdfText($download->streamedContent());
        $this->assertStringContainsString('RECEIVED BY DILG', $text);
        $this->assertStringContainsString($entry->referenceNumber(), $text);
        $this->assertStringContainsString($stamp->verification_code, $text);
        $this->assertStringContainsString('Barangay report body', $text);
    }

    public function test_docx_stamping_keeps_original_and_outputs_stamped_docx(): void
    {
        $entry = $this->submitted($this->barangay(), 'report.docx', $original = $this->docxBytes());

        $this->actingAs($this->user(User::ROLE_ADMIN))->post("/digital-stamping/{$entry->id}/stamp")->assertSessionHas('success');

        $stamp = ReportStamp::sole();
        $this->assertStringEndsWith('.docx', $stamp->stamped_file_path);
        $this->assertSame('report (stamped).docx', $stamp->stamped_original_name);
        $this->assertSame($original, Storage::disk('local')->get($entry->refresh()->report_file_path));

        $zip = new ZipArchive;
        $zip->open(Storage::disk('local')->path($stamp->stamped_file_path));
        $xml = $zip->getFromName('word/document.xml');
        $this->assertStringContainsString($stamp->verification_code, $xml);
        $this->assertStringContainsString('Original body', $xml);
        $this->assertLessThan(strpos($xml, 'Original body'), strpos($xml, 'RECEIVED BY DILG'));
    }

    public function test_barangay_and_staff_cannot_use_stamping_routes(): void
    {
        $barangay = $this->barangay();
        $entry = $this->submitted($barangay, 'report.pdf', $this->pdfBytes());
        $this->actingAs($this->user(User::ROLE_ADMIN))->post("/digital-stamping/{$entry->id}/stamp");
        $stamp = ReportStamp::sole();

        foreach ([$this->user(User::ROLE_BARANGAY, $barangay), $this->user(User::ROLE_STAFF)] as $user) {
            $this->actingAs($user)->post("/digital-stamping/{$entry->id}/stamp")->assertForbidden();
            $this->actingAs($user)->get('/digital-stamping')->assertForbidden();
            $this->actingAs($user)->get("/digital-stamping/{$entry->id}")->assertForbidden();
            $this->actingAs($user)->get("/digital-stamping/stamps/{$stamp->id}/download")->assertForbidden();
        }

        auth()->logout();
        $this->post("/digital-stamping/{$entry->id}/stamp")->assertRedirect(route('login'));
        $this->assertSame(1, ReportStamp::count());
    }

    public function test_repeated_stamp_requests_create_one_stamp(): void
    {
        $entry = $this->submitted($this->barangay(), 'report.pdf', $this->pdfBytes());
        $admin = $this->user(User::ROLE_ADMIN);

        $this->actingAs($admin)->post("/digital-stamping/{$entry->id}/stamp")->assertSessionHas('success');
        $this->actingAs($admin)->post("/digital-stamping/{$entry->id}/stamp")->assertSessionHas('error');

        $this->assertSame(1, ReportStamp::count());
        $this->assertCount(1, Storage::disk('local')->allFiles('stamped-reports'));
    }

    public function test_failed_stamping_leaves_report_unstamped_and_is_audited(): void
    {
        $entry = $this->submitted($this->barangay(), 'broken.pdf', 'this is not a pdf');
        $admin = $this->user(User::ROLE_ADMIN);

        $this->actingAs($admin)->post("/digital-stamping/{$entry->id}/stamp")->assertSessionHas('error');

        $this->assertSame(0, ReportStamp::count());
        $this->assertSame([], Storage::disk('local')->allFiles('stamped-reports'));
        $this->assertSame('Awaiting stamp', $entry->refresh()->stampingStatusLabel());
        $this->assertTrue(AuditLog::where('user_id', $admin->id)->where('action', 'like', 'Stamping FAILED for report '.$entry->referenceNumber().'%')->exists());
    }

    public function test_unsupported_file_type_cannot_be_stamped(): void
    {
        $entry = $this->submitted($this->barangay(), 'photo.png', 'png-bytes');

        $this->actingAs($this->user(User::ROLE_ADMIN))->post("/digital-stamping/{$entry->id}/stamp")
            ->assertSessionHas('error', 'This file type cannot be stamped. Only PDF and DOCX reports are supported.');

        $this->assertSame(0, ReportStamp::count());
    }

    public function test_successful_stamp_is_audited_with_admin_report_and_hashes(): void
    {
        $entry = $this->submitted($this->barangay(), 'report.pdf', $this->pdfBytes());
        $admin = $this->user(User::ROLE_ADMIN);

        $this->actingAs($admin)->post("/digital-stamping/{$entry->id}/stamp");

        $stamp = ReportStamp::sole();
        $log = AuditLog::where('action', 'like', 'Stamped report%')->sole();
        $this->assertSame($admin->id, $log->user_id);
        $this->assertNotNull($log->log_date);
        $this->assertStringContainsString($entry->referenceNumber(), $log->action);
        $this->assertStringContainsString($stamp->verification_code, $log->action);
        $this->assertStringContainsString($stamp->stamped_file_hash, $log->action);
    }

    public function test_resubmission_needs_a_new_stamp_and_keeps_history(): void
    {
        $barangay = $this->barangay();
        $entry = $this->submitted($barangay, 'report.pdf', $this->pdfBytes('First version'));
        $admin = $this->user(User::ROLE_ADMIN);
        $this->actingAs($admin)->post("/digital-stamping/{$entry->id}/stamp");
        $first = ReportStamp::sole();

        // DILG records an updated submission with a new file.
        $this->actingAs($admin)->patch("/barangay-log-details/{$entry->id}/submit", [
            'date_submitted' => now()->format('Y-m-d H:i:s'),
            'report_file' => UploadedFile::fake()->createWithContent('report-v2.pdf', $this->pdfBytes('Second version')),
        ])->assertSessionHasNoErrors();

        $entry->refresh()->load('stamps');
        $this->assertSame('Resubmitted - awaiting new stamp', $entry->stampingStatusLabel());
        $this->actingAs($this->user(User::ROLE_BARANGAY, $barangay))->get("/my-reports/{$entry->id}/stamped")->assertNotFound();

        $this->actingAs($admin)->post("/digital-stamping/{$entry->id}/stamp")->assertSessionHas('success');
        $this->assertSame(2, ReportStamp::count());

        $this->actingAs($admin)->get('/verify-stamp?code='.$first->verification_code)->assertSee('earlier submission');
        $this->actingAs($admin)->get("/digital-stamping/{$entry->id}")->assertSee('Earlier submission')->assertSee('Current file');
    }

    public function test_barangay_cannot_download_another_barangays_stamped_report(): void
    {
        $entry = $this->submitted($this->barangay('Centro East'), 'report.pdf', $this->pdfBytes());
        $this->actingAs($this->user(User::ROLE_ADMIN))->post("/digital-stamping/{$entry->id}/stamp");

        $this->actingAs($this->user(User::ROLE_BARANGAY, $this->barangay('Centro West')))
            ->get("/my-reports/{$entry->id}/stamped")->assertNotFound();
    }

    public function test_verification_page_uses_server_records(): void
    {
        $entry = $this->submitted($this->barangay(), 'report.pdf', $this->pdfBytes());
        $this->actingAs($admin = $this->user(User::ROLE_ADMIN))->post("/digital-stamping/{$entry->id}/stamp");
        $code = ReportStamp::sole()->verification_code;

        $this->actingAs($this->user(User::ROLE_STAFF))->get('/verify-stamp?code='.$code)
            ->assertOk()->assertSee('Valid stamp.')->assertSee($entry->referenceNumber())->assertDontSee('report.pdf');
        $this->get('/verify-stamp?code=NOTAREALCODE')->assertSee('Invalid code.');
    }

    public function test_stamp_records_cannot_be_modified(): void
    {
        $entry = $this->submitted($this->barangay(), 'report.pdf', $this->pdfBytes());
        $this->actingAs($this->user(User::ROLE_ADMIN))->post("/digital-stamping/{$entry->id}/stamp");

        $this->expectException(\LogicException::class);
        ReportStamp::sole()->update(['stamped_by_name' => 'Forged']);
    }
}
