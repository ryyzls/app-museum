<?php

declare(strict_types=1);

namespace LaraMint\LaravelBrain\Analysis;

/**
 * A single middleware declared at controller level, optionally scoped to specific actions.
 * Supports both the HasMiddleware static method and $this->middleware() constructor calls.
 */
class ControllerMiddleware
{
    public function __construct(
        public string $middleware,
        /** @var string[]|null null = applies to every action */
        public ?array $only = null,
        /** @var string[]|null null = no exclusions */
        public ?array $except = null,
    ) {}

    public function appliesToAction(string $action): bool
    {
        if ($this->only !== null && ! in_array($action, $this->only, true)) {
            return false;
        }
        if ($this->except !== null && in_array($action, $this->except, true)) {
            return false;
        }

        return true;
    }
}
