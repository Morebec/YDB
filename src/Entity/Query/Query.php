<?php 

namespace Morebec\YDB\Entity\Query;

use Morebec\ValueObjects\ValueObjectInterface;
use Morebec\YDB\Database\QueryInterface;
use Morebec\YDB\Database\RecordIdInterface;
use Morebec\YDB\Database\RecordInterface;

/**
 * Query
 */
class Query implements QueryInterface
{
    /** @var array list of criteria */
    private $ands;

    /** @var array list of criteria */
    private $ors;

    /**
     * Creates a query object with a single Criterion
     * @param  string   $fieldName name of the field
     * @param  Operator $operator  nameof the operator
     * @param  [type]   $value     value
     * @return Query
     */
    public static function findByField(
        string $fieldName, 
        Operator $operator, 
        $value
    ): Query
    {
        return new Query([
            new Criterion($fieldName, Operator::STRICTLY_EQUAL(), $value)
        ]);
    }

    /**
     * Creates a find by id is equal to a certain value Query object
     * @param  RecordIdInterface $id id
     * @return Query
     */
    public static function findById(RecordIdInterface $id): Query
    {
        return new Query([
            new Criterion('id', Operator::EQUAL(), $id)
        ]);
    }

    /**
     * Constructs the query instance
     * It uses two lists of queries:
     * - ands: list of all criteria a record must match to be part of the result
     * - ors:  list of criteria a record can match to override the ands criteria
     * @param array $ands mandatory queries
     * @param array $ors  optional queries
     */
    function __construct(array $ands, array $ors = [])
    {
        $this->ands = $ands;
        $this->ors = $ors;
    }

    /**
     * Indicates if a record matches this query
     * @param  RecordInterface $r query
     * @return bool             true if it matches otherwise false
     */
    public function matches(RecordInterface $record): bool
    {
        return $this->matchesAnds($record) || $this->matchesOr($record);
    }

    /**
     * Indicates if a record matches all the queries from
     * the ands list
     * @param  RecordInterface $record record
     * @return bool                  true if matches all, otherwise false
     */
    private function matchesAnds(RecordInterface $record): bool
    {
        foreach ($this->ands as $criteria) {
            if(!$criteria->matches($record)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Indicates if a record matches at least one query from the ands list
     * @param  RecordInterface $record record
     * @return bool                  true if it maches at least one, otherwise false
     */
    private function matchesOr(RecordInterface $record): bool
    {
        foreach ($this->ors as $criteria) {
            if($criteria->matches($record)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the list of criteria used in this query
     * @return array
     */
    public function getCriteria(): array
    {
        return array_merge($this->ands, $this->ors);
    }

    public function getAndCriteria(): array
    {
        return $this->ands;
    }

    public function getOrCriteria(): array
    {
        return $this->ors;
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

    /**
     * Returns a string representation of the value object
     *
     * @return string
     */
    public function __toString()
    {
        $ands = join(' and ', $this->ands);
        $ors = join(' or ', $this->ors);

        if (!empty($ors)) {
            return join(" or ", [$ands, $ors]);
        }


        return $ands;
    }
}