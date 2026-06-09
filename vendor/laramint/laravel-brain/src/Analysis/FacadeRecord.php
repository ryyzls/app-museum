<?php

declare(strict_types=1);

namespace LaraMint\LaravelBrain\Analysis;

/**
 * One Laravel Facade discovered in the application.
 *
 * $accessor is the raw value returned by getFacadeAccessor():
 *   - A FQCN string (e.g. 'App\Services\ShortUrlV3Service') when resolved via ::class
 *   - A container key (e.g. 'short-url-v3') when returned as a plain string
 *
 * $concreteFqcn is populated once the accessor is cross-referenced with the
 * ContainerBindingRegistry or identified directly as a FQCN.
 */
final class FacadeRecord
{
    public function __construct(
        public string $facadeFqcn,
        public string $accessor,
        public ?string $concreteFqcn = null,
    ) {}
}
