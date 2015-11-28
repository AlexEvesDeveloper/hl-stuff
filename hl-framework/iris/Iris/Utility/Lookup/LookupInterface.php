<?php

namespace Iris\Utility\Lookup;

/**
 * Class LookupInterface
 *
 * @package Iris\Utility\Lookup
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
interface LookupInterface
{
    /**
     * Find a single lookup item by ID
     *
     * @param string $categoryName
     * @param int $id
     * @return \Iris\Utility\Lookup\Model\LookupItem
     */
    public function findById($categoryName, $id);
}