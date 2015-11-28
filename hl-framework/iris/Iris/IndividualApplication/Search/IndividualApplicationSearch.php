<?php

namespace Iris\IndividualApplication\Search;

use Iris\IndividualApplication\Model\SearchIndividualApplicationsCriteria;
use Iris\IndividualApplication\Model\SearchIndividualApplicationsResults;
use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\ReferencingApplicationClient;
use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplicationFindResult;
use Zend_Cache;
use Zend_Registry;

/**
 * Class IndividualApplicationSearch
 *
 * @todo The search interleaving and caching belongs in a generic set of its own classes.
 * @todo Skinny search sometimes misses a record here and there so is currently disabled.
 *
 * @package Iris\IndividualApplication\Search
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 * @author Paul Swift <paul.swift@barbon.com>
 */
class IndividualApplicationSearch implements IndividualApplicationSearchInterface
{
    /**
     * Data source string for tagging HRT data
     */
    const DATA_SOURCE_HRT = 'hrt';

    /**
     * Data source string for tagging IRIS data
     */
    const DATA_SOURCE_IRIS = 'iris';

    /**
     * Application status incomplete ID, mapped to categories from applicationStatuses lookup.
     */
    const APPLICATION_STATUS_INCOMPLETE = 1;

    /**
     * Application status in progress ID, mapped to categories from applicationStatuses lookup.
     */
    const APPLICATION_STATUS_IN_PROGRESS = 2;

    /**
     * Application status complete ID, mapped to categories from applicationStatuses lookup.
     */
    const APPLICATION_STATUS_COMPLETE = 3;

    /**
     * Application status awaiting applicant details ID, mapped to categories from applicationStatuses lookup.
     */
    const APPLICATION_STATUS_AWAITING_APPLICANT_DETAILS = 4;

    /**
     * Application status cancelled ID, mapped to categories from applicationStatuses lookup.
     */
    const APPLICATION_STATUS_CANCELLED = 5;

    /**
     * Application status declined ID, mapped to categories from applicationStatuses lookup.
     */
    const APPLICATION_STATUS_DECLINED = 6;

    /**
     * @var array Map between the application status strings used in HRT and IDs used in IRIS.
     */
    private static $applicationStatusMap = array(
        'Incomplete' => array(
            self::APPLICATION_STATUS_INCOMPLETE,
            self::APPLICATION_STATUS_IN_PROGRESS,
            self::APPLICATION_STATUS_AWAITING_APPLICANT_DETAILS,
        ),
        'Complete' => array(
            self::APPLICATION_STATUS_COMPLETE,
            self::APPLICATION_STATUS_CANCELLED,
            self::APPLICATION_STATUS_DECLINED,
        ),
    );

    /**
     * @var ReferencingApplicationClient
     */
    private $referencingApplicationClient;

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

        $this->referencingApplicationClient = Zend_Registry::get('iris_container')
            ->get('iris_sdk_client_registry')
            ->getAgentContext()
            ->getReferencingApplicationClient()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function search($currentAgentSchemeNumber, SearchIndividualApplicationsCriteria $criteria, $offset, $limit)
    {
        if ($this->debugMode) {
            error_log('--- Search selection method invoked ---');
        }

        // Check to see if search is just for the result count
        if (0 == $offset && 0 == $limit) {
            // Get a result count only
            return $this->resultCount($currentAgentSchemeNumber, $criteria);
        } else {
            // Do a full search
            return $this->mainSearch($currentAgentSchemeNumber, $criteria, $offset, $limit);
        }
    }

    /**
     * Gets the total number of results across IRIS and HRT that are searchable for a given agent scheme number and
     * search criteria.
     *
     * @param $currentAgentSchemeNumber
     * @param SearchIndividualApplicationsCriteria $criteria
     * @return SearchIndividualApplicationsResults
     */
    protected function resultCount($currentAgentSchemeNumber, SearchIndividualApplicationsCriteria $criteria)
    {
        if ($this->debugMode) {
            error_log('--- Result count method invoked ---');
        }

        // Get class name for use in cache keys
        $classNameExploded = explode('\\', __CLASS__);
        $className = array_pop($classNameExploded);

        // Check data cache for a result

        // Instantiate data cache (DC)
        $dataCache = $this->initCache('data');

        // Generate hash for key into data cache - again include ASN
        $dataCriteriaHash = $className . '_DATA_' . $criteria->getHash(array(
            'asn' => $currentAgentSchemeNumber,
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
            $aData = $this->searchInIris($currentAgentSchemeNumber, $criteria, 0, 1);
            $dData = $this->searchInHrt($currentAgentSchemeNumber, $criteria, 0, 1);

            $dataCacheResults['totalRecords'] = array();
            $totalRecords = $dataCacheResults['totalRecords'] = $aData['totalRecords'] + $dData['totalRecords'];

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

        return new SearchIndividualApplicationsResults($records, $totalRecords);
    }

    /**
     * Runs a search across IRIS and HRT and uses a caching strategy to minimise the impact on the databases.
     *
     * @param $currentAgentSchemeNumber
     * @param SearchIndividualApplicationsCriteria $criteria
     * @param $offset
     * @param $limit
     * @return SearchIndividualApplicationsResults
     */
    protected function mainSearch($currentAgentSchemeNumber, SearchIndividualApplicationsCriteria $criteria, $offset, $limit)
    {
        if ($this->debugMode) {
            error_log('--- Main search method invoked, offset: ' . $offset . ', limit: ' . $limit . ' ---');
        }

        // Instantiate paged results cache (PC)
        $pagedResultsCache = $this->initCache('paged_results');

        // Get class name for use in cache keys
        $classNameExploded = explode('\\', __CLASS__);
        $className = array_pop($classNameExploded);

        // 1.  Check if hash(C + ASN + O + L) for P(n) gives a PC hit, if not:

        // Generate hash for key into paged results cache - includes ASN so not to overwrite/accidentally read queries
        //   for other agents!
        $pagedResultsCriteriaHash = $className . '_PAGERESULT_' . $criteria->getHash(array(
            'asn' => $currentAgentSchemeNumber,
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
            $dataCache = $this->initCache('data');

            // Generate hash for key into data cache - again include ASN
            $dataCriteriaHash = $className . '_DATA_' . $criteria->getHash(array(
                'asn' => $currentAgentSchemeNumber,
            ));

            if ($this->debugMode) {
                error_log('--- Checking data cache ---');
            }
            $dataCacheResults = $dataCache->load($dataCriteriaHash);

            // Get some stats about data cache contents (if any)
            $dataCacheStoredTotalRecordCount = 0;
            $dataCacheStoredARecordCount = 0;
            $dataCacheStoredDRecordCount = 0;

            if (
                false !== $dataCacheResults &&
                isset($dataCacheResults['records']) &&
                is_array($dataCacheResults['records'])
            ) {
                if ($this->debugMode) {
                    error_log('--- Data cache exists, getting stats ---');
                }

                foreach($dataCacheResults['records'] as $record) {
                    if ($record->getDataSource() == self::DATA_SOURCE_IRIS) {
                        $dataCacheStoredARecordCount++;
                    }
                    else {
                        $dataCacheStoredDRecordCount++;
                    }
                }
                $dataCacheStoredTotalRecordCount = $dataCacheStoredARecordCount + $dataCacheStoredDRecordCount;

                if ($this->debugMode) {
                    error_log('--- Data cache current stats: A records: ' . $dataCacheStoredARecordCount . ', D records: ' . $dataCacheStoredDRecordCount . ', total: ' . $dataCacheStoredTotalRecordCount . ' ---');
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
                    // todo: Fix skinny search - it seems to occasionally miss records
                    if (false) {//$dataCacheStoredTotalRecordCount > $offset) {

                        if ($this->debugMode) {
                            error_log('--- Running skinny search, A offset: ' . ($dataCacheStoredARecordCount + 1) . ', A limit: ' . $limit . ', D offset: ' . ($dataCacheStoredDRecordCount + 1) . ', D limit: ' . $limit . ' ---');
                        }

                        // 8.              Get L results from A starting at count(DC(A)) + 1
                        $aData = $this->searchInIris($currentAgentSchemeNumber, $criteria, $dataCacheStoredARecordCount + 1, $limit);

                        // 9.              Get L results from D starting at count(DC(D)) + 1
                        $dData = $this->searchInHrt($currentAgentSchemeNumber, $criteria, $dataCacheStoredDRecordCount + 1, $limit);

                        // 10.             Interleave sub-results and save the order to an array on end of DC (eg: D, D, A, D, D, D, D, D, A, D), store in DC against hash(C + ASN)
                        $dataCacheResults['records'] = array_merge(
                            $dataCacheResults['records'],
                            $this->interleaveSearches($aData['records'], $dData['records'])
                        );
                        $dataCacheResults['totalRecords'] = $aData['totalRecords'] + $dData['totalRecords'];

                        if ($this->debugMode) {
                            error_log('--- Storing skinny search results to data cache ---');
                        }
                        $dataCache->save(
                            $dataCacheResults, // Data to cache
                            $dataCriteriaHash, // Cache key based on search criteria and ASN
                            // Tag for forcing this data to become stale from elsewhere in the HLF
                            array($this->cacheTagPrefix . $currentAgentSchemeNumber)
                        );

                    }
                    // 7.          Else:
                    else {

                        if ($this->debugMode) {
                            error_log('--- Running fat search, A & D offset: 0, A & D limit: ' . ($offset + $limit) . ' ---');
                        }

                        // 4.              Get L * P from A
                        $aData = $this->searchInIris($currentAgentSchemeNumber, $criteria, 0, $offset + $limit);

                        // 5.              Get L * P from D
                        $dData = $this->searchInHrt($currentAgentSchemeNumber, $criteria, 0, $offset + $limit);

                        // 6.              Interleave results and save the order to an array (eg: A, A, A, D, A, D, D, A, D, A), store in DC against hash(C + ASN)
                        $dataCacheResults['records'] = $this->interleaveSearches($aData['records'], $dData['records']);
                        $dataCacheResults['totalRecords'] = $aData['totalRecords'] + $dData['totalRecords'];

                        if ($this->debugMode) {
                            error_log('--- Storing fat search results to data cache ---');
                        }
                        $dataCache->save(
                            $dataCacheResults, // Data to cache
                            $dataCriteriaHash, // Cache key based on search criteria and ASN
                            // Tag for forcing this data to become stale from elsewhere in the HLF
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

        }
        // 13. Else:
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

        return new SearchIndividualApplicationsResults($records, $totalRecords);
    }

    /**
     * Interleaves and de-duplicates records from two arrays in the format as returned by $this->searchInIris() and
     * $this->searchInHrt() (currently hardcoded to order by creation date).
     *
     * @param array $a
     * @param array $b
     * @return array Array of records
     */
    private function interleaveSearches($a, $b)
    {
        if ($this->debugMode) {
            error_log('--- Interleave method invoked ---');
        }

        $aRecords = array();
        $bRecords = array();

        // Key records by reference application UUID (to de-duplicate during merge)
        foreach ($a as $key => $val) {
            $aRecords[$val->getReferencingApplicationUuId()] = $val;
        }
        foreach ($b as $key => $val) {
            $bRecords[$val->getReferencingApplicationUuId()] = $val;
        }

        // Merge records from A and B
        $records = array_merge($aRecords, $bRecords);

        // Sort records by descending creation date
        usort($records, function ($x, $y) {
            if ($x->getCreatedAt() == $y->getCreatedAt()) {
                return 0;
            }
            return ($x->getCreatedAt() < $y->getCreatedAt()) ? 1 : -1;
        });

        return $records;
    }

    /**
     * Search in IRIS.
     *
     * @param int $currentAgentSchemeNumber Redundant, included only for signature compatibility with
     *                                      $this->searchInHrt()
     * @param SearchIndividualApplicationsCriteria $criteria
     * @param int $offset
     * @param int $limit
     * @return array Simple array of 'records' containing an array of ReferencingApplicationFindResult and
     *               'totalRecords' containing an integer of all possible records findable with the current criteria
     */
    private function searchInIris($currentAgentSchemeNumber, SearchIndividualApplicationsCriteria $criteria, $offset, $limit)
    {
        if ($this->debugMode) {
            error_log('--- Search in IRIS method invoked ---');
        }

        $applicationStatusIdList = self::getApplicationStatusIdsFromString($criteria->getApplicationStatus());

        $irisResults = $this->referencingApplicationClient->findReferencingApplications(array(
            'referenceNumber' => $criteria->getReferenceNumber(),
            'applicantFirstName' => $criteria->getApplicantFirstName(),
            'applicantLastName' => $criteria->getApplicantLastName(),
            'propertyAddress' => $criteria->getPropertyAddress(),
            'propertyTown' => $criteria->getPropertyTown(),
            'propertyPostcode' => $criteria->getPropertyPostcode(),
            'applicationStatus' => $applicationStatusIdList,
            'productType' => $criteria->getProductType(),
            'offset' => $offset,
            'numberOfRecords' => $limit,
        ));

        $irisRecords = $irisResults->getRecords()->toArray();

        // Tag data source as IRIS
        $records = array();
        foreach ($irisRecords as $key => $record) {
            $record->setDataSource(self::DATA_SOURCE_IRIS);
            $records[$key] = $record;
        }

        $totalRecords = $irisResults->getTotalRecords();

        return array(
            'records' => $records,
            'totalRecords' => $totalRecords,
        );
    }

    /**
     * Search in HRT.
     *
     * @param int $currentAgentSchemeNumber
     * @param SearchIndividualApplicationsCriteria $criteria
     * @param int $offset
     * @param int $limit
     * @return array Simple array of 'records' containing an array of ReferencingApplicationFindResult and
     *               'totalRecords' containing an integer of all possible records findable with the current criteria
     */
    private function searchInHrt($currentAgentSchemeNumber, SearchIndividualApplicationsCriteria $criteria, $offset, $limit)
    {
        if ($this->debugMode) {
            error_log('--- Search in HRT method invoked ---');
        }

        $records = array();

        $hrtReferenceManager = new \Manager_ReferencingLegacy_Munt();

        $hrtCriteria = array(
            'refno' => $criteria->getReferenceNumber(),
            'firstname' => $criteria->getApplicantFirstName(),
            'lastname' => $criteria->getApplicantLastName(),
            'address' => $criteria->getPropertyAddress(),
            'town' => $criteria->getPropertyTown(),
            'postcode' => $criteria->getPropertyPostcode(),
            'state' => $criteria->getApplicationStatus(),
            'type' => $criteria->getProductType(),
        );

        $hrtRows = $hrtReferenceManager->searchLegacyReferences(
            $currentAgentSchemeNumber,
            $hrtCriteria,
            \Model_Referencing_SearchResult::STARTDATE_DESC,
            0,
            $limit,
            $offset
        );

        foreach ($hrtRows->results as $hrtRow) {

            $status = self::APPLICATION_STATUS_INCOMPLETE;
            if ('Complete' == ucfirst(strtolower(trim($hrtRow['resulttx'])))) {
                $status = self::APPLICATION_STATUS_COMPLETE;
            }

            $model = new ReferencingApplicationFindResult();

            $model
                ->setReferenceNumber($hrtRow['RefNo'])
                ->setReferencingApplicationUuId($hrtRow['RefNo'])
                ->setApplicantFirstName($hrtRow['firstname'])
                ->setApplicantLastName($hrtRow['lastname'])
                ->setStreet($hrtRow['address1'])
                ->setCreatedAt($hrtRow['start_time'])
                ->setStatusId($status)
                ->setDataSource(self::DATA_SOURCE_HRT) // Tag data source as HRT
            ;

            $records[] = $model;
        }

        // Get total records by passing in a zero for both offset and limit
        $hrtRecordCount = $hrtReferenceManager->searchLegacyReferences(
            $currentAgentSchemeNumber,
            $hrtCriteria,
            null,
            0,
            0,
            0
        );
        $totalRecords = (int)$hrtRecordCount->totalNumberOfResults;

        return array(
            'records' => $records,
            'totalRecords' => $totalRecords,
        );
    }

    /**
     * Look up an application string as used by HRT from the IRIS ID equivalent.
     *
     * @param int $id IRIS ID (self::APPLICATION_STATUS_INCOMPLETE or self::APPLICATION_STATUS_COMPLETE)
     * @return string|null
     */
    private static function getApplicationStatusStringFromId($id)
    {
        foreach(self::$applicationStatusMap as $key => $val) {
            if (in_array($id, $val)) {
                return $key;
            }
        }

        return null;
    }

    /**
     * Look up a set of application IDs as used by IRIS from the HRT string equivalent and return a comma-separated
     * list.
     *
     * @param string $string HRT string ("Complete" or "Incomplete")
     * @return string|null
     */
    private static function getApplicationStatusIdsFromString($string)
    {
        if (isset(self::$applicationStatusMap[$string])) {
            return implode(',', self::$applicationStatusMap[$string]);
        }

        return null;
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