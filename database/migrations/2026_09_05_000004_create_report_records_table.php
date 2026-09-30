<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('report_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('barangay_id')
                ->constrained('barangays')
                ->restrictOnDelete();

            $table->foreignId('requirement_id')
                ->constrained('report_requirements')
                ->restrictOnDelete();

            // e.g. "August 2026" - which reporting period this submission
            // covers. Not in the manuscript's Data Dictionary, added because
            // otherwise there'd be no way to tell one month's submission
            // apart from another for the same barangay/report.
            $table->string('reporting_period');

            $table->date('submission_date');

            // Computed automatically by the app (on_time / late) by
            // comparing submission_date to the report requirement's
            // deadline at the time this record is saved - never entered
            // by hand.
            $table->string('status');

            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_records');
    }
};
