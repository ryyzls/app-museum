<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('ticket_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('transaction_code')
                ->unique();

            $table->integer('quantity');

            $table->decimal('total_price', 12, 2);

            $table->date('visit_date');

            $table->enum('payment_status', [

                'pending',
                'paid',
                'cancelled'

            ])->default('pending');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

