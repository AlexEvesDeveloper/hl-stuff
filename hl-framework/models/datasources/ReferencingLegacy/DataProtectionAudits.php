<?php

/**
* Model definition for the DataProtectionAudit datasource.
*/
class Datasource_ReferencingLegacy_DataProtectionAudits extends Zend_Db_Table_Multidb {    
    
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_legacy_referencing';
    protected $_name = 'data_protection_audit';
    protected $_primary = 'id';
    /**#@-*/
    
    
    /**
     * Inserts an audit note into the data protection audit datasource.
     * 
     * Should be called when unsubscribing from a tenant mailer to record
     * the action.
     * 
     * @param integer $enquiryId
     * The unique internal enquiry identifier.
     *
     * @param boolean $isPhonePostChanged
     * True if the phone/post preferences were changed, false otherwise.
     *
     * @param boolean $isSmsEmailChanged
     * True if the sms/email preferences were changed, false otherwise.
     * 
     * @return void
     */
    public function unsubscribeFromTenantMailer($enquiryId, $isPhonePostChanged, $isSmsEmailChanged, $isNonDigitalPreferencehanged, $isDigitalPreferencehanged) {
		
		//Insert a record for the 'Opt out of marketing phone and post' action, if appropriate.
		$date = Zend_Date::now();
    	$data = array(
        	'enquiry_id' => $enquiryId,
        	'action_id' => 0, //Placeholder - the real value for this field will be set later.
        	'date' => $date->toString('yyyy-MM-dd'),
        	'csu_id' => 0,
        	'opt_out_stage_id' => 3
        );
		
		if($isPhonePostChanged) {
        
			$data['action_id'] = 4;
			$this->insert($data);
		}
		
		if($isSmsEmailChanged) {
			
			$data['action_id'] = 5;
			$this->insert($data);
		}
		
		if($isNonDigitalPreferencehanged) {
			
			$data['action_id'] = 9;
			$this->insert($data);
		}
		
		if($isDigitalPreferencehanged) {
			
			$data['action_id'] = 10;
			$this->insert($data);
		}
    }
}

?>