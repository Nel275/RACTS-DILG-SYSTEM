<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Follow-up round: Report Filing (the barangay_logs /
     * barangay_log_details catalog and entries behind the "Report
     * Filing" tab) was Barangay-only - this adds the same council_type
     * split that report_requirements already uses for the Compliance
     * Grid, so SK Reports can get its own Report Filing tab with its
     * own report catalog, independent of the Barangay one.
     *
     * Deliberately NOT renaming barangay_logs / barangay_log_details or
     * their routes to something council-neutral - that would touch far
     * more of the app (every route, controller, and view that
     * references them) for a cosmetic win. council_type on the catalog
     * table is enough to scope an SK catalog item from a Barangay one;
     * barangay_log_details doesn't need its own column, since every
     * entry already points at a barangay_logs row via barangay_log_id,
     * and that row's council_type is what an entry's own council is.
     *
     * Defaults to 'barangay' so every existing catalog item - all of
     * which were created before SK Report Filing existed - keeps
     * showing up on the Barangay side exactly as before.
     */
    public function up(): void
    {
        Schema::table('barangay_logs', function (Blueprint $table) {
            $table->enum('council_type', ['barangay', 'sk'])
                ->default('barangay')
                ->after('item_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barangay_logs', function (Blueprint $table) {
            $table->dropColumn('council_type');
        });
    }
};
