<?php
/**
* Model definition for the tax postcode exclusion table
* 
*/
class Datasource_Core_Tax_PostcodeExclusions extends Zend_Db_Table_Multidb {
    protected $_name = 'tax_postcode_exclusions';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet';
    
    /**
    * Check to see if a specific postcode is excluded from a specific tax
    *
    * @param string $taxID
    * @param string $postcode
    * @return boolean 
    *
    * @example $excludeTax = $taxPostcodeExclusions->isPostcodeExcluded(3,'LN1 1LN');
    */ 
    function isPostcodeExcluded ($taxID, $postcode) {
    	// Remove spaces and convert postcode to upper case - just to be on the safe side
        $postcode = strtoupper(str_replace(' ', '', $postcode));
        $select = $this->select()
        		       ->where('postcode = ?', $postcode)
                       ->where('tax_id = ?', $taxID);
        
        $row = $this->fetchRow($select);
        if (count($row)>0) return true;
        
        return false;
    }
}
?>