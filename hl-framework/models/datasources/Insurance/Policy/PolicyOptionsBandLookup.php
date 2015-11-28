<?php
/**
* Model definition for the tax table
* 
*/
class Datasource_Insurance_Policy_PolicyOptionsBandLookup extends Zend_Db_Table_Multidb {
    protected $_name = 'policyOptionsBandLookup';
    protected $_primary = 'policyOptionID';
    protected $_multidb = 'db_legacy_homelet';
    
    /**
    * Finds a set of band by policy option 
    *
    * @param int id
    * @return Array 
    *
    * @example $policyOptionBand[]=$policyOptionBand->getBandbyOptionID(24);
    */ 
    function getBandbyOptionID ($policyOptionID) {
		 $fields = array('band','uplimit');
		
        $select = $this->select()
		          ->from($this->_name, $fields)
                  ->where('policyOptionID = ?', $policyOptionID);
				  
				  
        $row = $this->fetchAll($select);
        
		
		$returnArray = array();
		foreach($row as $currentRow) {						
			$returnArray[$currentRow['band']]=$currentRow['uplimit'];
		}
		
		
		//Finalise the return value consistent with this functions contract.
		if(empty($returnArray)) {
			
			return false;
		}
		else {
			
			return $returnArray;
		}
		
		       
    }
}
?>