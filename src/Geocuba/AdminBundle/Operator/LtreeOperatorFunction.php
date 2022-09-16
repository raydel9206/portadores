<?php

namespace Geocuba\AdminBundle\Operator;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\{
    Lexer, Parser, SqlWalker
};

/**
 * Class LtreeOperatorFunction
 * @package Geocuba\AdminBundle\Operator
 */
class LtreeOperatorFunction extends FunctionNode
{
    /**
     * @var Node
     */
    protected $left;

    /**
     * @var Node
     */
    protected $operator;

    /**
     * @var Node
     */
    protected $right;

    /**
     * @param SqlWalker $sqlWalker
     *
     * @return string
     * @throws \Doctrine\ORM\Query\AST\ASTException
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        return sprintf("(unaccent(lower(trim(%s))) %s unaccent(lower(trim(%s))))",
            $this->left->dispatch($sqlWalker),
            $this->operator->value,
            $this->right->dispatch($sqlWalker));
    }

    /**
     * @param Parser $parser
     *
     * @return void
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->left = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->operator = $parser->StringExpression();
        $parser->match(Lexer::T_COMMA);
        $this->right = $parser->StringPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}