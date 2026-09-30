<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Client-requested redesign (Section 1 / 3.7): the barangay code is
     * no longer used anywhere in the system - the barangay name alone
     * identifies it. This drops the column and its unique index.
     */
    public function up(): void
    {
        Schema::table('barangays', function (Blueprint $table) {
            $table->dropUnique(['barangay_code']);
            $table->dropColumn('barangay_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barangays', function (Blueprint $table) {
            // Nullable on the way back down - the original codes are gone,
            // so this can't restore the old unique data, only the column.
            $table->string('barangay_code')->nullable()->unique()->after('barangay_name');
        });
    }
};
