<?php

namespace RRP\Model;

use RRP\Utility\Pagination\CollectionInterface;

/**
 * Class RentRecoveryPlusSearchResults
 *
 * @package RRP\Search
 * @author April Portus <april.portus@barbon.com>
 */
class RentRecoveryPlusSearchResults implements CollectionInterface
{
    /**
     * @var array
     */
    protected $records;

    /**
     * @var int
     */
    protected $totalRecords;

    /**
     * Constructor
     *
     * @param array $records
     * @param int $totalRecords
     */
    public function __construct(array $records, $totalRecords)
    {
        $this->records = $records;
        $this->totalRecords = $totalRecords;
    }

    /**
     * Set records
     *
     * @param array $records
     * @return $this
     */
    public function setRecords(array $records)
    {
        $this->records = $records;
        return $this;
    }

    /**
     * Get records
     *
     * @return array
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * Set totalRecords
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
     * Get totalRecords
     *
     * @return int
     */
    public function getTotalRecords()
    {
        return $this->totalRecords;
    }
}