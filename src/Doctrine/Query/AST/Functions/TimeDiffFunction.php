<?php

declare(strict_types=1);

namespace Pehapkari\Doctrine\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\PathExpression;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * @see https://stackoverflow.com/questions/21490993/expected-known-function-got-timedifffunction-not-found-in-doctrine-orm
 * Custom DQL function returning the difference between two DateTime values
 *
 * usage TIME_DIFF(dateTime1, dateTime2)
 */
final class TimeDiffFunction extends FunctionNode
{
    /**
     * @var PathExpression
     */
    public $dateTime1;

    /**
     * @var PathExpression
     */
    public $dateTime2;

    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->dateTime1 = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);

        $this->dateTime2 = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        return 'TIME_TO_SEC(TIMEDIFF(' .
            $this->dateTime1->dispatch($sqlWalker) . ', ' .
            $this->dateTime2->dispatch($sqlWalker) . '))';
    }
}
