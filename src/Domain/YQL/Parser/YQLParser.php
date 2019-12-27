<?php


namespace Morebec\YDB\Domain\YQL\Parser;

use Morebec\YDB\Domain\YQL\ExpressionNode;
use Morebec\YDB\Domain\YQL\ExpressionOperator;
use Morebec\YDB\Domain\YQL\Query\Operator;
use Morebec\YDB\Domain\YQL\Query\Term;
use Morebec\YDB\Domain\YQL\TermNode;
use Morebec\YDB\Utils\Stack;

class YQLParser
{
    /** @var Stack */
    private $operatorStack;

    /** @var Stack */
    private $termStack;

    public function __construct()
    {
        $this->termStack = new Stack();
        $this->operatorStack = new Stack();
    }

    /**
     * Implementation of the shunting yard algorithm where logical operators can be considered
     * as if they were + and *
     * @param Token[] $tokens
     * @return mixed
     */
    public function parse(array $tokens) {
        // Skip the FIND CARDINALITY FROM TABLE WHERE, this was already verified in the TermLexer
        $tokens = array_slice($tokens, 5);

        foreach ($tokens as $token) {
            $type = $token->getType();

            if($this->isTokenTerm($token)) {
                /** @var TermToken $token */
                $this->termStack->push(new TermNode(new Term(
                    $token->getColumnToken()->getValue(),
                    new Operator($token->getOperatorToken()->getValue()),
                    $token->getValueToken()->getValue()
                )));
            } elseif($this->isTokenOperator($token) && $this->operatorStack->isEmpty()) {
                $this->operatorStack->push($token);
            } elseif($this->isTokenOperator($token) && !$this->operatorStack->isEmpty()) {
                $top = $this->operatorStack->peek();
                $topPrecedence = $this->getPrecedenceFor($top);
                $tokenPrecedence = $this->getPrecedenceFor($token);
                if($tokenPrecedence > $topPrecedence) {
                    $this->operatorStack->push($token);
                }
            } elseif($type->isEqualTo(TokenType::PAREN()) && $token->getValue() === '(') {
                $this->operatorStack->push($token);
            } elseif ($type->isEqualTo(TokenType::PAREN()) && $token->getValue() === ')') {
                $top = $this->operatorStack->peek();
                while($top->getValue() !== '(') {
                    $operatorToken = $this->operatorStack->pop();

                    // reverse order since we are relying on a stack
                    $term2 = $this->termStack->pop();
                    $term1 = $this->termStack->pop();

                    $operator = new ExpressionOperator($operatorToken->getValue());

                    $node = new ExpressionNode($term1, $operator, $term2);
                    $this->termStack->push($node);
                    $top = $this->operatorStack->peek();
                }
                $this->operatorStack->pop();
            } else {
                if(!$token->getType()->isEqualTo(TokenType::EOX())) {
                    throw new \InvalidArgumentException("Unexpected token {$token}");
                }
            }
        }

        $top = $this->operatorStack->peek();
        while(!$this->operatorStack->isEmpty() && $top->getValue() !== '(') {
            $operatorToken = $this->operatorStack->pop();
            $term2 = $this->termStack->pop();
            $term1 = $this->termStack->pop();
            $operator = new ExpressionOperator($operatorToken->getValue());

            $node = new ExpressionNode($term1, $operator, $term2);
            $this->termStack->push($node);
            $top = $this->operatorStack->peek();
        }
        // There should be only a single node that is the root node
        return $this->termStack->pop();
    }

    /**
     * Returns the precedence of an operator or a ()
     * @param string $o
     * @return int
     */
    private function getPrecedenceFor(string $o): int
    {
        $precedences = [
            ')' => 0,
            '(' => 0,
            TokenType::EXPR_OPERATOR_AND => 2,
            TokenType::EXPR_OPERATOR_OR => 1
        ];
        return $precedences[$o];
    }

    /**Indicates if a token is an expression operator
     * @param Token $token
     * @return bool
     */
    private function isTokenOperator(Token $token): bool
    {
        $type = $token->getType();
        return $type->isEqualTo(TokenType::EXPR_OPERATOR_AND()) || $type->isEqualTo(TokenType::EXPR_OPERATOR_OR());
    }

    /**
     * Indicates if a token is a term
     * @param Token $token
     * @return bool
     */
    private function isTokenTerm(Token $token): bool
    {
        $type = $token->getType();
        return $type->isEqualTo(TokenType::TERM());
    }
}