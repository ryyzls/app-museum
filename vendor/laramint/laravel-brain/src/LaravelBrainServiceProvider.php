<?php

declare(strict_types=1);

namespace LaraMint\LaravelBrain;

use Illuminate\Support\ServiceProvider;
use LaraMint\LaravelBrain\Commands\ExportContextCommand;
use LaraMint\LaravelBrain\Commands\GenerateRulesCommand;
use LaraMint\LaravelBrain\Commands\ScanCommand;

class LaravelBrainServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-brain.php', 'laravel-brain');

        $this->registerGraphConnections();
    }

    /**
     * Expose the package's self-contained DB connections to Laravel so the
     * graph can live in a dedicated database with its own credentials,
     * without the user editing config/database.php.
     */
    private function registerGraphConnections(): void
    {
        $connections = config('laravel-brain.database.connections', []);

        if (! is_array($connections) || $connections === []) {
            return;
        }

        config([
            'database.connections' => array_merge(
                (array) config('database.connections', []),
                $connections,
            ),
        ]);
    }

    public function boot(): void
    {
        // Only register routes and commands in local environment for security
        if (! $this->app->isLocal()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/laravel-brain.php' => config_path('laravel-brain.php'),
        ], 'laravel-brain-config');

        // The graph table is created on demand by the database driver the
        // first time a scan runs (CLI or UI), so the migration is NOT loaded
        // automatically. It is still publishable for anyone who prefers to
        // manage it with `php artisan migrate`.
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'laravel-brain-migrations');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-brain');
        $this->commands([ScanCommand::class, ExportContextCommand::class, GenerateRulesCommand::class]);
        $this->loadRoutesFrom(__DIR__.'/../routes/brain.php');
    }
}
