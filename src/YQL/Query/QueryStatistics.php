<?php 

namespace Morebec\YDB\YQL\Query;

use Morebec\ValueObjects\DateTime\Time\Timestamp;

/**
 * QueryStatistics
 */
class QueryStatistics
{
    /** @var int timestamp when the query was started */
    private $startTime;

    /** @var int timestamp of when the query was completed */
    private $endTime;

    private $queryPlannerStartTime;

    private $queryPlannerEndTime;

    public function __construct(
        Timestamp $startTime, 
        Timestamp $endTime,
        Timestamp $queryPlannerStartTime,
        Timestamp $queryPlannerEndTime
    )
    {
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->queryPlannerStartTime = $queryPlannerStartTime;
        $this->queryPlannerEndTime = $queryPlannerEndTime;
    }

    /**
     * @return int
     */
    public function getStartTime(): int
    {
        return $this->startTime->toInt();
    }

    /**
     * @return int
     */
    public function getEndTime(): int
    {
        return $this->endTime->toInt();
    }

    /**
     * Returns the duration of the query in ms
     * @return int
     */
    public function getDuration(): int
    {
        return $this->endTime->toInt() - $this->startTime->toInt();
    }

    /**
     * Returns the duration of the query in ms
     * @return int
     */
    public function getQueryPlannerDuration(): int
    {
        return $this->queryPlannerEndTime->toInt() - $this->queryPlannerStartTime->toInt();
    }

    /**
     * @return int
     */
    public function getQueryPlannerStartTime(): int
    {
        return $this->queryPlannerStartTime->toInt();
    }

    /**
     * @return int
     */
    public function getQueryPlannerEndTime(): int
    {
        return $this->queryPlannerEndTime->toInt();
    }
}