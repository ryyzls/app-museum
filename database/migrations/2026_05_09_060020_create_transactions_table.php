<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {

            $table->id();

            $table->foreignId('ticket_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('quantity');

            $table->decimal('total_price', 12, 2);

            $table->string('payment_method')->nullable();

            $table->enum('payment_status', [
                'Pending',
                'Paid',
                'Cancelled'
            ])->default('Pending');

            $table->string('transaction_code')
                ->unique();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};