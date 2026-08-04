<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            // Add role field: 'student' or 'adult'
            $table->string('role')->nullable()->after('type');
            
            // Add age field (required for students)
            $table->integer('age')->nullable()->after('role');
            
            // Add email field (optional for students, required for adults)
            $table->string('email')->nullable()->after('age');
            
            // Add index for role for faster queries
            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropColumn(['role', 'age', 'email']);
        });
    }
};
