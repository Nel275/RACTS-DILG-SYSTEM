<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Follow-up round: mirrors the reference `userslogs` table - a
     * roster of barangay personnel/officials (name, birthdate, gender,
     * designation, role, status), completely separate from the app's
     * real `users` login accounts table. Named `user_logs` (and the
     * "User Logs" nav item) to match what was asked for, but nothing
     * here can log in - it's a personnel record, not an account.
     *
     * Same foreign-key choice as barangay_log_details above:
     * `barangay_id` instead of a free-text `BarangayName` column, so
     * this stays consistent with how the rest of RACTS relates to
     * barangays.
     */
    public function up(): void
    {
        Schema::create('user_logs', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('birthdate');
            $table->string('gender');
            $table->string('designation');
            $table->string('roles');
            $table->string('status')->default('active');
            $table->date('date_process')->nullable();
            $table->string('processed_by')->nullable();
            $table->foreignId('barangay_id')->nullable()->constrained('barangays')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_logs');
    }
};
