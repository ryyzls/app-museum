<?php

declare(strict_types=1);

namespace LaraMint\LaravelBrain\Analysis;

/**
 * Bridge to the optional `laramint/laravel-security-scanner` package.
 *
 * Shells out to `vendor/bin/php-security-scanner` in the analyzed project
 * (which loads the Laravel rule extension via composer auto-discovery),
 * parses its JSON report, and returns findings grouped by absolute file path.
 *
 * Returns null when the binary is absent or the run fails, so the caller
 * can gracefully fall back to Brain's built-in AST source scan.
 */
class ExternalSecurityScanner
{
    /**
     * Map an external rule id to Brain's internal issue `type`.
     * Anything unmapped falls back to the upper-cased final segment.
     */
    private const RULE_TYPE_MAP = [
        'laravel.sql-injection' => 'SQL_INJECTION',
        'laravel.mass-assignment' => 'MASS_ASSIGNMENT',
        'laravel.unsafe-validator' => 'UNVALIDATED_INPUT',
        'laravel.weak-validation' => 'UNVALIDATED_INPUT',
        'laravel.blade-raw-echo' => 'XSS_BLADE_UNESCAPED',
        'laravel.open-redirect' => 'OPEN_REDIRECT',
        'laravel.ssrf.http-client' => 'SSRF',
        'laravel.debug-code' => 'DEBUG_CODE',
        'laravel.env-leak' => 'ENV_LEAK',
        'laravel.csrf-bypass' => 'CSRF_BYPASS',
        'laravel.cookie-insecure' => 'INSECURE_COOKIE',
        'laravel.unsafe-storage-path' => 'UNSAFE_STORAGE_PATH',
        'laravel.file-upload-validation' => 'FILE_UPLOAD_VALIDATION',
        'laravel.unsafe-auth' => 'UNSAFE_AUTH',
        'laravel.unsafe-crypt' => 'UNSAFE_CRYPT',
        'laravel.artisan-call' => 'ARTISAN_CALL',
        'laravel.process-shell' => 'PROCESS_SHELL',
        'laravel.config-injection' => 'CONFIG_INJECTION',
        'laravel.tainted-view-name' => 'TAINTED_VIEW_NAME',
        'laravel.session-fixation' => 'SESSION_FIXATION',
        'laravel.mail-tainted-header' => 'MAIL_TAINTED_HEADER',
    ];

    /**
     * Scan the project. Returns:
     *   absoluteFilePath => list<array{type,severity,message,line,ruleId}>
     *
     * @return array<string, list<array<string, mixed>>>|null
     */
    public function scan(string $projectRoot): ?array
    {
        $binary = $this->locateBinary($projectRoot);
        if ($binary === null) {
            return null;
        }

        $targets = array_values(array_filter(
            ['app', 'src', 'routes'],
            fn (string $d) => is_dir($projectRoot.DIRECTORY_SEPARATOR.$d),
        ));
        if ($targets === []) {
            return null;
        }

        $outFile = sys_get_temp_dir().DIRECTORY_SEPARATOR.'lb_sec_'.bin2hex(random_bytes(8)).'.json';

        $cmd = array_merge(
            [PHP_BINARY, $binary, 'scan'],
            $targets,
            ['--format=json', '--output='.$outFile, '--fail-on=none', '--no-interaction'],
        );

        $json = $this->run($cmd, $projectRoot, $outFile);
        @unlink($outFile);

        if ($json === null) {
            return null;
        }

        $decoded = json_decode($json, true);
        if (! is_array($decoded) || ! isset($decoded['findings']) || ! is_array($decoded['findings'])) {
            return null;
        }

        $byFile = [];
        foreach ($decoded['findings'] as $f) {
            if (! is_array($f) || ! isset($f['file'])) {
                continue;
            }
            $real = realpath((string) $f['file']) ?: (string) $f['file'];
            $ruleId = (string) ($f['ruleId'] ?? 'unknown');

            $byFile[$real][] = [
                'type' => $this->mapType($ruleId),
                'severity' => $this->normalizeSeverity((string) ($f['severity'] ?? 'medium')),
                'message' => (string) ($f['message'] ?? $ruleId),
                'line' => isset($f['line']) ? (int) $f['line'] : null,
                'ruleId' => $ruleId,
            ];
        }

        return $byFile;
    }

    private function locateBinary(string $projectRoot): ?string
    {
        foreach (['vendor/bin/php-security-scanner', 'vendor/bin/php-security-scanner.bat'] as $rel) {
            $path = $projectRoot.DIRECTORY_SEPARATOR.$rel;
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * @param  list<string>  $cmd
     */
    private function run(array $cmd, string $cwd, string $outFile): ?string
    {
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $proc = @proc_open($cmd, $descriptors, $pipes, $cwd);
        if (! is_resource($proc)) {
            return null;
        }

        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]) ?: '';
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($proc);

        // Reporter writes JSON to --output; prefer that, fall back to stdout.
        if (is_file($outFile)) {
            $contents = file_get_contents($outFile);
            if ($contents !== false && trim($contents) !== '') {
                return $contents;
            }
        }

        return trim($stdout) !== '' ? $stdout : null;
    }

    private function mapType(string $ruleId): string
    {
        if (isset(self::RULE_TYPE_MAP[$ruleId])) {
            return self::RULE_TYPE_MAP[$ruleId];
        }

        $segment = str_contains($ruleId, '.') ? substr(strrchr($ruleId, '.'), 1) : $ruleId;

        return strtoupper(str_replace('-', '_', $segment));
    }

    private function normalizeSeverity(string $severity): string
    {
        $s = strtolower($severity);

        return in_array($s, ['critical', 'high', 'medium', 'low'], true) ? $s : 'medium';
    }
}
