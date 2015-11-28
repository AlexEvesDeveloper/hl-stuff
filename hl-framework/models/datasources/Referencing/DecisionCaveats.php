<?php

/**
* Model definition for the decision_caveat_map datasource.
*/
class Datasource_Referencing_DecisionCaveats extends Zend_Db_Table_Multidb {
    
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_referencing';
    protected $_name = 'decision_caveat_map';
    protected $_primary = array('reference_id', 'caveat_id', 'caveat_reason_id');
    /**#@-*/

    
    /**
     * Updates or inserts Reference decision caveats into the datasource.
     *
     * Provided as an 'upsert' because calling code may not know if a caveat
     * already exists in the datasource for the current reference.
     *
     * @param Model_Referencing_Decision
     * The Decision for the current Reference, which links to the related
     * caveats.
     *
     * @return void
     */
    public function upsertCaveats($decision) {
        
    	if(empty($decision)) {
        	
        	//Nothing to do.
        	return;
        }
        
        //Delete old caveats.
        $where = $this->quoteInto('reference_id = ? ', $decision->referenceId);
        $this->delete($where);
        
        //Insert the new caveats.
        if(!empty($decision->caveats)) {
        	
	        foreach($decision->caveats as $currentCaveat) {
	        	
		        $data = array(
		            'reference_id' => $decision->referenceId,
		            'caveat_id' => $currentCaveat->caveat,
		            'caveat_reason_id' => $currentCaveat->caveatReason
		        );
	        	$this->insert($data);
	        }
        }
    }
    
    
    /**
     * Retrieves an array of Model_Referencing_DecisionCaveat objects.
     *
     * @param integer $referenceId
     * The unique Reference identifier.
     *
     * @return mixed
     * An array of Model_Referencing_DecisionCaveats, or null if none found.
     */
    public function getCaveats($referenceId) {

        $select = $this->select();
        $select->where('reference_id = ?', $referenceId);
        $caveatRows = $this->fetchAll($select);
        
        if(count($caveatRows) == 0) {

            $returnVal = null;
        }
        else {        
	        
	        //Load up the decision and the reasons for the decision.
	        $caveatArray = array();
	        foreach($caveatRows as $currentCaveat) {
        		
	        	$caveat = new Model_Referencing_DecisionCaveat();
	        	$caveat->caveat = $currentCaveat->caveat_id;
	        	$caveat->caveatReason = $currentCaveat->caveat_reason_id;
	        	$caveatArray[] = $caveat;
        	}
        	
        	$returnVal = $caveatArray;
        }
        
        return $returnVal;
    }
}

?>