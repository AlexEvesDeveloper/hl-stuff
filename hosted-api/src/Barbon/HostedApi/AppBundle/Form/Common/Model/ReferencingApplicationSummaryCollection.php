<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Model;

use Barbon\IrisRestClient\Annotation as Iris;

/**
 * @Iris\Entity\ReferencingApplicationSummaryCollection
 */
class ReferencingApplicationSummaryCollection
{
    /**
     * @Iris\Field
     * @var int
     */
    private $totalRecords;

    /**
     * @Iris\Field
     * @var ReferencingApplicationSummary[]
     */
    private $records;

    /**
     * Get total records
     *
     * @return int
     */
    public function getTotalRecords()
    {
        return $this->totalRecords;
    }

    /**
     * Set total records
     *
     * @param int $totalRecords
     * @return $this
     */
    public function setTotalRecords($totalRecords)
    {
        $this->totalRecords = $totalRecords;
        return $this;
    }

    /**
     * Get all records
     *
     * @return ReferencingApplicationSummary[]
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * Set all records
     *
     * @param ReferencingApplicationSummary[] $records
     * @return $this
     */
    public function setRecords(array $records)
    {
        $this->records = $records;
        return $this;
    }
}