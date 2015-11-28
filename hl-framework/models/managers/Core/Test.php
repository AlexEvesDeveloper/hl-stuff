<?php

/**
 * Manager responsible for identifying test quote/policies and test agent accounts.
 */
class Manager_Core_Test {

	protected $_quoteDatasource;
	protected $_testAgentDatasource;

    
    /**
     * Checks if a quote/policy is a test quote/policy.
     *
     * @param string $policyNumber
     * The full, unique quote/policynumber.
     *
     * @return boolean
     * Returns true if the quote/policy is a test quote/policy, false otherwise.
     */
    public function isTestPolicy($policyNumber) {
    	
    	//First retireve the agent scheme number, then test for a test agent.
    	if(empty($this->_quoteDatasource)) {
    		
    		$this->_quoteDatasource = new Datasource_Insurance_LegacyQuotes();
    	}

    	$quote = $this->_quoteDatasource->getByPolicyNumber($policyNumber);
    	return $this->isTestAgent($quote->agentSchemeNumber);
    }
    
    
    /**
     * Checks if an agent is a test agent.
     *
     * @param string $agentSchemeNumber
     * The agent scheme number to check.
     *
     * @return boolean
     * Returns true if the agent is a test agent, false otherwise.
     */
    public function isTestAgent($agentSchemeNumber) {
    	
    	if(empty($this->_testAgentDatasource)) {
    		
    		$this->_testAgentDatasource = new Datasource_Insurance_Policy_TestAgent();
        }
        return $this->_testAgentDatasource->isTestAgent($agentSchemeNumber);
    }
}
?>