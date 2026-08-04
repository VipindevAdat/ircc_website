<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Populate role field for existing participants based on type field
        // type 0 = student, type 1 = adult/guest
        DB::statement("
            UPDATE participants 
            SET role = CASE 
                WHEN type = 0 THEN 'student'
                WHEN type = 1 THEN 'adult'
                ELSE 'student'
            END
            WHERE role IS NULL OR role = ''
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse - we're just populating data
    }
};
