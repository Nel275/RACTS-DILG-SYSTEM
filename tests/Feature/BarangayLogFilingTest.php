<?php

namespace Tests\Feature;

use App\Models\Barangay;
use App\Models\BarangayLog;
use App\Models\BarangayLogDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Follow-up round: replaces the Log Details list's View/Delete actions
 * with Download (files the uploaded report into the barangay's own
 * storage folder) and Close (marks the entry done/archived). Every
 * test here fakes the 'local' disk so nothing touches the real
 * filesystem.
 */
class BarangayLogFilingTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin User',
            'username' => 'admin_'.uniqid(),
            'password' => 'password123',
            'role' => User::ROLE_ADMIN,
        ]);
    }

    private function staff(): User
    {
        return User::create([
            'name' => 'Staff User',
            'username' => 'staff_'.uniqid(),
            'password' => 'password123',
            'role' => User::ROLE_STAFF,
        ]);
    }

    private function entry(): BarangayLogDetail
    {
        $barangay = Barangay::create(['barangay_name' => 'Test Barangay', 'status' => Barangay::STATUS_ACTIVE]);
        $item = BarangayLog::create([
            'item_description' => 'Monthly Accomplishment Report',
            'deadline_date' => '2026-09-30',
            'status' => BarangayLog::STATUS_ACTIVE,
        ]);

        return BarangayLogDetail::create([
            'barangay_id' => $barangay->id,
            'barangay_log_id' => $item->id,
            'item_description' => $item->item_description,
            'deadline_date' => '2026-09-30',
            'status' => BarangayLogDetail::STATUS_PENDING,
        ]);
    }

    public function test_creating_a_barangay_creates_its_storage_folder(): void
    {
        Storage::fake('local');

        $this->actingAs($this->admin())->post('/barangays', [
            'barangay_name' => 'Folder Test',
            'status' => Barangay::STATUS_ACTIVE,
        ]);

        $created = Barangay::where('barangay_name', 'Folder Test')->firstOrFail();

        // Storage::assertExists() checks for a FILE specifically (Laravel's
        // local adapter uses Flysystem's fileExists(), which is false for
        // directories) - is_dir() against the disk's real path is the
        // reliable way to confirm a directory was actually created.
        $this->assertTrue(is_dir(Storage::disk('local')->path($created->storagePath())));
    }

    public function test_marking_submitted_with_a_file_stages_it_without_filing_it(): void
    {
        Storage::fake('local');

        $entry = $this->entry();
        $file = UploadedFile::fake()->create('report.pdf', 100, 'application/pdf');

        $this->actingAs($this->staff())->patch("/barangay-log-details/{$entry->id}/submit", [
            'date_submitted' => '2026-09-28 10:00:00',
            'report_file' => $file,
        ])->assertRedirect(route('barangay-log-details.show', $entry));

        $fresh = $entry->fresh();
        $this->assertTrue($fresh->hasReportFile());
        $this->assertFalse($fresh->isFiled());
        $this->assertSame('report.pdf', $fresh->report_original_name);
        Storage::disk('local')->assertExists($fresh->report_file_path);
    }

    public function test_download_files_the_staged_report_into_the_barangays_folder(): void
    {
        Storage::fake('local');

        $entry = $this->entry();
        $file = UploadedFile::fake()->create('report.pdf', 100, 'application/pdf');

        $this->actingAs($this->staff())->patch("/barangay-log-details/{$entry->id}/submit", [
            'date_submitted' => '2026-09-28 10:00:00',
            'report_file' => $file,
        ]);

        $stagedPath = $entry->fresh()->report_file_path;

        $this->actingAs($this->admin())->patch("/barangay-log-details/{$entry->id}/download")
            ->assertRedirect();

        $fresh = $entry->fresh();
        $this->assertTrue($fresh->isFiled());
        $this->assertStringStartsWith($fresh->barangay->storagePath(), $fresh->report_file_path);
        Storage::disk('local')->assertMissing($stagedPath);
        Storage::disk('local')->assertExists($fresh->report_file_path);
    }

    public function test_download_is_blocked_when_no_file_is_attached(): void
    {
        Storage::fake('local');

        $entry = $this->entry();

        $this->actingAs($this->admin())->patch("/barangay-log-details/{$entry->id}/download")
            ->assertRedirect();

        $this->assertFalse($entry->fresh()->isFiled());
    }

    public function test_download_is_blocked_once_already_filed(): void
    {
        Storage::fake('local');

        $entry = $this->entry();
        $file = UploadedFile::fake()->create('report.pdf', 100, 'application/pdf');

        $this->actingAs($this->staff())->patch("/barangay-log-details/{$entry->id}/submit", [
            'date_submitted' => '2026-09-28 10:00:00',
            'report_file' => $file,
        ]);
        $this->actingAs($this->admin())->patch("/barangay-log-details/{$entry->id}/download");

        $filedPath = $entry->fresh()->report_file_path;

        $this->actingAs($this->admin())->patch("/barangay-log-details/{$entry->id}/download");

        $this->assertSame($filedPath, $entry->fresh()->report_file_path);
    }

    public function test_close_marks_the_entry_closed(): void
    {
        $entry = $this->entry();

        $this->actingAs($this->staff())->patch("/barangay-log-details/{$entry->id}/close")
            ->assertRedirect();

        $this->assertSame(BarangayLogDetail::STATUS_CLOSED, $entry->fresh()->status);
    }

    /**
     * Follow-up round: View File opens whatever's actually attached,
     * regardless of whether it's been filed yet - staged or filed,
     * report_file_path always points at the real current location.
     */
    public function test_view_file_streams_the_staged_report(): void
    {
        Storage::fake('local');

        $entry = $this->entry();
        $file = UploadedFile::fake()->create('report.pdf', 100, 'application/pdf');

        $this->actingAs($this->staff())->patch("/barangay-log-details/{$entry->id}/submit", [
            'date_submitted' => '2026-09-28 10:00:00',
            'report_file' => $file,
        ]);

        $this->actingAs($this->staff())->get("/barangay-log-details/{$entry->id}/view-file")
            ->assertOk();
    }

    /**
     * Follow-up round: regression test for a report where View File
     * downloaded a PDF instead of opening it in the browser's built-in
     * viewer - the Content-Type has to actually say "application/pdf"
     * for that viewer to kick in, and it has to come from the file's
     * extension rather than content-sniffing, since PHP's fileinfo
     * extension (what content-sniffing depends on) isn't guaranteed to
     * be enabled - XAMPP in particular often ships without it.
     */
    public function test_view_file_sets_the_correct_content_type_for_a_pdf(): void
    {
        Storage::fake('local');

        $entry = $this->entry();
        $file = UploadedFile::fake()->create('report.pdf', 100, 'application/pdf');

        $this->actingAs($this->staff())->patch("/barangay-log-details/{$entry->id}/submit", [
            'date_submitted' => '2026-09-28 10:00:00',
            'report_file' => $file,
        ]);

        $this->actingAs($this->staff())->get("/barangay-log-details/{$entry->id}/view-file")
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_view_file_still_works_after_the_report_has_been_filed(): void
    {
        Storage::fake('local');

        $entry = $this->entry();
        $file = UploadedFile::fake()->create('report.pdf', 100, 'application/pdf');

        $this->actingAs($this->staff())->patch("/barangay-log-details/{$entry->id}/submit", [
            'date_submitted' => '2026-09-28 10:00:00',
            'report_file' => $file,
        ]);
        $this->actingAs($this->admin())->patch("/barangay-log-details/{$entry->id}/download");

        $this->actingAs($this->admin())->get("/barangay-log-details/{$entry->id}/view-file")
            ->assertOk();
    }

    public function test_view_file_404s_when_no_file_is_attached(): void
    {
        Storage::fake('local');

        $entry = $this->entry();

        $this->actingAs($this->admin())->get("/barangay-log-details/{$entry->id}/view-file")
            ->assertNotFound();
    }
}
