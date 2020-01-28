<?php

namespace YQL\Parser;

use Morebec\YDB\YQL\Parser\Lexer;
use Morebec\YDB\YQL\Parser\TokenType;
use PHPUnit\Framework\TestCase;

class LexerTest extends TestCase
{

    public function testLex()
    {
        $lexer = new Lexer();
        $token = $lexer->lex('FIND')[0];
        $this->assertTrue($token->getType()->isEqualTo(TokenType::FIND()));

        $token = $lexer->lex('ALL')[0];
        $this->assertTrue($token->getType()->isEqualTo(TokenType::ALL()));

        $token = $lexer->lex('ONE')[0];
        $this->assertTrue($token->getType()->isEqualTo(TokenType::ONE()));

        $token = $lexer->lex('FROM')[0];
        $this->assertTrue($token->getType()->isEqualTo(TokenType::FROM()));

        $token = $lexer->lex('Identifier')[0];
        $this->assertTrue($token->getType()->isEqualTo(TokenType::IDENTIFIER()));

        $token = $lexer->lex('WHERE')[0];
        $this->assertTrue($token->getType()->isEqualTo(TokenType::WHERE()));

        $token = $lexer->lex('===')[0];
        $this->assertTrue($token->getType()->isEqualTo(TokenType::OPERATOR_STRICTLY_EQUAL()));

        $token = $lexer->lex('!==')[0];
        $this->assertTrue($token->getType()->isEqualTo(TokenType::OPERATOR_STRICTLY_NOT_EQUAL()));

        $token = $lexer->lex('>')[0];
        $this->assertTrue($token->getType()->isEqualTo(TokenType::OPERATOR_GREATER_THAN()));

        $token = $lexer->lex('<')[0];
        $this->assertTrue($token->getType()->isEqualTo(TokenType::OPERATOR_LESS_THAN()));

        $token = $lexer->lex('>=')[0];
        $this->assertTrue($token->getType()->isEqualTo(TokenType::OPERATOR_GREATER_OR_EQUAL()));

        $token = $lexer->lex('<=')[0];
        $this->assertTrue($token->getType()->isEqualTo(TokenType::OPERATOR_LESS_OR_EQUAL()));

        $token = $lexer->lex('in')[0];
        $this->assertTrue($token->getType()->isEqualTo(TokenType::OPERATOR_IN()));

        $token = $lexer->lex('not_in')[0];
        $this->assertTrue($token->getType()->isEqualTo(TokenType::OPERATOR_NOT_IN()));

        $token = $lexer->lex('56')[0];
        $this->assertTrue($token->getType()->isEqualTo(TokenType::NUMERIC_LITERAL()), "Not equal, got: {$token->getType()}");

        $token = $lexer->lex('56.0')[0];
        $this->assertTrue($token->getType()->isEqualTo(TokenType::NUMERIC_LITERAL()));

        $token = $lexer->lex("'Hello World'")[0];
        $this->assertTrue($token->getType()->isEqualTo(TokenType::STRING_LITERAL()));

        $token = $lexer->lex("'Hello O\' World'")[0];
        $this->assertTrue($token->getType()->isEqualTo(TokenType::STRING_LITERAL()));
    }
}
