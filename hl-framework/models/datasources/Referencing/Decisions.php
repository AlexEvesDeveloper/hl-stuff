<?php

/**
* Model definition for the decision_map datasource.
*/
class Datasource_Referencing_Decisions extends Zend_Db_Table_Multidb {
    
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_referencing';
    protected $_name = 'decision_map';
    protected $_primary = array('reference_id', 'decision_id', 'decision_reason_id');
    /**#@-*/
    
    
    public function __construct() {
    	
    	parent::__construct();
    }
    
    /**
     * Updates or inserts a Reference decision into the datasource.
     *
     * Provided as an 'upsert' because calling code may not know if a decision
     * already exists in the datasource for the current reference.
     *
     * @param Model_Referencing_Decision
     * The Decision for the current Reference.
     *
     * @return void
     */
    public function upsertDecision($decision) {
        
        if(empty($decision)) {
        	
        	//Nothing to do.
        	return;
        }
        
        //Delete old decision.
        $where = $this->quoteInto('reference_id = ? ', $decision->referenceId);
        $this->delete($where);
        
        //Insert the new decision.
        foreach($decision->decisionReasons as $currentReason) {
        	
	        $data = array(
	            'reference_id' => $decision->referenceId,
	            'decision_id' => $decision->decision,
	            'decision_reason_id' => $currentReason
	        );
        	$this->insert($data);
        }
    }
    
    
    /**
     * Retrieves a Model_Referencing_Decision object.
     *
     * @param integer $referenceId
     * The unique Reference identifier.
     *
     * @return mixed
     * A Model_Referencing_Decision, or null if none found.
     */
    public function getDecision($referenceId) {

        $select = $this->select();
        $select->where('reference_id = ?', $referenceId);
        $decisionRows = $this->fetchAll($select);
        
        if(count($decisionRows) == 0) {

            $returnVal = null;
        }
        else {
        	
        	$decision = new Model_Referencing_Decision();
	        $decision->referenceId = $referenceId;
	        
	        //Load up the decision and the reasons for the decision.
	        $decision->decisionReasons = array();
	        foreach($decisionRows as $row) {
        		
	            //Populate the details into an CustomerMap object.
	            $decision->decision = $row->decision_id;
	            $decision->decisionReasons[] = $row->decision_reason_id;
        	}

        	$returnVal = $decision;
        }
        
        return $returnVal;
    }
}

?>