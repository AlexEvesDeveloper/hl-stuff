<?php

namespace RRP\Utility\Pagination;

/**
 * Interface CollectionInterface
 *
 * @package RRP\Utility\Pagination
 * @author April Portus <april.portus@barbon.com>
 */
interface CollectionInterface
{
    /**
     * Set records
     *
     * @param array $records
     * @return $this
     */
    public function setRecords(array $records);

    /**
     * Get records
     *
     * @return array
     */
    public function getRecords();

    /**
     * Set totalRecords
     *
     * @param int $totalRecords
     * @return $this
     */
    public function setTotalRecords($totalRecords);

    /**
     * Get totalRecords
     *
     * @return int
     */
    public function getTotalRecords();
}