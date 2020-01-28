<?php 

namespace Morebec\YDB\YQL\Query;

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
        int $startTime,
        int $endTime,
        int $queryPlannerStartTime,
        int $queryPlannerEndTime
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
        return $this->startTime;
    }

    /**
     * @return int
     */
    public function getEndTime(): int
    {
        return $this->endTime;
    }

    /**
     * Returns the duration of the query in ms
     * @return int
     */
    public function getDuration(): int
    {
        return $this->endTime - $this->startTime;
    }

    /**
     * Returns the duration of the query in ms
     * @return int
     */
    public function getQueryPlannerDuration(): int
    {
        return $this->queryPlannerEndTime - $this->queryPlannerStartTime;
    }

    /**
     * @return int
     */
    public function getQueryPlannerStartTime(): int
    {
        return $this->queryPlannerStartTime;
    }

    /**
     * @return int
     */
    public function getQueryPlannerEndTime(): int
    {
        return $this->queryPlannerEndTime;
    }
}