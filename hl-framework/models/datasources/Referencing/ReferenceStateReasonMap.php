<?php

/**
* Responsible for reading and writing the reason for the reference state.
*/
class Datasource_Referencing_ReferenceStateReasonMap extends Zend_Db_Table_Multidb {

    protected $_multidb = 'db_referencing';
    protected $_name = 'reference_state_reason_map';
    protected $_primary = array('reference_id', 'reason_id');
    
    public function setReferenceStateReason($referenceStatus) {    
    
        //The reference status may not have a reason (the status may be 'In progress',
		//for example, which does not require a reason).
		if(empty($referenceStatus)) {
			
			return null;
		}
		
		//Delete existing progress items, as its easier than checking for existing items
        //then updating them.
        $where = $this->quoteInto('reference_id = ? ', $referenceStatus->referenceId);
        $this->delete($where);
    	
        //Insert the new state.
        if(!empty($referenceStatus->reasonForState)) {
        	
	    	$data = array(
	            'reference_id' => $referenceStatus->referenceId,
	            'reason_id' => $referenceStatus->reasonForState
	    	);
	        $this->insert($data);
        }
    }
    
    public function getReferenceStateReason($referenceId) {
        
        $select = $this->select();
        $select->where('reference_id = ?', $referenceId);
        $mapRow = $this->fetchRow($select);
        
        if(empty($mapRow)) {
            
            $returnVal = null;
        }
        else {
            
        	$returnVal = $mapRow->reason_id;
        }
        
        return $returnVal;
    }
}

?>