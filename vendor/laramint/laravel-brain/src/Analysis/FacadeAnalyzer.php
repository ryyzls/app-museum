<?php

declare(strict_types=1);

namespace LaraMint\LaravelBrain\Analysis;

use LaraMint\LaravelBrain\Parser\PhpExtendsFqcnResolver;
use LaraMint\LaravelBrain\Parser\PhpFileParser;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;

/**
 * Scans app/ for application-level Laravel facades and builds a FacadeRegistry.
 *
 * A facade is any concrete class whose inheritance chain leads to
 * Illuminate\Support\Facades\Facade (multi-level inheritance is supported, e.g.
 * ShortUrlV3Facade → AbstractVersionedShortUrlFacade → Facade).
 *
 * getFacadeAccessor() is searched in the class itself and then up the chain.
 * When the accessor resolves to a FQCN via ::class, concreteFqcn is set
 * immediately; plain string keys (e.g. 'cache') are left for
 * FacadeRegistry::resolveWith() to match against the ContainerBindingRegistry.
 */
final class FacadeAnalyzer
{
    private const FACADE_BASE = 'Illuminate\\Support\\Facades\\Facade';

    private PhpFileParser $parser;

    private string $appDir = '';

    /** @var array<string, array{ast: mixed, useMap: array<string,string>}|null> */
    private array $parseCache = [];

    public function __construct(?PhpFileParser $parser = null)
    {
        $this->parser = $parser ?? new PhpFileParser;
    }

    public function analyze(string $projectRoot): FacadeRegistry
    {
        $registry = new FacadeRegistry;
        $this->parseCache = [];
        $this->appDir = rtrim($projectRoot, '/').'/app';

        if (! is_dir($this->appDir)) {
            return $registry;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->appDir, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }
            $this->scanFile($file->getPathname(), $registry);
        }

        return $registry;
    }

    private function scanFile(string $file, FacadeRegistry $registry): void
    {
        $parsed = $this->parseWithCache($file);
        if ($parsed === null) {
            return;
        }

        $ns = PhpExtendsFqcnResolver::namespaceFromAst($parsed['ast']);
        $useMap = $parsed['useMap'];
        $stmts = $this->topLevelStmts($parsed['ast']);

        foreach ($stmts as $stmt) {
            if (! ($stmt instanceof Class_) || $stmt->name === null) {
                continue;
            }

            // Skip abstract classes — they cannot be injected directly.
            if ($stmt->isAbstract()) {
                break;
            }

            $parentFqcn = PhpExtendsFqcnResolver::resolveExtends($stmt->extends, $ns, $useMap);
            if ($parentFqcn === null) {
                break;
            }

            // Check if this class is (directly or transitively) a Facade subclass.
            if (! $this->isInFacadeChain($parentFqcn, 0)) {
                break;
            }

            $short = $stmt->name->toString();
            $facadeFqcn = $ns !== '' ? $ns.'\\'.$short : $short;

            // Find getFacadeAccessor() in this class or an ancestor.
            $accessor = $this->findAccessorInChain($stmt, $ns, $useMap, 0);
            if ($accessor === null) {
                break;
            }

            $concreteFqcn = str_contains($accessor, '\\') ? $accessor : null;
            $registry->add(new FacadeRecord($facadeFqcn, $accessor, $concreteFqcn));
            break;
        }
    }

    // ── Inheritance chain helpers ─────────────────────────────────────────────

    /**
     * Return true when $fqcn is Illuminate\Support\Facades\Facade or extends it
     * (directly or through intermediate app-level classes).
     */
    private function isInFacadeChain(string $fqcn, int $depth): bool
    {
        if ($fqcn === self::FACADE_BASE) {
            return true;
        }
        if ($depth >= 5 || str_starts_with($fqcn, 'Illuminate\\') || str_starts_with($fqcn, 'Laravel\\')) {
            return false;
        }

        $file = $this->findFileInAppDir($fqcn);
        if ($file === null) {
            return false;
        }

        $parsed = $this->parseWithCache($file);
        if ($parsed === null) {
            return false;
        }

        $ns = PhpExtendsFqcnResolver::namespaceFromAst($parsed['ast']);
        foreach ($this->topLevelStmts($parsed['ast']) as $stmt) {
            if (! ($stmt instanceof Class_)) {
                continue;
            }
            $parentFqcn = PhpExtendsFqcnResolver::resolveExtends($stmt->extends, $ns, $parsed['useMap']);

            return $parentFqcn !== null && $this->isInFacadeChain($parentFqcn, $depth + 1);
        }

        return false;
    }

    /**
     * Look for getFacadeAccessor() in $class, then walk up parent classes in app/.
     *
     * @param  array<string, string>  $useMap
     */
    private function findAccessorInChain(Class_ $class, string $ns, array $useMap, int $depth): ?string
    {
        $accessor = $this->extractAccessor($class, $ns, $useMap);
        if ($accessor !== null) {
            return $accessor;
        }

        if ($depth >= 5) {
            return null;
        }

        $parentFqcn = PhpExtendsFqcnResolver::resolveExtends($class->extends, $ns, $useMap);
        if (
            $parentFqcn === null
            || $parentFqcn === self::FACADE_BASE
            || str_starts_with($parentFqcn, 'Illuminate\\')
            || str_starts_with($parentFqcn, 'Laravel\\')
        ) {
            return null;
        }

        $file = $this->findFileInAppDir($parentFqcn);
        if ($file === null) {
            return null;
        }

        $parsed = $this->parseWithCache($file);
        if ($parsed === null) {
            return null;
        }

        $parentNs = PhpExtendsFqcnResolver::namespaceFromAst($parsed['ast']);
        foreach ($this->topLevelStmts($parsed['ast']) as $stmt) {
            if (! ($stmt instanceof Class_)) {
                continue;
            }

            return $this->findAccessorInChain($stmt, $parentNs, $parsed['useMap'], $depth + 1);
        }

        return null;
    }

    // ── Low-level parsing helpers ─────────────────────────────────────────────

    /**
     * Find getFacadeAccessor() in $class and return its string return value.
     *
     * @param  array<string, string>  $useMap
     */
    private function extractAccessor(Class_ $class, string $namespace, array $useMap): ?string
    {
        foreach ($class->stmts as $stmt) {
            if (! ($stmt instanceof Node\Stmt\ClassMethod)) {
                continue;
            }
            if ($stmt->name->toString() !== 'getFacadeAccessor') {
                continue;
            }
            if ($stmt->stmts === null) {
                continue;
            }

            foreach ($stmt->stmts as $bodyStmt) {
                if (! ($bodyStmt instanceof Node\Stmt\Return_)) {
                    continue;
                }
                $expr = $bodyStmt->expr;
                if ($expr === null) {
                    continue;
                }

                // return SomeClass::class
                if (
                    $expr instanceof Expr\ClassConstFetch
                    && $expr->name instanceof Identifier
                    && $expr->name->toString() === 'class'
                    && $expr->class instanceof Node\Name
                ) {
                    $resolved = $this->resolveNameToFqcn($expr->class, $namespace, $useMap);
                    if ($resolved !== '') {
                        return $resolved;
                    }
                }

                // return 'App\Services\Foo' or 'some-container-key'
                if ($expr instanceof Scalar\String_ && $expr->value !== '') {
                    return ltrim($expr->value, '\\');
                }
            }
        }

        return null;
    }

    /**
     * @param  array<string, string>  $useMap
     */
    private function resolveNameToFqcn(Node\Name $name, string $namespace, array $useMap): string
    {
        if ($name instanceof Node\Name\FullyQualified) {
            return ltrim($name->toString(), '\\');
        }

        $short = $name->toString();
        if (isset($useMap[$short])) {
            return $useMap[$short];
        }

        if (str_contains($short, '\\')) {
            return ($namespace !== '' ? $namespace.'\\' : '').$short;
        }

        return $namespace !== '' ? $namespace.'\\'.$short : $short;
    }

    /**
     * Find the PHP file for a FQCN by searching app/ for a file whose name
     * matches the short class name.
     */
    private function findFileInAppDir(string $fqcn): ?string
    {
        if ($this->appDir === '' || ! is_dir($this->appDir)) {
            return null;
        }

        $shortName = str_contains($fqcn, '\\')
            ? substr($fqcn, strrpos($fqcn, '\\') + 1)
            : $fqcn;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->appDir, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->getFilename() === $shortName.'.php') {
                return $file->getPathname();
            }
        }

        return null;
    }

    /**
     * @return array{ast: mixed, useMap: array<string,string>}|null
     */
    private function parseWithCache(string $file): ?array
    {
        if (array_key_exists($file, $this->parseCache)) {
            return $this->parseCache[$file];
        }

        $parsed = $this->parser->parse($file);
        $result = $parsed['ast'] !== null ? $parsed : null;

        return $this->parseCache[$file] = $result;
    }

    /**
     * Return the top-level statements, unwrapping a Namespace_ wrapper if present.
     *
     * @return Node\Stmt[]
     */
    private function topLevelStmts(mixed $ast): array
    {
        if (! is_array($ast)) {
            return [];
        }
        if (isset($ast[0]) && $ast[0] instanceof Namespace_) {
            return $ast[0]->stmts;
        }

        return $ast;
    }
}
