<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Client-requested redesign (Section 1 / 4): lets a notification
     * link back to the specific report_records row it's about (when one
     * exists yet), and distinguishes system-generated late alerts from
     * anything else, since the Notifications page is being repurposed
     * to show only automated entries (Section 3.6).
     */
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->foreignId('report_record_id')->nullable()->after('user_id')
                ->constrained('report_records')->nullOnDelete();
            $table->boolean('auto_generated')->default(false)->after('is_read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('report_record_id');
            $table->dropColumn('auto_generated');
        });
    }
};
