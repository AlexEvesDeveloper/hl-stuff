<?php
    /**
    * Manager class responsible for New Letting agent application logic
    */
    class LettingAgents_Manager_AgentApplication {
    	
    	//The record id of the last record inserted or updated
    	private $_index;
    	
        /**
        * Save a record to the database and return the id on success
        * @param $data array
        * @return
        * @author John Burrin
        * @since
        */
        public function save($agencyData){
        	//Zend_Debug::dump($agencyData);die();
            $agent = new LettingAgents_Datasource_AgentApplication();
            
            $this->_index = $agent->save($agencyData);
            
            // Save the general email address
            $objEmail = new LettingAgents_Object_AgencyEmail();
            $dsEmail = new LettingAgents_Datasource_Email();
            $objEmail->setAddress_type(LettingAgents_Object_EmailTypes::General);
            $objEmail->setAgency_id($this->_index);
            $objEmail->setEmail_address($agencyData->get_contact_email());
            $dsEmail->deleteById($this->_index,LettingAgents_Object_EmailTypes::General);
            $dsEmail->save($objEmail);
            return ($this->_index);
        }
        
        
        /**
         * 
         * 
         * @param string $uid
         * @return LettingAgents_Object_AgentApplication
         */
        public function fetchByUid($uid){
        	//Zend_Debug::dump($uid);die();
        	$agent = new LettingAgents_Datasource_AgentApplication();
        	return ($agent->fetchByUid($uid));
        }
        
        /**
         * Delete a Agent application and asscociated data from the database 
         * @param $uid
         * @return bool
         */
   		public function deleteByUid($uid){
   			$agent = new LettingAgents_Datasource_AgentApplication();
   			$agent->cascadeDeleteByUid($uid);
   			return;
   		}
    }