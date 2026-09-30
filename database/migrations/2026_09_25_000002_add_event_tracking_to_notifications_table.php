<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Follow-up Round 5: adds the four notification event types
     * (period-open, deadline-today, report-late, late-submission) on
     * top of the existing single "late" alert. Dedup for those events
     * used to rely on comparing message TEXT (fragile - see Round 4's
     * council-collision fix), which also has no way to represent
     * "nothing has been submitted yet at all" since there's no
     * report_records row to link report_record_id to in that case.
     *
     * These four columns fix both problems: type + barangay_id +
     * requirement_id + period is a precise, collision-proof key for
     * "has this exact user already been told about this exact event,"
     * and it works even before any report_records row exists. All
     * nullable and purely additive - nothing existing is changed, and
     * older notification rows (from before this round) are left with
     * these columns null, which is fine since nothing new needs to
     * dedup against them (their message text already makes them
     * distinct from anything the new logic would generate).
     */
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('type')->nullable()->after('report_record_id');
            $table->foreignId('barangay_id')->nullable()->after('type')
                ->constrained('barangays')->cascadeOnDelete();
            $table->foreignId('requirement_id')->nullable()->after('barangay_id')
                ->constrained('report_requirements')->cascadeOnDelete();
            $table->string('period')->nullable()->after('requirement_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('barangay_id');
            $table->dropConstrainedForeignId('requirement_id');
            $table->dropColumn(['type', 'period']);
        });
    }
};
