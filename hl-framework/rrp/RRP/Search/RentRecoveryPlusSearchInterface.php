<?php

namespace RRP\Search;

use RRP\Model\RentRecoveryPlusSearchCriteria;

/**
 * Interface RentRecoveryPlusSearchCriteriaInterface
 *
 * @package RRP\Search
 * @author April Portus <april.portus@barbon.com>
 */
interface RentRecoveryPlusSearchInterface
{
    /**
     * Search individual applications
     *
     * @param int $currentAgentSchemeNumber Agent scheme number of searcher
     * @param RentRecoveryPlusSearchCriteria $criteria
     * @param int $offset
     * @param int $limit
     * @return \RRP\Model\RentRecoveryPlusSearchResults
     */
    public function search($currentAgentSchemeNumber, RentRecoveryPlusSearchCriteria $criteria, $offset, $limit);
}