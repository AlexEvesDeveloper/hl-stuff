<?php

/**
* Model definition for the TAT optouts datasource.
*/
class Datasource_Referencing_TatOptedStatus extends Zend_Db_Table_Multidb {
    
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_referencing';
    protected $_name = 'tat_optouts';
    protected $_primary = 'agent_scheme_number';
    /**#@-*/
    
    
    /**
	 * Opts an agent out of the TAT service.
	 *
	 * @param string $agentSchemeNumber
	 * The agent to opt out of the TAT service.
	 *
	 * @return void
	 */
    public function setOptedOut($agentSchemeNumber) {
        
        //By inserting the agent scheme number into the tat_optouts table, this
		//implies the agent is opted out.
		$data = array(
            'agent_scheme_number' => $agentSchemeNumber
        );

        $this->insert($data);
    }
	
	
	/**
	 * Opts an agent into the TAT service.
	 *
	 * All agents are by default opted into the TAT service unless they are recorded
	 * in this datasource. This method will therefore attempt to remove the agent
	 * scheme number from the datasource, thereby ensuring the default rules once
	 * again apply to the agent.
	 *
	 * @param string $agentSchemeNumber
	 * The agent to opt into the TAT service.
	 *
	 * @return void
	 */
	public function setOptedIn($agentSchemeNumber) {
	
        //By removing the agent scheme number from the tat_optouts table, this implies the
		//agent is opted in.
		$where = $this->quoteInto('agent_scheme_number = ?', $agentSchemeNumber);
        $this->delete($where);
	}
    
    
	/**
	 * Returns the agent opted status regarding the TAT service.
	 *
	 * By default all agents are opted into the TAT service. The only ones
	 * not opted in are those recorded in this datasource.
	 *
	 * @param string $agentSchemeNumber
	 * Identifies the agent.
	 *
	 * @return string
	 * String corresponding to one of the consts exposed by the
	 * Model_Referencing_TatOptedStates class.
	 */
    public function getOptedStatus($agentSchemeNumber) {
        
        $select = $this->select();
        $select->where('agent_scheme_number = ?', $agentSchemeNumber);
        $optedOutRow = $this->fetchRow($select);
        
        if(empty($optedOutRow)) {

			$returnVal = Model_Referencing_TatOptedStates::OPTED_IN;
        }
        else {
            
            $returnVal = Model_Referencing_TatOptedStates::OPTED_OUT;
        }
        
        return $returnVal;
    }
}

?>