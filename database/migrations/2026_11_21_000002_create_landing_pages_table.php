<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('landing_pages')) {
            Schema::create('landing_pages', function (Blueprint $table) {
                $table->id();
                $table->unsignedSmallInteger('year')->unique();
                $table->string('title')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_pages');
    }
};
