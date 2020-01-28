<?php


namespace Morebec\YDB\YQL\Parser;

use InvalidArgumentException;

class TermParser
{
    private $tokens = [];

    private $queryTokens = [];

    /**
     * @param Token[] $tokens
     * @return Token[]
     */
    public function lexTerms(array $tokens): array
    {
        if(count($tokens) === 0) {
            return [];
        }

        $ret = $this->processFindAt(0, $tokens);
        return array_reverse($ret);
    }

    /**
     * @param int $index
     * @param Token[] $tokens
     * @return Token[]
     */
    private function processFindAt(int $index, array $tokens): array {
        $this->expectOneInTokenTypeAt([TokenType::FIND()], $index, $tokens);

        $ret = $this->processCardinalityAt($index + 1, $tokens);
        $ret[] = $tokens[$index];
        return $ret;
    }

    /**
     * @param int $index
     * @param Token[] $tokens
     * @return Token[]
     */
    private function processCardinalityAt(int $index, array $tokens): array
    {
        // Expect that the current token is find
        $this->expectOneInTokenTypeAt([TokenType::ALL(), TokenType::ONE()], $index, $tokens);

        $ret = $this->processFromAt($index + 1, $tokens);
        $ret[] = $tokens[$index];
        return $ret;
    }

    /**
     * @param int $index
     * @param Token[] $tokens
     * @return Token[]
     */
    private function processFromAt(int $index, array $tokens): array
    {
        $this->expectOneInTokenTypeAt([TokenType::FROM()], $index, $tokens);

        $ret = $this->processCollectionAt($index + 1, $tokens);
        $ret[] = $tokens[$index];
        return $ret;
    }

    /**
     * @param int $index
     * @param Token[] $tokens
     * @return Token[]
     */
    private function processCollectionAt(int $index, array $tokens): array
    {
        // Either a where clause or EOX (For cases like FIND ALL FROM collection)
        $this->expectOneInTokenTypeAt([TokenType::IDENTIFIER(), TokenType::EOX()], $index, $tokens);

        $token = $tokens[$index];
        $nextIndex = $index + 1;
        $nextToken = $tokens[$nextIndex];

        if ($nextToken->getType()->isEqualTo(TokenType::EOX())) {
            $ret =  $this->parseEndOfExpression($nextIndex, $tokens);
            $ret[] = $token;
            return $ret;
        }

        $ret = $this->processWhereAt($nextIndex, $tokens);
        $ret[] = $tokens[$index];
        return $ret;
    }

    /**
     * @param int $index
     * @param Token[] $tokens
     * @return Token[]
     */
    private function processWhereAt(int $index, array $tokens): array
    {
        $this->expectOneInTokenTypeAt([TokenType::WHERE()], $index, $tokens);
        $token = $tokens[$index  + 1];

        if($token->getType()->isEqualTo(TokenType::PAREN())) {
            $ret = $this->processOpeningParenAt($index + 1, $tokens);
            $ret[] = $tokens[$index];
            return $ret;
        }

        if($token->getType()->isEqualTo(TokenType::IDENTIFIER())) {
            $ret = $this->processTermAt($index + 1, $tokens);
            $ret[] = $tokens[$index];
            return $ret;
        }

        $this->throwUnexpectedTokenException($token, $index + 1, [TokenType::PAREN(), TokenType::IDENTIFIER()]);
    }

    /**
     * @param int $index
     * @param Token[] $tokens
     * @return Token[]
     */
    private function processOpeningParenAt(int $index, array $tokens): array
    {
        $this->expectOneInTokenTypeAt([TokenType::PAREN()], $index, $tokens);
        $token = $tokens[$index];
        if($token->getValue() !== '(') {
            $this->throwUnexpectedTokenException($token, $index, ['(']);
        }


        $ret = $this->processTermAt($index + 1, $tokens);
        $ret[] = $tokens[$index];
        return $ret;
    }

    /**
     * @param int $index
     * @param Token[] $tokens
     * @return Token[]
     */
    private function processClosingParenAt(int $index, array $tokens): array
    {
        $token = $tokens[$index];
        if($token->getValue() !== ')') {
            $this->throwUnexpectedTokenException($token, $index, [')']);
        }

        // After a closing it must be either the end or an Expression operator
        $nextIndex = $index + 1;
        $nextToken = $tokens[$nextIndex];
        $nextTokenType = $nextToken->getType();

        if($nextTokenType->isEqualTo(TokenType::EOX())) {
            $ret =  $this->parseEndOfExpression($index + 1, $tokens);
            $ret[] = $tokens[$index];
            return $ret;
        }

        if($nextTokenType->isEqualTo(TokenType::EXPR_OPERATOR_AND()) || $nextTokenType->isEqualTo(TokenType::EXPR_OPERATOR_OR())) {
            $ret = $this->processExpressionOperator($nextIndex, $tokens);
            $ret[] = $tokens[$index];
            return $ret;
        }

        $this->throwUnexpectedTokenException($token, $nextIndex, [TokenType::EOX(), TokenType::EXPR_OPERATOR_AND(), TokenType::EXPR_OPERATOR_OR()]);
    }

    /**
     * @param int $index
     * @param Token[] $tokens
     * @return Token[]
     */
    private function processTermAt(int $index, array $tokens): array
    {
        // A Term should be of the form IDENTIFIER OPERATOR IDENTIFIER
        $fieldIndex = $index;
        $operatorIndex = $index + 1;
        $valueIndex = $operatorIndex + 1;

        $this->expectOneInTokenTypeAt([TokenType::IDENTIFIER()], $fieldIndex, $tokens);
        $this->expectOneInTokenTypeAt(TokenType::TERM_OPERATORS(), $operatorIndex, $tokens);
        $this->expectOneInTokenTypeAt([TokenType::IDENTIFIER(), TokenType::STRING_LITERAL(), TokenType::NUMERIC_LITERAL()], $valueIndex, $tokens);

        $field = $tokens[$fieldIndex];
        $operator = $tokens[$operatorIndex];
        $value = $tokens[$valueIndex];

        $term = new TermToken($field, $operator, $value);

        // At this stage we can either have a ) an expression operator (AND\OR) or an EOX

        $nextIndex = $valueIndex + 1;

        $this->expectOneInTokenTypeAt([
            TokenType::PAREN(),
            TokenType::EXPR_OPERATOR_AND(),
            TokenType::EXPR_OPERATOR_OR(),
            TokenType::EOX()
        ], $nextIndex, $tokens);

        $nextToken = $tokens[$nextIndex];
        $nextTokenType = $nextToken->getType();

        if($nextTokenType->isEqualTo(TokenType::EXPR_OPERATOR_AND()) || $nextTokenType->isEqualTo(TokenType::EXPR_OPERATOR_OR())) {
            $ret = $this->processExpressionOperator($nextIndex, $tokens);
            $ret[] = $term;
            return $ret;
        }

        if($nextTokenType->isEqualTo(TokenType::PAREN())) {
            $ret = $this->processClosingParenAt($nextIndex, $tokens);
            $ret[] = $term;
            return $ret;
        }

        if($nextTokenType->isEqualTo(TokenType::EOX())) {
            $ret =  $this->parseEndOfExpression($nextIndex, $tokens);
            $ret[] = $term;
            return $ret;
        }

        throw new \LogicException('Should not reach');
    }

    /**
     * @param int $index
     * @param Token[] $tokens
     * @return Token[]
     */
    private function processExpressionOperator(int $index, array $tokens): array
    {
        $this->expectOneInTokenTypeAt([TokenType::EXPR_OPERATOR_AND(), TokenType::EXPR_OPERATOR_OR()], $index, $tokens);

        // Next can either be a TERM or a (
        $nextIndex = $index  + 1;
        $nextToken = $tokens[$nextIndex];
        $nextTokenType = $nextToken->getType();

        if($nextTokenType->isEqualTo(TokenType::PAREN())) {
            $ret = $this->processOpeningParenAt($nextIndex, $tokens);
            $ret[] = $tokens[$index];
            return $ret;
        }

        $ret = $this->processTermAt($nextIndex, $tokens);
        $ret[] = $tokens[$index];
        return $ret;
    }

    /**
     * @param int $index
     * @param Token[] $tokens
     * @return Token[]
     */
    private function parseEndOfExpression(int $index, array $tokens): array
    {
        $token = $tokens[$index];
        return [$token];
    }

    /**
     * @param TokenType[] $expectedTypes
     * @param int $index
     * @param Token[] $tokens
     */
    private function expectOneInTokenTypeAt(array $expectedTypes, int $index, array $tokens): void
    {
        $token = $tokens[$index];
        foreach ($expectedTypes as $expectedType) {
            $tokenType = $token->getType();
            if($tokenType->isEqualTo($expectedType)) {
                return;
            }
        }
        $this->throwUnexpectedTokenException($token, $index, $expectedTypes);
    }

    /**
     * @param Token $token
     * @param int $index
     * @param TokenType[]|string[] $expectedTypes
     * @throws InvalidArgumentException
     */
    private function throwUnexpectedTokenException(Token $token, int $index, array $expectedTypes): void
    {
        $expectedTypesAsString = implode(', ', $expectedTypes);
        throw new InvalidArgumentException("Unexpected token $token, expected $expectedTypesAsString at $index");
    }
}