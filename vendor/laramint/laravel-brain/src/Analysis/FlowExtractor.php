<?php

declare(strict_types=1);

namespace LaraMint\LaravelBrain\Analysis;

use PhpParser\Node;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;

/**
 * Converts a PHP method AST into a simplified list of flowchart steps.
 *
 * Each step is one of:
 *   { type: 'call',     label: string }
 *   { type: 'return',   label: string }
 *   { type: 'dispatch', label: string }
 *   { type: 'event',    label: string }
 *   { type: 'if',       label: string, then: step[], else: step[] }
 *   { type: 'loop',     label: string, body: step[] }
 *   { type: 'assign',   label: string }
 *   { type: 'throw',    label: string }
 *   { type: 'comment',  label: string }
 */
class FlowExtractor
{
    private PrettyPrinter $printer;

    private array $useMap;

    public function __construct()
    {
        $this->printer = new PrettyPrinter;
        $this->useMap = [];
    }

    /**
     * Extract flow steps from a method AST.
     *
     * @return array[]
     */
    public function extract(Node\Stmt\ClassMethod $method, array $useMap = []): array
    {
        $this->useMap = $useMap;

        return $this->stmtsToSteps($method->stmts ?? []);
    }

    /**
     * Extract flow steps from an inline closure or arrow function.
     * Arrow functions wrap their single expression in an implicit return step.
     *
     * @return array[]
     */
    public function extractFromClosure(
        Node\Expr\Closure|Node\Expr\ArrowFunction $closure,
        array $useMap = [],
    ): array {
        $this->useMap = $useMap;

        if ($closure instanceof Node\Expr\ArrowFunction) {
            // Arrow functions have a single expression body, no statement list
            return $this->stmtsToSteps([new Node\Stmt\Return_($closure->expr)]);
        }

        return $this->stmtsToSteps($closure->stmts ?? []);
    }

    /**
     * Compute static complexity metrics for a single method.
     *
     * Returns:
     *   lineCount            – physical lines (endLine - startLine + 1)
     *   statementCount       – number of top-level statements in the body
     *   cyclomaticComplexity – 1 + number of branching points (if/elseif/for/foreach/while/catch/case/&&/||)
     *   paramCount           – number of declared parameters
     */
    public function metrics(Node\Stmt\ClassMethod $method): array
    {
        $startLine = $method->getStartLine();
        $endLine = $method->getEndLine();
        $lineCount = ($startLine > 0 && $endLine >= $startLine) ? ($endLine - $startLine + 1) : 0;

        $stmts = $method->stmts ?? [];
        $statementCount = count($stmts);

        $cc = 1 + $this->countBranches($stmts);

        return [
            'lineCount' => $lineCount,
            'statementCount' => $statementCount,
            'cyclomaticComplexity' => $cc,
            'paramCount' => count($method->params),
        ];
    }

    /**
     * Recursively count branching nodes that increase cyclomatic complexity.
     *
     * @param  Node\Stmt[]  $stmts
     */
    private function countBranches(array $stmts): int
    {
        $count = 0;
        foreach ($stmts as $stmt) {
            if ($stmt instanceof Node\Stmt\If_) {
                $count++; // the if itself
                $count += count($stmt->elseifs); // each elseif
                $count += $this->countBranches($stmt->stmts);
                foreach ($stmt->elseifs as $ei) {
                    $count += $this->countBranches($ei->stmts);
                }
                if ($stmt->else) {
                    $count += $this->countBranches($stmt->else->stmts);
                }
            } elseif ($stmt instanceof Node\Stmt\Foreach_) {
                $count++;
                $count += $this->countBranches($stmt->stmts);
            } elseif ($stmt instanceof Node\Stmt\For_) {
                $count++;
                $count += $this->countBranches($stmt->stmts);
            } elseif ($stmt instanceof Node\Stmt\While_) {
                $count++;
                $count += $this->countBranches($stmt->stmts);
            } elseif ($stmt instanceof Node\Stmt\Do_) {
                $count++;
                $count += $this->countBranches($stmt->stmts);
            } elseif ($stmt instanceof Node\Stmt\TryCatch) {
                $count += count($stmt->catches); // each catch adds a branch
                $count += $this->countBranches($stmt->stmts);
                foreach ($stmt->catches as $catch) {
                    $count += $this->countBranches($catch->stmts);
                }
            } elseif ($stmt instanceof Node\Stmt\Switch_) {
                foreach ($stmt->cases as $case) {
                    if ($case->cond !== null) {
                        $count++;
                    } // each non-default case
                    $count += $this->countBranches($case->stmts);
                }
            } elseif ($stmt instanceof Node\Stmt\Expression) {
                // Count short-circuit operators inside expressions
                $count += $this->countLogicalOps($stmt->expr);
            } elseif ($stmt instanceof Node\Stmt\Return_ && $stmt->expr !== null) {
                $count += $this->countLogicalOps($stmt->expr);
            }
        }

        return $count;
    }

    /**
     * Count && and || operators in an expression (each adds a branch).
     */
    private function countLogicalOps(Node\Expr $expr): int
    {
        $count = 0;
        if ($expr instanceof Node\Expr\BinaryOp\BooleanAnd
            || $expr instanceof Node\Expr\BinaryOp\BooleanOr
            || $expr instanceof Node\Expr\BinaryOp\LogicalAnd
            || $expr instanceof Node\Expr\BinaryOp\LogicalOr
        ) {
            $count++;
            $count += $this->countLogicalOps($expr->left);
            $count += $this->countLogicalOps($expr->right);
        } elseif ($expr instanceof Node\Expr\Ternary) {
            $count++;
        }

        return $count;
    }

    // ── Statement walker ──────────────────────────────────────────────────────

    /**
     * @param  Node\Stmt[]  $stmts
     * @return array[]
     */
    private function stmtsToSteps(array $stmts, bool $inLoop = false): array
    {
        $steps = [];
        foreach ($stmts as $stmt) {
            $step = $this->stmtToStep($stmt, $inLoop);
            if ($step !== null) {
                $steps[] = $step;
            }
        }

        return $steps;
    }

    private function stmtToStep(Node\Stmt $stmt, bool $inLoop = false): ?array
    {
        // return $something;
        if ($stmt instanceof Node\Stmt\Return_) {
            $label = $stmt->expr !== null
                ? 'return '.$this->shortExpr($stmt->expr)
                : 'return';

            return ['type' => 'return', 'label' => $label];
        }

        // if (...) { ... } else { ... }
        if ($stmt instanceof Node\Stmt\If_) {
            return $this->ifToStep($stmt, $inLoop);
        }

        // foreach (... as ...) { ... }
        if ($stmt instanceof Node\Stmt\Foreach_) {
            $expr = $this->shortExpr($stmt->expr);
            $value = $this->shortExpr($stmt->valueVar);
            $label = "foreach ({$expr} as {$value})";
            $body = $this->stmtsToSteps($stmt->stmts, true);

            return [
                'type' => 'loop',
                'label' => $label,
                'body' => $body,
                'n1' => $this->hasQueryInside($stmt->stmts),
            ];
        }

        // for (...) / while (...) / do-while (...)
        if ($stmt instanceof Node\Stmt\For_) {
            $body = $this->stmtsToSteps($stmt->stmts, true);

            return [
                'type' => 'loop', 'label' => 'for (...)', 'body' => $body,
                'n1' => $this->hasQueryInside($stmt->stmts),
            ];
        }
        if ($stmt instanceof Node\Stmt\While_) {
            $body = $this->stmtsToSteps($stmt->stmts, true);

            return [
                'type' => 'loop', 'label' => 'while ('.$this->shortExpr($stmt->cond).')', 'body' => $body,
                'n1' => $this->hasQueryInside($stmt->stmts),
            ];
        }

        // try { ... } catch (...) { ... }
        if ($stmt instanceof Node\Stmt\TryCatch) {
            $inner = $this->stmtsToSteps($stmt->stmts, $inLoop);
            $catchSteps = [];
            foreach ($stmt->catches as $catch) {
                $exType = isset($catch->types[0]) ? $catch->types[0]->toString() : 'Exception';
                $catchSteps[] = ['type' => 'if', 'label' => "catch ({$exType})", 'then' => $this->stmtsToSteps($catch->stmts, $inLoop), 'else' => []];
            }

            return ['type' => 'loop', 'label' => 'try', 'body' => array_merge($inner, $catchSteps)];
        }

        // Expression statement: method calls, assignments, etc.
        if ($stmt instanceof Node\Stmt\Expression) {
            $step = $this->exprStmtToStep($stmt->expr);
            if ($step && $inLoop && $this->isQuery($stmt->expr)) {
                $step['n1'] = true;
            }

            return $step;
        }

        return null;
    }

    private function ifToStep(Node\Stmt\If_ $stmt, bool $inLoop = false): array
    {
        $cond = $this->shortExpr($stmt->cond);
        $then = $this->stmtsToSteps($stmt->stmts, $inLoop);

        $else = [];
        if ($stmt->else !== null) {
            $else = $this->stmtsToSteps($stmt->else->stmts, $inLoop);
        }
        foreach ($stmt->elseifs as $elseif) {
            $else = [['type' => 'if', 'label' => 'elseif ('.$this->shortExpr($elseif->cond).')',
                'then' => $this->stmtsToSteps($elseif->stmts, $inLoop), 'else' => $else]];
        }

        return [
            'type' => 'if',
            'label' => "if ({$cond})",
            'then' => $then,
            'else' => $else,
        ];
    }

    private function exprStmtToStep(Node\Expr $expr): ?array
    {
        // throw new SomeException(...)
        if ($expr instanceof Node\Expr\Throw_) {
            return ['type' => 'throw', 'label' => 'throw '.$this->shortExpr($expr->expr)];
        }

        // $var = value  /  $this->prop = value
        if ($expr instanceof Node\Expr\Assign) {
            $varLabel = $this->shortExpr($expr->var);
            $valLabel = $this->shortExpr($expr->expr);

            return ['type' => 'assign', 'label' => "{$varLabel} = {$valLabel}"];
        }

        // SomeJob::dispatch(...)  →  dispatch
        if ($expr instanceof Node\Expr\StaticCall) {
            $class = $expr->class instanceof Node\Name ? $expr->class->toString() : '?';
            $method = $expr->name instanceof Node\Identifier ? $expr->name->toString() : '?';
            $fqcn = $this->useMap[$class] ?? $class;
            $short = $this->baseName($fqcn);

            if ($method === 'dispatch' && (str_contains($fqcn, 'Job') || str_contains($fqcn, '\\Jobs\\'))) {
                return ['type' => 'dispatch', 'label' => "dispatch({$short})"];
            }
            if (in_array($class, ['Event', 'Illuminate\\Support\\Facades\\Event']) && $method === 'dispatch') {
                return ['type' => 'event', 'label' => 'Event::dispatch(...)'];
            }

            return ['type' => 'call', 'label' => "{$short}::{$method}(...)"];
        }

        // $this->service->method(...)  /  $var->method(...)
        if ($expr instanceof Node\Expr\MethodCall) {
            return ['type' => 'call', 'label' => $this->shortExpr($expr)];
        }

        // event(new SomeEvent)  /  dispatch(new SomeJob)
        if ($expr instanceof Node\Expr\FuncCall && $expr->name instanceof Node\Name) {
            $fn = $expr->name->toString();
            if ($fn === 'event') {
                return ['type' => 'event', 'label' => $this->shortExpr($expr)];
            }
            if ($fn === 'dispatch') {
                return ['type' => 'dispatch', 'label' => $this->shortExpr($expr)];
            }

            return ['type' => 'call', 'label' => $this->shortExpr($expr)];
        }

        return null;
    }

    // ── Expression prettifier ─────────────────────────────────────────────────

    /**
     * Convert an expression to a short human-readable string.
     */
    private function shortExpr(Node\Expr $expr): string
    {
        // $this->prop or $this->prop->chain
        if ($expr instanceof Node\Expr\PropertyFetch) {
            $obj = $this->shortExpr($expr->var);
            $prop = $expr->name instanceof Node\Identifier ? $expr->name->toString() : '?';

            return "{$obj}->{$prop}";
        }

        // $var
        if ($expr instanceof Node\Expr\Variable) {
            return is_string($expr->name) ? '$'.$expr->name : '$?';
        }

        // $obj->method(args)
        if ($expr instanceof Node\Expr\MethodCall) {
            $obj = $this->shortExpr($expr->var);
            $method = $expr->name instanceof Node\Identifier ? $expr->name->toString() : '?';
            $args = $this->argsLabel($expr->args);

            return "{$obj}->{$method}({$args})";
        }

        // Class::method(args)
        if ($expr instanceof Node\Expr\StaticCall) {
            $class = $expr->class instanceof Node\Name ? $this->baseName($expr->class->toString()) : '?';
            $method = $expr->name instanceof Node\Identifier ? $expr->name->toString() : '?';
            $args = $this->argsLabel($expr->args);

            return "{$class}::{$method}({$args})";
        }

        // new Class(args)
        if ($expr instanceof Node\Expr\New_) {
            $class = $expr->class instanceof Node\Name ? $this->baseName($expr->class->toString()) : '?';

            return "new {$class}(...)";
        }

        // Function call
        if ($expr instanceof Node\Expr\FuncCall && $expr->name instanceof Node\Name) {
            $fn = $expr->name->toString();
            $args = $this->argsLabel($expr->args);

            return "{$fn}({$args})";
        }

        // Scalar values
        if ($expr instanceof Node\Scalar\String_) {
            return "\"{$expr->value}\"";
        }
        if ($expr instanceof Node\Scalar\LNumber) {
            return (string) $expr->value;
        }
        if ($expr instanceof Node\Expr\ConstFetch) {
            return $expr->name->toString();
        }

        // Array access: $arr['key']
        if ($expr instanceof Node\Expr\ArrayDimFetch) {
            $var = $this->shortExpr($expr->var);
            $dim = $expr->dim !== null ? $this->shortExpr($expr->dim) : '';

            return "{$var}[{$dim}]";
        }

        // Ternary
        if ($expr instanceof Node\Expr\Ternary) {
            return $this->shortExpr($expr->cond).' ? ... : ...';
        }

        // Comparison / logical
        if ($expr instanceof Node\Expr\BinaryOp) {
            $left = $this->shortExpr($expr->left);
            $right = $this->shortExpr($expr->right);
            $op = $this->binaryOpSymbol($expr);

            return "{$left} {$op} {$right}";
        }

        if ($expr instanceof Node\Expr\BooleanNot) {
            return '!'.$this->shortExpr($expr->expr);
        }

        // Fallback: use pretty printer for full expression
        try {
            return $this->printer->prettyPrintExpr($expr);
        } catch (\Throwable) {
            return '...';
        }
    }

    private function argsLabel(array $args): string
    {
        if (empty($args)) {
            return '';
        }
        if (count($args) === 1) {
            $arg = $args[0];
            if ($arg instanceof Node\Arg) {
                return $this->shortExpr($arg->value);
            }
        }

        return '...';
    }

    private function binaryOpSymbol(Node\Expr\BinaryOp $op): string
    {
        return match (true) {
            $op instanceof Node\Expr\BinaryOp\Equal => '==',
            $op instanceof Node\Expr\BinaryOp\Identical => '===',
            $op instanceof Node\Expr\BinaryOp\NotEqual => '!=',
            $op instanceof Node\Expr\BinaryOp\NotIdentical => '!==',
            $op instanceof Node\Expr\BinaryOp\Greater => '>',
            $op instanceof Node\Expr\BinaryOp\GreaterOrEqual => '>=',
            $op instanceof Node\Expr\BinaryOp\Smaller => '<',
            $op instanceof Node\Expr\BinaryOp\SmallerOrEqual => '<=',
            $op instanceof Node\Expr\BinaryOp\BooleanAnd => '&&',
            $op instanceof Node\Expr\BinaryOp\BooleanOr => '||',
            $op instanceof Node\Expr\BinaryOp\Plus => '+',
            $op instanceof Node\Expr\BinaryOp\Minus => '-',
            $op instanceof Node\Expr\BinaryOp\Concat => '.',
            default => '?',
        };
    }

    private function hasQueryInside(array $stmts): bool
    {
        foreach ($stmts as $stmt) {
            if ($stmt instanceof Node\Stmt\Expression) {
                if ($this->isQuery($stmt->expr)) {
                    return true;
                }
            }
            if ($stmt instanceof Node\Stmt\If_) {
                if ($this->hasQueryInside($stmt->stmts)) {
                    return true;
                }
                if ($stmt->else !== null && $this->hasQueryInside($stmt->else->stmts)) {
                    return true;
                }
                foreach ($stmt->elseifs as $elseif) {
                    if ($this->hasQueryInside($elseif->stmts)) {
                        return true;
                    }
                }
            }
            if ($stmt instanceof Node\Stmt\Foreach_ && $this->hasQueryInside($stmt->stmts)) {
                return true;
            }
            if ($stmt instanceof Node\Stmt\For_ && $this->hasQueryInside($stmt->stmts)) {
                return true;
            }
            if ($stmt instanceof Node\Stmt\While_ && $this->hasQueryInside($stmt->stmts)) {
                return true;
            }
            if ($stmt instanceof Node\Stmt\TryCatch && $this->hasQueryInside($stmt->stmts)) {
                return true;
            }
        }

        return false;
    }

    private function isQuery(Node\Expr $expr): bool
    {
        // $model->relation  (Property Fetch on model variable is highly suspicious in loops)
        if ($expr instanceof Node\Expr\PropertyFetch) {
            $obj = $this->shortExpr($expr->var);

            // If it's $this->relation or $item->relation, it's often an N+1
            return true;
        }

        // $model->save() / $model->relation()->get()
        if ($expr instanceof Node\Expr\MethodCall) {
            $name = $expr->name instanceof Node\Identifier ? $expr->name->toString() : '';
            $queryMethods = [
                'get', 'first', 'find', 'all', 'paginate', 'simplePaginate', 'cursor',
                'save', 'update', 'delete', 'create', 'push', 'touch', 'sync', 'attach', 'detach', 'toggle',
            ];
            if (in_array($name, $queryMethods)) {
                return true;
            }

            // Chain: $q->where(...)->get()
            if ($this->isQuery($expr->var)) {
                return true;
            }
        }

        // Model::find()
        if ($expr instanceof Node\Expr\StaticCall) {
            $name = $expr->name instanceof Node\Identifier ? $expr->name->toString() : '';

            return in_array($name, ['find', 'first', 'all', 'where', 'create', 'updateOrCreate', 'firstOrCreate']);
        }

        // $var = query
        if ($expr instanceof Node\Expr\Assign) {
            return $this->isQuery($expr->expr);
        }

        return false;
    }

    private function baseName(string $fqcn): string
    {
        $short = $this->useMap[$fqcn] ?? $fqcn;
        $parts = explode('\\', $short);

        return end($parts);
    }
}
