<?php
/**
* Datasource for handling the new agents database, these will be stored in a new database in mysql 5 then
* pushed into the legacy database at the end of the process, 
* @author John Burrin
* @since
*/

class LettingAgents_Datasource_AgentApplication extends Zend_Db_Table_Multidb {
    protected $_name = 'agency';
    protected $_primary = 'id';
    protected $_multidb = 'db_letting_agents';
    
    private $_index;

    /**
    * Insert a new row into the additional_information table
    *
    * @param mixed array $data The data to be inserted
    *
    * @return interger record id or false of failure
    */
    public function save($Obj){
        $rsArray = array();
        // convert the object to an Array
       
        $Obj->toArray($rsArray);
		#Zend_Debug::dump($rsArray);die();
        if($this->_exists($Obj->get_uid() ) ){
        	//die("UPDATE - Agent");
        	$this->_index = $this->_update($rsArray);
        //	die($this->_index);
            return $this->_index; 
        }else{
        	//die("INSERT - Agent");
        	$this->_index = $this->_insert($rsArray);
            return $this->_index; 
        }
    }

    /**
     * Fetch record by uid, 
     * @param string Unique identifier
     * @return LettingAgents_Object_AgentApplication
     */
    
    public function fetchByUid($uid){
    	//Zend_Debug::dump($uid);die();
    	$data = new LettingAgents_Object_AgentApplication();
    	$select = $this->select()
            ->from($this->_name)
			->where('uid = ?', $uid);
		            
   //     die($select->__toString());
        $row = $this->fetchRow($select);
        if($row){
	       	$data->set_id($row['id']);
	        $data->set_uid($row['uid']);
	        $data->set_is_previous_client($row['is_previous_client']);
	        $data->set_campaign_code($row['campaign_code']);
	        $data->set_legal_name($row['legal_name']);
	        $data->set_trading_name($row['trading_name']);
	        $data->set_organisation_type($row['organisation_type']);
	        $data->set_date_established($row['date_established']);
	        $data->set_is_associated($row['is_associated']);
	        $data->set_associated_text($row['associated_text']);
	        $data->set_company_registration_number($row['company_registration_number']);
	        $data->set_contact_name($row['contact_name']);
	        $data->set_contact_number($row['contact_number']);
	        $data->set_contact_email($row['contact_email']);
	        $data->set_current_referencing_supplier($row['current_referencing_supplier']);
	        $data->set_number_of_branches($row['number_of_branches']);
	        $data->set_number_of_employees($row['number_of_employees']);
	        $data->set_number_of_landlords($row['number_of_landlords']);
	        $data->set_number_of_lets($row['number_of_lets']);
	        $data->set_fax_number($row['fax_number']);
	        $data->set_company_website_address($row['company_website_address']);
	        
	        return($data);
        }
        return false;
        
        
    }
    
    /**
     * Deletes an agent application and accociated Contact/Email records 
     * return id of record of record deleted
     */
    public function cascadeDeleteByUid($uid){
    	$select = $this->select()
            ->from($this->_name,array('id'))
            ->where('uid = ?', $uid);
 
		$row = $this->fetchRow($select);
		if($row){
			// Delete associated Contacts 
            $contacts = new LettingAgents_Datasource_Contacts();
            $contacts->deleteById($row['id']);
            $this->deleteById($row['id']);
            
            // Delete associated Emails
            $emails = new LettingAgents_Datasource_Email();
            $emails->deleteById($row['id']);
        }
        
        return false;           
    }
    
    
	public function deleteById($id){
		$where = $this->quoteInto('id = ?', $id);
		$this->delete($where);
        
	}
	    
    /**
    * Check to see if the current record exists
    * @param 
    * @return int id of record on success or false on failure
    * @author John Burrin
    */
    private function _exists($uid){
        $select = $this->select()
            ->from($this->_name,array('uid'))
            ->where('uid = ?', $uid);
        $row = $this->fetchRow($select);
		
       // Zend_Debug::dump($select->__toString());
        if($row){
            return true;
        }
        return false;
        
    }
    
   
    /**
     * 
     * Private member function to inseter a new record in the data
     * @param array $data
     */
    private function _insert($data){
    	
    	if(isset($data['date_established'] )){
    		$data['date_established'] = Application_Core_Utilities::ukDateToMysql($data['date_established']);
    	}
    	
       if($lastId = $this->insert($data)){
            return $lastId;
        }else{
            // Error
            Application_Core_Logger::log("Could not insert into table {$this->_name}", 'Error');
            return false;
        }     	
    }
    
    /**
     * @param mixed array $data
     */
	private function _update($data){
		
		if(isset( $dataToSave['date_established'])){
			$dataToSave['date_established'] =  Application_Core_Utilities::ukDateToMysql($data['date_established'] );
		}	   
		$dataToSave = array_diff_key($data,array('id'=>""));    
		
		
        $where = $this->quoteInto('uid = ?', $dataToSave['uid']);
        $this->update($dataToSave,$where);
		return $data['id'];
         
    }
    
    
}