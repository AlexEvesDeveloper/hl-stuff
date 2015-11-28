<?php

/**
* Model definition for the contact_details datasource.
*/
class Datasource_Core_ContactDetails extends Zend_Db_Table_Multidb {
    
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_referencing';
    protected $_name = 'contact_details';
    protected $_primary = 'id';
    /**#@-*/
    
    
    /**
     * Inserts a new, empty contact into the datasource and returns a corresponding object.
     *
	 * @return Model_Core_ContactDetails
	 * Encapsulates the details of the newly inserted contact.
     */
    public function createContactDetails() {    
    
        $contactId = $this->insert(array());
        if (empty($contactId)) {
            
            // Failed insertion
            Application_Core_Logger::log("Can't create record in table {$this->_name}", 'error');
            $returnVal = null;
        }
        else {
         
            $contactDetails = new Model_Core_ContactDetails();
            $contactDetails->id = $contactId;
            $returnVal = $contactDetails;
        }
        
        return $returnVal;
    }
    
    
    /**
     * Updates an existing contact.
     *
     * @param Model_Core_ContactDetails
     * The ContactDetails to update in the datasource.
     *
     * @return void
     */
    public function updateContactDetails($contactDetails) {
        
        if(empty($contactDetails)) {
            
            return;
        }
        
        $data = array(
            'telephone1' => $contactDetails->telephone1,
            'telephone2' => $contactDetails->telephone2,
            'email1' => $contactDetails->email1,
            'email2' => $contactDetails->email2,
            'fax1' => $contactDetails->fax1,
            'fax2' => $contactDetails->fax2
        );

        $where = $this->quoteInto('id = ?', $contactDetails->id);
        $this->update($data, $where);
    }
    
    
    /**
     * Returns an existing ContactDetails.
     *
     * @param integer $contactId
     * The unique ContactDetails identifier.
     *
     * @return mixed
     * A Model_Core_ContactDetails encapsulating the contact details, or null if
     * the ContactDetails cannot be found.
     */
    public function getById($contactId) {
        
        if(empty($contactId)) {
            
            return null;
        }
        
        
        $select = $this->select();
        $select->where('id = ?', $contactId);
        $contactRow = $this->fetchRow($select);
        
        if(empty($contactRow)) {
            
            Application_Core_Logger::log(get_class() . '::' . __FUNCTION__ . ': Unable to find ContactDetails.');
            $returnVal = null;
        }
        else {
            
            $contactDetails = new Model_Core_ContactDetails();
            $contactDetails->id = $contactRow->id;
            $contactDetails->telephone1 = $contactRow->telephone1;
            $contactDetails->telephone2 = $contactRow->telephone2;
            $contactDetails->email1 = $contactRow->email1;
            $contactDetails->email2 = $contactRow->email2;
            $contactDetails->fax1 = $contactRow->fax1;
            $contactDetails->fax2 = $contactRow->fax2;            
            $returnVal = $contactDetails;
        }
        
        return $returnVal;
    }
}

?>