<?php

declare(strict_types=1);

namespace LaraMint\LaravelBrain\Analysis;

use LaraMint\LaravelBrain\Parser\PhpFileParser;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

// ── Data classes ──────────────────────────────────────────────────────────────

class FilamentPanelDefinition
{
    public function __construct(
        public string $id,
        public string $fqcn,
        public string $file,
        public string $path,
        /** @var string[] FQCNs (explicit ->resources([...]) array) */
        public array $resources,
        /** @var string[] FQCNs (explicit ->pages([...]) array) */
        public array $pages,
        /** @var string[] FQCNs (explicit ->widgets([...]) array) */
        public array $widgets,
        /** @var string[] namespace prefixes from ->discoverResources(for: '...') */
        public array $discoverResourcesFor = [],
        /** @var string[] namespace prefixes from ->discoverPages(for: '...') */
        public array $discoverPagesFor = [],
        /** @var string[] namespace prefixes from ->discoverWidgets(for: '...') */
        public array $discoverWidgetsFor = [],
    ) {}
}

class FilamentResourceDefinition
{
    public function __construct(
        public string $fqcn,
        public string $file,
        public string $modelFqcn,
        public string $panelId,
        /** @var array<string, string> pageKey => FQCN */
        public array $pages,
        /** @var string[] FQCNs */
        public array $relations,
        /** Computed Filament URL, e.g. /admin_dashboard/branches */
        public string $route = '',
        /** @var array<string, array{string, string}> pageKey => [method, path] */
        public array $pageRoutes = [],
        /** Explicit $slug declared on the resource class, e.g. 'shop/products' */
        public string $slug = '',
    ) {}
}

class FilamentPageDefinition
{
    public function __construct(
        public string $fqcn,
        public string $file,
        public string $parentResourceFqcn,
        public string $pageType,
        /** @var string[] method names declared directly in this page class */
        public array $methods = [],
        /** Explicit $slug property on the page class (empty = derive from class name) */
        public string $slug = '',
        /** Panel this custom page belongs to (empty for resource pages) */
        public string $panelId = '',
        /** Computed Filament URL for custom pages, e.g. /app/settings */
        public string $route = '',
    ) {}
}

class FilamentWidgetDefinition
{
    public function __construct(
        public string $fqcn,
        public string $file,
        public string $widgetType,
    ) {}
}

class FilamentRelationManagerDefinition
{
    public function __construct(
        public string $fqcn,
        public string $file,
        public string $parentResourceFqcn,
        public string $relationship,
    ) {}
}

// ── Analyzer ──────────────────────────────────────────────────────────────────

class FilamentAnalyzer
{
    private PhpFileParser $parser;

    public function __construct()
    {
        $this->parser = new PhpFileParser;
    }

    /**
     * @return array{
     *   detected: bool,
     *   panels: FilamentPanelDefinition[],
     *   resources: FilamentResourceDefinition[],
     *   pages: FilamentPageDefinition[],
     *   widgets: FilamentWidgetDefinition[],
     *   relationManagers: FilamentRelationManagerDefinition[],
     * }
     */
    public function analyze(string $projectRoot): array
    {
        $empty = [
            'detected' => false,
            'panels' => [],
            'resources' => [],
            'pages' => [],
            'widgets' => [],
            'relationManagers' => [],
        ];

        if (! is_dir($projectRoot.'/vendor/filament/filament')) {
            return $empty;
        }

        $panels = $this->scanPanels($projectRoot);
        $resources = $this->scanResources($projectRoot, $panels);
        $pages = $this->scanPages($projectRoot, $panels);
        $widgets = $this->scanWidgets($projectRoot, $panels);
        $relationManagers = $this->scanRelationManagers($projectRoot);

        return [
            'detected' => true,
            'panels' => $panels,
            'resources' => $resources,
            'pages' => $pages,
            'widgets' => $widgets,
            'relationManagers' => $relationManagers,
        ];
    }

    // ── Panel scanning ────────────────────────────────────────────────────────

    /** @return FilamentPanelDefinition[] */
    private function scanPanels(string $projectRoot): array
    {
        $panelsDir = $projectRoot.'/app/Providers/Filament';
        if (! is_dir($panelsDir)) {
            return [];
        }

        $panels = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($panelsDir, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $entry) {
            if (! $entry->isFile() || $entry->getExtension() !== 'php') {
                continue;
            }
            if (! str_ends_with($entry->getBasename(), 'PanelProvider.php')) {
                continue;
            }

            $parsed = $this->parser->parse($entry->getPathname());
            if (! $parsed || ! $parsed['ast']) {
                continue;
            }

            $def = $this->extractPanel($parsed['ast'], $parsed['useMap'], $entry->getPathname());
            if ($def !== null) {
                $panels[] = $def;
            }
        }

        return $panels;
    }

    private function extractPanel(array $ast, array $useMap, string $file): ?FilamentPanelDefinition
    {
        $traverser = new NodeTraverser;
        $visitor = new class($file, $useMap) extends NodeVisitorAbstract
        {
            public ?FilamentPanelDefinition $result = null;

            private string $namespace = '';

            private string $className = '';

            private string $panelId = '';

            private string $panelPath = '';

            /** @var string[] */
            private array $resources = [];

            /** @var string[] */
            private array $pages = [];

            /** @var string[] */
            private array $widgets = [];

            /** @var string[] */
            private array $discoverResourcesFor = [];

            /** @var string[] */
            private array $discoverPagesFor = [];

            /** @var string[] */
            private array $discoverWidgetsFor = [];

            public function __construct(
                private string $file,
                private array $useMap,
            ) {}

            public function enterNode(Node $node): ?int
            {
                if ($node instanceof Node\Stmt\Namespace_) {
                    $this->namespace = $node->name ? $node->name->toString() : '';
                }

                if ($node instanceof Node\Stmt\Class_) {
                    $this->className = $node->name ? $node->name->toString() : '';
                }

                // Detect ->id('admin') method chains
                if ($node instanceof Node\Expr\MethodCall
                    && $node->name instanceof Node\Identifier
                    && $node->name->toString() === 'id') {
                    $arg = $node->args[0] ?? null;
                    if ($arg && $arg->value instanceof Node\Scalar\String_) {
                        $this->panelId = $arg->value->value;
                    }
                }

                // Detect ->path('/admin')
                if ($node instanceof Node\Expr\MethodCall
                    && $node->name instanceof Node\Identifier
                    && $node->name->toString() === 'path') {
                    $arg = $node->args[0] ?? null;
                    if ($arg && $arg->value instanceof Node\Scalar\String_) {
                        $this->panelPath = $arg->value->value;
                    }
                }

                // Detect ->resources([...]), ->pages([...]), ->widgets([...])
                if ($node instanceof Node\Expr\MethodCall
                    && $node->name instanceof Node\Identifier) {
                    $method = $node->name->toString();
                    if (in_array($method, ['resources', 'pages', 'widgets'], true)) {
                        $arg = $node->args[0] ?? null;
                        if ($arg && $arg->value instanceof Node\Expr\Array_) {
                            $fqcns = $this->extractClassArray($arg->value);
                            match ($method) {
                                'resources' => $this->resources = array_merge($this->resources, $fqcns),
                                'pages' => $this->pages = array_merge($this->pages, $fqcns),
                                'widgets' => $this->widgets = array_merge($this->widgets, $fqcns),
                            };
                        }
                    }

                    // Detect ->discoverResources(for: '...'), ->discoverPages(for: '...'), ->discoverWidgets(for: '...')
                    if (in_array($method, ['discoverResources', 'discoverPages', 'discoverWidgets'], true)) {
                        $ns = $this->extractForArgument($node->args);
                        if ($ns !== '') {
                            match ($method) {
                                'discoverResources' => $this->discoverResourcesFor[] = $ns,
                                'discoverPages' => $this->discoverPagesFor[] = $ns,
                                'discoverWidgets' => $this->discoverWidgetsFor[] = $ns,
                            };
                        }
                    }
                }

                return null;
            }

            public function afterTraverse(array $nodes): ?array
            {
                if ($this->className === '') {
                    return null;
                }
                $fqcn = $this->namespace !== ''
                    ? $this->namespace.'\\'.$this->className
                    : $this->className;

                $this->result = new FilamentPanelDefinition(
                    id: $this->panelId ?: strtolower($this->className),
                    fqcn: $fqcn,
                    file: $this->file,
                    path: $this->panelPath,
                    resources: $this->resources,
                    pages: $this->pages,
                    widgets: $this->widgets,
                    discoverResourcesFor: $this->discoverResourcesFor,
                    discoverPagesFor: $this->discoverPagesFor,
                    discoverWidgetsFor: $this->discoverWidgetsFor,
                );

                return null;
            }

            /**
             * Extract the value of the `for:` named argument (or positional arg[1])
             * from discoverResources(in: ..., for: '...') calls.
             */
            private function extractForArgument(array $args): string
            {
                foreach ($args as $arg) {
                    if (! ($arg instanceof Node\Arg)) {
                        continue;
                    }
                    // Named argument: for: '...'
                    if ($arg->name instanceof Node\Identifier && $arg->name->toString() === 'for') {
                        return $arg->value instanceof Node\Scalar\String_ ? $arg->value->value : '';
                    }
                }

                // Positional fallback: discoverResources($dir, $namespace) — second arg
                $arg = $args[1] ?? null;
                if ($arg instanceof Node\Arg && $arg->value instanceof Node\Scalar\String_) {
                    return $arg->value->value;
                }

                return '';
            }

            /** @return string[] */
            private function extractClassArray(Node\Expr\Array_ $arrayNode): array
            {
                $fqcns = [];
                foreach ($arrayNode->items as $item) {
                    if ($item === null) {
                        continue;
                    }
                    $val = $item->value;
                    // SomeClass::class
                    if ($val instanceof Node\Expr\ClassConstFetch
                        && $val->class instanceof Node\Name) {
                        $name = $val->class->toString();
                        $fqcns[] = $this->useMap[$name] ?? $name;
                    }
                }

                return $fqcns;
            }
        };

        $traverser->addVisitor($visitor);
        $traverser->traverse($ast);

        return $visitor->result;
    }

    // ── Resource scanning ─────────────────────────────────────────────────────

    /**
     * @param  FilamentPanelDefinition[]  $panels
     * @return FilamentResourceDefinition[]
     */
    private function scanResources(string $projectRoot, array &$panels): array
    {
        $filamentDir = $projectRoot.'/app/Filament';
        if (! is_dir($filamentDir)) {
            return [];
        }

        // Build reverse map: explicit resource FQCN => panelId
        $resourceToPanelId = [];
        foreach ($panels as $panel) {
            foreach ($panel->resources as $resourceFqcn) {
                $resourceToPanelId[$resourceFqcn] = $panel->id;
            }
        }

        $resources = [];

        // Scan all PHP files under app/Filament/ recursively.
        // Only process files inside a Resources/ directory; skip files that live in
        // known non-resource sub-directories (Pages, RelationManagers, Schemas, Tables, Widgets).
        // This supports both flat layouts (Resources/PostResource.php) and grouped layouts
        // (Resources/Shop/Products/ProductResource.php).
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($filamentDir, \FilesystemIterator::SKIP_DOTS)
        );
        foreach ($iterator as $entry) {
            if (! $entry->isFile() || $entry->getExtension() !== 'php') {
                continue;
            }
            $normalizedPath = str_replace('\\', '/', $entry->getPathname());
            if (! str_contains($normalizedPath, '/Resources/')) {
                continue;
            }
            if (preg_match('#/(Pages|RelationManagers|Schemas|Tables|Widgets)/#', $normalizedPath)) {
                continue;
            }

            $parsed = $this->parser->parse($entry->getPathname());
            if (! $parsed || ! $parsed['ast']) {
                continue;
            }

            $def = $this->extractResource($parsed['ast'], $parsed['useMap'], $entry->getPathname());
            if ($def === null) {
                continue;
            }

            // Resolve panel ID — first check explicit list, then check discovery namespace prefixes
            $panelId = $resourceToPanelId[$def->fqcn] ?? '';
            $panelPath = '';
            if ($panelId === '') {
                foreach ($panels as $panel) {
                    foreach ($panel->discoverResourcesFor as $ns) {
                        if (str_starts_with($def->fqcn, rtrim($ns, '\\').'\\') || $def->fqcn === $ns) {
                            $panelId = $panel->id;
                            $panelPath = $panel->path;
                            break 2;
                        }
                    }
                }
            } else {
                foreach ($panels as $panel) {
                    if ($panel->id === $panelId) {
                        $panelPath = $panel->path;
                        break;
                    }
                }
            }

            // Use the explicit $slug from the resource class when available;
            // fall back to deriving a slug from the FQCN (supports both flat and
            // grouped/nested resource layouts, matching Filament v3+ behaviour).
            $resourceSlug = $def->slug !== ''
                ? $def->slug
                : $this->filamentResourceSlug($def->fqcn);

            $route = $this->computeResourceRoute($panelPath, $resourceSlug);

            // Compute per-page HTTP routes from known page types
            $pageRoutes = [];
            foreach ($def->pages as $pageKey => $pageFqcn) {
                $path = match ($pageKey) {
                    'index' => $route,
                    'create' => $route.'/create',
                    'edit' => $route.'/{record}/edit',
                    'view' => $route.'/{record}',
                    default => null,
                };
                if ($path !== null) {
                    $pageRoutes[$pageKey] = ['GET', $path];
                }
            }

            $def = new FilamentResourceDefinition(
                fqcn: $def->fqcn,
                file: $def->file,
                modelFqcn: $def->modelFqcn,
                panelId: $panelId,
                pages: $def->pages,
                relations: $def->relations,
                route: $route,
                pageRoutes: $pageRoutes,
                slug: $def->slug,
            );

            $resources[] = $def;
        }

        // Populate panels that use discoverResources with the matched FQCNs
        foreach ($panels as $i => $panel) {
            if (empty($panel->discoverResourcesFor)) {
                continue;
            }
            $discovered = array_values(array_map(
                fn ($r) => $r->fqcn,
                array_filter($resources, fn ($r) => $r->panelId === $panel->id)
            ));
            if (! empty($discovered)) {
                $panels[$i] = new FilamentPanelDefinition(
                    id: $panel->id,
                    fqcn: $panel->fqcn,
                    file: $panel->file,
                    path: $panel->path,
                    resources: array_unique(array_merge($panel->resources, $discovered)),
                    pages: $panel->pages,
                    widgets: $panel->widgets,
                    discoverResourcesFor: $panel->discoverResourcesFor,
                    discoverPagesFor: $panel->discoverPagesFor,
                    discoverWidgetsFor: $panel->discoverWidgetsFor,
                );
            }
        }

        return $resources;
    }

    private function extractResource(array $ast, array $useMap, string $file): ?FilamentResourceDefinition
    {
        $traverser = new NodeTraverser;
        $visitor = new class($file, $useMap) extends NodeVisitorAbstract
        {
            public ?FilamentResourceDefinition $result = null;

            private string $namespace = '';

            private string $className = '';

            private string $modelFqcn = '';

            private string $slug = '';

            private bool $isResource = false;

            /** @var array<string, string> */
            private array $pages = [];

            /** @var string[] */
            private array $relations = [];

            public function __construct(
                private string $file,
                private array $useMap,
            ) {}

            public function enterNode(Node $node): ?int
            {
                if ($node instanceof Node\Stmt\Namespace_) {
                    $this->namespace = $node->name ? $node->name->toString() : '';
                }

                if ($node instanceof Node\Stmt\Class_) {
                    $this->className = $node->name ? $node->name->toString() : '';
                    // Check if extends Resource
                    if ($node->extends instanceof Node\Name) {
                        $parent = $node->extends->getLast();
                        $this->isResource = $parent === 'Resource';
                    }
                }

                // Extract protected static ?string $model = Post::class
                // Extract protected static ?string $slug = 'shop/products'
                if ($node instanceof Node\Stmt\Property) {
                    foreach ($node->props as $prop) {
                        $propName = $prop->name->toString();
                        if ($propName === 'model') {
                            if ($prop->default instanceof Node\Expr\ClassConstFetch
                                && $prop->default->class instanceof Node\Name) {
                                $name = $prop->default->class->toString();
                                $this->modelFqcn = $this->useMap[$name] ?? $name;
                            }
                        } elseif ($propName === 'slug') {
                            if ($prop->default instanceof Node\Scalar\String_) {
                                $this->slug = $prop->default->value;
                            }
                        }
                    }
                }

                return null;
            }

            public function leaveNode(Node $node): array|int|Node|null
            {
                // Extract return arrays from getPages() and getRelations()
                if (! ($node instanceof Node\Stmt\ClassMethod)) {
                    return null;
                }

                $methodName = $node->name->toString();

                if ($methodName === 'getPages') {
                    $this->pages = $this->extractReturnArray($node);
                }

                if ($methodName === 'getRelations') {
                    $this->relations = array_values($this->extractReturnArray($node));
                }

                return null;
            }

            public function afterTraverse(array $nodes): ?array
            {
                if ($this->className === '' || ! $this->isResource) {
                    return null;
                }
                $fqcn = $this->namespace !== ''
                    ? $this->namespace.'\\'.$this->className
                    : $this->className;

                $this->result = new FilamentResourceDefinition(
                    fqcn: $fqcn,
                    file: $this->file,
                    modelFqcn: $this->modelFqcn,
                    panelId: '',
                    pages: $this->pages,
                    relations: $this->relations,
                    slug: $this->slug,
                );

                return null;
            }

            /** @return array<string, string> for getPages(), string[] for getRelations() */
            private function extractReturnArray(Node\Stmt\ClassMethod $method): array
            {
                $result = [];
                foreach ($method->stmts ?? [] as $stmt) {
                    if (! ($stmt instanceof Node\Stmt\Return_)) {
                        continue;
                    }
                    if (! ($stmt->expr instanceof Node\Expr\Array_)) {
                        continue;
                    }
                    foreach ($stmt->expr->items as $item) {
                        if ($item === null) {
                            continue;
                        }
                        $val = $item->value;

                        // Resolve class reference (Pages\ListPosts::route('/'))
                        // The value may be a StaticCall like Pages\ListPosts::route('/')
                        $classFqcn = null;
                        if ($val instanceof Node\Expr\StaticCall
                            && $val->class instanceof Node\Name) {
                            $classFqcn = $this->resolveClassName($val->class->toString());
                        }
                        // Or a direct ::class reference
                        if ($val instanceof Node\Expr\ClassConstFetch
                            && $val->class instanceof Node\Name) {
                            $classFqcn = $this->resolveClassName($val->class->toString());
                        }

                        if ($classFqcn === null) {
                            continue;
                        }

                        // Key may be a string (for pages) or absent (for relations)
                        if ($item->key instanceof Node\Scalar\String_) {
                            $result[$item->key->value] = $classFqcn;
                        } else {
                            $result[] = $classFqcn;
                        }
                    }
                }

                return $result;
            }

            /**
             * Resolve a class name reference to a fully-qualified class name.
             *
             * Handles three cases:
             *  1. Direct useMap hit: `Post` → `App\Models\Post`
             *  2. First-segment useMap hit: `Pages\ListPosts` where `Pages` is a use-alias
             *  3. Relative namespace (no match): prepend the current class namespace,
             *     e.g. `Pages\ListPosts` in `App\Filament\Resources\PostResource`
             *     → `App\Filament\Resources\PostResource\Pages\ListPosts`
             */
            private function resolveClassName(string $name): string
            {
                if (isset($this->useMap[$name])) {
                    return $this->useMap[$name];
                }

                $parts = explode('\\', $name);
                if (count($parts) > 1 && isset($this->useMap[$parts[0]])) {
                    return $this->useMap[$parts[0]].'\\'.implode('\\', array_slice($parts, 1));
                }

                if ($this->namespace !== '') {
                    return $this->namespace.'\\'.$name;
                }

                return ltrim($name, '\\');
            }
        };

        $traverser->addVisitor($visitor);
        $traverser->traverse($ast);

        return $visitor->result;
    }

    // ── Page scanning ─────────────────────────────────────────────────────────

    /**
     * @param  FilamentPanelDefinition[]  $panels
     * @return FilamentPageDefinition[]
     */
    private function scanPages(string $projectRoot, array &$panels): array
    {
        $pages = [];

        // Scan the entire app/Filament/ tree once and categorise pages by path.
        // - Resource pages: live inside a Resources/ directory AND inside a Pages/ sub-directory.
        // - Custom pages:   live inside a Pages/ directory but NOT inside a Resources/ directory.
        //   This covers both the standard app/Filament/Pages/ location and non-standard
        //   panel layouts such as app/Filament/App/Pages/ (common in Filament v3+ multi-panel apps).
        $filamentDir = $projectRoot.'/app/Filament';
        if (is_dir($filamentDir)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($filamentDir, \FilesystemIterator::SKIP_DOTS)
            );
            foreach ($iterator as $entry) {
                if (! $entry->isFile() || $entry->getExtension() !== 'php') {
                    continue;
                }
                $normalizedPath = str_replace('\\', '/', $entry->getPathname());
                if (! str_contains($normalizedPath, '/Pages/')) {
                    continue;
                }
                $def = $this->extractPage($entry->getPathname());
                if ($def !== null) {
                    $pages[] = $def;
                }
            }
        }

        // Populate panels that use discoverPages with matched custom page FQCNs
        foreach ($panels as $i => $panel) {
            if (empty($panel->discoverPagesFor)) {
                continue;
            }
            $discovered = [];
            foreach ($pages as $page) {
                if ($page->parentResourceFqcn !== '') {
                    continue; // skip resource pages — those are registered via resources
                }
                foreach ($panel->discoverPagesFor as $ns) {
                    if (str_starts_with($page->fqcn, rtrim($ns, '\\').'\\') || $page->fqcn === $ns) {
                        $discovered[] = $page->fqcn;
                        break;
                    }
                }
            }
            if (! empty($discovered)) {
                $panels[$i] = new FilamentPanelDefinition(
                    id: $panel->id,
                    fqcn: $panel->fqcn,
                    file: $panel->file,
                    path: $panel->path,
                    resources: $panel->resources,
                    pages: array_unique(array_merge($panel->pages, $discovered)),
                    widgets: $panel->widgets,
                    discoverResourcesFor: $panel->discoverResourcesFor,
                    discoverPagesFor: $panel->discoverPagesFor,
                    discoverWidgetsFor: $panel->discoverWidgetsFor,
                );
            }
        }

        // Build a FQCN → panel map so we can stamp panelId + route onto custom pages.
        // Custom page route = GET /{panelPath}/{pageSlug}
        // where pageSlug = explicit $slug ?? Str::kebab(class_basename($fqcn))
        $fqcnToPanel = [];
        foreach ($panels as $panel) {
            foreach ($panel->pages as $pageFqcn) {
                $fqcnToPanel[$pageFqcn] = $panel;
            }
        }

        foreach ($pages as $i => $page) {
            if ($page->parentResourceFqcn !== '' || ! isset($fqcnToPanel[$page->fqcn])) {
                continue;
            }
            $panel = $fqcnToPanel[$page->fqcn];
            $slug = $page->slug !== '' ? $page->slug : $this->pageSlug($page->fqcn);
            $route = $this->computeResourceRoute($panel->path, $slug);
            $pages[$i] = new FilamentPageDefinition(
                fqcn: $page->fqcn,
                file: $page->file,
                parentResourceFqcn: $page->parentResourceFqcn,
                pageType: $page->pageType,
                methods: $page->methods,
                slug: $page->slug,
                panelId: $panel->id,
                route: $route,
            );
        }

        return $pages;
    }

    /**
     * Derive a Filament page slug from its FQCN.
     * Mirrors Filament's Page::getSlug() fallback: Str::kebab(class_basename($class))
     */
    private function pageSlug(string $fqcn): string
    {
        $className = ltrim(strrchr($fqcn, '\\') ?: $fqcn, '\\');

        return strtolower((string) preg_replace('/([a-z])([A-Z])/', '$1-$2', $className));
    }

    private function extractPage(string $file): ?FilamentPageDefinition
    {
        $parsed = $this->parser->parse($file);
        if (! $parsed || ! $parsed['ast']) {
            return null;
        }

        $traverser = new NodeTraverser;
        $visitor = new class($file) extends NodeVisitorAbstract
        {
            public ?FilamentPageDefinition $result = null;

            private string $namespace = '';

            private string $className = '';

            private string $parentClass = '';

            private string $slug = '';

            /** @var string[] */
            private array $methods = [];

            public function __construct(private string $file) {}

            public function enterNode(Node $node): ?int
            {
                if ($node instanceof Node\Stmt\Namespace_) {
                    $this->namespace = $node->name ? $node->name->toString() : '';
                }

                if ($node instanceof Node\Stmt\Class_ && $node->extends instanceof Node\Name) {
                    $this->className = $node->name ? $node->name->toString() : '';
                    $this->parentClass = $node->extends->getLast();
                }

                if ($node instanceof Node\Stmt\ClassMethod) {
                    $this->methods[] = $node->name->toString();
                }

                // Extract protected static ?string $slug = 'settings'
                if ($node instanceof Node\Stmt\Property) {
                    foreach ($node->props as $prop) {
                        if ($prop->name->toString() === 'slug'
                            && $prop->default instanceof Node\Scalar\String_) {
                            $this->slug = $prop->default->value;
                        }
                    }
                }

                return null;
            }

            public function afterTraverse(array $nodes): ?array
            {
                if ($this->className === '') {
                    return null;
                }

                $fqcn = $this->namespace !== ''
                    ? $this->namespace.'\\'.$this->className
                    : $this->className;

                $pageType = match ($this->parentClass) {
                    'ListRecords' => 'index',
                    'CreateRecord' => 'create',
                    'EditRecord' => 'edit',
                    'ViewRecord' => 'view',
                    default => 'custom',
                };

                // Determine parent resource from namespace convention.
                // Flat layout:  App\Filament\Resources\PostResource\Pages    → App\Filament\Resources\PostResource
                // Nested layout: App\Filament\Resources\Shop\Products\Pages  → App\Filament\Resources\Shop\Products
                $parentResourceFqcn = '';
                if (preg_match('#^(.+\\\\Resources\\\\.+)\\\\Pages$#', $this->namespace, $m)) {
                    $parentResourceFqcn = $m[1];
                }

                $this->result = new FilamentPageDefinition(
                    fqcn: $fqcn,
                    file: $this->file,
                    parentResourceFqcn: $parentResourceFqcn,
                    pageType: $pageType,
                    methods: $this->methods,
                    slug: $this->slug,
                );

                return null;
            }
        };

        $traverser->addVisitor($visitor);
        $traverser->traverse($parsed['ast']);

        return $visitor->result;
    }

    // ── Widget scanning ───────────────────────────────────────────────────────

    /**
     * @param  FilamentPanelDefinition[]  $panels
     * @return FilamentWidgetDefinition[]
     */
    private function scanWidgets(string $projectRoot, array &$panels): array
    {
        $widgets = [];
        $seenFiles = [];

        // Helper closure to scan a directory recursively for widget PHP files.
        $scanDir = function (string $dir) use (&$widgets, &$seenFiles): void {
            if (! is_dir($dir)) {
                return;
            }
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
            );
            foreach ($iterator as $entry) {
                if (! $entry->isFile() || $entry->getExtension() !== 'php') {
                    continue;
                }
                $realPath = $entry->getRealPath();
                if (isset($seenFiles[$realPath])) {
                    continue;
                }
                $seenFiles[$realPath] = true;
                $def = $this->extractWidget($entry->getPathname());
                if ($def !== null) {
                    $widgets[] = $def;
                }
            }
        };

        // Standard panel-level widgets: app/Filament/Widgets/
        $scanDir($projectRoot.'/app/Filament/Widgets');

        // Widgets co-located with resources (Filament v3+ grouped-resource pattern):
        // app/Filament/Resources/**/Widgets/
        $resourcesDir = $projectRoot.'/app/Filament/Resources';
        if (is_dir($resourcesDir)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($resourcesDir, \FilesystemIterator::SKIP_DOTS)
            );
            foreach ($iterator as $entry) {
                if (! $entry->isFile() || $entry->getExtension() !== 'php') {
                    continue;
                }
                if (! str_contains(str_replace('\\', '/', $entry->getPathname()), '/Widgets/')) {
                    continue;
                }
                $realPath = $entry->getRealPath();
                if (isset($seenFiles[$realPath])) {
                    continue;
                }
                $seenFiles[$realPath] = true;
                $def = $this->extractWidget($entry->getPathname());
                if ($def !== null) {
                    $widgets[] = $def;
                }
            }
        }

        if (empty($widgets)) {
            return [];
        }

        // Populate panels that use discoverWidgets with matched widget FQCNs
        foreach ($panels as $i => $panel) {
            if (empty($panel->discoverWidgetsFor)) {
                continue;
            }
            $discovered = [];
            foreach ($widgets as $widget) {
                foreach ($panel->discoverWidgetsFor as $ns) {
                    if (str_starts_with($widget->fqcn, rtrim($ns, '\\').'\\') || $widget->fqcn === $ns) {
                        $discovered[] = $widget->fqcn;
                        break;
                    }
                }
            }
            if (! empty($discovered)) {
                $panels[$i] = new FilamentPanelDefinition(
                    id: $panel->id,
                    fqcn: $panel->fqcn,
                    file: $panel->file,
                    path: $panel->path,
                    resources: $panel->resources,
                    pages: $panel->pages,
                    widgets: array_unique(array_merge($panel->widgets, $discovered)),
                    discoverResourcesFor: $panel->discoverResourcesFor,
                    discoverPagesFor: $panel->discoverPagesFor,
                    discoverWidgetsFor: $panel->discoverWidgetsFor,
                );
            }
        }

        return $widgets;
    }

    private function extractWidget(string $file): ?FilamentWidgetDefinition
    {
        $parsed = $this->parser->parse($file);
        if (! $parsed || ! $parsed['ast']) {
            return null;
        }

        $traverser = new NodeTraverser;
        $visitor = new class($file) extends NodeVisitorAbstract
        {
            public ?FilamentWidgetDefinition $result = null;

            private string $namespace = '';

            private string $className = '';

            private string $parentClass = '';

            public function __construct(private string $file) {}

            public function enterNode(Node $node): ?int
            {
                if ($node instanceof Node\Stmt\Namespace_) {
                    $this->namespace = $node->name ? $node->name->toString() : '';
                }

                if ($node instanceof Node\Stmt\Class_ && $node->extends instanceof Node\Name) {
                    $this->className = $node->name ? $node->name->toString() : '';
                    $this->parentClass = $node->extends->getLast();
                }

                return null;
            }

            public function afterTraverse(array $nodes): ?array
            {
                if ($this->className === '') {
                    return null;
                }

                $fqcn = $this->namespace !== ''
                    ? $this->namespace.'\\'.$this->className
                    : $this->className;

                $widgetType = match ($this->parentClass) {
                    'StatsOverviewWidget' => 'stats-overview',
                    'ChartWidget' => 'chart',
                    'TableWidget' => 'table',
                    default => 'custom',
                };

                $this->result = new FilamentWidgetDefinition(
                    fqcn: $fqcn,
                    file: $this->file,
                    widgetType: $widgetType,
                );

                return null;
            }
        };

        $traverser->addVisitor($visitor);
        $traverser->traverse($parsed['ast']);

        return $visitor->result;
    }

    // ── Relation Manager scanning ─────────────────────────────────────────────

    /** @return FilamentRelationManagerDefinition[] */
    private function scanRelationManagers(string $projectRoot): array
    {
        $filamentDir = $projectRoot.'/app/Filament';
        if (! is_dir($filamentDir)) {
            return [];
        }

        $managers = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($filamentDir, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $entry) {
            if (! $entry->isFile() || $entry->getExtension() !== 'php') {
                continue;
            }
            if (! str_contains(str_replace('\\', '/', $entry->getPathname()), '/RelationManagers/')) {
                continue;
            }

            $def = $this->extractRelationManager($entry->getPathname());
            if ($def !== null) {
                $managers[] = $def;
            }
        }

        return $managers;
    }

    private function extractRelationManager(string $file): ?FilamentRelationManagerDefinition
    {
        $parsed = $this->parser->parse($file);
        if (! $parsed || ! $parsed['ast']) {
            return null;
        }

        $traverser = new NodeTraverser;
        $visitor = new class($file) extends NodeVisitorAbstract
        {
            public ?FilamentRelationManagerDefinition $result = null;

            private string $namespace = '';

            private string $className = '';

            private string $parentClass = '';

            private string $relationship = '';

            public function __construct(private string $file) {}

            public function enterNode(Node $node): ?int
            {
                if ($node instanceof Node\Stmt\Namespace_) {
                    $this->namespace = $node->name ? $node->name->toString() : '';
                }

                if ($node instanceof Node\Stmt\Class_) {
                    $this->className = $node->name ? $node->name->toString() : '';
                    if ($node->extends instanceof Node\Name) {
                        $this->parentClass = $node->extends->getLast();
                    }
                }

                // Extract protected static string $relationship = 'comments'
                if ($node instanceof Node\Stmt\Property) {
                    foreach ($node->props as $prop) {
                        if ($prop->name->toString() !== 'relationship') {
                            continue;
                        }
                        if ($prop->default instanceof Node\Scalar\String_) {
                            $this->relationship = $prop->default->value;
                        }
                    }
                }

                return null;
            }

            public function afterTraverse(array $nodes): ?array
            {
                if ($this->className === '' || $this->parentClass !== 'RelationManager') {
                    return null;
                }

                $fqcn = $this->namespace !== ''
                    ? $this->namespace.'\\'.$this->className
                    : $this->className;

                // Derive parent resource from namespace convention.
                // Flat layout:   App\Filament\Resources\PostResource\RelationManagers   → App\Filament\Resources\PostResource
                // Nested layout: App\Filament\Resources\Shop\Products\RelationManagers  → App\Filament\Resources\Shop\Products
                $parentResourceFqcn = '';
                if (preg_match('#^(.+\\\\Resources\\\\.+)\\\\RelationManagers$#', $this->namespace, $m)) {
                    $parentResourceFqcn = $m[1];
                }

                $this->result = new FilamentRelationManagerDefinition(
                    fqcn: $fqcn,
                    file: $this->file,
                    parentResourceFqcn: $parentResourceFqcn,
                    relationship: $this->relationship,
                );

                return null;
            }
        };

        $traverser->addVisitor($visitor);
        $traverser->traverse($parsed['ast']);

        return $visitor->result;
    }

    // ── Route computation ─────────────────────────────────────────────────────

    /**
     * Derive a Filament resource slug from a fully-qualified class name.
     *
     * Mirrors Filament v3+ slug generation:
     *
     *  Flat layout (Resources\PostResource):
     *    → "posts"
     *
     *  Grouped/nested layout where the parent namespace segment is the plural
     *  of the class base name (Resources\Shop\Products\ProductResource):
     *    → "shop/products"    (namespace path kebab-joined)
     *
     *  Grouped layout where they differ (Resources\Blog\PostResource):
     *    → "blog/posts"       (namespace prefix + pluralised class base)
     */
    private function filamentResourceSlug(string $fqcn): string
    {
        // Extract everything after \Resources\ (or fall back to the bare class name)
        $pos = strpos($fqcn, '\\Resources\\');
        $afterResources = $pos !== false
            ? substr($fqcn, $pos + strlen('\\Resources\\'))
            : ltrim(strrchr($fqcn, '\\') ?: $fqcn, '\\');

        $parts = explode('\\', $afterResources);
        $className = array_pop($parts);       // e.g. "ProductResource"
        $parentNs = $parts === [] ? '' : end($parts); // e.g. "Products"

        $baseName = str_ends_with($className, 'Resource')
            ? substr($className, 0, -8)
            : $className;
        $pluralBase = $this->pluralizeWord($baseName); // e.g. "Products"

        $toKebab = static fn (string $s): string => strtolower((string) preg_replace('/([a-z])([A-Z])/', '$1-$2', $s));

        if ($pluralBase === $parentNs && $parts !== []) {
            // Grouped layout: parent namespace already holds the plural
            // → derive slug from namespace segments only (e.g. Shop/Products → shop/products)
            return implode('/', array_map($toKebab, $parts));
        }

        // Flat or mixed layout: kebab-join namespace prefix (if any) + pluralised base name
        $slugParts = array_map($toKebab, [...$parts, $pluralBase]);

        return implode('/', array_filter($slugParts, fn ($s) => $s !== ''));
    }

    private function pluralizeWord(string $word): string
    {
        if ($word === '') {
            return $word;
        }
        $vowels = ['a', 'e', 'i', 'o', 'u'];

        // Words ending in consonant + y → ies
        if (str_ends_with($word, 'y') && ! in_array($word[strlen($word) - 2], $vowels, true)) {
            return substr($word, 0, -1).'ies';
        }

        // Words ending in s, x, z, ch, sh → es
        if (preg_match('/(s|x|z|ch|sh)$/', $word)) {
            return $word.'es';
        }

        return $word.'s';
    }

    /**
     * Compute the full Filament URL for a resource.
     *
     * @param  string  $panelPath  The panel's URL prefix (may be empty for the default panel).
     * @param  string  $resourceSlug  Pre-resolved slug (explicit $slug or FQCN-derived).
     */
    public function computeResourceRoute(string $panelPath, string $resourceSlug): string
    {
        $prefix = '/'.ltrim($panelPath, '/');

        return rtrim($prefix, '/').'/'.$resourceSlug;
    }
}
