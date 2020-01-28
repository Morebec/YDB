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
    /**
     * @var string
     */
    private $rawValue;

    protected function __construct(TokenType $tokenType, $value, string $rawValue)
    {
        $this->tokenType = $tokenType;
        $this->value = $value;
        $this->rawValue = $rawValue;
    }

    /**
     * @param TokenType $type
     * @param mixed $value
     * @param string $rawValue
     * @return self
     */
    public static function create(TokenType $type, $value, string $rawValue): self
    {
        return new static($type, $value, $rawValue);
    }

    /**
     * @return TokenType
     */
    public function getType(): TokenType
    {
        return $this->tokenType;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getRawValue(): string
    {
        return $this->rawValue;
    }

    public function __toString()
    {
        return (string)$this->value;
    }
}
