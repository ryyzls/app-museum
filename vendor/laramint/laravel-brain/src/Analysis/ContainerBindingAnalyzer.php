<?php

declare(strict_types=1);

namespace LaraMint\LaravelBrain\Analysis;

use LaraMint\LaravelBrain\Parser\PhpExtendsFqcnResolver;
use LaraMint\LaravelBrain\Parser\PhpFileParser;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

/**
 * Scans app/Providers for Laravel container registrations (bind/singleton/scoped and $bindings).
 */
final class ContainerBindingAnalyzer
{
    private PhpFileParser $parser;

    /** @var list<string> */
    private const BIND_METHODS = ['bind', 'singleton', 'scoped', 'bindIf', 'singletonIf', 'scopedIf'];

    public function __construct(?PhpFileParser $parser = null)
    {
        $this->parser = $parser ?? new PhpFileParser;
    }

    public function analyze(string $projectRoot): ContainerBindingRegistry
    {
        $registry = new ContainerBindingRegistry;
        $root = rtrim($projectRoot, '/');
        if (! is_dir($root.'/app/Providers')) {
            return $registry;
        }

        $files = [];
        foreach ([$root.'/app/Providers/*.php', $root.'/app/Providers/**/*.php'] as $pattern) {
            foreach (glob($pattern) ?: [] as $file) {
                $files[$file] = true;
            }
        }
        $paths = array_keys($files);
        sort($paths);

        foreach ($paths as $file) {
            $this->scanFile($file, $registry);
        }

        return $registry;
    }

    private function scanFile(string $file, ContainerBindingRegistry $registry): void
    {
        $parsed = $this->parser->parse($file);
        if ($parsed['ast'] === null) {
            return;
        }
        $ast = $parsed['ast'];
        $useMap = $parsed['useMap'];
        $ns = PhpExtendsFqcnResolver::namespaceFromAst($ast);

        $stmts = $ast;
        if (isset($stmts[0]) && $stmts[0] instanceof Namespace_) {
            $stmts = $stmts[0]->stmts;
        }

        foreach ($stmts as $stmt) {
            if (! $stmt instanceof Class_) {
                continue;
            }
            if ($stmt->name === null) {
                continue;
            }
            $short = $stmt->name->toString();
            $providerFqcn = $ns !== '' ? $ns.'\\'.$short : $short;

            $this->walkClassStmts($stmt, $providerFqcn, $ns, $useMap, $registry);

            break;
        }
    }

    private function walkClassStmts(
        Class_ $class,
        string $providerFqcn,
        string $namespace,
        array $useMap,
        ContainerBindingRegistry $registry,
    ): void {
        foreach ($class->stmts as $stmt) {
            if ($stmt instanceof Node\Stmt\Property && ! $stmt->isStatic()) {
                foreach ($stmt->props as $prop) {
                    if (! $prop->name instanceof Identifier) {
                        continue;
                    }
                    $pname = $prop->name->toString();
                    if (! in_array($pname, ['bindings', 'singletons'], true)) {
                        continue;
                    }
                    $default = $prop->default;
                    if ($default instanceof Expr\Array_) {
                        $kind = $pname === 'singletons' ? 'singletons' : 'bindings';
                        $this->extractBindingArray($default, $providerFqcn, $namespace, $useMap, $kind, $registry);
                    }
                }
            }
        }

        $traverser = new NodeTraverser;
        $traverser->addVisitor(new class($providerFqcn, $namespace, $useMap, $registry, $this) extends NodeVisitorAbstract
        {
            public function __construct(
                private string $providerFqcn,
                private string $namespace,
                private array $useMap,
                private ContainerBindingRegistry $registry,
                private ContainerBindingAnalyzer $analyzer,
            ) {}

            public function enterNode(Node $node): ?int
            {
                if ($node instanceof MethodCall) {
                    $this->analyzer->tryExtractFromMethodCall(
                        $node,
                        $this->providerFqcn,
                        $this->namespace,
                        $this->useMap,
                        $this->registry,
                    );
                }

                return null;
            }
        });

        foreach ($class->stmts as $stmt) {
            if ($stmt instanceof Node\Stmt\ClassMethod && $stmt->stmts !== null) {
                $traverser->traverse($stmt->stmts);
            }
        }
    }

    /**
     * @param  'bindings'|'singletons'  $arrayKind
     */
    private function extractBindingArray(
        Expr\Array_ $array,
        string $providerFqcn,
        string $namespace,
        array $useMap,
        string $arrayKind,
        ContainerBindingRegistry $registry,
    ): void {
        $kind = $arrayKind === 'singletons' ? 'singleton' : 'bind';

        foreach ($array->items as $item) {
            if ($item === null) {
                continue;
            }
            $keyExpr = $item->key;
            $val = $item->value;
            if ($keyExpr instanceof Expr) {
                $abstract = $this->resolveClassLike($keyExpr, $namespace, $useMap);
                $concrete = $val instanceof Expr\Closure ? null : $this->resolveClassLike($val, $namespace, $useMap);
                if ($abstract !== null) {
                    $registry->add(new ContainerBindingRecord($abstract, $concrete, $providerFqcn, $kind));
                }
            }
        }
    }

    public function tryExtractFromMethodCall(
        MethodCall $node,
        string $providerFqcn,
        string $namespace,
        array $useMap,
        ContainerBindingRegistry $registry,
    ): void {
        if (! $node->name instanceof Identifier) {
            return;
        }
        $m = $node->name->toString();
        if (! in_array($m, self::BIND_METHODS, true)) {
            return;
        }
        if (! $this->isAppLikeInvokable($node->var)) {
            return;
        }

        $kind = match ($m) {
            'bind', 'bindIf' => 'bind',
            'singleton', 'singletonIf' => 'singleton',
            default => 'scoped',
        };

        $args = $node->args;
        if (count($args) < 2) {
            return;
        }

        $a0 = $args[0];
        $a1 = $args[1];
        $v0 = $a0 instanceof Node\Arg ? $a0->value : $a0;
        $v1 = $a1 instanceof Node\Arg ? $a1->value : $a1;

        $abstract = $this->resolveClassLike($v0, $namespace, $useMap);
        if ($abstract === null) {
            return;
        }

        $concrete = $v1 instanceof Expr\Closure ? null : $this->resolveClassLike($v1, $namespace, $useMap);

        $registry->add(new ContainerBindingRecord($abstract, $concrete, $providerFqcn, $kind));
    }

    private function isAppLikeInvokable(?Expr $var): bool
    {
        if ($var === null) {
            return false;
        }

        if (
            $var instanceof Expr\PropertyFetch
            && $var->var instanceof Expr\Variable
            && $var->var->name === 'this'
            && $var->name instanceof Identifier
            && $var->name->toString() === 'app'
        ) {
            return true;
        }

        if ($var instanceof Expr\Variable && is_string($var->name) && $var->name === 'app') {
            return true;
        }

        if (
            $var instanceof Expr\FuncCall
            && $var->name instanceof Name
            && $var->name->toString() === 'app'
        ) {
            return true;
        }

        return false;
    }

    private function resolveClassLike(?Expr $expr, string $namespace, array $useMap): ?string
    {
        if ($expr === null) {
            return null;
        }

        if (
            $expr instanceof Expr\ClassConstFetch
            && $expr->name instanceof Identifier
            && $expr->name->toString() === 'class'
            && $expr->class instanceof Name
        ) {
            return $this->resolveNameToFqcn($expr->class, $namespace, $useMap);
        }

        if ($expr instanceof Scalar\String_) {
            $s = $expr->value;
            if (str_contains($s, '\\') && preg_match('/^\\\\?[\w\\\\]+$/', $s) === 1) {
                return ltrim($s, '\\');
            }
        }

        return null;
    }

    /**
     * @param  array<string, string>  $useMap
     */
    private function resolveNameToFqcn(Name $name, string $namespace, array $useMap): string
    {
        if ($name instanceof Name\FullyQualified) {
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
}
