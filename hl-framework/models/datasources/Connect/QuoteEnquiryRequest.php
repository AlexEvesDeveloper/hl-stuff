<?php
/**
 * Datasource definition for the internal news ticker.
 */
class Datasource_Connect_QuoteEnquiryRequest extends Zend_Db_Table_Multidb {
    protected $_name = 'QuoteEnquiryRequest';
    protected $_primary = 'id';
    protected $_multidb = 'db_legacy_homelet';

    /**
     * This function will add a new note into the agentNotes table
     *
     * @param int schemeNumber
     * @param string note
     * @return boolean
     *
     */
    public function addEnquiry($enquiryData) {
    #	Zend_Debug::dump($enquiryData);
    	$rsAddress = new Manager_Core_Postcode();
    	$riskAddress = $rsAddress->getPropertyByID($enquiryData['ins_address'],false);
    	#Zend_Debug::dump($riskAddress);die();
        $insertData = array(
            'agentschemeno' =>  $enquiryData['agentschemeno'],
            'title' =>  $enquiryData['title'],
        	'first_name' =>  $enquiryData['first_name'],
        	'last_name' =>  $enquiryData['last_name'],
        	'phone_number' =>  $enquiryData['phone_number'],
        	'mobile_number' =>  $enquiryData['mobile_number'],
        	'address_id' => 	$riskAddress['id'],
            'houseNumber' => $riskAddress['houseNumber'],
            'buildingName' => $riskAddress['buildingName'],
        	'address1' =>  $riskAddress['address1'],
        	'address2' =>  $riskAddress['address2'],
        	'address3' =>  $riskAddress['address3'],
        	'address4' =>  $riskAddress['address4'],
        	'address5' =>  $riskAddress['address5'],
        	'postcode' =>  $riskAddress['postcode'],
        	'county' =>  $riskAddress['county'],
        	'postcode' =>  $riskAddress['postcode'],
        	'organisation' =>  $riskAddress['organisation'],
        	'department' =>  $riskAddress['department'],
        	'additional_information' =>  $enquiryData['additional_information'],
        	'isSent' =>  0,
        	'enquiry_type' =>  $enquiryData['prospector'],
        	'created'   =>  new Zend_Db_Expr('NOW()')
        );
           // Insert the data into a new row in the table
     
        if ($this->insert($insertData)) {
        	
            return true;
        } else {
            // Failed insertion
            Application_Core_Logger::log("Can't insert  in table {$this->_name} (AGENTSCHEMENUMBER = {$schemeNumber})", 'error');
            
            return false;
        }
    }
    public function getData(){
        $select = $this->select();
        $select->from($this->_name);
        // Where
        $newItems = $this->fetchAll($select);      
        return $newItems->toArray();
    }
}
?>