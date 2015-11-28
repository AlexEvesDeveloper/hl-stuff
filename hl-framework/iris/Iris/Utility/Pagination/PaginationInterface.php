<?php

namespace Iris\Utility\Pagination;

/**
 * Class PaginationInterface
 *
 * @package Iris\Utility\Pagination
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
interface PaginationInterface
{
    /**
     * Set queryString
     *
     * @param string $queryString
     * @return $this
     */
    public function setQueryString($queryString);

    /**
     * Get queryString
     *
     * @return string
     */
    public function getQueryString();

    /**
     * Set pages
     *
     * @param array $pages
     * @return $this
     */
    public function setPages(array $pages);

    /**
     * Get pages
     *
     * @return array
     */
    public function getPages();

    /**
     * Set currentPageNumber
     *
     * @param int $currentPageNumber
     * @return $this
     */
    public function setCurrentPageNumber($currentPageNumber);

    /**
     * Get currentPageNumber
     *
     * @return int
     */
    public function getCurrentPageNumber();

    /**
     * Set firstPageNumber
     *
     * @param int $firstPageNumber
     * @return $this
     */
    public function setFirstPageNumber($firstPageNumber);

    /**
     * Get firstPageNumber
     *
     * @return int
     */
    public function getFirstPageNumber();

    /**
     * Set items
     *
     * @param array $items
     * @return $this
     */
    public function setItems(array $items);

    /**
     * Get items
     *
     * @return array
     */
    public function getItems();

    /**
     * Set itemsPerPage
     *
     * @param int $itemsPerPage
     * @return $this
     */
    public function setItemsPerPage($itemsPerPage);

    /**
     * Get itemsPerPage
     *
     * @return int
     */
    public function getItemsPerPage();

    /**
     * Set lastPageNumber
     *
     * @param int $lastPageNumber
     * @return $this
     */
    public function setLastPageNumber($lastPageNumber);

    /**
     * Get lastPageNumber
     *
     * @return int
     */
    public function getLastPageNumber();

    /**
     * Set totalItemCount
     *
     * @param int $totalItemCount
     * @return $this
     */
    public function setTotalItemCount($totalItemCount);

    /**
     * Get totalItemCount
     *
     * @return int
     */
    public function getTotalItemCount();

    /**
     * Set totalPageCount
     *
     * @param int $totalPageCount
     * @return $this
     */
    public function setTotalPageCount($totalPageCount);

    /**
     * Get totalPageCount
     *
     * @return int
     */
    public function getTotalPageCount();

    /**
     * Set firstPageInRange
     *
     * @param int $firstPageInRange
     * @return $this
     */
    public function setFirstPageInRange($firstPageInRange);

    /**
     * Get firstPageInRange
     *
     * @return int
     */
    public function getFirstPageInRange();

    /**
     * Set lastPageInRange
     *
     * @param int $lastPageInRange
     * @return $this
     */
    public function setLastPageInRange($lastPageInRange);

    /**
     * Get lastPageInRange
     *
     * @return int
     */
    public function getLastPageInRange();

    /**
     * Set nextPageNumber
     *
     * @param int $nextPageNumber
     * @return $this
     */
    public function setNextPageNumber($nextPageNumber);

    /**
     * Get nextPageNumber
     *
     * @return int
     */
    public function getNextPageNumber();

    /**
     * Set previousPageNumber
     *
     * @param int $previousPageNumber
     * @return $this
     */
    public function setPreviousPageNumber($previousPageNumber);

    /**
     * Get previousPageNumber
     *
     * @return int
     */
    public function getPreviousPageNumber();

    /**
     * Set currentItemCount
     *
     * @param int $currentItemCount
     * @return $this
     */
    public function setCurrentItemCount($currentItemCount);

    /**
     * Get currentItemCount
     *
     * @return int
     */
    public function getCurrentItemCount();
}