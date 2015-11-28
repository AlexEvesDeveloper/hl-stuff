<?php
/**
* Model definition for the FSA tables.
* 
*/
class Datasource_Fsa_AgentStatus extends Zend_Db_Table_Multidb {
	
    protected $_name = 'agent';
    protected $_primary = 'agent_id';
    protected $_multidb = 'db_fsa';
    
	/**
	 * Gets an Aganet's FSA status referenced by their scheme number
	 *
	 * @param string $agentSchemeNumber
	 * @return array 
	 */
	public function getAgentFsaStatus($agentSchemeNumber) {

		$query = $this->select()
				->setIntegrityCheck(false)
				->from($this->_name)
				->joinLeft('FSA_agent_status', 'agent.agent_id = FSA_agent_status.agent_id')
				->joinLeft('FSA_status', 'FSA_agent_status.FSA_status_id = FSA_status.id')
				->where('agent.database_origin_key = ?', $agentSchemeNumber)
				->order(array('FSA_agent_status.id DESC'))
				->limit(1);

		$row = $this->fetchRow($query);
		
		return array(
			'agentschemeno' => $row->database_origin_key,
			'status' => $row->status,
			'status_abbr' => $row->status_abbr,
		);
	}
}

?>