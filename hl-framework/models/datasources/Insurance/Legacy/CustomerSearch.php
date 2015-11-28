<?php

/**
 * Model definition for the legacy customer search datasource.  Generally used
 * by the Insurance Munt Manager.
 */
class Datasource_Insurance_Legacy_CustomerSearch extends Zend_Db_Table_Multidb {

    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_legacy_homelet';
    protected $_name = 'policyQuoteMerge';
    protected $_primary = 'policynumber';
    /**#@-*/

    /**
     * Searches the legacy DB for insurance customers that match the given
     * criteria.
     *
     * Original query available in homeletuk-www/connect/actions/customerSearchResults.php
     *
     * @param mixed $agentschemeno
     * Agent's scheme number.
     * 
     * @param array $criteria
     * Optional associative array of insurance customer search criteria.
     *
     * @return array
     * An array of arrays of Zend_Db_Table_Row_Abstract results,
     * empty array if no results.
     */
    public function getCustomers($agentschemeno, $criteria = array()) {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('p' => $this->_name), array('*', 'policyNumber' => 'policynumber'));
        $select->joinInner('customer', 'customer.refno = p.refno');
        
        $select->where($this->quoteInto('companyschemenumber = ? ', $agentschemeno));
        $select->where("p.paystatus != 'DELETED' ");
        
        if(!empty($criteria['firstName'])) {
            
            $like = '%' . $criteria['firstName'] . '%';
            $select->where($this->quoteInto('firstname LIKE ? ', $like));
        }
        
        if(!empty($criteria['lastName'])) {
            
            $like = '%' . $criteria['lastName'] . '%';
            $select->where($this->quoteInto('lastname LIKE ? ', $like));
        }
        
        if(!empty($criteria['address1'])) {
            
            $like = '%' . $criteria['address1'] . '%';
            $select->where($this->quoteInto('personaladdress1 LIKE ? ', $like));
        }
        
        if(!empty($criteria['address2'])) {
            
            $like = '%' . $criteria['address2'] . '%';
            $select->where($this->quoteInto('personaladdress3 LIKE ? ', $like));
        }
        
        if(!empty($criteria['postcode'])) {
            
            $like = '%' . $criteria['postcode'] . '%';
            $select->where($this->quoteInto('personalpostcode LIKE ? ', $like));
        }
        
        if(!empty($criteria['telephone'])) {
            
            $select->where($this->quoteInto('phone1 = ? ', $criteria['telephone']));
        }
        
        if(!empty($criteria['email'])) {
            
            $like = '%' . $criteria['email'] . '%';
            $select->where($this->quoteInto('email LIKE ? ', $like));
        }
        
        $select->having("0 = (SELECT COUNT(*) FROM policyCover c LEFT JOIN policyOptions o ON c.policyOptionID = o.policyOptionID WHERE policyOption = 'liabilitytp' AND sumInsured > 0 AND c.policynumber = p.policynumber)");
        
        //Put results into a dirty array
        $output = array();
        foreach($this->fetchAll($select) as $resultRow) {
            
            $output[] = $resultRow;
        }

        return $output;
    }
}