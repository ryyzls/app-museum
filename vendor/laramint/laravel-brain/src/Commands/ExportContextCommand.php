<?php

declare(strict_types=1);

namespace LaraMint\LaravelBrain\Commands;

use Illuminate\Console\Command;
use LaraMint\LaravelBrain\Ai\ContextExporter;
use LaraMint\LaravelBrain\Storage\GraphStoreFactory;

class ExportContextCommand extends Command
{
    protected $signature = 'brain:export-context
                            {--route=      : Filter by route label or URI (case-insensitive)}
                            {--node=       : Target a specific node ID}
                            {--budget=6000 : Token budget}
                            {--format=markdown : Output format (markdown|json)}
                            {--output=     : Write to file path instead of stdout}
                            {--force       : Overwrite existing output file without prompting}';

    protected $description = 'Export a deterministic AI context snapshot from the scanned graph';

    public function handle(): int
    {
        $store = GraphStoreFactory::make();

        if (! $store->hasManifest()) {
            $this->error('No scan data found — run php artisan brain:scan first');

            return self::FAILURE;
        }

        $format = in_array($this->option('format'), ['markdown', 'json'], true)
            ? (string) $this->option('format')
            : 'markdown';

        $exporter = new ContextExporter($store, base_path());

        try {
            $output = $exporter->export(
                nodeId: $this->option('node') ? (string) $this->option('node') : null,
                routeLabel: $this->option('route') ? (string) $this->option('route') : null,
                budget: max(500, (int) $this->option('budget')),
                format: $format,
            );
        } catch (\RuntimeException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $outputPath = $this->option('output') ? (string) $this->option('output') : null;

        if ($outputPath) {
            if (file_exists($outputPath) && ! $this->option('force')) {
                if (! $this->confirm("<fg=yellow>{$outputPath}</> already exists. Overwrite?", false)) {
                    $this->line('<fg=yellow>Aborted.</> No file written.');

                    return self::SUCCESS;
                }
            }

            file_put_contents($outputPath, $output);
            $this->info("Context written to {$outputPath}");
        } else {
            $this->line($output);
        }

        return self::SUCCESS;
    }
}
