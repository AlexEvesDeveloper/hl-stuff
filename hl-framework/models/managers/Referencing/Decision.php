<?php

/**
 * Encapsulates referencing decision business logic.
 * 
 * All access to the decision datasources should be through this class.
 */
class Manager_Referencing_Decision {

	protected $_decisionsDatasource;
	protected $_decisionCaveatsDatasource;
    
    protected function _loadSources() {
    	
    	if(empty($this->_decisionsDatasource)) {
    		
			$this->_decisionsDatasource = new Datasource_Referencing_Decisions();
		}
		
		if(empty($this->_decisionCaveatsDatasource)) {
			
			$this->_decisionCaveatsDatasource = new Datasource_Referencing_DecisionCaveats();
		}
    }
    
    /**
     * Save a referencing decision.
     * 
     * @param Model_Referencing_Decision $decision
     * The referencing decision to save.
     */
    public function save($decision) {
    	
    	$this->_loadSources();
    	$this->_decisionsDatasource->upsertDecision($decision);
		$this->_decisionCaveatsDatasource->upsertCaveats($decision);
    }
    
    /**
     * Retrieve a referencing decision.
     * 
     * @param integer $referenceId
     * The unique reference identifier.
     */
    public function retrieve($referenceId) {
    	
    	$this->_loadSources();
		$decision = $this->_decisionsDatasource->getDecision($referenceId);
		if(!empty($decision)) {
		
			$decision->caveats = $this->_decisionCaveatsDatasource->getCaveats($referenceId);
		}
		return $decision;
    }
}

?>