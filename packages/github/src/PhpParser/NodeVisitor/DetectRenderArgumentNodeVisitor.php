<?php

declare(strict_types=1);

namespace Pehapkari\Github\PhpParser\NodeVisitor;

use Pehapkari\Exception\ShouldNotHappenException;
use Pehapkari\Github\Collector\ResolvedTemplateNameCollector;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeVisitorAbstract;

final class DetectRenderArgumentNodeVisitor extends NodeVisitorAbstract
{
    private string $methodName;

    private ResolvedTemplateNameCollector $resolvedTemplateNameCollector;

    public function __construct(ResolvedTemplateNameCollector $resolvedTemplateNameCollector)
    {
        $this->resolvedTemplateNameCollector = $resolvedTemplateNameCollector;
    }

    public function setMethodName(string $methodName): void
    {
        $this->methodName = $methodName;
    }

    public function enterNode(Node $node): ?Node
    {
        $this->ensureMethodNameIsSet();

        if (! $node instanceof ClassMethod) {
            return null;
        }

        if ($this->methodName !== (string) $node->name) {
            return null;
        }

        foreach ((array) $node->stmts as $stmt) {
            if (! $stmt instanceof Return_) {
                continue;
            }

            if (! $stmt->expr instanceof MethodCall) {
                continue;
            }

            // the render method :)
            if ($stmt->expr->name instanceof Expr) {
                continue;
            }

            if ((string) $stmt->expr->name !== 'render') {
                continue;
            }

            $this->resolveFirstRenderArgument($stmt->expr);

            return null;
        }

        return null;
    }

    private function ensureMethodNameIsSet(): void
    {
        if ($this->methodName === '') {
            throw new ShouldNotHappenException(
                'Configure method name via "$nodeVisitor->setMethodName(<name>)" first'
            );
        }
    }

    private function resolveFirstRenderArgument(MethodCall $methodCall): void
    {
        $templateNameString = $methodCall->args[0]->value;

        if (! $templateNameString instanceof String_) {
            throw new ShouldNotHappenException();
        }

        $this->resolvedTemplateNameCollector->setValue($templateNameString->value);
    }
}
