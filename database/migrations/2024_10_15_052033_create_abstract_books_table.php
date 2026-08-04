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
        Schema::create('abstract_books', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->date('date_from')->nullable();;
            $table->date('date_to')->nullable();;
            $table->text('location')->nullable();;
            $table->text('description')->nullable();
            $table->integer('sort_order');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abstract_books');
    }
};
