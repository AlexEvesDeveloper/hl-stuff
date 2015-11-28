<?php
    /**
    * Manager class responsible for New Letting agent Contacts
    */
    class LettingAgents_Manager_Office {
        /**
        * Save a record to the database and return the id on success
        * @param $data array
        * @return
        * @author John Burrin
        * @since
        */
        public function save($data){
        	#Zend_Debug::dump($data);die();
            $office = new LettingAgents_Datasource_Office();
            return ($office->save($data));
        }
        
         /**
         * 
         * 
         */
        public function fetchAllByAgencyUid($agencyUid){
        	#Zend_Debug::dump($agencyUid);die();
        	$agent = new LettingAgents_Manager_AgentApplication();
			$agentData = $agent->fetchByUid($agencyUid);

			$offices = new LettingAgents_Datasource_Office();
			#Zend_Debug::dump($agentData);die();
			return $offices->fetchAllByAgentId($agentData->get_id());
        }
    }