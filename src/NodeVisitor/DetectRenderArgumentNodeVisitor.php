<?php declare(strict_types=1);

namespace Pehapkari\NodeVisitor;

use Pehapkari\Exception\ShouldNotHappenException;
use PhpParser\Node;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeVisitorAbstract;

final class DetectRenderArgumentNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    private $methodName;

    /**
     * @var ResolvedTemplateNameCollector
     */
    private $resolvedTemplateNameCollector;

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
        if ($this->methodName === '') {
            throw new ShouldNotHappenException();
        }

        if (! $node instanceof Node\Stmt\ClassMethod) {
            return null;
        }

        if ($this->methodName !== (string) $node->name) {
            return null;
        }

        foreach ((array) $node->stmts as $stmt) {
            if (! $stmt instanceof Node\Stmt\Return_) {
                continue;
            }

            if (! $stmt->expr instanceof Node\Expr\MethodCall) {
                continue;
            }

            // the render methdo :)
            if ((string) $stmt->expr->name !== 'render') {
                continue;
            }

            $templateNameString = $stmt->expr->args[0]->value;
            if (! $templateNameString instanceof String_) {
                throw new ShouldNotHappenException();
            }

            $this->resolvedTemplateNameCollector->setValue($templateNameString->value);

            // final :)
            return null;
        }

        return null;
    }
}
