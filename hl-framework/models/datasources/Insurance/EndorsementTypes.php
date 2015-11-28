<?php

/**
 * Model definition for the endorsements table. 
 */
class Datasource_Insurance_EndorsementTypes extends Zend_Db_Table_Multidb {
    
	protected $_multidb = 'db_legacy_homelet';
	protected $_name = 'endorsements';
    protected $_primary = 'endID';
	
	
	/**
	 * Returns all the endorsement types.
	 *
	 * This method reads in all the endorsement types recognised by the
	 * system and populates each one into an EndorsementType object. An
	 * array of these objects will then be returned.
	 * 
	 * @return array
	 * An array of EndorsementType objects, each one encapsulating a valid endorsement
	 * type.
	 */
	public function getEndorsementTypes() {
		
	    $select = $this->select();
        $endorsements = $this->fetchAll($select);

        if (count($endorsements) > 0) {
        	
    		//Populate the data retrieved into PreviousClaimType objects.
    		$returnArray = array();
            foreach ($endorsements as $currentEndorsement) {
    
                $endorsementType = new Model_Insurance_EndorsementType();
                $endorsementType->setID($currentEndorsement['endID']);
                $endorsementType->setName($currentEndorsement['name']);
                $endorsementType->setDescription($currentEndorsement['text']);    				
    			$returnArray[] = $endorsementType;
            }
            
            return $returnArray;
        }
        else {
        	
            // Can't get previous claim types - log a warning
            Application_Core_Logger::log("Can't get endorsement types from table {$this->_name}", 'warning');
        }
	}
}

?>