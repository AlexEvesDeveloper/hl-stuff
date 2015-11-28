<?php

/**
 * This datasource accesses the testAgent Table.
 *
 * The table holds a simple list of agent scheme numbers that are TEST accounts.
 */
class Datasource_Insurance_Policy_TestAgent extends Zend_Db_Table_Multidb {
	
    protected $_name = 'testAgent';
    protected $_id = 'agentschemeno';
    protected $_multidb = 'db_legacy_homelet';
    
    
    /**
     * Determines if an agent is a test agent.
     *
     * @param string $agentschemeno
     * The agent scheme number of the agent to test.
     * 
     * @return bool
     * Returns true if the agent is a test agent, false otherwise.
     */
    public function isTestAgent($agentschemeno){

        $select = $this->select();
        $select->from($this->_name);
        $select->where('agentschemeno = ?', $agentschemeno);
        $row = $this->fetchRow($select);
        
        if ($row) {
            $returnVal = true;
        }
        else {
            $returnVal = false;
        }
        
        return $returnVal;
    }
}
?>