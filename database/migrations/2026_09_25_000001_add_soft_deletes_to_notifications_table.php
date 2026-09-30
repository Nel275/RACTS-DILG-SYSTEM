<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Follow-up fix: "Clear Notifications" previously hard-deleted rows,
     * which meant the automation's per-user dedup check (see
     * CheckLateSubmissions - it looks up which users already have a
     * notification for a given message) could no longer see that this
     * user had ever been notified, and would recreate the exact same
     * alert on the very next check as long as the underlying report was
     * still missing. Soft-deleting instead means a cleared notification
     * disappears from the user's list and unread badge (Eloquent's
     * SoftDeletes trait excludes trashed rows from normal queries
     * automatically) while still existing for the dedup check - which
     * explicitly looks at trashed rows too (see the withTrashed() call
     * added to CheckLateSubmissions) - to see and skip. A genuinely new
     * problem (a different period, once next month closes) still gets a
     * fresh alert either way, since its message text is different.
     */
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
