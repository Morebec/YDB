<?php

namespace Morebec\YDB\Entity\Query;

use Assert\Assertion;
use Morebec\ValueObjects\ValueObjectInterface;
use Morebec\YDB\Contract\TermInterface;
use Morebec\YDB\Contract\RecordInterface;

/**
 * Term
 */
class Term implements TermInterface
{
    /** @var string field */
    private $field;

    /** @var operator */
    private $operator;

    /** @var mixed value */
    private $value;

    /**
     * Constructs a Term
     * @param string   $field    name of the field to test
     * @param Operator $operator operator
     * @param mixed   $value    expected value
     */
    public function __construct(string $field, Operator $operator, $value)
    {
        Assertion::notBlank($field);

        $this->field = $field;
        $this->operator = $operator;
        $this->value = $value;
    }

    /**
     * Returns the name of the field of evaluated by this Term
     * @return string
     */
    public function getFieldName(): string
    {
        return $this->field;
    }

    /**
     * Indicates if the Term supports a given field
     * @param  string $fieldName name of the field
     * @return bool              true if the field is supported, otherwise false
     */
    public function supportsField(string $fieldName): bool
    {
        $this->field === $fieldName;
    }

    /**
     * Indicates if a value matches this Term
     * @param  mixed $value the value to test
     * @return bool true if record matches, otherwise false
     */
    public function valueMatches($value): bool
    {
        // TODO: Replace with switch
        if ($this->operator == Operator::EQUAL) {
            return $value == $this->value;
        } elseif ($this->operator == Operator::STRICTLY_EQUAL) {
            return $value === $this->value;
        } elseif ($this->operator == Operator::NOT_EQUAL) {
            return $value != $this->value;
        } elseif ($this->operator == Operator::STRICTLY_NOT_EQUAL) {
            return $value !== $this->value;
        } elseif ($this->operator == Operator::LESS_THAN) {
            return $value < $this->value;
        } elseif ($this->operator == Operator::GREATER_THAN) {
            return $value > $this->value;
        } elseif ($this->operator == Operator::LESS_OR_EQUAL) {
            return $value <= $this->value;
        } elseif ($this->operator == Operator::GREATER_OR_EQUAL) {
            return $value >= $this->value;
        } elseif ($this->operator == Operator::IN) {
            return in_array($value, $this->value);
        } elseif ($this->operator == Operator::NOT_IN) {
            return !in_array($value, $this->value);
        } elseif ($this->operator == Operator::CONTAINS) {
            return in_array($this->value, $value);
        } elseif ($this->operator == Operator::NOT_CONTAINS) {
            return !in_array($this->value, $value);
        }

        throw new \LogicException(sprintf("Unsupported operator: '%s'", $this->operator));
    }

    /**
     * Indicates if it matches a record
     * @param  RecordInterface $record record
     * @return bool                  true if record matches, otherwise false
     */
    public function matchesRecord(RecordInterface $record): bool
    {
        if (!$record->hasField($this->field)) {
            return false;
        }

        $value = $record->getFieldValue($this->field);

        return $this->valueMatches($value);
    }

    public function __toString()
    {
        $valueString = is_string($this->value) ? sprintf("'%s'", $this->value) : (string)$this->value;
        return sprintf("%s %s %s", $this->field, $this->operator, $valueString);
    }

    public function isEqualTo(ValueObjectInterface $vo): bool
    {
        return (string)$this === (string)$vo;
    }
}
