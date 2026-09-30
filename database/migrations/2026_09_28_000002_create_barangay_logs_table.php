<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Follow-up round: mirrors the reference `barangaylogs` table - the
     * catalog of trackable "items" (report/document types) a barangay
     * can be logged against, each with its own deadline. This is what
     * populates the "Report" dropdown on the new Barangay Log Details
     * list (see barangay_log_details below) - conceptually parallel to
     * report_requirements (the grid's report slots), but kept as its
     * own table on purpose since this is a separate feature the client
     * asked to track independently of the existing grid.
     */
    public function up(): void
    {
        Schema::create('barangay_logs', function (Blueprint $table) {
            $table->id();
            $table->string('item_description');
            $table->date('deadline_date');
            $table->string('status')->default('active');
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
        Schema::dropIfExists('barangay_logs');
    }
};
