<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Client-requested redesign (Section 1, "optional but recommended").
     * Not used yet as of this migration/phase - RankingCalculator
     * computes rankings live for now. This table exists so a later
     * phase (Analytics & Export) can add a "refresh" step to avoid
     * recomputing every barangay's ranking on every page load, without
     * needing another schema change at that point.
     */
    public function up(): void
    {
        Schema::create('report_rankings_cache', function (Blueprint $table) {
            $table->id();
            $table->enum('council_type', ['barangay', 'sk']);
            $table->date('period_start');
            $table->date('period_end');
            $table->foreignId('barangay_id')->constrained('barangays')->cascadeOnDelete();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->unsignedInteger('rank');
            $table->dateTime('computed_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_rankings_cache');
    }
};
