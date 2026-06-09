<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function __construct()
    {
        // Run on the connection configured for the database driver
        // (null = the app's default connection).
        $this->connection = config('laravel-brain.database.connection');
    }

    public function up(): void
    {
        $this->schema()->create($this->table(), function (Blueprint $table): void {
            $table->id();
            $table->string('tab')->unique();
            $table->longText('payload');
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        $this->schema()->dropIfExists($this->table());
    }

    private function schema(): Builder
    {
        return Schema::connection($this->getConnection());
    }

    private function table(): string
    {
        return (string) config('laravel-brain.database.table', 'laravel_brain_graphs');
    }
};
