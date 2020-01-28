<?php


namespace Morebec\YDB\YQL\Parser;

class TermToken extends Token
{
    /**
     * @var Token
     */
    private $fieldToken;
    /**
     * @var Token
     */
    private $operatorToken;
    /**
     * @var Token
     */
    private $valueToken;

    public function __construct(Token $fieldToken, Token $operatorToken, Token $valueToken)
    {
        parent::__construct(TokenType::TERM(), "$fieldToken $operatorToken $valueToken", $valueToken);
        $this->fieldToken = $fieldToken;
        $this->operatorToken = $operatorToken;
        $this->valueToken = $valueToken;
    }

    /**
     * @return Token
     */
    public function getFieldToken(): Token
    {
        return $this->fieldToken;
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
