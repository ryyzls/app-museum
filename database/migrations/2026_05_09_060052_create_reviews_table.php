<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('artwork_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('rating')->unsigned(); // Nilai 1 - 5
            $table->text('comment')->nullable();
            $table->timestamps();

            // Kunci utama: Mencegah 1 user menduplikasi review di artwork yang sama
            $table->unique(['user_id', 'artwork_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};