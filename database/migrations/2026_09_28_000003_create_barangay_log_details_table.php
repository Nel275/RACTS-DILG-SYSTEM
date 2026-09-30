<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Follow-up round: mirrors the reference `barangaylogsdetails` table
     * - one row per barangay per logged item, which is the data behind
     * the new Barangay Log Details list (the page built from the
     * "Purchases" screenshot: Number -> barangay, Supplier -> Report).
     *
     * One deliberate change from the reference dump: `barangay_name` is
     * stored as a real foreign key (`barangay_id`) to the existing
     * `barangays` table instead of a free-text column. Every other page
     * in RACTS already relates to barangays this way, and it's what
     * lets this list show/filter by barangay without typos or drift if
     * a barangay is ever renamed. `item_description` is still kept as
     * its own column (a snapshot of the barangay_logs item's
     * description at the time this row was created), matching the
     * reference dump - so a later rename of the catalog item doesn't
     * silently rewrite historical entries.
     */
    public function up(): void
    {
        Schema::create('barangay_log_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barangay_log_id')->constrained('barangay_logs')->cascadeOnDelete();
            $table->foreignId('barangay_id')->constrained('barangays')->cascadeOnDelete();
            $table->string('item_description');
            $table->date('deadline_date');
            $table->dateTime('date_submitted')->nullable();
            $table->string('status')->default('pending');
            $table->date('date_process')->nullable();
            $table->string('processed_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangay_log_details');
    }
};
