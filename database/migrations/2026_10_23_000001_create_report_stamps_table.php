<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Digital stamping. report_file_hash fingerprints the file a report
     * entry currently carries; each report_stamps row records which
     * fingerprint it stamped, so a resubmitted (different) file is
     * automatically "awaiting stamp" again while every earlier stamp
     * stays in the history. The original upload is never modified - the
     * stamped copy is a separate file (stamped_file_path).
     */
    public function up(): void
    {
        Schema::table('barangay_log_details', function (Blueprint $table) {
            $table->string('report_file_hash', 64)->nullable()->after('report_original_name');
        });

        Schema::create('report_stamps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barangay_log_detail_id')->constrained('barangay_log_details')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('stamped_by_name');
            $table->string('verification_code', 32)->unique();
            $table->string('original_file_hash', 64)->index();
            $table->string('stamped_file_path');
            $table->string('stamped_original_name');
            $table->string('stamped_file_hash', 64);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_stamps');

        Schema::table('barangay_log_details', function (Blueprint $table) {
            $table->dropColumn('report_file_hash');
        });
    }
};
