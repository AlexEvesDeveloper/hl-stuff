<?php

/**
* Model definition for the property lease datasource.
*/
class Datasource_ReferencingLegacy_PropertyLease extends Zend_Db_Table_Multidb {
    
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_legacy_referencing';
    protected $_name = 'property';
    protected $_primary = 'refno';
    /**#@-*/
    
    
    /**
     * Inserts a new record into the legacy property table.
     *
     * @param Model_Referencing_PropertyLease
     * The PropertyLease details to insert.
     *
     * @return integer
     * The newly created primary key identifer.
     */
    public function insertPropertyLease($propertyLease) {
    
    	if(empty($propertyLease->rentPerMonth)) {
    		
    		$rentPerMonth = 0;
    	}
    	else {

    		$rentPerMonth = $propertyLease->rentPerMonth->getValue();
    	}
    	
        $data = array(
            'address1' => empty($propertyLease->address->addressLine1) ? '' : $propertyLease->address->addressLine1,
            'address2' => empty($propertyLease->address->addressLine2) ? '' : $propertyLease->address->addressLine2,
            'town' => empty($propertyLease->address->town) ? '' : $propertyLease->address->town,
            'postcode' => empty($propertyLease->address->postCode) ? '' : $propertyLease->address->postCode,
            'rent' => $rentPerMonth,
            'start_date' => $propertyLease->tenancyStartDate->toString(Zend_Date::ISO_8601),
            'term' => $propertyLease->tenancyTerm,
            'notens' => $propertyLease->noOfTenants
        );
        
        return $this->insert($data);
    }
    
    
    /**
     * Not implemented in this release.
     */
    public function updatePropertyLease($propertyLease) {
        
        throw new Zend_Exception(get_class() . __FUNCTION__ . ': not implemented in this release.');
    }
    
    
    /**
     * Retrieves the specified property lease.
     *
     * @param mixed $propertyLeaseId
     * The unique property lease identifier. May be an integer or string.
     *
     * @param string $enquiryId
     * The unique external Enquiry identifier.
     *
     * @return mixed
     * The property lease details, encapsulated in a Model_Referencing_PropertyLease
     * object, or null if the property lease cannot be found.
     */
    public function getPropertyLease($propertyLeaseId, $enquiryId) {
        
        $select = $this->select();
        $select->where('refno = ?', $propertyLeaseId);
        $propertyLeaseRow = $this->fetchRow($select);
        
        if(empty($propertyLeaseRow)) {
            
            Application_Core_Logger::log(get_class() . '::' . __FUNCTION__ . ':Unable to find property lease.');
            $returnVal = null;
        }
        else {
            
            $propertyLease = new Model_Referencing_PropertyLease();
            $propertyLease->id = $propertyLeaseRow->refno;
            
            
            //Add the prospective landlord details        
            $prospectiveLandlordDatasource = new Datasource_ReferencingLegacy_ProspectiveLandlord();
            $propertyLease->prospectiveLandlord = $prospectiveLandlordDatasource->getByEnquiry($enquiryId);
            
            
            //Add the address
            $propertyAddress = new Model_Core_Address();
            $propertyAddress->addressLine1 = $propertyLeaseRow->address1;
            $propertyAddress->addressLine2 = $propertyLeaseRow->address2;
            $propertyAddress->town = $propertyLeaseRow->town;
            $propertyAddress->postCode = $propertyLeaseRow->postcode;
            $propertyLease->address = $propertyAddress;
            
            if($propertyLeaseRow->rent >= 0) {
 
                $propertyLease->rentPerMonth = new Zend_Currency(
                    array(
                        'value' => $propertyLeaseRow->rent,
                        'precision' => 0
                    ));
            }

            if($propertyLeaseRow->start_date != '0000-00-00') {
            
                $propertyLease->tenancyStartDate = new Zend_Date(
                    $propertyLeaseRow->start_date, Zend_Date::ISO_8601);
            }
            
            $propertyLease->tenancyTerm = $propertyLeaseRow->term;
            $propertyLease->noOfTenants = $propertyLeaseRow->notens;
            
            
            //Next load up the property aspect details.
            $propertyAspectDatasource = new Datasource_ReferencingLegacy_PropertyAspects();
            $propertyLease->propertyAspects = $propertyAspectDatasource->getAspects($enquiryId);
            
            $returnVal = $propertyLease;
        }
        
        return $returnVal;
    }
}

?>