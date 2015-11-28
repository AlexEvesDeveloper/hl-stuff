<?php

namespace Iris\Utility\Pagination;

/**
 * Class CollectionInterface
 *
 * @package Iris\Utility\Pagination
 * @author Ashley Dawson <ashley.dawson@barbon.com>
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