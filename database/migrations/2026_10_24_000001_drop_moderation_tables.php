<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The moderation page and its database dictionary were removed: the
     * word list now ships in code (App\Services\ProfanityDictionary).
     * Drops the tables where an earlier build created them.
     */
    public function up(): void
    {
        Schema::dropIfExists('moderation_flags');
        Schema::dropIfExists('prohibited_terms');
    }

    public function down(): void
    {
        // Not recreated - nothing reads these tables any more.
    }
};
