<?php
/**
* Datasource for handling the new agentscontacts database, these will be stored in a new database in mysql 5 then
* pushed into the legacy database at the end of the process, 
* @author John Burrin
* @since
*/

class LettingAgents_Datasource_Office extends Zend_Db_Table_Multidb {
    protected $_name = 'agency_office';
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
        $Obj->toArray($rsArray);
        //Zend_Debug::dump($Obj);die();
   		if(empty($rsArray['uid']) ){	
   			$rsArray['uid'] = uniqid();
        	return $this->_insert($rsArray);
   		}else{
   			//die($Obj->get_uid());
	        if($this->_exists($Obj->get_uid())){
        		return $this->_update($rsArray);
	        }
        } 
    }

	public function fetchAllByAgentId($agency_id){
		   $select = $this->select()
	       ->from($this->_name)
	        ->where('agency_id = ? ', $agency_id )
			->limit(2);
	        $rows = $this->fetchAll($select);
	        return $rows;
	}
    
    
	public function deleteById($id){
		$where = $this->quoteInto('agency_id = ?', $id);
		$this->delete($where);
        
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
		// Remove the id field
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