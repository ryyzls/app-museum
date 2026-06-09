<?php

declare(strict_types=1);

namespace LaraMint\LaravelBrain\Parser;

use PhpParser\Error;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Parser;
use PhpParser\ParserFactory;

class PhpFileParser
{
    private Parser $parser;

    public function __construct()
    {
        $this->parser = (new ParserFactory)->createForNewestSupportedVersion();
    }

    /**
     * @return array{ast: Node\Stmt[]|null, useMap: array<string, string>}
     */
    public function parse(string $filePath): array
    {
        $code = file_get_contents($filePath);
        if ($code === false) {
            return ['ast' => null, 'useMap' => []];
        }

        try {
            $ast = $this->parser->parse($code);
        } catch (Error) {
            return ['ast' => null, 'useMap' => []];
        }
        $useMap = $this->extractUseMap($ast ?? []);

        return ['ast' => $ast, 'useMap' => $useMap];
    }

    /**
     * @param  Node\Stmt[]  $ast
     * @return array<string, string> alias → FQCN
     */
    private function extractUseMap(array $ast): array
    {
        $useMap = [];
        $traverser = new NodeTraverser;

        $visitor = new class extends NodeVisitorAbstract
        {
            public array $useMap = [];

            public function enterNode(Node $node): ?int
            {
                if ($node instanceof Node\Stmt\Use_) {
                    foreach ($node->uses as $use) {
                        $fqcn = $use->name->toString();
                        $alias = $use->alias !== null ? $use->alias->toString() : $use->name->getLast();
                        $this->useMap[$alias] = $fqcn;
                    }
                }

                return null;
            }
        };

        $traverser->addVisitor($visitor);
        $traverser->traverse($ast);

        return $visitor->useMap;
    }
}
