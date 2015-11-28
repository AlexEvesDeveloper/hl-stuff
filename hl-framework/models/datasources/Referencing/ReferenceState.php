<?php

/**
* Responsible for reading and writing the reference state.
*/
class Datasource_Referencing_ReferenceState extends Zend_Db_Table_Multidb {

    protected $_multidb = 'db_referencing';
    protected $_name = 'reference_state_map';
    protected $_primary = array('reference_id', 'reference_state_id');
    
    public function __construct() {
    	
    	parent::__construct();
    }
    
    public function setReferenceState($referenceStatus) {    
    
        if(empty($referenceStatus)) {
        	
        	return;
        }
    	
    	//Delete existing progress items, as its easier than checking for existing items
        //then updating them.
        $where = $this->quoteInto('reference_id = ? ', $referenceStatus->referenceId);
        $this->delete($where);
    	
        //Insert the new state.
    	$data = array(
            'reference_id' => $referenceStatus->referenceId,
            'reference_state_id' => $referenceStatus->state
    	);
        $this->insert($data);
    }
    
    public function getReferenceState($referenceId) {
        
        $select = $this->select();
        $select->where('reference_id = ?', $referenceId);
        $mapRow = $this->fetchRow($select);

        if(empty($mapRow)) {
     
            $returnVal = null;
        }
        else {
            
            $referenceStatus = new Model_Referencing_ReferenceStatus();
        	$referenceStatus->referenceId = $referenceId;
        	$referenceStatus->state = $mapRow->reference_state_id;
        	$returnVal = $referenceStatus;
        }
        
        return $returnVal;
    }
}

?>