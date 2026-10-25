<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Automatic My Reports entries + DILG review.
     *
     * barangay_logs (the Report Filing catalog) becomes the official
     * requirement: a frequency (once/monthly/annual), the deadline rule
     * for recurring ones, an effective-from date and a mandatory flag.
     * deadline_date is now only used by one-time items, so it may be null.
     *
     * barangay_log_details gets the reporting period ("2026-10" monthly,
     * "2026" annual, null for one-time/manual entries) - unique per
     * requirement + barangay + period, so the generator can never create
     * the same entry twice - plus the DILG review fields.
     *
     * notifications gets a link to the entry it is about, the dedup key
     * for the My Reports reminders.
     */
    public function up(): void
    {
        Schema::table('barangay_logs', function (Blueprint $table) {
            $table->date('deadline_date')->nullable()->change();
            $table->string('frequency')->default('once')->after('council_type');
            $table->unsignedTinyInteger('deadline_day')->nullable()->after('deadline_date');
            $table->unsignedTinyInteger('deadline_month')->nullable()->after('deadline_day');
            $table->date('effective_from')->nullable()->after('deadline_month');
            $table->boolean('is_mandatory')->default(true)->after('effective_from');
        });

        Schema::table('barangay_log_details', function (Blueprint $table) {
            $table->string('period', 7)->nullable()->after('item_description');
            $table->unsignedSmallInteger('submission_count')->default(0)->after('status');
            $table->text('review_remarks')->nullable()->after('submission_count');
            $table->string('reviewed_by')->nullable()->after('review_remarks');
            $table->dateTime('reviewed_at')->nullable()->after('reviewed_by');
            $table->unique(['barangay_log_id', 'barangay_id', 'period'], 'barangay_log_details_period_unique');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->foreignId('barangay_log_detail_id')->nullable()->after('requirement_id')
                ->constrained('barangay_log_details')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('barangay_log_detail_id');
        });

        Schema::table('barangay_log_details', function (Blueprint $table) {
            $table->dropUnique('barangay_log_details_period_unique');
            $table->dropColumn(['period', 'submission_count', 'review_remarks', 'reviewed_by', 'reviewed_at']);
        });

        Schema::table('barangay_logs', function (Blueprint $table) {
            $table->dropColumn(['frequency', 'deadline_day', 'deadline_month', 'effective_from', 'is_mandatory']);
        });
    }
};
