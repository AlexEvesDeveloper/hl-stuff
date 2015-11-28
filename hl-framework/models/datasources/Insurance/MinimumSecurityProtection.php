<?php

/**
 * Datasource for querying MSP
 */
class Datasource_Insurance_MinimumSecurityProtection extends Zend_Db_Table_Multidb {
    
	protected $_multidb = 'db_homelet_insurance_com';     
	protected $_name = 'HLTPPminsecurityriskareas';
    protected $_primary = 'id';
    
	/**
	 * Retrieves MSP flag.
	 *
	 * Attempts to retrieve riskarea record (minimum security protection flag) associated
	 * with postcode passed in.  
	 *
     * @param string $postcode
     * Find out HLTPPminsecurityriskareas.riskarea ~ msp
	 *
	 * @return riskarea which is minimum security protection (0 or 1)
	 */
	public function getMinimumSecurityProtection($postcode) {	 		
		
		$select = $this->select();
		$select->from($this->_name,array('minp' => 'riskarea'));
		$select->where('postcode = ?', $postcode);
        $select->where('startdate <= NOW()');
        $select->where('(enddate >= NOW() OR enddate = "0000-00-00")');
        $row = $this->fetchRow($select);
	
		return $row['minp'];
	}
	
}

?>