<?php

/**
 * Model definition for the sharer_occuptions table.
 */
class Datasource_Insurance_TenantsContentsPlus_SharerOccupations extends Zend_Db_Table_Multidb {
    
    protected $_multidb = 'db_homelet';  
	protected $_name = 'sharer_occupations';
    protected $_primary = 'id';
	
	
	/**
	 * Gets all the sharer occupations.
	 *
	 * Retrieves all the sharer occupations, encapsulates them in individual
	 * Model_Insurance_TenantsContentsPlus_SharerOccupation objects, and returns them
	 * in an array.
	 *
	 * @return array
	 * Returns an array of Model_Insurance_TenantsContentsPlus_SharerOccupation objects.
	 */
	public function getOccupations() {
		
		$select = $this->select();
        $occupationTypesArray = $this->fetchAll($select);
		
        if (count($occupationTypesArray) > 0) {
    		$returnArray = array();
            foreach($occupationTypesArray as $currentOccupationType) {
                
                $sharerOccupation = new Model_Insurance_TenantsContentsPlus_SharerOccupation();
    			$sharerOccupation->setId($currentOccupationType->id);
    			$sharerOccupation->setType($currentOccupationType->type);
    			$sharerOccupation->setLabel($currentOccupationType->label);
    			
    			$returnArray[] = $sharerOccupation;
            }
    		return $returnArray;
        } else {
            // Can't get sharer occupations - log a warning
            Application_Core_Logger::log("Can't get sharer occupations from table {$this->_name}", 'warning');
            return false;
        }
	}
}

?>