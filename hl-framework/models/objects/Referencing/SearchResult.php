<?php

/**
 * Encapsulates the results of a search for a reference.
 */
class Model_Referencing_SearchResult {

    /**#@+
     * Use these consts to indicate how results should be ordered.
     */
    const REFNO_ASC = 'refno_asc';
    const REFNO_DESC = 'refno_desc';
    const FIRSTNAME_ASC = 'firstname_asc';
    const FIRSTNAME_DESC = 'firstname_desc';
    const LASTNAME_ASC = 'lastname_asc';
    const LASTNAME_DESC = 'lastname_desc';
    const ADDRESS1_ASC = 'address1_asc';
    const ADDRESS1_DESC = 'address1_desc';
    const STARTDATE_ASC = 'startdate_asc';
    const STARTDATE_DESC = 'startdate_desc';
    /**#@-*/
    
    /**#@+
     * Use these consts to indicate the search result row limit
     */
    const TEN_ROWS = 10;
    const TWENTY_FIVE_ROWS = 25;
    const FIFTY_ROWS = 50;
    const ONE_HUNDRED_ROWS = 100;
    const ALL_ROWS = 'All';
    /**#@-*/
	
	/**
	 * Holds the array of search results for the current page.
	 *
	 * @var array
	 */
    public $results;
	
	/**
	 * Defines the number of results per page.
	 *
	 * @var integer
	 */
	public $resultsPerPage;
    
	/**#@+
	 * Page numbering variables.
	 *
	 * @var integer
	 */
	public $previousPageNumber;
	public $currentPageNumber;
	public $nextPageNumber;
	public $maxPageNumber;
    public $totalNumberOfResults;
	/**#@-*/
}

?>