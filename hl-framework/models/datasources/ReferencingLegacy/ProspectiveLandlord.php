<?php

/**
* Model definition for the Prospective Landlord datasource.
*/
class Datasource_ReferencingLegacy_ProspectiveLandlord extends Zend_Db_Table_Multidb {
    
    /**#@+
     * Mandatory attributes.
     */
    protected $_multidb = 'db_legacy_referencing';
    protected $_name = 'landlordref';
    protected $_primary = 'refno';
    /**#@-*/
    
    
    /**
     * Not implemented in this release.
     */
    public function insertLandlord($landlord) {
    
        throw new Zend_Exception(get_class() . __FUNCTION__ . ': not implemented in this release.');
    }
    
    
    /**
     * Not implemented in this release.
     */
    public function updateLandlord($landlord) {
        
        throw new Zend_Exception(get_class() . __FUNCTION__ . ': not implemented in this release.');
    }
    
    
    /**
     * Retrieves the specified prospective landlord using the Enquiry identifier.
     *
     * @param string $enquiryId
     * The unique external Enquiry identifier.
     *
     * @return mixed
     * The prospective landlord details encapsulated in a
     * Model_Referencing_ProspectiveLandlord object, or null if the prospective
     * landlord cannot be found.
     */
    public function getByEnquiry($enquiryId) {
        
        $enquiryDataSource = new Datasource_ReferencingLegacy_Enquiry();
        $prospectiveLandlordId = $enquiryDataSource->getProspectiveLandlordId($enquiryId);
        return $this->getLandlord($prospectiveLandlordId);
    }
    
    
    /**
     * Retrieves the specified prospective landlord.
     *
     * @param string $landlordId
     * The unique prospective landlord identifier.
     *
     * @return mixed
     * The prospective landlord details encapsulated in a
     * Model_Referencing_ProspectiveLandlord object, or null if the prospective
     * landlord cannot be found.
     */
    public function getLandlord($landlordId) {
        
        $select = $this->select();
        $select->where('refno = ?', $landlordId);
        $landlordRow = $this->fetchRow($select);
        
        $returnVal = null;
        if(!empty($landlordRow)) {

            $prospectiveLandlord = new Model_Referencing_ProspectiveLandlord();
            $prospectiveLandlord->id = $landlordRow->refno;
            
            $name = new Model_Core_Name();
            $name->firstName = $landlordRow->name;
            $prospectiveLandlord->name = $name;
        
            $address = new Model_Core_Address();
            $address->addressLine1 = $landlordRow->address1;
            $address->addressLine2 = $landlordRow->address2;
            $address->town = $landlordRow->town;
            $address->postCode = $landlordRow->postcode;
            $prospectiveLandlord->address = $address;
            
            $contactDetails = new Model_Core_ContactDetails();
            $contactDetails->telephone1 = $landlordRow->telday;
            $contactDetails->telephone2 = $landlordRow->televe;
            $contactDetails->fax1 = $landlordRow->fax;
            $contactDetails->email1 = $landlordRow->email;
            $prospectiveLandlord->contactDetails = $contactDetails;
            
            $returnVal = $prospectiveLandlord;
        }
        return $returnVal;
    }
}

?>