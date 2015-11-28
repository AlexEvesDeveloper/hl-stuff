<?php

namespace RRP\Search;

use RRP\DependencyInjection\LegacyContainer;
use RRP\Model\RentRecoveryPlusSummary;
use RRP\Model\RentRecoveryPlusSearchCriteria;
use RRP\Model\RentRecoveryPlusSearchResults;
use Zend_Cache;
use Zend_Registry;

/**
 * Class RentRecoveryPlusSearch
 *
 * @package RRP\Search
 * @author April Portus <april.portus@barbon.com>
 */
class RentRecoveryPlusSearch implements RentRecoveryPlusSearchInterface
{
    /**
     * @var object
     */
    private $searchClient;

    /**
     * @var array Associative array of Zend_Cache
     */
    private $cache;

    /**
     * @var string
     */
    private $cachePath;

    /**
     * @var string
     */
    private $cacheTagPrefix;

    /**
     * @var int
     */
    private $cacheLifetime;

    /**
     * @var bool Switch for whether to error_log() what's happening during execution, very useful for debugging the
     *           caching strategy.
     */
    private $debugMode = false;

    /**
     * Constructor
     */
    public function __construct($cachePath, $cacheTagPrefix, $cacheLifetime)
    {
        $this->cachePath = $cachePath;
        $this->cacheTagPrefix = $cacheTagPrefix;
        $this->cacheLifetime = $cacheLifetime;

        $container = new LegacyContainer();
        $this->searchClient = $container->get('rrp.legacy.datasource.search');
    }

    /**
     * {@inheritdoc}
     */
    public function search($currentAgentSchemeNumber, RentRecoveryPlusSearchCriteria $criteria, $offset, $limit)
    {
        if ($this->debugMode) {
            error_log('--- Search selection method invoked ---');
        }

        // Check to see if search is just for the result count
        if (0 == $offset && 0 == $limit) {
            // Get a result count only
            return $this->resultCount($currentAgentSchemeNumber, $criteria);
        }
        else {
            // Do a full search
            return $this->mainSearch($currentAgentSchemeNumber, $criteria, $offset, $limit);
        }
    }

    /**
     * Gets the total number of results that are searchable for a given agent scheme number and search criteria.
     *
     * @param $currentAgentSchemeNumber
     * @param RentRecoveryPlusSearchCriteria $criteria
     * @return RentRecoveryPlusSearchResults
     */
    protected function resultCount($currentAgentSchemeNumber, RentRecoveryPlusSearchCriteria $criteria)
    {
        if ($this->debugMode) {
            error_log('--- Result count method invoked ---');
        }

        // Get class name for use in cache keys
        $classNameExploded = explode('\\', __CLASS__);
        $className = array_pop($classNameExploded);

        // Check data cache for a result

        // Instantiate data cache (DC)
        /** @var object $dataCache */
        $dataCache = $this->initCache('data');

        // Generate hash for key into data cache - again include ASN
        $dataCriteriaHash = $className . '_DATA_' . $criteria->getHash(array(
                'asn' => $currentAgentSchemeNumber,
                'criteria' => $criteria,
            ));

        $dataCacheResults = $dataCache->load($dataCriteriaHash);

        $records = array();

        if (
            false !== $dataCacheResults &&
            isset($dataCacheResults['totalRecords']) &&
            is_numeric($dataCacheResults['totalRecords'])
        ) {
            // Result count hit in data cache
            if ($this->debugMode) {
                error_log('--- Result count found in data cache ---');
            }
            $totalRecords = $dataCacheResults['totalRecords'];
        }
        else {
            // Result count miss in data cache, run simple searches
            if ($this->debugMode) {
                error_log('--- Result count cache miss in data cache, looking up result count ---');
            }
            $data = $this->rrpSearch($currentAgentSchemeNumber, $criteria, 0, 1);

            $dataCacheResults['totalRecords'] = array();
            $totalRecords = $dataCacheResults['totalRecords'] = $data['totalRecords'];

            // Store number of results in data cache
            if ($this->debugMode) {
                error_log('--- Saving result count to data cache ---');
            }
            $dataCache->save(
                $dataCacheResults, // Data to cache
                $dataCriteriaHash, // Cache key based on search criteria and ASN
                // Tag for forcing this data to become stale from elsewhere in the HLF
                array($this->cacheTagPrefix . $currentAgentSchemeNumber)
            );
        }

        return new RentRecoveryPlusSearchResults($records, $totalRecords);
    }

    /**
     * Runs a search and uses a caching strategy to minimise the impact on the databases.
     *
     * @param $currentAgentSchemeNumber
     * @param RentRecoveryPlusSearchCriteria $criteria
     * @param $offset
     * @param $limit
     * @return RentRecoveryPlusSearchResults
     */
    protected function mainSearch($currentAgentSchemeNumber, RentRecoveryPlusSearchCriteria $criteria, $offset, $limit)
    {
        if ($this->debugMode) {
            error_log('--- Main search method invoked, offset: ' . $offset . ', limit: ' . $limit . ' ---');
        }

        // Instantiate paged results cache (PC)
        /** @var object $pagedResultsCache */
        $pagedResultsCache = $this->initCache('paged_results');

        // Get class name for use in cache keys
        $classNameExploded = explode('\\', __CLASS__);
        $className = array_pop($classNameExploded);

        // 1.  Check if hash(C + ASN + O + L) for P(n) gives a PC hit, if not:

        // Generate hash for key into paged results cache - includes ASN so not to overwrite/accidentally read queries
        //   for other agents!
        $pagedResultsCriteriaHash = $className . '_PAGERESULT_' . $criteria->getHash(array(
                'asn' => $currentAgentSchemeNumber,
                'criteria' => $criteria,
                'offset' => $offset,
                'limit' => $limit,
            ));

        if ($this->debugMode) {
            error_log('--- Checking paged results cache ---');
        }
        $pagedResultsCacheResults = $pagedResultsCache->load($pagedResultsCriteriaHash);

        if (false === $pagedResultsCacheResults) {

            if ($this->debugMode) {
                error_log('--- Paged results cache miss ---');
            }

            // 2.      Check if hash(C + ASN) gives a DC hit for P(n), if not:

            // Instantiate data cache (DC)
            /** @var object $dataCache */
            $dataCache = $this->initCache('data');

            // Generate hash for key into data cache - again include ASN
            $dataCriteriaHash = $className . '_DATA_' . $criteria->getHash(array(
                    'asn' => $currentAgentSchemeNumber,
                    'criteria' => $criteria,
                ));

            if ($this->debugMode) {
                error_log('--- Checking data cache ---');
            }
            $dataCacheResults = $dataCache->load($dataCriteriaHash);

            $dataCacheStoredTotalRecordCount = 0;

            if (
                false !== $dataCacheResults &&
                isset($dataCacheResults['records']) &&
                is_array($dataCacheResults['records'])
            ) {
                if ($this->debugMode) {
                    error_log('--- Data cache exists, getting stats ---');
                }

                $dataCacheStoredTotalRecordCount = count($dataCacheResults['records']);

                if ($this->debugMode) {
                    error_log('--- Data cache current stats: total: ' . $dataCacheStoredTotalRecordCount . ' ---');
                }
            }

            if (
                false === $dataCacheResults ||
                $dataCacheStoredTotalRecordCount < ($offset + $limit) * 2
            ) {

                if ($this->debugMode) {
                    error_log('--- Data cache miss or not enough records ---');
                }

                // 3.          Check if hash(C + ASN) gives a DC hit for P(n - 1), if not:

                if (
                    false === $dataCacheResults ||
                    $dataCacheStoredTotalRecordCount < ($offset + $limit) * 2
                ) {
                    if ($dataCacheStoredTotalRecordCount > $offset) {
                        if ($this->debugMode) {
                            error_log('--- Running skinny search, offset: ' . ($dataCacheStoredTotalRecordCount + 1) . ', limit: ' . $limit . ' ---');
                        }

                        $data = $this->rrpSearch($currentAgentSchemeNumber, $criteria, $dataCacheStoredTotalRecordCount + 1, $limit);

                        $dataCacheResults['records'] = array_merge($dataCacheResults['records'], $data['records']);
                        $dataCacheResults['totalRecords'] = $data['totalRecords'];

                        if ($this->debugMode) {
                            error_log('--- Storing skinny search results to data cache ---');
                        }
                        $dataCache->save(
                            $dataCacheResults, // Data to cache
                            $dataCriteriaHash, // Cache key based on search criteria and ASN
                            // Tag for forcing this data to become stale from elsewhere in the HLF
                            array($this->cacheTagPrefix . $currentAgentSchemeNumber)
                        );

                    } // 7.          Else:
                    else {

                        if ($this->debugMode) {
                            error_log('--- Running fat search, offset: 0, limit: ' . ($offset + $limit) . ' ---');
                        }

                        $data = $this->rrpSearch($currentAgentSchemeNumber, $criteria, 0, $offset + $limit);

                        $dataCacheResults['records'] = $data['records'];
                        $dataCacheResults['totalRecords'] = $data['totalRecords'];

                        if ($this->debugMode) {
                            error_log('--- Storing fat search results to data cache ---');
                        }
                        $dataCache->save(
                            $dataCacheResults, // Data to cache
                            $dataCriteriaHash, // Cache key based on search criteria and ASN
                            array($this->cacheTagPrefix . $currentAgentSchemeNumber)
                        );

                    }

                }

            }

            if ($this->debugMode) {
                error_log('--- Taking slice from data cache to make paged results, offset: ' . $offset . ', limit: ' . $limit . ', total held records: ' . count($dataCacheResults['records']) . ' ---');
            }

            // 11.     Get first L results from DC starting at (n - 1) * L
            $records = array_slice($dataCacheResults['records'], $offset, $limit);

            $totalRecords = $dataCacheResults['totalRecords'];

            // 12.     Store in PC for P(n) against hash(C + ASN + O + L)
            $pagedResults = array(
                'records' => $records,
                'totalRecords' => $totalRecords,
            );
            if ($this->debugMode) {
                error_log('--- Storing paged search results to paged results cache ---');
            }
            $pagedResultsCache->save(
                $pagedResults, // Data to cache
                $pagedResultsCriteriaHash, // Cache key based on search criteria, paging and ASN
                // Tag for forcing this data to become stale from elsewhere in the HLF
                array($this->cacheTagPrefix . $currentAgentSchemeNumber)
            );

        } // 13. Else:
        else {
            if ($this->debugMode) {
                error_log('--- Paged results cache hit ---');
            }

            // 14.     Fetch from PC
            $records = $pagedResultsCacheResults['records'];
            $totalRecords = $pagedResultsCacheResults['totalRecords'];
        }

        // 15. Display

        if ($this->debugMode) {
            error_log('--- Finished, returning results ---');
        }

        return new RentRecoveryPlusSearchResults($records, $totalRecords);
    }

    /**
     * rrpSearch
     *
     * @param int $currentAgentSchemeNumber
     * @param RentRecoveryPlusSearchCriteria $criteria
     * @param int $offset
     * @param int $limit
     * @return array Simple array of 'records' containing an array of RentRecoveryPlusSearchResult and
     *               'totalRecords' containing an integer of all possible records findable with the current criteria
     */
    private function rrpSearch($currentAgentSchemeNumber, RentRecoveryPlusSearchCriteria $criteria, $offset, $limit)
    {
        if ($this->debugMode) {
            error_log('--- Search method invoked ---');
        }

        if ($criteria) {
            $this->searchClient->setCriteria(
                $currentAgentSchemeNumber,
                $criteria->getPolicyNumber(),
                $criteria->getLandlordName(),
                $criteria->getPropertyPostcode()
            );
        }

        $rows = $this->searchClient->searchForPolicies($offset, $limit);
        $records = array();
        foreach ($rows as $row) {
            $records[] = RentRecoveryPlusSummary::hydrateFromRow($row);
        }
        $totalRecords = $this->searchClient->getTotalCount();

        return array(
            'records' => $records,
            'totalRecords' => $totalRecords,
        );
    }

    /**
     * Instantiate or fetch a cache.
     *
     * @param string $cacheName
     * @return Zend_Cache
     */
    private function initCache($cacheName)
    {
        if ($this->debugMode) {
            error_log('--- Cache initialisation method invoked for "' . $cacheName . '" ---');
        }

        // Use existing cache if already instantiated
        if (
            isset($this->cache[$cacheName]) &&
            $this->cache[$cacheName] instanceof Zend_Cache
        ) {
            return $this->cache[$cacheName];
        }

        // Configure the cache options
        $frontendOptions = array(
            'lifetime' => $this->cacheLifetime,
            'automatic_serialization' => true,
        );
        $backendOptions = array(
            'cache_dir' => $this->cachePath,
        );

        // Factory off a new cache
        $this->cache[$cacheName] = Zend_Cache::factory(
            'Core',
            'File',
            $frontendOptions,
            $backendOptions
        );

        return $this->cache[$cacheName];
    }
}