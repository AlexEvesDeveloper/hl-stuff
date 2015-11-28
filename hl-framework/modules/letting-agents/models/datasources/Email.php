<?php
/**
* Datasource for handling the emails table, these will be stored in a new database in mysql 5 then
* pushed into the legacy database at the end of the process, 
* @author John Burrin
* @since
*/

class LettingAgents_Datasource_Email extends Zend_Db_Table_Multidb {
    protected $_name = 'agency_email';
    protected $_primary = 'id';
    protected $_multidb = 'db_letting_agents';

    /**
    * 
    *
    * @param LettingAgents_Object_AgencyEmail
    *
    * @return interger record id or false of failure
    */
    public function save($emailData){
    	
        $rsArray = array();
        // Convert to array
        $emailData->toArray($rsArray);
   			
   			switch($rsArray['address_type']){
   				case "general_email_address": 
   					$rsArray['address_type'] = LettingAgents_Object_EmailTypes::General;
   					break;
   				case "email_for_referencing":
   					$rsArray['address_type'] = LettingAgents_Object_EmailTypes::Referencing;
   					break;
   				case "email_for_insurance":
   					$rsArray['address_type'] = LettingAgents_Object_EmailTypes::Insurance;
   					break;
   				case "email_for_rg_renewals":
   					$rsArray['address_type'] = LettingAgents_Object_EmailTypes::RentGuaranteeRenewals;
   					break;
   				case "email_for_invoicing":
   					$rsArray['address_type'] = LettingAgents_Object_EmailTypes::Invoicing;
   					break;
   				case "email_for_marketing":
   					$rsArray['address_type'] = LettingAgents_Object_EmailTypes::HomeLetUpdates;
   					break;
   			}   
        return $this->_insert($rsArray);
    }
   
    
    /**
     * Fetch all the email address for an agency based on the id, 
     * an optional parameter may be passed to filter to an email type
     * @param $id integer
     * @param $addressType integer
     * 
     * @return array
     *   
     */
    public function fetchById($id, $addressType=null){
    $where = $this->quoteInto('agency_id = ?', $id);
		if(!is_null($addressType)){
			$where .= $this->quoteInto(' AND address_type = ?', $addressType);
		}
    	$select = $this->select()
			->from($this->_name)
            ->where($where);
            
		$rows = $this->fetchAll($select);
		return $rows->toArray();             
    }
    
    
	/**
	 * 
	 * Delete All the email addresses by the agency id
	 * @param integer $id
	 * @param integer $addressType, optional
	 */
    public function deleteById($id,$addressType=null){
		$where = $this->quoteInto('agency_id = ?', $id);
		if(!is_null($addressType)){
			$where .= $this->quoteInto(' AND address_type = ?', $addressType);
		}
		//die($where);
		$this->delete($where);
        
	}    
    
   
    /**
     * 
     * Private member function to insert a new record in the data
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
}