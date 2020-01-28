<?php


namespace Morebec\YDB\YQL\Parser;


class Token
{
    /**
     * @var TokenType
     */
    private $tokenType;

    /**
     * @var mixed
     */
    private $value;

    protected function __construct(TokenType $tokenType, $value)
    {
        $this->tokenType = $tokenType;
        $this->value = $value;
    }

    /**
     * @param TokenType $type
     * @param mixed $value
     * @return self
     */
    public static function create(TokenType $type, $value): self
    {
        return new static($type, $value);
    }

    /**
     * @return TokenType
     */
    public function getType(): TokenType
    {
        return $this->tokenType;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString()
    {
        return (string)$this->value;
    }
}