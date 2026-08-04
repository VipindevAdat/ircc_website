<?php

use App\Models\AwardGraphic;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('award_graphics', function (Blueprint $table) {
            $table->string('public_asset_path', 512)->nullable()->after('alt_text');
        });

        if (AwardGraphic::query()->count() !== 0) {
            return;
        }

        $now = now();
        AwardGraphic::query()->insert([
            [
                'alt_text' => "IRCC'26 Awards - Award Categories and Prizes",
                'public_asset_path' => 'front/images/awards-ircc26.PNG',
                'sort_order' => 0,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'alt_text' => "IRCC'26 Special Awards - Agriculture and Literature Categories",
                'public_asset_path' => 'front/images/awards-26.PNG',
                'sort_order' => 1,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::table('award_graphics', function (Blueprint $table) {
            $table->dropColumn('public_asset_path');
        });
    }
};
