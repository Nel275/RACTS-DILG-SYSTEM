<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Client-requested redesign (Sections 1-2): restructures
     * report_requirements and report_records together, since they're
     * directly coupled by a foreign key and by this same design change.
     *
     * report_requirements moves from "any report, any frequency, one
     * deadline" to a fixed set of monthly/yearly slots per council type
     * (barangay or SK), each independently rename-able/toggle-able by
     * the admin but not freely created with arbitrary frequencies.
     *
     * report_records moves from an auto-computed on_time/late status to
     * a status_color staff set by hand (green/yellow/red), and from a
     * free-text "reporting_period" (e.g. "August 2026") to a structured
     * "period" ("2026-08" for monthly, "2026" for yearly) that the new
     * Barangay/SK Reports grids key off of. edited_by/edited_at are
     * added so a corrected entry is traceable to who changed it and when.
     *
     * IMPORTANT: the old per-report frequency values ("Quarterly",
     * "As Needed", etc.) and per-report deadlines have no equivalent
     * under the new fixed monthly/yearly-slot model, so this migration
     * clears report_records and report_requirements before rebuilding
     * the schema - there is no way to convert old rows into the new
     * shape. The new seeder repopulates report_requirements with the
     * client's 18 canonical slots. If you need any existing submission
     * data, export it from phpMyAdmin (Export tab -> SQL) before
     * running this migration.
     */
    public function up(): void
    {
        DB::table('report_records')->delete();
        DB::table('report_requirements')->delete();

        Schema::table('report_requirements', function (Blueprint $table) {
            $table->dropColumn(['frequency', 'deadline']);
        });

        Schema::table('report_requirements', function (Blueprint $table) {
            $table->enum('council_type', ['barangay', 'sk'])->after('report_name');
            $table->enum('frequency', ['monthly', 'yearly'])->after('council_type');
            // Lets the admin control the left-to-right column order in the
            // Barangay/SK Reports grid without depending on insertion order.
            $table->unsignedInteger('display_order')->default(0)->after('frequency');
            // Soft-remove a slot (e.g. a discontinued report) without
            // deleting the historical report_records rows tied to it.
            $table->boolean('is_active')->default(true)->after('display_order');
        });

        Schema::table('report_records', function (Blueprint $table) {
            $table->dropColumn(['reporting_period', 'status']);
        });

        Schema::table('report_records', function (Blueprint $table) {
            $table->string('period')->after('requirement_id');

            // Set by hand by staff in the grid - never auto-computed.
            // Null means the cell hasn't been graded yet (distinct from
            // "red", which means confirmed not submitted).
            $table->enum('status_color', ['green', 'yellow', 'red'])
                ->nullable()
                ->after('submission_date');

            $table->foreignId('edited_by')->nullable()->after('remarks')
                ->constrained('users')->nullOnDelete();
            $table->dateTime('edited_at')->nullable()->after('edited_by');

            $table->unique(['barangay_id', 'requirement_id', 'period'], 'report_records_unique_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('report_records', function (Blueprint $table) {
            $table->dropUnique('report_records_unique_period');
            $table->dropConstrainedForeignId('edited_by');
            $table->dropColumn(['period', 'status_color', 'edited_at']);
        });

        Schema::table('report_records', function (Blueprint $table) {
            $table->string('reporting_period')->after('requirement_id');
            $table->string('status')->after('submission_date');
        });

        Schema::table('report_requirements', function (Blueprint $table) {
            $table->dropColumn(['council_type', 'frequency', 'display_order', 'is_active']);
        });

        Schema::table('report_requirements', function (Blueprint $table) {
            $table->string('frequency')->after('report_name');
            $table->date('deadline')->after('frequency');
        });
    }
};
