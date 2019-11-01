<?php 

namespace Morebec\YDB;

use Assert\Assertion;
use Morebec\YDB\Database\RecordInterface;

/**
 * Criterion
 */
class Criterion
{
    /** @var string field */
    private $field;

    /** @var operator */
    private $operator;

    /** @var mixed value */
    private $value;

    function __construct(string $field, Operator $operator, $value)
    {
        Assertion::notBlank($field);

        $this->field = $field;
        $this->operator = $operator;
        $this->value = $value;
    }

    /**
     * Indicates if it matches a record
     * @param  RecordInterface $record record
     * @return bool                  true if record matches, otherwise false
     */
    public function matches(RecordInterface $record): bool
    {
        if(!$record->hasField($this->field)) {
            return false;
        }

        
        $value = $record->getFieldValue($this->field);

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
            return in_array($this->value, $value);

        } elseif ($this->operator == Operator::NOT_IN) {
            return !in_array($this->value, $value);
        }

        throw new \LogicException(sprintf("Unsupported operator: '%s'", $this->operator));
        
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return Operator
     */
    public function getOperator(): Operator
    {
        return $this->operator;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function __toString()
    {
        return sprintf("%s %s %s", $this->field, $this->operator, $this->value);
    }
}