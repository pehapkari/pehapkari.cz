<?php declare(strict_types=1);

namespace Pehapkari\Utils\PHPStan\Rule;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrors\RuleErrorWithMessage;

final class SelectWithGroupByRule implements Rule
{
    /**
     * @var Expr[]|MethodCall[]
     */
    private $reportedParentMethodCallNodes = [];

    /**
     * @var Expr|MethodCall
     */
    private $parentMethodCallNode;

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->name instanceof Identifier) {
            return [];
        }

        $methodName = $node->name->toString();
        if (! in_array($methodName, ['groupBy', 'addGroupBy'], true)) {
            return [];
        }

        if ($node->args === []) {
            return [];
        }

        $calledMethodNames = $this->getChainCalledMethodNames($node);
        if (in_array('select', $calledMethodNames, true)) {
            return [];
        }

        // report just once, even for more groupBy()/addGroupBy() in one chain
        if (in_array($this->parentMethodCallNode, $this->reportedParentMethodCallNodes, true)) {
            return [];
        }

        $ruleErrorWithMessage = new RuleErrorWithMessage(sprintf(
            'Add "select()" while calling "%s()" to query builder chain calls.It has bad side effects if missing, see %s',
            $methodName,
            'https://stackoverflow.com/a/41887524/1348344'
        ));

        $this->reportedParentMethodCallNodes[] = $this->parentMethodCallNode;

        return [$ruleErrorWithMessage];
    }

    /**
     * @return string[]
     */
    private function getChainCalledMethodNames(MethodCall $methodCall): array
    {
        // climb the chain call
        $calledMethodNames = [];

        $previousNode = $methodCall;
        while ($previousNode instanceof MethodCall) {
            if ($previousNode->name instanceof Identifier) {
                $calledMethodNames[] = $previousNode->name->toString();
            }
            $previousNode = $previousNode->var;
        }

        $this->parentMethodCallNode = $previousNode;

        return $calledMethodNames;
    }
}
