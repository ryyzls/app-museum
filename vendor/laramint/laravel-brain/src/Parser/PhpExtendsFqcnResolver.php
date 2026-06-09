<?php

declare(strict_types=1);

namespace LaraMint\LaravelBrain\Parser;

use PhpParser\Node;

/**
 * Resolves `extends` clause names to FQCNs using file namespace and import aliases.
 */
final class PhpExtendsFqcnResolver
{
    /**
     * @param  Node\Stmt[]  $ast
     */
    public static function namespaceFromAst(array $ast): string
    {
        foreach ($ast as $stmt) {
            if ($stmt instanceof Node\Stmt\Namespace_) {
                return $stmt->name !== null ? $stmt->name->toString() : '';
            }
        }

        return '';
    }

    /**
     * @param  array<string, string>  $useMap  short name / alias => FQCN
     */
    public static function resolveExtends(?Node $extends, string $fileNamespace, array $useMap): ?string
    {
        if (! $extends instanceof Node\Name) {
            return null;
        }
        if ($extends instanceof Node\Name\FullyQualified) {
            return ltrim($extends->toString(), '\\');
        }

        $name = $extends->toString();
        if (isset($useMap[$name])) {
            return $useMap[$name];
        }

        if (str_contains($name, '\\')) {
            return ($fileNamespace !== '' ? $fileNamespace.'\\' : '').$name;
        }

        return $fileNamespace !== '' ? $fileNamespace.'\\'.$name : $name;
    }
}
