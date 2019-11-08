<?php 

namespace Morebec\YDB\YQL;

use Assert\Assertion;
use Morebec\ValueObjects\ValueObjectInterface;
use Morebec\YDB\Database\QueryInterface;
use Morebec\YDB\Database\RecordInterface;

/**
 * Query supporting the YQL language
 */
class YQLQuery implements QueryInterface
{
    /** @var string expression */
    private $expression;

    /** @var YQL */
    private $expressionLanguage;

    function __construct(string $expression)
    {
        Assertion::notBlank($expression, 'Query Expression cannot be blank');
        $this->expressionLanguage = new YQL();
        $this->expression = $expression;
    }

    /**
     * Indicates if a record matches this query
     * @param  RecordInterface $r query
     * @return bool             true if it matches otherwise false
     */
    public function matches(RecordInterface $record): bool
    {
        return $this->expressionLanguage->evaluate($this->expression, $record->toArray());
    }

    /**
     * Returns the list of criteria used in this query
     * @return array
     */
    public function getCriteria(): array
    {
        return [];
    }

    /**
     * Indicates if this value object is equal to abother value object
     * @param  ValueObjectInterface $valueObject othervalue object to compare to
     * @return boolean                           true if equal otherwise false
     */
    public function isEqualTo(ValueObjectInterface $valueObject): bool
    {
        return (string)$this == (string)$valueObject;
    }

    public function __toString()
    {
        return $this->expression;
    }
}
