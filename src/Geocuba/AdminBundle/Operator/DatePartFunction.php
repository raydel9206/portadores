<?php

namespace Geocuba\AdminBundle\Operator;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\{
    AST\Node, Lexer, Parser, SqlWalker
};

/**
 * DatePartFunction ::= "DATE_PART" "(" StringPrimary "," ? ")"
 *
 * @author  Felix A. Prieto Carratalá <felix@pinar.geocuba.cu>
 */
class DatePartFunction extends FunctionNode
{
    /**
     * @var Node
     */
    public $part = null;

    /**
     * @var Node
     */
    public $source = null;

    /**
     * @param \Doctrine\ORM\Query\SqlWalker $sqlWalker
     *
     * @return string
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        return 'date_part(' . $this->part->dispatch($sqlWalker) . ', ' . $this->source->dispatch($sqlWalker) . ')';
    }

    /**
     * @param \Doctrine\ORM\Query\Parser $parser
     *
     * @return void
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->part = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->source = $parser->StringPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}