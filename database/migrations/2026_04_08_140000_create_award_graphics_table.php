<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('award_graphics', function (Blueprint $table) {
            $table->id();
            $table->string('alt_text')->nullable();
            $table->integer('sort_order')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        foreach ([
            [
                'field_type' => 'text',
                'dataset' => null,
                'data_source' => '',
                'code' => 'awards_banner_title',
                'title' => 'Awards page — banner title (H1)',
                'placeholder' => 'Awards',
                'value' => 'Awards',
            ],
            [
                'field_type' => 'text',
                'dataset' => null,
                'data_source' => '',
                'code' => 'awards_section_heading',
                'title' => 'Awards page — section heading (H2)',
                'placeholder' => 'GYRA Awards',
                'value' => 'GYRA Awards',
            ],
        ] as $row) {
            if (! Setting::query()->where('code', $row['code'])->exists()) {
                Setting::query()->create(array_merge($row, ['status' => 1]));
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('award_graphics');
        Setting::query()->whereIn('code', ['awards_banner_title', 'awards_section_heading'])->delete();
    }
};
