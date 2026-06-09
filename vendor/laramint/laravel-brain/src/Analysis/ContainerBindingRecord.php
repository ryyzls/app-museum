<?php

declare(strict_types=1);

namespace LaraMint\LaravelBrain\Analysis;

/**
 * One IoC registration discovered in an application service provider.
 *
 * $kind is one of: bind, singleton, scoped, bindings, singletons.
 */
final class ContainerBindingRecord
{
    public function __construct(
        public string $abstractFqcn,
        public ?string $concreteFqcn,
        public string $providerFqcn,
        public string $kind,
    ) {}
}
