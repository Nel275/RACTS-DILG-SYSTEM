<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Panelist revision: barangay accounts, personalized profiles, and
     * account activation. `is_active` lets an administrator disable an
     * account without deleting it (login is refused while false).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('role');
            $table->string('contact_number', 20)->nullable()->after('is_active');
            $table->string('position', 100)->nullable()->after('contact_number');
            $table->string('avatar_path')->nullable()->after('position');
            $table->timestamp('last_login_at')->nullable()->after('avatar_path');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'contact_number', 'position', 'avatar_path', 'last_login_at']);
        });
    }
};
