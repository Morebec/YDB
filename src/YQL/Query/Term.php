<?php

namespace Morebec\YDB\YQL\Query;


use Morebec\ValueObjects\ValueObjectInterface;
use Morebec\YDB\Document;

/**
 * Term
 */
class Term
{
    /** @var string field */
    private $field;

    /** @var TermOperator */
    private $operator;

    /** @var mixed value */
    private $value;

    /**
     * Constructs a Term
     * @param string $field name of the field to test
     * @param TermOperator $operator operator
     * @param mixed $value expected value
     */
    public function __construct(string $field, TermOperator $operator, $value)
    {
        if(!$field) {
            throw new \InvalidArgumentException('The field name of a term cannot be blank');
        }
        $this->field = $field;
        $this->operator = $operator;
        $this->value = $value;

        if ($operator->isEqualTo(TermOperator::IN()) || $operator->isEqualTo(TermOperator::NOT_IN())) {
            if(!is_array($value)) {
                throw new \InvalidArgumentException("The right operand of a term must ba an array when used with operator {$operator}");
            }
        }
    }

    /**
     * Returns the name of the field of evaluated by this Term
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    public function getValue()
    {
        return $this->value;
    }

    /**
     * Indicates if the Term supports a given field
     * @param  string $fieldName name of the field
     * @return bool              true if the field is supported, otherwise false
     */
    public function supportsField(string $fieldName): bool
    {
        return $this->field === $fieldName;
    }

    /**
     * Indicates if a value matches this Term
     * @param  mixed $value the value to test
     * @return bool true if record matches, otherwise false
     */
    public function valueMatches($value): bool
    {
        switch ($this->operator) {
            case TermOperator::EQUAL:
                return $value === $this->value;

            case TermOperator::LOOSELY_EQUALS:
                return $value == $this->value;

            case TermOperator::NOT_EQUAL:
                return $value !== $this->value;

            case TermOperator::LOOSELY_NOT_EQUAL:
                return $value != $this->value;

            case TermOperator::LESS_THAN:
                return $value < $this->value;

            case TermOperator::GREATER_THAN:
                return $value > $this->value;

            case TermOperator::LESS_OR_EQUAL:
                return $value <= $this->value;

            case TermOperator::GREATER_OR_EQUAL:
                return $value >= $this->value;

            case TermOperator::IN:
                return in_array($value, $this->value, true);

            case TermOperator::NOT_IN:
                return !in_array($value, $this->value, true);

            case TermOperator::CONTAINS:
                return in_array($this->value, $value, true);

            case TermOperator::NOT_CONTAINS:
                return !in_array($this->value, $value, true);
        }

        throw new \LogicException("Unsupported operator: '{$this->operator}'");
    }

    /**
     * Indicates if it matches a record
     * @param Document $document record
     * @return bool                  true if record matches, otherwise false
     */
    public function matchesDocument(Document $document): bool
    {
        if (!$document->hasField($this->field)) {
            return false;
        }

        $value = $document[$this->field];

        return $this->valueMatches($value);
    }

    public function __toString()
    {
        try {
            return "{$this->field} {$this->operator} {$this->convertValueToString($this->value)}";
        } catch (\Exception $e) {
            return 'INVALID EXPRESSION';
        }
    }

    /**
     * Converts a value to a string
     * @param $value
     * @return string
     * @throws \Exception
     */
    private function convertValueToString($value): string
    {
        try {
            $str = \json_encode($value, JSON_THROW_ON_ERROR, 512);
            if(!$str) {
                throw new \Exception('Could not convert value to string');
            }
        } catch (\Exception $e) {
            throw new \Exception("Error converting value to string: {$e->getMessage()}", $e);
        }

        return $str;
    }

    public function isEqualTo(ValueObjectInterface $vo): bool
    {
        return (string)$this === (string)$vo;
    }
}
