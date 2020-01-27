<?php


namespace Morebec\YDB\YQL\Parser;


class TermToken extends Token
{
    /**
     * @var Token
     */
    private $columnToken;
    /**
     * @var Token
     */
    private $operatorToken;
    /**
     * @var Token
     */
    private $valueToken;

    public function __construct(Token $columnToken, Token $operatorToken, Token $valueToken)
    {
        parent::__construct(TokenType::TERM(), "$columnToken $operatorToken $valueToken");
        $this->columnToken = $columnToken;
        $this->operatorToken = $operatorToken;
        $this->valueToken = $valueToken;
    }

    /**
     * @return Token
     */
    public function getColumnToken(): Token
    {
        return $this->columnToken;
    }

    /**
     * @return Token
     */
    public function getOperatorToken(): Token
    {
        return $this->operatorToken;
    }

    /**
     * @return Token
     */
    public function getValueToken(): Token
    {
        return $this->valueToken;
    }
}