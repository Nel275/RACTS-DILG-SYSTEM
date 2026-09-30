<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Follow-up round: the reference database the client sent for the
     * Barangay page (table `barangay`: ID, BarangayName, Stats,
     * DateProcess, ProcessBy) tracks who last touched a barangay record
     * and when. `id`, `barangay_name`, and `status` already exist here
     * and are exactly those first three columns (renamed to Laravel's
     * snake_case convention back in the original build) - this only
     * adds the two that are missing: `date_process` and `processed_by`.
     *
     * Deliberately an ADD, not a recreate: `barangays` already has
     * foreign keys pointing at it from `users`, `report_records`, and
     * `notifications` (barangay_id) - recreating the table to match the
     * reference schema literally would have broken every one of those
     * and every other page built on this table so far. Both new columns
     * are nullable so existing rows don't need backfilling.
     */
    public function up(): void
    {
        Schema::table('barangays', function (Blueprint $table) {
            $table->date('date_process')->nullable()->after('status');
            $table->string('processed_by')->nullable()->after('date_process');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barangays', function (Blueprint $table) {
            $table->dropColumn(['date_process', 'processed_by']);
        });
    }
};
