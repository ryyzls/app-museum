<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exhibitions', function (Blueprint $table) {

            $table->id();

            $table->string('title');

            $table->string('subtitle')->nullable();

            $table->text('description')->nullable();

            $table->string('banner_image')->nullable();

            $table->date('start_date');

            $table->date('end_date');

            $table->enum('status', [
                'Past',
                'Current',
                'Upcoming'
            ])->default('Upcoming');

            $table->foreignId('museum_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exhibitions');
    }
};