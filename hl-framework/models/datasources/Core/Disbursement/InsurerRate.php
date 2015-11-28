<?php
/**
* Model definition for the InsurerRate table
* 
*/
class Datasource_Core_Disbursement_InsurerRate extends Zend_Db_Table_Multidb {
    protected $_name = 'InsurerRate';
    protected $_primary = 'id';
    protected $_multidb = 'db_legacy_homelet';
    
    /**
    * Finds a set of insurer rates by policy option and policy startdate
    *
    * @param int id
    * @param date $date
    * @return Array 
    *
    * @example $Insurerrate[]=$insurerRate->getInsurerRatebyDate(24,'2011-01-01');
    */ 
    function getInsurerRatebyDate ($policyOptionID, $date = null) {
        if (is_null($date)) $date = date("Y-m-d");
        
        $select = $this->select()
                  ->where("policyOptionID = ?", $policyOptionID)
                  ->where("startdate <= ?", $date)
                  ->where(" (enddate >= ? OR enddate='0000-00-00') ", $date)
                  ->where("whitelabelID is null");
        $row = $this->fetchAll($select);
        
        return $row->toArray();
       /* $returnArray = array();
		foreach($row as $currentRow) {						
			$returnArray[$currentRow['insurerID']] = $currentRow['rate'];
		}
		
		
		//Finalise the return value consistent with this functions contract.
		if(empty($returnArray)) {
			
			return false;
		}
		else {
			
			return $returnArray;
		}*/
        
    }
}
?>