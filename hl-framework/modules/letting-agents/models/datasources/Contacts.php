<?php
/**
* Datasource for handling the new agentscontacts database, these will be stored in a new database in mysql 5 then
* pushed into the legacy database at the end of the process, 
* @author John Burrin
* @since
*/

class LettingAgents_Datasource_Contacts extends Zend_Db_Table_Multidb {
    protected $_name = 'contact';
    protected $_primary = 'id';
    protected $_multidb = 'db_letting_agents';

    /**
    * 
    *
    * @param mixed array $data The data to be inserted
    *
    * @return interger record id or false of failure
    */
    public function save($Obj){
        $rsArray = array();
        $rsArray = $Obj->toArray();
        //Zend_Debug::dump($Obj);
       // Zend_Debug::dump($rsArray);die();
   		if(empty($rsArray['uid']) ){	
   			$rsArray['uid'] = uniqid();
        	return $this->_insert($rsArray);
   		}else{
	        if($this->_exists($Obj->get_uid())){
        		return $this->_update($rsArray);
	        }
        } 
    }

    /**
     * 
     * Returns all the contacts linked to an agency by contact.agency_id =  agency.id
     * @param Integer $agentId
     * @return mixed array
     */
    public function fetchByAgentId($agentId){
		$select = $this->select()
			->from($this->_name)
			->where('agency_id = ?', $agentId);
		$rows = $this->fetchAll($select);
		return $rows->toArray();
    }

	/**
	 * 
	 * Remove multiple contacts by agency_id
	 * @param Integer $id
	 */
    public function deleteByAgencyId($id){
		$where = $this->quoteInto('agency_id = ?', $id);
		$this->delete($where);
        return;
	}

	/**
	 * 
	 * Remove a single contact bu it uid
	 * @param String $uid
	 */
	public function deleteByUid($uid){
		$where = $this->quoteInto('uid = ?', $uid);
		$this->delete($where);
		return;
	}   
    
    /**
    * Check to see if the current record exists
    * @param interger $uid
    * @return bool
    * @author John Burrin
    */
    private function _exists($uid){
	   $select = $this->select()
	       ->from($this->_name,array('uid'))
	        ->where('uid = ? ', $uid );
	        $row = $this->fetchRow($select);

        if(count($row) > 0){
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
		if(isset($data['birth_date'] )){
    		$data['birth_date'] = Application_Core_Utilities::ukDateToMysql($data['birth_date']);
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
		//Zend_Debug::dump($data);
        //	die("UPDATE");
			if(isset($data['birth_date'] )){
    		$data['birth_date'] = Application_Core_Utilities::ukDateToMysql($data['birth_date']);
		}
		$dataToSave = array_diff_key($data,array('id'=>""));
        $where = $this->quoteInto('uid = ?', $data['uid']);
        if($lastId = $this->update($dataToSave,$where)){
            return $lastId;
        }else{
            // Error
            Application_Core_Logger::log("Could not update table {$this->_name}", 'error');
            return false;
        } 
    }   
}