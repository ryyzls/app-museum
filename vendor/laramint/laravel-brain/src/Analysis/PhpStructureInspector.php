<?php

declare(strict_types=1);

namespace LaraMint\LaravelBrain\Analysis;

use LaraMint\LaravelBrain\Parser\PhpFileParser;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\NodeVisitorAbstract;

/**
 * Extracts enum cases, interface methods, and other structural members from PHP files
 * for graph inspection (sidebar / AI context).
 */
class PhpStructureInspector
{
    private PhpFileParser $parser;

    public function __construct(?PhpFileParser $parser = null)
    {
        $this->parser = $parser ?? new PhpFileParser;
    }

    /**
     * @return array{kind: string, members: list<array<string, mixed>>}|null
     */
    public function inspectFile(string $file): ?array
    {
        if (! is_file($file)) {
            return null;
        }

        $parsed = $this->parser->parse($file);
        if ($parsed['ast'] === null) {
            return null;
        }

        $traverser = new NodeTraverser;
        $visitor = new class extends NodeVisitorAbstract
        {
            public ?array $result = null;

            public function enterNode(Node $node): ?int
            {
                if ($node instanceof Node\Stmt\Enum_) {
                    $this->result = self::extractEnum($node);

                    return NodeVisitor::DONT_TRAVERSE_CHILDREN;
                }
                if ($node instanceof Node\Stmt\Interface_) {
                    $this->result = self::extractInterface($node);

                    return NodeVisitor::DONT_TRAVERSE_CHILDREN;
                }
                if ($node instanceof Node\Stmt\Trait_) {
                    $this->result = self::extractTrait($node);

                    return NodeVisitor::DONT_TRAVERSE_CHILDREN;
                }
                if ($node instanceof Node\Stmt\Class_
                    && $node->isAbstract()
                    && $node->name instanceof Node\Identifier) {
                    $this->result = self::extractAbstractClass($node);

                    return NodeVisitor::DONT_TRAVERSE_CHILDREN;
                }

                return null;
            }

            /**
             * @return array{kind: 'enum', members: list<array<string, mixed>>}
             */
            private static function extractEnum(Node\Stmt\Enum_ $node): array
            {
                $members = [];
                foreach ($node->stmts as $stmt) {
                    if ($stmt instanceof Node\Stmt\EnumCase) {
                        $row = [
                            'kind' => 'case',
                            'name' => $stmt->name->toString(),
                        ];
                        if ($stmt->expr instanceof Node\Scalar\String_) {
                            $row['value'] = $stmt->expr->value;
                        } elseif ($stmt->expr instanceof Node\Scalar\Int_) {
                            $row['value'] = $stmt->expr->value;
                        }
                        $members[] = $row;
                    } elseif ($stmt instanceof Node\Stmt\ClassMethod) {
                        $members[] = [
                            'kind' => 'method',
                            'name' => $stmt->name->toString(),
                            'static' => $stmt->isStatic(),
                        ];
                    }
                }

                return ['kind' => 'enum', 'members' => $members];
            }

            /**
             * @return array{kind: 'interface', members: list<array<string, mixed>>}
             */
            private static function extractInterface(Node\Stmt\Interface_ $node): array
            {
                $members = [];
                foreach ($node->stmts as $stmt) {
                    if ($stmt instanceof Node\Stmt\ClassMethod) {
                        $members[] = [
                            'kind' => 'method',
                            'name' => $stmt->name->toString(),
                        ];
                    }
                }

                return ['kind' => 'interface', 'members' => $members];
            }

            /**
             * @return array{kind: 'trait', members: list<array<string, mixed>>}
             */
            private static function extractTrait(Node\Stmt\Trait_ $node): array
            {
                $members = [];
                foreach ($node->stmts as $stmt) {
                    if ($stmt instanceof Node\Stmt\ClassMethod) {
                        $name = $stmt->name->toString();
                        if (str_starts_with($name, '__')) {
                            continue;
                        }
                        $members[] = [
                            'kind' => 'method',
                            'name' => $name,
                            'static' => $stmt->isStatic(),
                        ];
                    }
                }

                return ['kind' => 'trait', 'members' => $members];
            }

            /**
             * @return array{kind: 'abstract_class', members: list<array<string, mixed>>}
             */
            private static function extractAbstractClass(Node\Stmt\Class_ $node): array
            {
                $members = [];
                foreach ($node->stmts as $stmt) {
                    if (! $stmt instanceof Node\Stmt\ClassMethod) {
                        continue;
                    }
                    $name = $stmt->name->toString();
                    if (str_starts_with($name, '__')) {
                        continue;
                    }
                    $vis = $stmt->isPrivate() ? 'private' : ($stmt->isProtected() ? 'protected' : 'public');
                    if ($vis === 'private') {
                        continue;
                    }
                    $members[] = [
                        'kind' => 'method',
                        'name' => $name,
                        'static' => $stmt->isStatic(),
                        'abstract' => $stmt->isAbstract(),
                        'visibility' => $vis,
                    ];
                }

                return ['kind' => 'abstract_class', 'members' => $members];
            }
        };

        $traverser->addVisitor($visitor);
        $traverser->traverse($parsed['ast']);

        return $visitor->result;
    }

    /**
     * Public + non-magic methods on a class (for mailables, notifications, etc.).
     *
     * @return list<array{name: string, static: bool, visibility: string}>
     */
    public function listClassMethods(string $file): array
    {
        if (! is_file($file)) {
            return [];
        }
        $parsed = $this->parser->parse($file);
        if ($parsed['ast'] === null) {
            return [];
        }

        $out = [];
        $traverser = new NodeTraverser;
        $visitor = new class($out) extends NodeVisitorAbstract
        {
            /**
             * @param  list<array{name: string, static: bool, visibility: string}>  $out
             */
            // @phpstan-ignore-next-line property.onlyWritten
            public function __construct(private array &$out) {}

            public function enterNode(Node $node): ?int
            {
                if (! $node instanceof Node\Stmt\Class_) {
                    return null;
                }
                foreach ($node->stmts as $stmt) {
                    if (! $stmt instanceof Node\Stmt\ClassMethod) {
                        continue;
                    }
                    $name = $stmt->name->toString();
                    if (str_starts_with($name, '__')) {
                        continue;
                    }
                    $vis = $stmt->isPrivate() ? 'private' : ($stmt->isProtected() ? 'protected' : 'public');
                    if ($vis === 'private') {
                        continue;
                    }
                    $this->out[] = [
                        'name' => $name,
                        'static' => $stmt->isStatic(),
                        'visibility' => $vis,
                    ];
                }

                return NodeVisitor::STOP_TRAVERSAL;
            }
        };
        $traverser->addVisitor($visitor);
        $traverser->traverse($parsed['ast']);

        return $out;
    }

    public function fileDeclaresEnumOrInterface(string $file): bool
    {
        $info = $this->inspectFile($file);

        return $info !== null && in_array($info['kind'], ['enum', 'interface'], true);
    }
}
