<?php

/**
* Model definition for the policynotes table
*
* You are going to explode when you read this, the current policy notes table contails 3 fields
* policynumber, dateofentry and notes
*
* This is a 1 to 1 relationship to the policy, there for the notes field is of type medium text and is continuously
* added to.
*
* Also Legacy perl does not update the dates of entry field
*/
class Datasource_Core_Notes extends Zend_Db_Table_Multidb {
	
    protected $_name = 'policynotes';
    protected $_primary = 'policynumber';
    protected $_multidb = 'db_legacy_homelet';
    
    
    /**
    * update a note in the policynotes table
    *
    * @param Model_Insurance_Note $policyNoteObject a policy note data oblect containing the data to use for the update
    * @return bool
    */
    public function appendToPolicyNotes($policyNoteObject) {
        // Do a select and if we get a record back do an update otherwise do an insert
        $currentPolicyNoteObject = new Model_Core_Note();
        $currentPolicyNoteObject = $this->retrievePolicyNotes($policyNoteObject->policyNumber);
        // $currentPolicyNoteObject now contains the current notes entry for $policyNoteObject->policyNumber

        if($currentPolicyNoteObject){
            // Note Exists update (append) to exiting one
            $data['notes'] = $currentPolicyNoteObject->notes;
            
            $data['notes'] .= "\n\n" . date("d/m/Y") . " " . $policyNoteObject->notes;
            $where = $this->quoteInto('policynumber = ?', $policyNoteObject->policyNumber);
            $this->update($data,$where);
            
        }else{
            // No Note Exists insert a new one           
            $data['policynumber'] = $policyNoteObject->policyNumber;
            $data['dateOfEntry'] = date("Y-m-d");
            $data['notes'] = date("d/m/Y") . " " . $policyNoteObject->notes;

            $this->insert($data);
        }
    }
    
    
    /**
    * Retrieve a policy notes object by the policy number
    * @return Model_Core_Note
    */
    public function retrievePolicyNotes($policyNumber){
    	
        $policyNoteObject = new Model_Core_Note();
                
        $select = $this->select()
                  ->from($this->_name)
                  ->where('policynumber = ?', $policyNumber);
                  
        $row = $this->fetchRow($select);
        
        if(!empty($row)){
        	
            $policyNoteObject->policyNumber = $row->policynumber;
            $policyNoteObject->dateOfEntry = $row->dateofentry;
            $policyNoteObject->notes = $row->notes;
        return $policyNoteObject;
        }
        // No record so return false
        return false;
    }
}

?>
