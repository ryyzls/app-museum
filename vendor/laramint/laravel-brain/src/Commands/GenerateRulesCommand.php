<?php

declare(strict_types=1);

namespace LaraMint\LaravelBrain\Commands;

use Illuminate\Console\Command;
use LaraMint\LaravelBrain\Ai\RulesExporter;
use LaraMint\LaravelBrain\Storage\GraphStoreFactory;

class GenerateRulesCommand extends Command
{
    protected $signature = 'brain:generate-rules
                            {--target=* : Targets to generate (claude,cursor,windsurf,copilot,junie,aider,agents). Defaults to all.}
                            {--force    : Overwrite existing files without prompting}
                            {--dry-run  : Show which files would be written without writing them}';

    protected $description = 'Generate AI assistant context files (CLAUDE.md, .cursorrules, copilot-instructions, etc.) from the scanned graph';

    public function handle(): int
    {
        $store = GraphStoreFactory::make();

        if (! $store->hasManifest()) {
            $this->error('No scan data found — run php artisan brain:scan first');

            return self::FAILURE;
        }

        $exporter = new RulesExporter($store, base_path());

        /** @var list<string> $targets */
        $targets = (array) $this->option('target');
        $targets = array_filter(array_map('trim', $targets));

        if (empty($targets)) {
            $targets = array_keys(RulesExporter::TARGETS);
        }

        $unknown = array_diff($targets, array_keys(RulesExporter::TARGETS));
        if (! empty($unknown)) {
            $this->error('Unknown target(s): '.implode(', ', $unknown));
            $this->line('Valid targets: '.implode(', ', array_keys(RulesExporter::TARGETS)));

            return self::FAILURE;
        }

        $isDryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');

        $wrote = 0;
        $errors = 0;

        foreach ($targets as $target) {
            $label = RulesExporter::TARGETS[$target]['label'];
            $destPath = $exporter->targetPath($target);
            $relative = str_replace(base_path().'/', '', $destPath);

            if ($isDryRun) {
                $this->line("  <fg=cyan>dry-run</> {$label} → {$relative}");

                continue;
            }

            // Prompt before overwriting unless --force
            if (file_exists($destPath) && ! $force) {
                if (! $this->confirm("  <fg=yellow>{$relative}</> already exists. Overwrite?", false)) {
                    $this->line("  <fg=yellow>skipped</>  {$label}");

                    continue;
                }
            }

            try {
                $content = $exporter->generate($target);
                $dir = dirname($destPath);

                if (! is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }

                file_put_contents($destPath, $content);
                $this->line("  <fg=green>written</>   {$label} → {$relative}");
                $wrote++;
            } catch (\Exception $e) {
                $this->error("  {$label}: ".$e->getMessage());
                $errors++;
            }
        }

        if ($isDryRun) {
            $this->newLine();
            $this->info('Dry run complete — no files written. Remove --dry-run to generate.');

            return self::SUCCESS;
        }

        $this->newLine();
        $this->info("{$wrote} file(s) written.".($errors > 0 ? " {$errors} error(s)." : ''));

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }
}
