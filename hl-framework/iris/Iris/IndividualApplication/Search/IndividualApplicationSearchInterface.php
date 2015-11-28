<?php

namespace Iris\IndividualApplication\Search;

use Iris\IndividualApplication\Model\SearchIndividualApplicationsCriteria;

/**
 * Class IndividualApplicationSearchInterface
 *
 * @package Iris\IndividualApplication\Search
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
interface IndividualApplicationSearchInterface
{
    /**
     * Search individual applications
     *
     * @param int $currentAgentSchemeNumber Agent scheme number of searcher
     * @param SearchIndividualApplicationsCriteria $criteria
     * @param int $offset
     * @param int $limit
     * @return \Iris\IndividualApplication\Model\SearchIndividualApplicationsResults
     */
    public function search($currentAgentSchemeNumber, SearchIndividualApplicationsCriteria $criteria, $offset, $limit);
}