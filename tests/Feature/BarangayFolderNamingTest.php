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
 * Follow-up round: a barangay's storage folder is now named after the
 * barangay itself (storage/app/barangays/Malvar) instead of its id
 * (storage/app/barangays/5). Every test here fakes the 'local' disk so
 * nothing touches the real filesystem.
 */
class BarangayFolderNamingTest extends TestCase
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

    public function test_creating_a_barangay_names_its_folder_after_the_barangay(): void
    {
        Storage::fake('local');

        $this->actingAs($this->admin())->post('/barangays', [
            'barangay_name' => 'Malvar',
            'status' => Barangay::STATUS_ACTIVE,
        ]);

        $created = Barangay::where('barangay_name', 'Malvar')->firstOrFail();

        $this->assertSame('barangays/Malvar', $created->storagePath());
        $this->assertTrue(is_dir(Storage::disk('local')->path('barangays/Malvar')));
    }

    public function test_renaming_a_barangay_renames_its_folder(): void
    {
        Storage::fake('local');

        $barangay = Barangay::create(['barangay_name' => 'Old Name', 'status' => Barangay::STATUS_ACTIVE]);
        $barangay->ensureFolder();
        Storage::disk('local')->put('barangays/Old Name/keepme.txt', 'hello');

        $this->actingAs($this->admin())->put("/barangays/{$barangay->id}", [
            'barangay_name' => 'New Name',
            'status' => Barangay::STATUS_ACTIVE,
        ]);

        $this->assertFalse(is_dir(Storage::disk('local')->path('barangays/Old Name')));
        $this->assertTrue(is_dir(Storage::disk('local')->path('barangays/New Name')));
        Storage::disk('local')->assertExists('barangays/New Name/keepme.txt');
    }

    public function test_renaming_a_barangay_updates_already_filed_report_paths(): void
    {
        Storage::fake('local');

        $barangay = Barangay::create(['barangay_name' => 'Cotcot', 'status' => Barangay::STATUS_ACTIVE]);
        $item = BarangayLog::create([
            'item_description' => 'Monthly Report',
            'deadline_date' => '2026-09-30',
            'status' => BarangayLog::STATUS_ACTIVE,
        ]);
        $entry = BarangayLogDetail::create([
            'barangay_id' => $barangay->id,
            'barangay_log_id' => $item->id,
            'item_description' => $item->item_description,
            'deadline_date' => '2026-09-30',
            'status' => BarangayLogDetail::STATUS_PENDING,
        ]);

        $file = UploadedFile::fake()->create('report.pdf', 100, 'application/pdf');
        $entry->markSubmitted(now(), 'Admin User', $file->store('incoming-reports/'.$entry->id, 'local'), 'report.pdf');
        $entry->fileIntoBarangayFolder('Admin User');

        $this->assertStringStartsWith('barangays/Cotcot/', $entry->fresh()->report_file_path);

        $this->actingAs($this->admin())->put("/barangays/{$barangay->id}", [
            'barangay_name' => 'Cotcot Norte',
            'status' => Barangay::STATUS_ACTIVE,
        ]);

        $fresh = $entry->fresh();
        $this->assertStringStartsWith('barangays/Cotcot Norte/', $fresh->report_file_path);
        Storage::disk('local')->assertExists($fresh->report_file_path);
    }

    public function test_duplicate_barangay_name_is_rejected(): void
    {
        Barangay::create(['barangay_name' => 'Villasis', 'status' => Barangay::STATUS_ACTIVE]);

        $this->actingAs($this->admin())->post('/barangays', [
            'barangay_name' => 'Villasis',
            'status' => Barangay::STATUS_ACTIVE,
        ])->assertSessionHasErrors('barangay_name');

        $this->assertSame(1, Barangay::where('barangay_name', 'Villasis')->count());
    }

    public function test_legacy_id_based_folder_migrates_to_the_name_based_path_automatically(): void
    {
        Storage::fake('local');

        // Simulate a barangay created before this round, whose folder
        // still sits under the old barangays/{id} scheme.
        $barangay = Barangay::create(['barangay_name' => 'Centro East', 'status' => Barangay::STATUS_ACTIVE]);
        Storage::disk('local')->makeDirectory('barangays/'.$barangay->id);
        Storage::disk('local')->put('barangays/'.$barangay->id.'/old-report.pdf', 'hello');

        $barangay->ensureFolder();

        $this->assertFalse(is_dir(Storage::disk('local')->path('barangays/'.$barangay->id)));
        Storage::disk('local')->assertExists('barangays/Centro East/old-report.pdf');
    }
}
