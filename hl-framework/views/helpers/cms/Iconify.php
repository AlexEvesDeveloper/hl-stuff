<?php

/**
 * Helper class for replacing specified values with icons.
 */
class Cms_View_Helper_Iconify extends Zend_View_Helper_Abstract {
	
	const AGENT_TYPES = 1;
	const AGENT_USER_TYPES = 2;


	/**
	 * Expects a comma-separated string of agent types, or agent user types.
	 * Converts the agent types or agent user types into a HTML image string, which
	 * is returned to the caller.
	 *
	 * @param string $args
	 * A comma separated string of agent types or agent user types.
	 *
	 * @param int $iconifyRequestType
	 * Integer which indicates the types contained in the comma-separated string $args.
	 * Must correspond to a constant exposed by this class.
	 *
	 * @return string
	 * HTML string comprised of one or more image tags. Will return an empty string if
	 * none of the types in the $args string are matched.
	 *
	 * @throws Exception
	 * Throws an Exception if the $iconifyRequestType does not correspond to a constant
	 * exposed by this class.
	 */
	public function iconify($args, $iconifyRequestType) {
		
		//Despatch to internal functions.
		switch($iconifyRequestType) {
			
			case self::AGENT_TYPES: $returnVal = $this->_iconifyAgentTypes($args); break;
			case self::AGENT_USER_TYPES: $returnVal = $this->_iconifyAgentUserTypes($args); break;
			default:
				throw new Exception("Iconify request type not specified.");
		}
		
		return $returnVal;
	}
	
	
	protected function _iconifyAgentTypes($args) {
		
		//Replace the motd agent type identifiers with icons - looks dead good.
		$agentTypesArray = explode(',', $args);
		sort($agentTypesArray, SORT_NUMERIC);
		
		$agentTypesDisplay = '';
		foreach($agentTypesArray as $currentAgentType) {

			switch($currentAgentType) {
				
				case 1: $agentTypesDisplay .= '<img src="/assets/cms_admin/design/user-red.png" title="Standard agents" />'; break;
				case 2: $agentTypesDisplay .= '<img src="/assets/cms_admin/design/user-orange.png" title="Premier agents"/>'; break;
				case 3: $agentTypesDisplay .= '<img src="/assets/cms_admin/design/user-green.png" title="Premier-plus agents" />'; break;
			}
		}
		
		return $agentTypesDisplay;
	}
	
	
	protected function _iconifyAgentUserTypes($args) {
		
		$agentUserTypesArray = explode(',', $args);
		sort($agentUserTypesArray, SORT_NUMERIC);
		
		$agentUserTypesDisplay = '';
		foreach($agentUserTypesArray as $currentAgentUserType) {
			
			switch($currentAgentUserType) {
				
				case 1: $agentUserTypesDisplay .= '<img src="/assets/cms_admin/design/user-orange.png" title="Basic users" />'; break;
				case 2: $agentUserTypesDisplay .= '<img src="/assets/cms_admin/design/user-green.png" title="Master users" />'; break;
			}
		}
		
		return $agentUserTypesDisplay;
	}
}

?>