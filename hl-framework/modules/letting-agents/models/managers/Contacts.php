<?php
    /**
    * Manager class responsible for New Letting agent Contacts
    */
    class LettingAgents_Manager_Contacts {
        /**
        * Save a record to the database and return the id on success
        * @param $data array
        * @return
        * @author John Burrin
        * @since
        */
        public function save($data){
        	#Zend_Debug::dump($data);die();
            $contact = new LettingAgents_Datasource_Contacts();
            return ($contact->save($data));
        }
          
        /**
         * 
         * 
         */
        public function fetchByAgencyUid($agencyUid){
        	#Zend_Debug::dump($agencyUid);die();
        	$agent = new LettingAgents_Manager_AgentApplication();
			$agentData = $agent->fetchByUid($agencyUid);

			$contacts = new LettingAgents_Datasource_Contacts();
			#Zend_Debug::dump($agentData);die();
			return $contacts->fetchByAgentId($agentData->get_id());
        }
        
        /**
         * Remove a contact by its uid
         */
        public function deleteByUid($uid){
        	$contacts = new LettingAgents_Datasource_Contacts();
        	return $contacts->deleteByUid($uid);
        }
    }