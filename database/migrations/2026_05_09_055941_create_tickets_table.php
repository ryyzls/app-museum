<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {

            $table->id();

            $table->foreignId('exhibition_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('ticket_type');

            $table->decimal('price', 10, 2);

            $table->integer('quota');

            $table->integer('available_quota');

            $table->date('visit_date');

            $table->enum('status', [
                'Available',
                'Sold Out',
                'Closed'
            ])->default('Available');

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};