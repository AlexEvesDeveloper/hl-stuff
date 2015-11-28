<?php

/**
* Model definition for the property lease datasource.
*/
class Datasource_Referencing_PropertyLease extends Zend_Db_Table_Multidb {

    protected $_multidb = 'db_referencing';
    protected $_name = 'property_lease';
    protected $_primary = 'id';

    /**
     * Inserts a new, empty PropertyLease into the datasource and returns a corresponding object.
     *
     * @param integer $referenceId
     * Links the new PropertyLease to the Reference.
     *
	 * @return Model_Referencing_PropertyLease
	 * Encapsulates the details of the newly inserted PropertyLease.
     */
    public function insertPlaceholder($referenceId) {
    
        $propertyLeaseId = $this->insert(array());
        if (empty($propertyLeaseId)) {

            throw new Zend_Exception("Can't create record in table {$this->_name}");
        }
        else {
         
            $propertyLease = new Model_Referencing_PropertyLease();
            $propertyLease->id = $propertyLeaseId;
            $returnVal = $propertyLease;
        }
        
        return $returnVal;
    }
    
    /**
     * Updates an existing PropertyLease.
     *
     * @param Model_Referencing_PropertyLease
     * The property lease details to update in the datasource.
     *
     * @return void
     */
    public function updatePropertyLease($propertyLease) {
        
        if(empty($propertyLease->address)) {
            
            $addressId = null;
        }
        else {
            
            //Obtain the addressId for storage in this datasource.
            $addressId = $propertyLease->address->id;
            
            //Update linked type.
            $addressDatasource = new Datasource_Core_Addresses();
            $addressDatasource->updateAddress($propertyLease->address);
        }
        
        $data = array(
            'address_id' => $addressId,
            'monthly_rent' => empty($propertyLease->rentPerMonth) ? null : $propertyLease->rentPerMonth->getValue(),
            'start_date' => empty($propertyLease->tenancyStartDate) ? null : $propertyLease->tenancyStartDate->toString(Zend_Date::ISO_8601),
            'term' => empty($propertyLease->tenancyTerm) ? null : $propertyLease->tenancyTerm,
            'no_of_tenants' => empty($propertyLease->noOfTenants) ? 1 : $propertyLease->noOfTenants
        );

        $where = $this->quoteInto('id = ?', $propertyLease->id);
        $this->update($data, $where);
    }
    
    /**
     * Retrieves the specified property lease.
     *
     * @param integer $propertyLeaseId
     * The unique property lease identifier.
     *
     * @return mixed
     * The property lease details, encapsulated in a Model_Referencing_PropertyLease
     * object, or null if the property lease cannot be found.
     */
    public function getByPropertyLeaseId($propertyLeaseId) {
        
        $select = $this->select();
        $select->where('id = ?', $propertyLeaseId);
        $propertyLeaseRow = $this->fetchRow($select);
        
        if(empty($propertyLeaseRow)) {
            
            Application_Core_Logger::log(get_class() . '::' . __FUNCTION__ . ':Unable to find property lease.');
            $returnVal = null;
        }
        else {
            
            $propertyLease = new Model_Referencing_PropertyLease();
            $propertyLease->id = $propertyLeaseRow->id;
            
            //Add the address details
            if(!empty($propertyLeaseRow->address_id)) {

                $addressDatasource = new Datasource_Core_Addresses();
                $propertyLease->address = $addressDatasource->getById($propertyLeaseRow->address_id);
            }
            
            if($propertyLeaseRow->monthly_rent >= 0) {
 
                $propertyLease->rentPerMonth = new Zend_Currency(
                    array(
                        'value' => $propertyLeaseRow->monthly_rent,
                        'precision' => 0
                    ));
            }

            if(!empty($propertyLeaseRow->start_date)) {
                
                if($propertyLeaseRow->start_date != '0000-00-00') {
                
                    $propertyLease->tenancyStartDate = new Zend_Date(
                        $propertyLeaseRow->start_date, Zend_Date::ISO_8601);
                }
            }
            
            $propertyLease->tenancyTerm = $propertyLeaseRow->term;
            $propertyLease->noOfTenants = $propertyLeaseRow->no_of_tenants;
            $returnVal = $propertyLease;
        }
        
        return $returnVal;
    }
}

?>