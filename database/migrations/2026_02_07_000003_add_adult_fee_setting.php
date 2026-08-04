<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add adult_fee setting if it doesn't exist
        Setting::firstOrCreate(
            ['code' => 'adult_fee'],
            [
                'field_type' => 'text',
                'dataset' => null,
                'data_source' => '',
                'title' => 'Accompanying Adult Fee',
                'placeholder' => '50',
                'value' => '50',
                'status' => 1,
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove adult_fee setting
        Setting::where('code', 'adult_fee')->delete();
    }
};
