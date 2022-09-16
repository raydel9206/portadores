<?php

namespace Geocuba\AdminBundle\Operator;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * Class DateOperatorFunction
 * @package Geocuba\CommonsBundle\Operator
 */
class DateOperatorFunction extends FunctionNode
{
    /**
     * @var \DateTime
     */
    protected $date1;

    /**
     * @var \DateTime
     */
    protected $date2;

    /**
     * @param SqlWalker $sqlWalker
     *
     * @return string
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        $day = $this->date2->format('d');
        $month = $this->date2->format('m');
        $year = $this->date2->format('Y');

        $query = sprintf("date_part('day', %1\$s) = %2\$s AND date_part('month', %1\$s) = %3\$s AND date_part('year', %1\$s) = %4\$s");

        // error_log(print_r($query, true));

        return sprintf($query, $this->date1->dispatch($sqlWalker), $day, $month, $year);
    }

    /**
     * @param Parser $parser
     *
     * @return void
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->date1 = $parser->EntityExpression();
        // error_log(print_r($this->date1, true));
        $parser->match(Lexer::T_COMMA);
        $v = $parser->StringExpression();
        // error_log(print_r($v, true));
        $this->date2 = \DateTime::createFromFormat('m/d/Y', $v);
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}