<?php

namespace Iris\Utility\Pagination;

use Iris\Utility\Pagination\PaginationInterface;

/**
 * Class Pagination
 *
 * @package Iris\Utility\Pagination
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class Pagination implements PaginationInterface
{
    /**
     * @var array
     */
    protected $items;

    /**
     * @var array
     */
    protected $pages;

    /**
     * @var int
     */
    protected $totalItemCount;

    /**
     * @var int
     */
    protected $itemsPerPage;

    /**
     * @var int
     */
    protected $currentPageNumber;

    /**
     * @var int
     */
    protected $firstPageNumber;

    /**
     * @var int
     */
    protected $lastPageNumber;

    /**
     * @var int
     */
    protected $totalPageCount;

    /**
     * @var string
     */
    protected $queryString;

    /**
     * @var int
     */
    protected $previousPageNumber;

    /**
     * @var int
     */
    protected $nextPageNumber;

    /**
     * @var int
     */
    protected $firstPageInRange;

    /**
     * @var int
     */
    protected $lastPageInRange;

    /**
     * @var int
     */
    protected $currentItemCount;

    /**
     * {@inheritdoc}
     */
    public function setQueryString($queryString)
    {
        $this->queryString = $queryString;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryString()
    {
        return $this->queryString;
    }

    /**
     * {@inheritdoc}
     */
    public function setPages(array $pages)
    {
        $this->pages = $pages;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentPageNumber($currentPageNumber)
    {
        $this->currentPageNumber = $currentPageNumber;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentPageNumber()
    {
        return $this->currentPageNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function setFirstPageNumber($firstPageNumber)
    {
        $this->firstPageNumber = $firstPageNumber;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstPageNumber()
    {
        return $this->firstPageNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function setItems(array $items)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function setItemsPerPage($itemsPerPage)
    {
        $this->itemsPerPage = $itemsPerPage;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastPageNumber($lastPageNumber)
    {
        $this->lastPageNumber = $lastPageNumber;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastPageNumber()
    {
        return $this->lastPageNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalItemCount($totalItemCount)
    {
        $this->totalItemCount = $totalItemCount;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalItemCount()
    {
        return $this->totalItemCount;
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalPageCount($totalPageCount)
    {
        $this->totalPageCount = $totalPageCount;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalPageCount()
    {
        return $this->totalPageCount;
    }

    /**
     * {@inheritdoc}
     */
    public function setFirstPageInRange($firstPageInRange)
    {
        $this->firstPageInRange = $firstPageInRange;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstPageInRange()
    {
        return $this->firstPageInRange;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastPageInRange($lastPageInRange)
    {
        $this->lastPageInRange = $lastPageInRange;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastPageInRange()
    {
        return $this->lastPageInRange;
    }

    /**
     * {@inheritdoc}
     */
    public function setNextPageNumber($nextPageNumber)
    {
        $this->nextPageNumber = $nextPageNumber;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getNextPageNumber()
    {
        return $this->nextPageNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function setPreviousPageNumber($previousPageNumber)
    {
        $this->previousPageNumber = $previousPageNumber;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPreviousPageNumber()
    {
        return $this->previousPageNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentItemCount($currentItemCount)
    {
        $this->currentItemCount = $currentItemCount;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentItemCount()
    {
        return $this->currentItemCount;
    }
}