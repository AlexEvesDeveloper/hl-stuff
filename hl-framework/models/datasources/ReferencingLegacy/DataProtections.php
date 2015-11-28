<?php

/**
 * Wraps around the legacy referencing_uk.data_protection_map table.
*/
class Datasource_ReferencingLegacy_DataProtections extends Zend_Db_Table_Multidb {    
    
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_legacy_referencing';
    protected $_name = 'data_protection_map';
    protected $_primary = 'enquiry_id';
    /**#@-*/
	
	
	/**
	 * Inserts an array of data protection items.
	 * 
	 * Each item of the array must be an instance of 
	 * Model_Referencing_DataProtectionItem.
	 * 
	 * @param array
	 * An array of Model_Referencing_DataProtectionItem objects.
	 * 
	 * @return void
	*/
	public function createNewDataProtections(array $dpaList) {

		foreach($dpaList as $currentItem) {
			
			$data = array(
				'enquiry_id' => $currentItem->referenceId,
				'data_protection_id' => $currentItem->dataProtectionId,
				'is_allowed' => $currentItem->dataProtectionValue
			);
			$this->insert($data);
		}
    }
	
	/**
     * Unsubscribes a reference from any further marketing.
     * 
     * @param string $enquiryId
     * The external enquiry identifier.
     * 
     * @return boolean
     * True on successful update, false otherwise.
     */
    public function unsubscribe($enquiryId) {
    	
    	$enquiry = new Datasource_ReferencingLegacy_Enquiry();
    	$enquiryId = $enquiry->getInternalIdentifier($enquiryId);
    	if(empty($enquiryId)) {
    		
    		return false;
    	}

        
		//Update.
        $data = array('is_allowed' => 0);
		$where = $this->quoteInto('enquiry_id = ? AND data_protection_id = 1', $enquiryId);
		if($this->update($data, $where) > 0) {

			$isPhonePostChanged = true;
		}
		else {
			
			$isPhonePostChanged = false;
		}

		$where = $this->getAdapter()->quoteInto('enquiry_id = ? AND data_protection_id = 3', $enquiryId);
		if($this->update($data, $where) > 0) {

			$isSmsEmailChanged = true;
		}
		else {
			
			$isSmsEmailChanged = false;
		}
		
		$where = $this->getAdapter()->quoteInto('enquiry_id = ? AND data_protection_id = 4', $enquiryId);
		if($this->update($data, $where) > 0) {

			$isNonDigitalPreferencehanged = true;
		}
		else {
			
			$isNonDigitalPreferencehanged = false;
		}
		
		$where = $this->getAdapter()->quoteInto('enquiry_id = ? AND data_protection_id = 5', $enquiryId);
		if($this->update($data, $where) > 0) {

			$isDigitalPreferencehanged = true;
		}
		else {
			
			$isDigitalPreferencehanged = false;
		}

        
		//Insertion was successful. Make a note of this in the data protection
        //audit table.
		$audit = new Datasource_ReferencingLegacy_DataProtectionAudits();
		$audit->unsubscribeFromTenantMailer($enquiryId, $isPhonePostChanged, $isSmsEmailChanged, $isNonDigitalPreferencehanged, $isDigitalPreferencehanged);
        return true;
    }
	
	public function deleteDataProtections($enquiryId) {
		
		$where = $this->quoteInto('enquiry_id = ?', $enquiryId);
        $this->delete($where);
	}
}

?>
