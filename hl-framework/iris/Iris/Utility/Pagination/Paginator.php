<?php

namespace Iris\Utility\Pagination;

use Iris\Utility\Pagination\PaginatorInterface;

/**
 * Class Paginator
 *
 * @package Iris\Utility\Pagination
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class Paginator implements PaginatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function paginate(
        \Closure $collectionCallback, $page, $itemsPerPage = 10, $pageRange = 5, $queryString = '', $pageParamName = 'page')
    {
        $pagination = new Pagination();

        // If items per page is null then set extremely high limit
        if (null === $itemsPerPage) {
            $itemsPerPage = 99999999;
        }

        if ($itemsPerPage <= 0) {
            throw new \InvalidArgumentException(
                'Invalid item per page number, must be an unsigned integer');
        }

        if ($page <= 0) {
            throw new \InvalidArgumentException(
                'Invalid page number, must be an unsigned integer');
        }

        /** @var \Iris\Utility\Pagination\CollectionInterface $collection */
        $collection = $collectionCallback(0, 0);

        if (!($collection instanceof CollectionInterface)) {
            throw new \RuntimeException(
                'Collection callback must return Iris\Utility\Pagination\CollectionInterface');
        }

        $pagination->setTotalItemCount($collection->getTotalRecords());

        $totalRecords = $collection->getTotalRecords();

        $pageCount = (int)ceil($totalRecords / $itemsPerPage);
        $current = $page;

        if ($pageRange > $pageCount) {
            $pageRange = $pageCount;
        }

        $delta = (int)ceil($pageRange / 2);

        if ($current - $delta > $pageCount - $pageRange) {
            $pages = range($pageCount - $pageRange + 1, $pageCount);
        }
        else {
            if ($current - $delta < 0) {
                $delta = $current;
            }
            $offset = $current - $delta;
            $pages = range($offset + 1, $offset + $pageRange);
        }

        $limit = ($current - 1) * $itemsPerPage;

        /** @var \Iris\Utility\Pagination\CollectionInterface $collection */
        $collection = $collectionCallback($limit, $itemsPerPage);

        $pagination
            ->setItems($collection->getRecords())
            ->setPages($pages)
            ->setTotalItemCount($totalRecords)
            ->setCurrentPageNumber($current)
            ->setFirstPageNumber(1)
            ->setTotalPageCount($pageCount)
            ->setItemsPerPage($itemsPerPage)
            ->setLastPageNumber($pageCount)
            ->setFirstPageInRange(min($pages))
            ->setLastPageInRange(max($pages))
            ->setCurrentItemCount(count($collection->getRecords()))
            ->setQueryString(
                $this->_buildQueryString($queryString, $pageParamName)
            )
        ;

        if ($current - 1 > 0) {
            $pagination->setPreviousPageNumber($current - 1);
        }

        if ($current + 1 <= $pageCount) {
            $pagination->setNextPageNumber($current + 1);
        }

        return $pagination;
    }

    /**
     * Build query string, subtracting the page number
     *
     * @param string $queryString
     * @param string $pageParamName
     * @return string
     */
    private function _buildQueryString($queryString, $pageParamName = 'page')
    {
        return ltrim(preg_replace('/'.$pageParamName.'=\d+/', '', urldecode($queryString)), '&');
    }
}