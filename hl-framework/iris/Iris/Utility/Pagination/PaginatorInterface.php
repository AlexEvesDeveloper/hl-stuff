<?php

namespace Iris\Utility\Pagination;

/**
 * Class PaginatorInterface
 *
 * @package Iris\Utility\Pagination
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
interface PaginatorInterface
{
    /**
     * Paginate based on results from a callback
     *
     * @param callable $collectionCallback
     * @param int $page
     * @param int|null $itemsPerPage Null being a search for all items
     * @param int $pageRange
     * @param string $queryString
     * @param string $pageParamName
     * @return \Iris\Utility\Pagination\PaginationInterface
     */
    public function paginate(
        \Closure $collectionCallback, $page, $itemsPerPage = 10, $pageRange = 5, $queryString = '', $pageParamName = 'page');
}