<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Follow-up round: User Logs can now optionally create a real RACTS
     * login account (DILG Administrator or DILG Monitoring Staff role
     * only) for the personnel record being added - a deliberate reversal
     * of the earlier "User Logs never creates a login account" rule,
     * per explicit request. This column is the link between the two:
     * null for a plain personnel record with no login access, set to
     * the matching `users.id` once one's been given an account.
     *
     * nullOnDelete() rather than cascadeOnDelete(): if that login
     * account is ever removed from the Users page, this personnel
     * record should survive and just go back to having no linked
     * account - not be deleted itself.
     */
    public function up(): void
    {
        Schema::table('user_logs', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('barangay_id')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('user_logs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
