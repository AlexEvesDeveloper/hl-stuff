<?php
/**
 * Model definition for the legacy customer search datasource.  Generally used
 * by the Insurance Munt Manager.
 */
class Datasource_Insurance_Legacy_CoverSearch extends Zend_Db_Table_Multidb {

    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_legacy_homelet';
    protected $_name = 'policy';
    protected $_primary = 'policynumber';
    /**#@-*/

    /**
     * Searches the legacy DB for insurance cover that match the given
     * criteria.
     *
     * Munting query and algorithm taken from legacy code in
     * homeletuk-www/connect/actions/login.php
     *
     * @todo This is a nasty hacky bit of code that belongs in a stored
     * procedure, it's only here because it has to use the legacy MySQL 4 DB.
     *
     * @param mixed $agentschemeno Agent's agent scheme number.
     * @param array $criteria Optional associative array of insurance customer
     * search criteria.
     * @param string $sort Optional sort-by string, value of which should be
     * empty or one of the keys in $muntingSearchOptions below.
     *
     * @return array Array of arrays of bare results, empty array if no results.
     */
    public function searchCovers($polno) {

     
        $muntingQuery = "SELECT printableName,
            sumInsured,
            premium 
            FROM policyCover, policyOptions
            WHERE policyCover.policyNumber = " . $this->_db->quote($polno) . "
        	AND policyOptions.policyOptionID = policyCover.policyOptionID ";
           
       
        // Now shove munting query through Zend_DB
        $db = $this->getAdapter();
        $select = $db->query($muntingQuery);

        // Put results into a dirty array
        $output = array();
        foreach($select->fetchAll() as $resultRow) {
            $output[] = $resultRow;
        }
        return $output;
    }

}