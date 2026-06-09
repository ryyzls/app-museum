<?php

declare(strict_types=1);

namespace LaraMint\LaravelBrain\Analysis;

use LaraMint\LaravelBrain\Parser\PhpExtendsFqcnResolver;
use LaraMint\LaravelBrain\Parser\PhpFileParser;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class MethodDefinition
{
    public function __construct(
        public string $name,
        public array $dependencies, // varName => FQCN
        public ?Node\Stmt\ClassMethod $ast = null,
        public string $visibility = 'public',
        /** @var array<string, string>|null  use map from the declaring file; null = use ControllerDefinition.useMap */
        public ?array $methodUseMap = null,
        /** FQCN of the class that declares this method in source (for inherited actions). */
        public ?string $declaringFqcn = null,
    ) {}
}

class ControllerDefinition
{
    /**
     * @param  MethodDefinition[]  $methods
     * @param  array<string, string>  $useMap
     * @param  ControllerMiddleware[]  $middlewares
     * @param  list<string>  $ancestorFqcns  immediate parent first, then each ancestor (stops before Illuminate/Laravel)
     */
    public function __construct(
        public string $fqcn,
        public string $file,
        public array $constructorDeps, // varName => FQCN
        public array $methods,
        public array $useMap = [],
        public ?string $parent = null,
        public array $middlewares = [],
        public array $ancestorFqcns = [],
    ) {}
}

class ControllerAnalyzer
{
    private PhpFileParser $parser;

    private array $psr4Map = [];

    public function __construct()
    {
        $this->parser = new PhpFileParser;
    }

    public function getPsr4Map(): array
    {
        return $this->psr4Map;
    }

    /**
     * @param  RouteDefinition[]  $routes
     * @return array<string, ControllerDefinition> FQCN => ControllerDefinition
     */
    public function analyze(string $projectRoot, array $routes): array
    {
        $this->psr4Map = $this->buildPsr4Map($projectRoot);

        $controllerFqcns = [];
        foreach ($routes as $route) {
            if ($route->controller !== 'Closure' && $route->controller !== '') {
                $controllerFqcns[$route->controller] = true;
            }
        }

        $definitions = [];
        foreach (array_keys($controllerFqcns) as $fqcn) {
            $file = $this->resolveFile($fqcn, $projectRoot);
            if ($file === null || ! file_exists($file)) {
                continue;
            }

            $definition = $this->analyzeFile($fqcn, $file);
            if ($definition !== null) {
                $visited = [];
                $definitions[$fqcn] = $this->mergeInheritedMethods($definition, $projectRoot, $visited);
            }
        }

        return $definitions;
    }

    public function analyzeFile(string $fqcn, string $file): ?ControllerDefinition
    {
        $parsed = $this->parser->parse($file);
        if ($parsed['ast'] === null) {
            return null;
        }

        $expectedShort = str_contains($fqcn, '\\')
            ? substr($fqcn, strrpos($fqcn, '\\') + 1)
            : $fqcn;
        $fileNamespace = PhpExtendsFqcnResolver::namespaceFromAst($parsed['ast']);

        $traverser = new NodeTraverser;
        $visitor = new class($parsed['useMap'], $expectedShort, $fqcn) extends NodeVisitorAbstract
        {
            public array $constructorDeps = [];

            public array $methods = [];

            public ?Node $extendsNode = null;

            /** @var ControllerMiddleware[] */
            public array $middlewares = [];

            private array $useMap;

            private string $expectedShort;

            private string $classFqcn;

            public function __construct(array $useMap, string $expectedShort, string $classFqcn)
            {
                $this->useMap = $useMap;
                $this->expectedShort = $expectedShort;
                $this->classFqcn = $classFqcn;
            }

            public function enterNode(Node $node): ?int
            {
                if ($node instanceof Node\Stmt\Class_) {
                    if ($node->name === null || $node->name->toString() !== $this->expectedShort) {
                        return null;
                    }
                    $this->extendsNode = $node->extends;
                }
                if (! $node instanceof Node\Stmt\ClassMethod) {
                    return null;
                }

                $methodName = $node->name->toString();
                $deps = $this->extractTypedParams($node->params);
                $visibility = $this->extractVisibility($node);

                if ($methodName === '__construct') {
                    $this->constructorDeps = $deps;
                    // $this->middleware('auth')->only([...])->except([...])
                    $this->middlewares = array_merge(
                        $this->middlewares,
                        $this->extractConstructorMiddlewares($node)
                    );
                } elseif ($methodName === 'middleware' && $node->isStatic()) {
                    // HasMiddleware interface: public static function middleware(): array { return [...] }
                    $this->middlewares = array_merge(
                        $this->middlewares,
                        $this->extractStaticMiddlewareMethod($node)
                    );
                } else {
                    $this->methods[] = new MethodDefinition($methodName, $deps, $node, $visibility, null, $this->classFqcn);
                }

                return null;
            }

            // ── Middleware extraction ─────────────────────────────────────────

            /**
             * Handles: public static function middleware(): array { return ['auth', new Middleware(...)] }
             *
             * @return ControllerMiddleware[]
             */
            private function extractStaticMiddlewareMethod(Node\Stmt\ClassMethod $node): array
            {
                $result = [];
                foreach ($node->stmts ?? [] as $stmt) {
                    if (! $stmt instanceof Node\Stmt\Return_) {
                        continue;
                    }
                    if (! $stmt->expr instanceof Node\Expr\Array_) {
                        continue;
                    }
                    foreach ($stmt->expr->items as $item) {
                        if (! $item) {
                            continue;
                        }
                        $val = $item->value;
                        if ($val instanceof Node\Scalar\String_) {
                            $result[] = new ControllerMiddleware($val->value);
                        } elseif ($val instanceof Node\Expr\New_) {
                            $cm = $this->extractFromNewMiddleware($val);
                            if ($cm !== null) {
                                $result[] = $cm;
                            }
                        }
                    }
                }

                return $result;
            }

            /**
             * Parses: new Middleware('name', only: ['index'], except: ['store'])
             * Also handles positional: new Middleware('name', ['index'], ['store'])
             */
            private function extractFromNewMiddleware(Node\Expr\New_ $expr): ?ControllerMiddleware
            {
                $mwName = null;
                $only = null;
                $except = null;

                foreach ($expr->args as $i => $arg) {
                    if (! $arg instanceof Node\Arg) {
                        continue;
                    }
                    // Named argument
                    if ($arg->name instanceof Node\Identifier) {
                        $argName = $arg->name->toString();
                        if ($argName === 'middleware' || $argName === 'name') {
                            $mwName = $arg->value instanceof Node\Scalar\String_ ? $arg->value->value : null;
                        } elseif ($argName === 'only') {
                            $only = $this->extractStringArray($arg->value);
                        } elseif ($argName === 'except') {
                            $except = $this->extractStringArray($arg->value);
                        }
                    } else {
                        // Positional: first = name, second = only, third = except
                        if ($i === 0) {
                            $mwName = $arg->value instanceof Node\Scalar\String_ ? $arg->value->value : null;
                        } elseif ($i === 1) {
                            $only = $this->extractStringArray($arg->value);
                        } elseif ($i === 2) {
                            $except = $this->extractStringArray($arg->value);
                        }
                    }
                }

                return $mwName !== null ? new ControllerMiddleware($mwName, $only, $except) : null;
            }

            /**
             * Handles both styles inside __construct:
             *   $this->middleware('auth');
             *   $this->middleware('auth', ['only' => ['index']]);
             *   $this->middleware('auth')->only(['index'])->except(['store']);
             *
             * @return ControllerMiddleware[]
             */
            private function extractConstructorMiddlewares(Node\Stmt\ClassMethod $node): array
            {
                $result = [];
                foreach ($node->stmts ?? [] as $stmt) {
                    if (! $stmt instanceof Node\Stmt\Expression) {
                        continue;
                    }
                    $cm = $this->extractThisMiddlewareChain($stmt->expr);
                    if ($cm !== null) {
                        $result[] = $cm;
                    }
                }

                return $result;
            }

            /**
             * Walks a method-call chain bottom-up, collecting ->only() / ->except() modifiers,
             * until it finds the base $this->middleware('name') call.
             */
            private function extractThisMiddlewareChain(Node\Expr $expr): ?ControllerMiddleware
            {
                $only = null;
                $except = null;
                $current = $expr;

                while ($current instanceof Node\Expr\MethodCall) {
                    $name = $current->name instanceof Node\Identifier ? $current->name->toString() : null;

                    if ($name === 'only' && ! empty($current->args)) {
                        $only = $this->extractStringArray($current->args[0]->value);
                    } elseif ($name === 'except' && ! empty($current->args)) {
                        $except = $this->extractStringArray($current->args[0]->value);
                    } elseif ($name === 'middleware') {
                        // Base call must be on $this
                        if (! ($current->var instanceof Node\Expr\Variable && $current->var->name === 'this')) {
                            return null;
                        }

                        $firstArg = $current->args[0] ?? null;
                        if (! $firstArg instanceof Node\Arg || ! $firstArg->value instanceof Node\Scalar\String_) {
                            return null;
                        }
                        $mwName = $firstArg->value->value;

                        // Also support direct array form: $this->middleware('x', ['only' => [...]])
                        $secondArg = $current->args[1] ?? null;
                        if ($secondArg instanceof Node\Arg && $secondArg->value instanceof Node\Expr\Array_) {
                            foreach ($secondArg->value->items as $item) {
                                if (! $item || ! $item->key instanceof Node\Scalar\String_) {
                                    continue;
                                }
                                if ($item->key->value === 'only' && $only === null) {
                                    $only = $this->extractStringArray($item->value);
                                } elseif ($item->key->value === 'except' && $except === null) {
                                    $except = $this->extractStringArray($item->value);
                                }
                            }
                        }

                        return new ControllerMiddleware($mwName, $only, $except);
                    }

                    $current = $current->var;
                }

                return null;
            }

            private function extractStringArray(Node $node): array
            {
                if (! $node instanceof Node\Expr\Array_) {
                    return [];
                }
                $result = [];
                foreach ($node->items as $item) {
                    if ($item && $item->value instanceof Node\Scalar\String_) {
                        $result[] = $item->value->value;
                    }
                }

                return $result;
            }

            // ── Existing helpers ──────────────────────────────────────────────

            private function extractTypedParams(array $params): array
            {
                $deps = [];
                foreach ($params as $param) {
                    if (! $param instanceof Node\Param) {
                        continue;
                    }
                    $varName = $param->var instanceof Node\Expr\Variable ? $param->var->name : null;
                    $type = $param->type;
                    if ($varName === null || $type === null) {
                        continue;
                    }

                    $typeName = $this->resolveType($type);
                    if ($typeName) {
                        $deps[(string) $varName] = $typeName;
                    }
                }

                return $deps;
            }

            private function extractVisibility(Node\Stmt\ClassMethod $node): string
            {
                if ($node->isPrivate()) {
                    return 'private';
                }
                if ($node->isProtected()) {
                    return 'protected';
                }

                return 'public';
            }

            private function resolveType(Node $type): ?string
            {
                if ($type instanceof Node\Name) {
                    $name = $type->toString();

                    return $this->useMap[$name] ?? $name;
                }
                if ($type instanceof Node\NullableType) {
                    return $this->resolveType($type->type);
                }

                return null;
            }
        };

        $traverser->addVisitor($visitor);
        $traverser->traverse($parsed['ast']);

        $parentFqcn = PhpExtendsFqcnResolver::resolveExtends(
            $visitor->extendsNode,
            $fileNamespace,
            $parsed['useMap'],
        );

        return new ControllerDefinition(
            fqcn: $fqcn,
            file: $file,
            constructorDeps: $visitor->constructorDeps,
            methods: $visitor->methods,
            useMap: $parsed['useMap'],
            parent: $parentFqcn,
            middlewares: $visitor->middlewares,
            ancestorFqcns: [],
        );
    }

    /**
     * Merge instance methods, promoted constructor deps, and middleware from parents so thin
     * controllers (only overrides) still expose route actions and DI from abstract bases.
     *
     * @param  array<string, true>  $visited  recursion / cycle guard (by ref)
     */
    private function mergeInheritedMethods(
        ControllerDefinition $def,
        string $projectRoot,
        array &$visited,
    ): ControllerDefinition {
        $withMaps = $this->withExplicitMethodUseMaps($def);
        if ($withMaps->parent === null) {
            return $withMaps;
        }
        if (str_starts_with($withMaps->parent, 'Illuminate\\')
            || str_starts_with($withMaps->parent, 'Laravel\\')
            || str_starts_with($withMaps->parent, 'Livewire\\')) {
            return $withMaps;
        }
        if (isset($visited[$withMaps->fqcn])) {
            return $withMaps;
        }
        $visited[$withMaps->fqcn] = true;

        $parentFile = $this->resolveFile($withMaps->parent, $projectRoot);
        if ($parentFile === null || ! is_file($parentFile)) {
            unset($visited[$withMaps->fqcn]);

            return $withMaps;
        }
        $parentDef = $this->analyzeFile($withMaps->parent, $parentFile);
        if ($parentDef === null) {
            unset($visited[$withMaps->fqcn]);

            return $withMaps;
        }
        $parentMerged = $this->mergeInheritedMethods($parentDef, $projectRoot, $visited);
        unset($visited[$withMaps->fqcn]);

        $childDeclaresConstruct = $withMaps->constructorDeps !== []
            || $withMaps->middlewares !== [];

        $mergedDeps = $childDeclaresConstruct ? $withMaps->constructorDeps : $parentMerged->constructorDeps;
        $mergedMw = $withMaps->middlewares;
        if (! $childDeclaresConstruct && $parentMerged->middlewares !== []) {
            $mergedMw = array_merge($parentMerged->middlewares, $mergedMw);
        }

        $byName = [];
        foreach ($parentMerged->methods as $m) {
            if ($m->name === '__construct' || $m->visibility === 'private') {
                continue;
            }
            $byName[$m->name] = $m;
        }
        foreach ($withMaps->methods as $m) {
            if ($m->name === '__construct') {
                continue;
            }
            $byName[$m->name] = $m;
        }

        $ancestorFqcns = array_values(array_merge([$withMaps->parent], $parentMerged->ancestorFqcns));

        return new ControllerDefinition(
            fqcn: $withMaps->fqcn,
            file: $withMaps->file,
            constructorDeps: $mergedDeps,
            methods: array_values($byName),
            useMap: $withMaps->useMap,
            parent: $withMaps->parent,
            middlewares: $mergedMw,
            ancestorFqcns: $ancestorFqcns,
        );
    }

    private function withExplicitMethodUseMaps(ControllerDefinition $def): ControllerDefinition
    {
        $methods = [];
        foreach ($def->methods as $m) {
            $methods[] = $m->ast !== null && $m->methodUseMap === null
                ? new MethodDefinition(
                    $m->name,
                    $m->dependencies,
                    $m->ast,
                    $m->visibility,
                    $def->useMap,
                    $m->declaringFqcn ?? $def->fqcn,
                )
                : $m;
        }

        return new ControllerDefinition(
            fqcn: $def->fqcn,
            file: $def->file,
            constructorDeps: $def->constructorDeps,
            methods: $methods,
            useMap: $def->useMap,
            parent: $def->parent,
            middlewares: $def->middlewares,
            ancestorFqcns: $def->ancestorFqcns,
        );
    }

    private function buildPsr4Map(string $projectRoot): array
    {
        $composerJson = $projectRoot.'/composer.json';
        if (! file_exists($composerJson)) {
            return [];
        }

        $data = json_decode(file_get_contents($composerJson), true);
        $map = [];

        foreach (['autoload', 'autoload-dev'] as $section) {
            foreach ($data[$section]['psr-4'] ?? [] as $namespace => $paths) {
                $ns = rtrim($namespace, '\\');
                foreach ((array) $paths as $path) {
                    $map[$ns][] = rtrim($projectRoot.'/'.$path, '/');
                }
            }
        }

        // Scan nwidart/laravel-modules: each Modules/{Name}/composer.json may declare its own PSR-4 map.
        // Also infer the conventional mapping Modules\{Name} → Modules/{Name}/ for older module structures.
        $modulesDir = $projectRoot.'/Modules';
        if (is_dir($modulesDir)) {
            foreach (scandir($modulesDir) as $modName) {
                if ($modName === '.' || $modName === '..') {
                    continue;
                }
                $modPath = $modulesDir.'/'.$modName;
                if (! is_dir($modPath)) {
                    continue;
                }

                // Read module's own composer.json for explicit PSR-4 entries
                $modComposer = $modPath.'/composer.json';
                if (file_exists($modComposer)) {
                    $modData = json_decode(file_get_contents($modComposer), true);
                    foreach (['autoload', 'autoload-dev'] as $section) {
                        foreach ($modData[$section]['psr-4'] ?? [] as $namespace => $paths) {
                            $ns = rtrim($namespace, '\\');
                            foreach ((array) $paths as $path) {
                                $map[$ns][] = rtrim($modPath.'/'.$path, '/');
                            }
                        }
                    }
                }

                // Conventional fallback: Modules\{Name} → Modules/{Name}/
                // Covers both old structure (Http/ directly) and new structure (app/)
                $ns = 'Modules\\'.$modName;
                if (! isset($map[$ns])) {
                    // New nwidart structure: Modules/{Name}/app/
                    if (is_dir($modPath.'/app')) {
                        $map[$ns] = [$modPath.'/app'];
                    } else {
                        $map[$ns] = [$modPath];
                    }
                }
            }
        }

        return $map;
    }

    private function resolveFile(string $fqcn, string $projectRoot): ?string
    {
        foreach ($this->psr4Map as $namespace => $basePaths) {
            if (str_starts_with($fqcn, $namespace.'\\')) {
                $relative = substr($fqcn, strlen($namespace) + 1);
                $relativeFile = str_replace('\\', '/', $relative).'.php';
                foreach ((array) $basePaths as $basePath) {
                    $filePath = $basePath.'/'.$relativeFile;
                    if (file_exists($filePath)) {
                        return $filePath;
                    }
                }
            }
        }

        // Fallback: try common locations using full relative path
        $relative = str_replace('\\', '/', $fqcn).'.php';
        foreach (['app/Http/Controllers/', 'app/', 'src/'] as $prefix) {
            $path = $projectRoot.'/'.$prefix.$relative;
            if (file_exists($path)) {
                return $path;
            }
        }

        // Last resort: search by short class name inside app/, src/, and Modules/
        return $this->searchByClassName($fqcn, $projectRoot);
    }

    private function searchByClassName(string $fqcn, string $projectRoot): ?string
    {
        $shortName = str_contains($fqcn, '\\')
            ? substr($fqcn, strrpos($fqcn, '\\') + 1)
            : $fqcn;

        $filename = $shortName.'.php';

        $searchDirs = ['app', 'src'];

        // Also search Modules/ for nwidart-style module controllers
        $modulesDir = $projectRoot.'/Modules';
        if (is_dir($modulesDir)) {
            $searchDirs[] = 'Modules';
        }

        foreach ($searchDirs as $dir) {
            $base = $projectRoot.'/'.$dir;
            if (! is_dir($base)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($base, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if ($file->getFilename() === $filename) {
                    return $file->getPathname();
                }
            }
        }

        return null;
    }
}
