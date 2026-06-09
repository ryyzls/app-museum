<?php

declare(strict_types=1);

namespace LaraMint\LaravelBrain\Analysis;

use Illuminate\Support\Str;
use LaraMint\LaravelBrain\Parser\PhpFileParser;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class RouteDefinition
{
    public function __construct(
        public string $method,
        public string $uri,
        public string $controller,
        public string $action,
        public array $middlewares,
        public string $name,
        public string $file,
        public int $line,
        public string $tabGroup = 'default',
        /** @var Node\Expr\Closure|Node\Expr\ArrowFunction|null Inline closure AST for closure routes */
        public ?Node $closureNode = null,
        /** @var array<string,string>|null Use-map from the route file, needed to resolve short class names inside the closure */
        public ?array $closureUseMap = null,
    ) {}
}

class RouteAnalyzer
{
    private PhpFileParser $parser;

    /** @var string[] */
    private array $routePaths;

    private bool $autoDiscover;

    private bool $excludeVendor;

    /**
     * @param  string[]  $routePaths  Glob patterns relative to the project root.
     *                                Defaults to ['routes/*\/*.php'].
     * @param  bool  $autoDiscover  When true, skip AST parsing and pull routes
     *                              from the running app's Route::getRoutes().
     * @param  bool  $excludeVendor  When auto-discover is on, drop any route
     *                               whose handler file lives under vendor/.
     */
    public function __construct(
        array $routePaths = ['routes/*/*.php'],
        bool $autoDiscover = false,
        bool $excludeVendor = true,
    ) {
        $this->parser = new PhpFileParser;
        $this->routePaths = $routePaths ?: ['routes/*/*.php'];
        $this->autoDiscover = $autoDiscover;
        $this->excludeVendor = $excludeVendor;
    }

    /**
     * @return RouteDefinition[]
     */
    public function analyze(string $projectRoot): array
    {
        if ($this->autoDiscover) {
            return $this->discoverFromRouter($projectRoot);
        }

        $routes = [];
        $routeFiles = $this->findRouteFiles($projectRoot);

        // First pass: parse every file once and collect every statically
        // resolvable require/include target. Files pulled in via require are
        // parsed only through the require (with the enclosing group's context),
        // never standalone — otherwise they'd appear twice / without middleware.
        $parsedFiles = [];
        $includedFiles = [];
        foreach ($routeFiles as $file) {
            $parsed = $this->parser->parse($file);
            if ($parsed['ast'] === null) {
                continue;
            }
            $parsedFiles[$file] = $parsed;
            foreach ($this->collectIncludeTargets($parsed['ast'], $file) as $target) {
                $includedFiles[$target] = true;
            }
        }

        // Second pass: extract from files that are not pulled in elsewhere.
        // Recursion into require'd files (RouteAnalyzer::extractIncludedFile)
        // attaches their routes once, with the correct parent context.
        foreach ($parsedFiles as $file => $parsed) {
            $real = realpath($file);
            if ($real !== false && isset($includedFiles[$real])) {
                continue;
            }

            $routes = array_merge($routes, $this->extractRoutes($parsed['ast'], $parsed['useMap'], $file));
        }

        return $routes;
    }

    /**
     * Statically resolves `__DIR__`/`__FILE__`/string-literal/concat include
     * paths. Returns null for anything it can't resolve without executing code.
     */
    public function resolveIncludePath(Node $expr, string $currentFile): ?string
    {
        if ($expr instanceof Node\Scalar\String_) {
            return $expr->value;
        }
        if ($expr instanceof Node\Scalar\MagicConst\Dir) {
            return \dirname($currentFile);
        }
        if ($expr instanceof Node\Scalar\MagicConst\File) {
            return $currentFile;
        }
        if ($expr instanceof Node\Expr\BinaryOp\Concat) {
            $left = $this->resolveIncludePath($expr->left, $currentFile);
            $right = $this->resolveIncludePath($expr->right, $currentFile);
            if ($left === null || $right === null) {
                return null;
            }

            return $left.$right;
        }

        return null;
    }

    /**
     * Parse a require'd route file and extract its routes with the enclosing
     * group's prefix/middleware/namespace/controller context applied.
     *
     * @param  string[]  $prefixStack
     * @param  array<int, string[]>  $middlewareStack
     * @param  string[]  $namespaceStack
     * @param  string[]  $controllerStack
     * @param  array<string, true>  $visited
     * @return RouteDefinition[]
     *
     * @internal
     */
    public function extractIncludedFile(
        string $absPath,
        array $prefixStack,
        array $middlewareStack,
        array $namespaceStack,
        array $controllerStack,
        array $visited,
    ): array {
        $parsed = $this->parser->parse($absPath);
        if ($parsed['ast'] === null) {
            return [];
        }

        return $this->extractRoutes(
            $parsed['ast'],
            $parsed['useMap'],
            $absPath,
            $prefixStack,
            $middlewareStack,
            $namespaceStack,
            $controllerStack,
            $visited,
        );
    }

    /**
     * Collects realpaths of every statically resolvable require/include target
     * within a parsed file.
     *
     * @param  Node\Stmt[]  $ast
     * @return string[]
     */
    private function collectIncludeTargets(array $ast, string $file): array
    {
        $analyzer = $this;
        $traverser = new NodeTraverser;
        $visitor = new class($analyzer, $file) extends NodeVisitorAbstract
        {
            /** @var string[] */
            public array $targets = [];

            private RouteAnalyzer $analyzer;

            private string $file;

            public function __construct(RouteAnalyzer $analyzer, string $file)
            {
                $this->analyzer = $analyzer;
                $this->file = $file;
            }

            public function enterNode(Node $node): ?int
            {
                if ($node instanceof Node\Expr\Include_) {
                    $path = $this->analyzer->resolveIncludePath($node->expr, $this->file);
                    if ($path !== null) {
                        $real = realpath($path);
                        if ($real !== false) {
                            $this->targets[] = $real;
                        }
                    }
                }

                return null;
            }
        };

        $traverser->addVisitor($visitor);
        $traverser->traverse($ast);

        return $visitor->targets;
    }

    /**
     * Build RouteDefinition[] from the running app's RouteCollection,
     * picking up everything packages and providers register at runtime.
     *
     * @return RouteDefinition[]
     */
    private function discoverFromRouter(string $projectRoot): array
    {
        if (! function_exists('app')) {
            return [];
        }

        $router = app('router');
        $collection = $router->getRoutes();

        $resolvedRoot = realpath($projectRoot);
        $vendorPrefix = ($resolvedRoot !== false ? $resolvedRoot : rtrim($projectRoot, '/')).'/vendor/';

        // Cache per-file AST so multiple closure routes in the same file parse it once.
        $fileCache = [];

        $routes = [];
        foreach ($collection->getRoutes() as $route) {
            $actionName = $route->getActionName();
            $uses = $route->getAction('uses');

            $closureNode = null;
            $closureUseMap = null;

            if ($actionName === 'Closure') {
                $controller = '';
                $actionMethod = 'closure';

                if ($uses instanceof \Closure) {
                    [$closureNode, $closureUseMap] = $this->locateClosureNode($uses, $fileCache);
                }
            } elseif (str_contains($actionName, '@')) {
                [$controller, $actionMethod] = explode('@', $actionName, 2);
            } else {
                $controller = $actionName;
                $actionMethod = '__invoke';
            }

            // Always drop this package's own UI/API routes (e.g. /_laravel-brain/*).
            if ($controller !== '' && str_starts_with($controller, 'LaraMint\\LaravelBrain\\')) {
                continue;
            }

            if ($this->excludeVendor && $this->isVendorRoute($controller, $uses, $vendorPrefix)) {
                continue;
            }

            $uri = '/'.ltrim($route->uri(), '/');
            $name = $route->getName() ?? '';
            $middlewares = array_values(array_unique(
                array_filter($route->gatherMiddleware(), 'is_string')
            ));

            foreach ($route->methods() as $method) {
                if (strtoupper($method) === 'HEAD') {
                    continue;
                }

                $upperMethod = strtoupper($method);
                $routes[] = new RouteDefinition(
                    method: $upperMethod,
                    uri: $uri,
                    controller: $controller,
                    action: $actionMethod,
                    middlewares: $middlewares,
                    name: $name,
                    file: '',
                    line: 0,
                    tabGroup: $upperMethod.' '.$uri,
                    closureNode: $closureNode,
                    closureUseMap: $closureUseMap,
                );
            }
        }

        return $routes;
    }

    /**
     * Decide if a route's handler lives under the project's vendor/ directory.
     * Controller routes reflect the class file; closure routes reflect the closure.
     * Routes we can't resolve (missing class, dynamic handlers) are kept.
     */
    private function isVendorRoute(string $controller, mixed $uses, string $vendorPrefix): bool
    {
        try {
            if ($controller !== '' && class_exists($controller)) {
                $file = (new \ReflectionClass($controller))->getFileName();

                return is_string($file) && str_starts_with($file, $vendorPrefix);
            }

            if ($uses instanceof \Closure) {
                $file = (new \ReflectionFunction($uses))->getFileName();

                return is_string($file) && str_starts_with($file, $vendorPrefix);
            }
        } catch (\ReflectionException) {
            // fall through
        }

        return false;
    }

    /**
     * Reflect a route closure, parse the file it lives in once, and locate the
     * matching Closure/ArrowFunction AST node so the lifecycle tracer can walk
     * inside the closure body (same shape as AST-mode closure routes).
     *
     * @param  array<string, array{ast: Node\Stmt[]|null, useMap: array<string,string>}>  $fileCache
     * @return array{0: Node\Expr\Closure|Node\Expr\ArrowFunction|null, 1: array<string,string>|null}
     */
    private function locateClosureNode(\Closure $closure, array &$fileCache): array
    {
        try {
            $ref = new \ReflectionFunction($closure);
        } catch (\ReflectionException) {
            return [null, null];
        }

        $file = $ref->getFileName();
        $startLine = $ref->getStartLine();
        if (! is_string($file) || $file === '' || ! is_int($startLine)) {
            return [null, null];
        }

        if (! isset($fileCache[$file])) {
            $fileCache[$file] = $this->parser->parse($file);
        }
        $parsed = $fileCache[$file];
        if ($parsed['ast'] === null) {
            return [null, null];
        }

        $finder = new class($startLine) extends NodeVisitorAbstract
        {
            public Node\Expr\Closure|Node\Expr\ArrowFunction|null $match = null;

            public function __construct(private int $targetLine) {}

            public function enterNode(Node $node): ?int
            {
                if (($node instanceof Node\Expr\Closure || $node instanceof Node\Expr\ArrowFunction)
                    && $node->getStartLine() === $this->targetLine) {
                    $this->match = $node;
                }

                return null;
            }
        };

        $traverser = new NodeTraverser;
        $traverser->addVisitor($finder);
        $traverser->traverse($parsed['ast']);

        if ($finder->match === null) {
            return [null, null];
        }

        return [$finder->match, $parsed['useMap']];
    }

    private function findRouteFiles(string $projectRoot): array
    {
        $root = rtrim($projectRoot, '/');
        $files = [];

        foreach ($this->routePaths as $pattern) {
            $baseDir = $this->resolveBaseDir($root, $pattern);

            if (! is_dir($baseDir)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($baseDir, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $entry) {
                if ($entry->isFile() && $entry->getExtension() === 'php') {
                    $files[] = $entry->getPathname();
                }
            }
        }

        return array_unique($files);
    }

    /**
     * Extracts the fixed directory prefix from a glob pattern.
     *
     * For 'routes/*\/*.php'  → '{root}/routes'
     * For '*\/*\/*.php'      → '{root}'
     * For 'app/routes/*.php' → '{root}/app/routes'
     */
    private function resolveBaseDir(string $root, string $pattern): string
    {
        $segments = explode('/', ltrim($pattern, '/'));
        $fixed = [];

        foreach ($segments as $segment) {
            if (str_contains($segment, '*') || str_contains($segment, '?') || str_contains($segment, '[')) {
                break;
            }
            $fixed[] = $segment;
        }

        // Drop trailing filename segment (e.g. '*.php') if all segments were literal
        if (! empty($fixed) && str_ends_with(end($fixed), '.php')) {
            array_pop($fixed);
        }

        $subPath = implode('/', $fixed);

        return $subPath !== '' ? $root.'/'.$subPath : $root;
    }

    /**
     * Path for one expanded {@see ResourceRegistrar} action.
     *
     * @internal
     */
    public function resourceUriForAction(string $prefixedBaseUri, string $wildcard, string $actionMethod): string
    {
        $base = rtrim($prefixedBaseUri, '/');
        $param = '{'.$wildcard.'}';

        return match ($actionMethod) {
            'index' => $base,
            'create' => $base.'/create',
            'store' => $base,
            'show', 'update', 'destroy' => $base.'/'.$param,
            'edit' => $base.'/'.$param.'/edit',
            default => $base,
        };
    }

    /**
     * Mirrors {@see ResourceRegistrar::getResourceWildcard()}.
     *
     * @internal
     */
    public function resourceRouteWildcard(string $value): string
    {
        if (str_contains($value, '.')) {
            $segments = explode('.', $value);
            $value = end($segments) ?: $value;
        }
        if (str_contains($value, '/')) {
            $segments = explode('/', $value);
            $value = end($segments) ?: $value;
        }
        $value = str_replace(['{', '}'], '', $value);
        if ($value === '') {
            return 'id';
        }
        if (str_contains($value, '-')) {
            $value = Str::camel($value);
        }

        return Str::camel(Str::singular($value));
    }

    /**
     * @param  Node\Stmt[]  $ast
     * @param  array<string, string>  $useMap
     * @param  string[]  $seedPrefix  Prefix stack inherited from an enclosing require
     * @param  array<int, string[]>  $seedMiddleware  Middleware stack inherited from an enclosing require
     * @param  string[]  $seedNamespace  Namespace stack inherited from an enclosing require
     * @param  string[]  $seedController  Controller stack inherited from an enclosing require
     * @param  array<string, true>  $visited  Realpaths already being parsed (include-cycle guard)
     * @return RouteDefinition[]
     */
    private function extractRoutes(
        array $ast,
        array $useMap,
        string $file,
        array $seedPrefix = [],
        array $seedMiddleware = [],
        array $seedNamespace = [],
        array $seedController = [],
        array $visited = [],
    ): array {
        $traverser = new NodeTraverser;

        $visitor = new class($useMap, $file, $this, $seedPrefix, $seedMiddleware, $seedNamespace, $seedController, $visited) extends NodeVisitorAbstract
        {
            public array $routes = [];

            private array $prefixStack = [];

            private array $middlewareStack = [];

            private array $namespaceStack = [];

            private array $controllerStack = [];

            private array $useMap;

            private string $file;

            private RouteAnalyzer $routeAnalyzer;

            /** @var array<string, true> */
            private array $visited;

            private const HTTP_METHODS = ['get', 'post', 'put', 'patch', 'delete', 'options', 'any'];

            /**
             * Methods that Laravel allows chaining AFTER a route definition:
             * Route::get(...)->middleware(...)->name(...)->where(...)
             * When the analyzer encounters one of these as the outermost call it must
             * walk down to find the HTTP method and collect middleware/name from the chain.
             */
            private const POST_ROUTE_CHAIN_METHODS = ['middleware', 'withoutMiddleware', 'name', 'where', 'defaults', 'scopeBindings', 'withTrashed', 'missing', 'can'];

            public function __construct(
                array $useMap,
                string $file,
                RouteAnalyzer $routeAnalyzer,
                array $seedPrefix,
                array $seedMiddleware,
                array $seedNamespace,
                array $seedController,
                array $visited,
            ) {
                $this->useMap = $useMap;
                $this->file = $file;
                $this->routeAnalyzer = $routeAnalyzer;
                $this->prefixStack = $seedPrefix;
                $this->middlewareStack = $seedMiddleware;
                $this->namespaceStack = $seedNamespace;
                $this->controllerStack = $seedController;
                $this->visited = $visited;
            }

            public function enterNode(Node $node): ?int
            {
                // require/include of another route file (e.g. require __DIR__.'/inc/notes.php';)
                // — recurse into it carrying the current group context (prefix/middleware/namespace/controller).
                if ($node instanceof Node\Expr\Include_) {
                    $this->handleInclude($node);

                    return null;
                }

                // StaticCall: Route::get(), Route::group(), Route::resource()
                if ($node instanceof Node\Expr\StaticCall) {
                    $class = $this->resolveClass($node->class);
                    if ($class !== 'Route') {
                        return null;
                    }

                    $methodName = $node->name instanceof Node\Identifier ? $node->name->toString() : null;
                    if ($methodName === null) {
                        return null;
                    }

                    if (in_array($methodName, self::HTTP_METHODS, true)) {
                        $this->handleHttpRoute($node, $methodName);
                    } elseif ($methodName === 'group') {
                        $this->enterGroupFromStaticCall($node);
                    } elseif (in_array($methodName, ['resource', 'apiResource'], true)) {
                        $this->handleResource($node, $methodName);
                    } elseif ($methodName === 'livewire') {
                        $this->handleLivewireRoute($node);
                    }

                    return null;
                }

                // MethodCall: Route::middleware([...])->group(), Route::prefix('x')->group(), Route::namespace('x')->group()
                // OR: Route::middleware([...])->get('/test', ...)
                // OR: Route::get('/test', ...)->middleware('ability:...') — post-route chain
                if ($node instanceof Node\Expr\MethodCall) {
                    $methodName = $node->name instanceof Node\Identifier ? $node->name->toString() : null;
                    if ($methodName === 'group') {
                        $this->enterGroupFromMethodChain($node);
                    } elseif (in_array($methodName, self::HTTP_METHODS, true)) {
                        $this->handleHttpRoute($node, $methodName);
                    } elseif (in_array($methodName, ['resource', 'apiResource'], true)) {
                        $this->handleResource($node, $methodName);
                    } elseif ($methodName === 'livewire') {
                        $this->handleLivewireRoute($node);
                    } elseif (in_array($methodName, self::POST_ROUTE_CHAIN_METHODS, true)) {
                        // Pattern: Route::get(...)->middleware('ability:...') or ->name('...')->middleware('...')
                        // The HTTP route call is below in the AST; collect post-chain middleware and handle it.
                        if ($this->tryHandlePostChainedRoute($node)) {
                            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
                        }
                    }
                }

                return null;
            }

            public function leaveNode(Node $node): ?int
            {
                $methodName = null;
                if ($node instanceof Node\Expr\StaticCall || $node instanceof Node\Expr\MethodCall) {
                    $methodName = $node->name instanceof Node\Identifier ? $node->name->toString() : null;
                }

                if ($methodName === 'group') {
                    if (! empty($this->prefixStack)) {
                        array_pop($this->prefixStack);
                    }
                    if (! empty($this->middlewareStack)) {
                        array_pop($this->middlewareStack);
                    }
                    if (! empty($this->namespaceStack)) {
                        array_pop($this->namespaceStack);
                    }
                    if (! empty($this->controllerStack)) {
                        array_pop($this->controllerStack);
                    }
                }

                return null;
            }

            /**
             * @param  string[]  $extraMiddlewares  Middleware collected from post-route chaining
             *                                      (e.g. Route::get(...)->middleware('ability:...'))
             */
            private function handleHttpRoute(Node\Expr\StaticCall|Node\Expr\MethodCall $node, string $method, array $extraMiddlewares = []): void
            {
                $uri = $this->extractString($node->args[0] ?? null);
                if ($uri === null) {
                    return;
                }

                // If it's a MethodCall, we might have prefixes/middlewares/controller in the chain
                $chainPrefix = '';
                $chainMiddlewares = [];
                $chainNamespace = '';
                $chainController = '';
                if ($node instanceof Node\Expr\MethodCall) {
                    $this->walkChain($node->var, $chainPrefix, $chainMiddlewares, $chainNamespace, $chainController);
                }

                $stackController = end($this->controllerStack) ?: '';
                $controllerContext = $chainController !== '' ? $chainController : $stackController;

                [$controller, $actionMethod, $closureNode] = $this->extractAction($node->args[1] ?? null, $controllerContext);

                if ($controller !== 'Closure' && $controller !== '' && ! str_starts_with($controller, '\\')) {
                    $namespace = implode('\\', array_filter($this->namespaceStack));
                    if ($chainNamespace) {
                        $namespace = $namespace ? $namespace.'\\'.$chainNamespace : $chainNamespace;
                    }
                    if ($namespace) {
                        $controller = rtrim($namespace, '\\').'\\'.ltrim($controller, '\\');
                    }
                }

                $fullUri = implode('', $this->prefixStack).$chainPrefix.'/'.ltrim($uri, '/');
                $fullUri = '/'.ltrim($fullUri, '/');

                $middlewares = array_merge(
                    array_merge(...$this->middlewareStack ?: [[]]),
                    $chainMiddlewares,
                    $extraMiddlewares
                );

                $this->routes[] = new RouteDefinition(
                    method: strtoupper($method),
                    uri: $fullUri,
                    controller: $controller,
                    action: $actionMethod,
                    middlewares: array_unique($middlewares),
                    name: '',
                    file: $this->file,
                    line: $node->getStartLine(),
                    tabGroup: strtoupper($method).' '.$fullUri,
                    closureNode: $closureNode,
                    closureUseMap: $closureNode !== null ? $this->useMap : null,
                );
            }

            /**
             * Handles Route::livewire('/uri', ComponentClass::class) — a Livewire v2 macro
             * that registers a GET route pointing to a Livewire component.
             *
             * @param  string[]  $extraMiddlewares  Middleware collected from post-route chaining
             *                                      (e.g. Route::livewire(...)->middleware('auth'))
             */
            private function handleLivewireRoute(Node\Expr\StaticCall|Node\Expr\MethodCall $node, array $extraMiddlewares = []): void
            {
                $uri = $this->extractString($node->args[0] ?? null);
                if ($uri === null) {
                    return;
                }

                $componentArg = $node->args[1] ?? null;
                $controller = '';
                if ($componentArg !== null) {
                    $val = $componentArg instanceof Node\Arg ? $componentArg->value : $componentArg;
                    $controller = $this->extractClassRef($val);
                    if ($controller === '') {
                        $controller = $this->extractString($componentArg) ?? '';
                    }
                }

                $chainPrefix = '';
                $chainMiddlewares = [];
                $chainNamespace = '';
                if ($node instanceof Node\Expr\MethodCall) {
                    $this->walkChain($node->var, $chainPrefix, $chainMiddlewares, $chainNamespace);
                }

                $fullUri = implode('', $this->prefixStack).$chainPrefix.'/'.ltrim($uri, '/');
                $fullUri = '/'.ltrim($fullUri, '/');

                $middlewares = array_merge(
                    array_merge(...$this->middlewareStack ?: [[]]),
                    $chainMiddlewares,
                    $extraMiddlewares,
                );

                $this->routes[] = new RouteDefinition(
                    method: 'GET',
                    uri: $fullUri,
                    controller: $controller,
                    action: 'render',
                    middlewares: array_unique($middlewares),
                    name: '',
                    file: $this->file,
                    line: $node->getStartLine(),
                    tabGroup: 'GET '.$fullUri,
                );
            }

            /**
             * Handles routes written as Route::get(...)->middleware('ability:...') where
             * the HTTP method call is the var of one or more post-route chain calls.
             *
             * Walks down through the MethodCall chain collecting middleware, name, etc.
             * until it finds the base HTTP route call (StaticCall or MethodCall), then
             * registers the route with all collected post-chain middleware merged in.
             *
             * Returns true when a route was registered (caller should skip children).
             */
            private function tryHandlePostChainedRoute(Node\Expr\MethodCall $outerNode): bool
            {
                $postMiddlewares = [];
                $current = $outerNode;

                // Walk DOWN through post-route chain methods collecting middleware
                while ($current instanceof Node\Expr\MethodCall) {
                    $name = $current->name instanceof Node\Identifier ? $current->name->toString() : null;

                    if ($name === 'middleware' && ! empty($current->args)) {
                        $postMiddlewares = array_merge(
                            $postMiddlewares,
                            $this->extractMiddlewareList($current->args[0]->value)
                        );
                    }

                    // If we've reached the HTTP method call (e.g. ->get(), ->post()) stop here
                    if ($name !== null && in_array($name, self::HTTP_METHODS, true)) {
                        $this->handleHttpRoute($current, $name, $postMiddlewares);

                        return true;
                    }

                    $current = $current->var;
                }

                // Base of the chain is a StaticCall — Route::get('/brands', [...]) or Route::livewire(...)
                if ($current instanceof Node\Expr\StaticCall) {
                    $class = $this->resolveClass($current->class);
                    $name = $current->name instanceof Node\Identifier ? $current->name->toString() : null;

                    if ($class === 'Route' && $name !== null) {
                        if (in_array($name, self::HTTP_METHODS, true)) {
                            $this->handleHttpRoute($current, $name, $postMiddlewares);

                            return true;
                        }

                        if ($name === 'livewire') {
                            $this->handleLivewireRoute($current, $postMiddlewares);

                            return true;
                        }
                    }
                }

                return false;
            }

            private function handleResource(Node\Expr\StaticCall|Node\Expr\MethodCall $node, string $type): void
            {
                $uri = $this->extractString($node->args[0] ?? null);
                $controllerArg = $node->args[1] ?? null;
                if ($uri === null || $controllerArg === null) {
                    return;
                }

                $controllerFqcn = $this->extractClassRef($controllerArg->value);

                // Chain handling
                $chainPrefix = '';
                $chainMiddlewares = [];
                $chainNamespace = '';
                if ($node instanceof Node\Expr\MethodCall) {
                    $this->walkChain($node->var, $chainPrefix, $chainMiddlewares, $chainNamespace);
                }

                if ($controllerFqcn !== '' && ! str_starts_with($controllerFqcn, '\\')) {
                    $namespace = implode('\\', array_filter($this->namespaceStack));
                    if ($chainNamespace) {
                        $namespace = $namespace ? $namespace.'\\'.$chainNamespace : $chainNamespace;
                    }
                    if ($namespace) {
                        $controllerFqcn = rtrim($namespace, '\\').'\\'.ltrim($controllerFqcn, '\\');
                    }
                }
                $fullUri = implode('', $this->prefixStack).$chainPrefix.'/'.ltrim($uri, '/');
                $fullUri = '/'.ltrim($fullUri, '/');
                $middlewares = array_merge(
                    array_merge(...$this->middlewareStack ?: [[]]),
                    $chainMiddlewares
                );

                $methods = $type === 'apiResource'
                    ? ['GET:index', 'POST:store', 'GET:show', 'PUT:update', 'PATCH:update', 'DELETE:destroy']
                    : ['GET:index', 'GET:create', 'POST:store', 'GET:show', 'GET:edit', 'PUT:update', 'PATCH:update', 'DELETE:destroy'];

                $wildcard = $this->routeAnalyzer->resourceRouteWildcard($uri);
                foreach ($methods as $spec) {
                    [$httpMethod, $actionMethod] = explode(':', $spec);
                    $routeUri = $this->routeAnalyzer->resourceUriForAction($fullUri, $wildcard, $actionMethod);
                    $this->routes[] = new RouteDefinition(
                        method: $httpMethod,
                        uri: $routeUri,
                        controller: $controllerFqcn,
                        action: $actionMethod,
                        middlewares: array_unique($middlewares),
                        name: '',
                        file: $this->file,
                        line: $node->getStartLine(),
                        tabGroup: $httpMethod.' '.$routeUri,
                    );
                }
            }

            private function enterGroupFromStaticCall(Node\Expr\StaticCall $node): void
            {
                $prefix = '';
                $middlewares = [];
                $namespace = '';

                foreach ($node->args as $arg) {
                    if (! $arg->value instanceof Node\Expr\Array_) {
                        continue;
                    }
                    foreach ($arg->value->items as $item) {
                        if ($item === null) {
                            continue;
                        }
                        $key = $item->key instanceof Node\Scalar\String_ ? $item->key->value : null;
                        if ($key === 'prefix') {
                            $prefix = $this->extractString($item) ?? '';
                        } elseif ($key === 'middleware') {
                            $middlewares = $this->extractMiddlewareList($item->value);
                        } elseif ($key === 'namespace') {
                            $namespace = $this->extractString($item) ?? '';
                        }
                    }
                }

                $this->prefixStack[] = $prefix ? '/'.ltrim($prefix, '/') : '';
                $this->middlewareStack[] = $middlewares;
                $this->namespaceStack[] = $namespace;
                $this->controllerStack[] = '';
            }

            private function enterGroupFromMethodChain(Node\Expr\MethodCall $node): void
            {
                // Walk up the chain: ->group() called on ->middleware([...])->prefix(...)->controller(...) etc.
                $prefix = '';
                $middlewares = [];
                $namespace = '';
                $controller = '';
                $this->walkChain($node->var, $prefix, $middlewares, $namespace, $controller);

                $this->prefixStack[] = $prefix ? '/'.ltrim($prefix, '/') : '';
                $this->middlewareStack[] = $middlewares;
                $this->namespaceStack[] = $namespace;
                $this->controllerStack[] = $controller;
            }

            private function walkChain(Node $node, string &$prefix, array &$middlewares, string &$namespace, string &$controller = ''): void
            {
                if ($node instanceof Node\Expr\StaticCall || $node instanceof Node\Expr\MethodCall) {
                    $method = $node->name instanceof Node\Identifier ? $node->name->toString() : null;

                    if ($method === 'middleware' && ! empty($node->args)) {
                        $middlewares = array_merge($middlewares, $this->extractMiddlewareList($node->args[0]->value));
                    } elseif ($method === 'prefix' && ! empty($node->args)) {
                        $prefix = $this->extractString($node->args[0]) ?? '';
                    } elseif ($method === 'namespace' && ! empty($node->args)) {
                        $namespace = $this->extractString($node->args[0]) ?? '';
                    } elseif ($method === 'controller' && ! empty($node->args)) {
                        $controller = $this->extractClassRef($node->args[0]->value);
                    }

                    // Walk the callee
                    $callee = $node instanceof Node\Expr\MethodCall ? $node->var : $node->class;
                    $this->walkChain($callee, $prefix, $middlewares, $namespace, $controller);
                }
            }

            private function handleInclude(Node\Expr\Include_ $node): void
            {
                $target = $this->routeAnalyzer->resolveIncludePath($node->expr, $this->file);
                if ($target === null) {
                    return;
                }

                $real = realpath($target);
                if ($real === false || ! is_file($real) || ! is_readable($real)) {
                    return;
                }

                // Guard against include cycles.
                if (isset($this->visited[$real])) {
                    return;
                }
                $selfReal = realpath($this->file);
                $visited = $this->visited;
                if ($selfReal !== false) {
                    $visited[$selfReal] = true;
                }
                $visited[$real] = true;

                $included = $this->routeAnalyzer->extractIncludedFile(
                    $real,
                    $this->prefixStack,
                    $this->middlewareStack,
                    $this->namespaceStack,
                    $this->controllerStack,
                    $visited,
                );

                foreach ($included as $route) {
                    $this->routes[] = $route;
                }
            }

            /**
             * @return array{0: string, 1: string, 2: Node\Expr\Closure|Node\Expr\ArrowFunction|null}
             */
            private function extractAction(?Node $node, string $controllerContext = ''): array
            {
                if ($node === null) {
                    return ['', '', null];
                }
                $value = $node instanceof Node\Arg ? $node->value : $node;

                // [Controller::class, 'method']
                if ($value instanceof Node\Expr\Array_ && count($value->items) >= 2) {
                    $classItem = $value->items[0];
                    $methodItem = $value->items[1];
                    if ($classItem && $methodItem) {
                        $controller = $this->extractClassRef($classItem->value);
                        $actionMethod = $this->extractString($methodItem) ?? '';

                        return [$controller, $actionMethod, null];
                    }
                }

                // 'Controller@method' OR 'Controller' (for __invoke)
                if ($value instanceof Node\Scalar\String_) {
                    if (str_contains($value->value, '@')) {
                        $parts = explode('@', $value->value, 2);

                        return [$parts[0], $parts[1], null];
                    }

                    // Inside Route::controller(X::class)->group(...) a bare string is a
                    // method name on the group controller, not an invokable controller.
                    if ($controllerContext !== '') {
                        return [$controllerContext, $value->value, null];
                    }

                    return [$value->value, '__invoke', null];
                }

                // Controller::class (for __invoke)
                $classRef = $this->extractClassRef($value);
                if ($classRef !== '') {
                    return [$classRef, '__invoke', null];
                }

                // Closure routes
                if ($value instanceof Node\Expr\Closure || $value instanceof Node\Expr\ArrowFunction) {
                    return ['Closure', '__invoke', $value];
                }

                return ['', '', null];
            }

            private function extractClassRef(Node $node): string
            {
                if ($node instanceof Node\Expr\ClassConstFetch) {
                    $class = $node->class;
                    if ($class instanceof Node\Name) {
                        $name = $class->toString();

                        // Return FQCN from use-map, not the short name
                        return $this->useMap[$name] ?? $name;
                    }
                }
                if ($node instanceof Node\Scalar\String_) {
                    return $node->value;
                }

                return '';
            }

            private function extractMiddlewareList(Node $node): array
            {
                if ($node instanceof Node\Scalar\String_) {
                    return [$node->value];
                }
                if ($node instanceof Node\Expr\ClassConstFetch) {
                    $resolved = $this->extractClassRef($node);

                    return $resolved !== '' ? [$resolved] : [];
                }
                if ($node instanceof Node\Expr\Array_) {
                    $result = [];
                    foreach ($node->items as $item) {
                        if (! $item) {
                            continue;
                        }
                        if ($item->value instanceof Node\Scalar\String_) {
                            $result[] = $item->value->value;
                        } elseif ($item->value instanceof Node\Expr\ClassConstFetch) {
                            $resolved = $this->extractClassRef($item->value);
                            if ($resolved !== '') {
                                $result[] = $resolved;
                            }
                        }
                    }

                    return $result;
                }

                return [];
            }

            private function extractString(?Node $node): ?string
            {
                if ($node === null) {
                    return null;
                }
                $value = $node instanceof Node\Arg ? $node->value : $node;
                if ($value instanceof Node\Scalar\String_) {
                    return $value->value;
                }
                if ($value instanceof Node\Expr\ArrayItem && $value->value instanceof Node\Scalar\String_) {
                    return $value->value->value;
                }

                return null;
            }

            private function resolveClass(Node $node): string
            {
                if ($node instanceof Node\Name) {
                    $name = $node->toString();
                    $resolved = $this->useMap[$name] ?? $name;
                    // Normalize to short name for Route facade matching
                    $parts = explode('\\', $resolved);

                    return end($parts);
                }

                return '';
            }
        };

        $traverser->addVisitor($visitor);
        $traverser->traverse($ast);

        return $visitor->routes;
    }
}
