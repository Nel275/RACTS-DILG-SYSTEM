<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Follow-up round: lets a log entry actually carry the uploaded
     * report file, so "Download" has something real to file away.
     *
     * - report_file_path: where the file currently sits on disk
     *   (storage/app/...), whether still staged (uploaded via the Mark
     *   Submitted form but not yet filed) or already moved into the
     *   barangay's folder.
     * - report_original_name: the file's original name as uploaded,
     *   kept separately since the stored path is renamed to avoid
     *   collisions.
     * - report_filed_at: null until "Download" has moved the file into
     *   the barangay's folder - once set, Download is done for this
     *   entry (see BarangayLogDetail::isFiled()).
     */
    public function up(): void
    {
        Schema::table('barangay_log_details', function (Blueprint $table) {
            $table->string('report_file_path')->nullable()->after('date_submitted');
            $table->string('report_original_name')->nullable()->after('report_file_path');
            $table->dateTime('report_filed_at')->nullable()->after('report_original_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barangay_log_details', function (Blueprint $table) {
            $table->dropColumn(['report_file_path', 'report_original_name', 'report_filed_at']);
        });
    }
};
