<?php

declare(strict_types=1);

namespace LaraMint\LaravelBrain\Analysis;

class SecurityIssue
{
    public function __construct(
        /**
         * Issue type identifier:
         * MASS_ASSIGNMENT    — request()->all() passed directly to create/fill
         * UNVALIDATED_INPUT  — request()->all() used without validate() or FormRequest
         * MISSING_THROTTLE   — sensitive endpoint (login/register/etc) missing throttle
         * PUBLIC_WRITE       — POST/PUT/PATCH/DELETE route with no auth middleware
         * OPEN_DELETE        — DELETE route with no auth middleware
         */
        public string $type,

        /** critical | high | medium | low */
        public string $severity,

        /** Human-readable description of the issue */
        public string $message,

        /** Absolute path to the file where the issue was found */
        public ?string $file = null,

        /** Line number in that file */
        public ?int $line = null,
    ) {}

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'severity' => $this->severity,
            'message' => $this->message,
            'file' => $this->file,
            'line' => $this->line,
        ];
    }
}
