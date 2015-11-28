<?php

################################################################################
#
#	DO NOT CHANGE THIS FILE!
#
#	This service is being consumed by FastAnt via classic ASP
#	Changing the WSDL may break their client. If the file needs to be changed
#	make sure that FastAnt are informed
#
################################################################################

/**
 * Class for fetching FSA status for an agent
 * 
 * @package Service_Referencing_FsaStatus
 */
class Service_Referencing_FsaStatus {

	/**
	 * SOAP Fault codes 
	 */
	const SOAP_FAULT_AGENT_SCHEME_NUMBER_NOT_VALID = 1,
		SOAP_FAULT_AGENT_FSA_STATUS_NOT_FOUND = 2;
	
	/**
	 * Returns the FSA status abbr. for an agent, referenced by 
	 * their ASN (Agent Scheme Number)
	 * 
	 * @throws Exception
	 * @param string $agentSchemeNumber 
	 * @return string
	 */
	public function getAgentFsaStatus($agentSchemeNumber) {

		if(!is_numeric($agentSchemeNumber)) {
			throw new Exception(sprintf("[%s] is not a valid agent scheme number", $agentSchemeNumber),
					self::SOAP_FAULT_AGENT_SCHEME_NUMBER_NOT_VALID);
		}
	
		$fsaAgentStatusDatasource = new Datasource_Fsa_AgentStatus();
		$fsaStatus = $fsaAgentStatusDatasource->getAgentFsaStatus($agentSchemeNumber);

		if(is_array($fsaStatus) && isset($fsaStatus['status_abbr'])) {
			return (string)$fsaStatus['status_abbr'];
		}
		
		throw new Exception(sprintf("Agent FSA status not found for [%s]", $agentSchemeNumber), 
				self::SOAP_FAULT_AGENT_FSA_STATUS_NOT_FOUND);
	}
}
