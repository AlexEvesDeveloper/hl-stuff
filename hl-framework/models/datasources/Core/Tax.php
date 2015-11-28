<?php
/**
* Model definition for the tax table
* 
*/
class Datasource_Core_Tax extends Zend_Db_Table_Multidb {
    protected $_name = 'tax';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet';
    
    /**
    * Finds a specific tax rate by its type
    *
    * @param string $type
    * @param date $date
    * @return double 
    *
    * @example $ipt=$tax->getTaxByType('ipt');
    */ 
    function getTaxByType ($type, $date = null) {
        if (is_null($date)) $date = date("Y-m-d");
        $type = strtoupper($type); // Just to be on the safe side ;-)
        $select = $this->select()
                  ->from($this->_name,array('id', 'rate'))
                  ->where('type = ?', $type)
                  ->where('start_date <= ?', $date)
                  ->where('end_date >= ? OR end_date IS NULL', $date);
        $row = $this->fetchRow($select);
        if (count($row) > 0) {
        	return array(
        	'id'	=> $row['id'],
        	'rate'	=> round($row['rate'],3));
        } else {
            // Can't get tax info - log a warning
            Application_Core_Logger::log("Can't get tax by type from table {$this->_name}", 'warning');
            return null;
        }
    }
    
    /**
    * Finds a specific tax rate by its type and postcode
    *
    * @param string $type
    * @param string $postcode
    * @param date $date
    * @return double 
    *
    * @example $ipt=$tax->getTaxByTypeAndPostcode('ipt', 'LN6 7DL');
    */ 
    function getTaxByTypeAndPostcode($type, $postcode, $date = null) {
    	/*
    	This fix was put in place to accompany the Compliance Remedial Action 3 project which had a task
    	to stop charging IPT incorrectly to Jersey, Guernsey and Isle of White postcodes
    	
    	However the CRA3 task was removed due to size and therefore this code can't go live until
    	the accompanying legacy fixes are done
    	
    	
    	REMOVE COMMENTS TO GO LIVE
    	
    	// First of all get the tax rate for this type and date
    	$tax = $this->getTaxByType($type, $date);
    	
    	if (!is_null($tax)) {
	    	// Now check to see if the postcode is in an exclusion zone
	    	$postcodeExclusions = new Datasource_Core_Tax_PostcodeExclusions();
	    	$isExcluded = $postcodeExclusions->isPostcodeExcluded($tax['id'], $postcode);
	    	
	    	if ($isExcluded) $tax['rate'] = 0;
	    	return $tax;
	    } else {
	    	return null;
	    }
	    */
	    
	    return $this->getTaxByType($type, $date);
    }
}
?>