<?php

/**
* Model definition for the customer to legacy customer map table.
*/
class Datasource_Core_CustomerMaps extends Zend_Db_Table_Multidb
{
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_homelet';
    protected $_name = 'customer_legacy_customer_map';
    protected $_primary = 'id';
    /**#@-*/
    
    /**
     * Links a customer in the DataStore to the same customer stored in the
     * LegacyDataStore.
     *
     * @param string $legacyIdentifier
     * Identifies the customer in the LegacyDataStore.
     * 
     * @param integer $identifier
     * Identifies the customer in the DataStore.
     *
     * @return void
     */
    public function insertMap($legacyIdentifier, $identifier)
    {
        // Check to make sure map doesn't already exist
        $select = $this->select();
        $select->where('legacy_customer_reference = ?', $legacyIdentifier);
        $select->where('customer_id = ?', $identifier);
        $mapRow = $this->fetchRow($select);
        
        if (count($mapRow) == 0 || is_null($mapRow)) {
            $data = array (
                'legacy_customer_reference' =>  $legacyIdentifier,
                'customer_id'               =>  $identifier
            );

            $this->insert($data);
        }
    }

    /**
     * Retrieves a customer map.
     *
     * This method retrieves a customer map and stores the data in a
     * Model_Core_CustomerMap object, which
     * it will then return.
     *
     * This is useful for customer logic when looking up a customer identifier
     * when only one is known, as neither the DataStore nor the LegacyData store hold
     * direct references to the other.
     *
     * @param integer $customerIdentifierType
     * Must correspond to a relevant const exposed by the Customer class
     * (LEGACY_IDENTIFIER or IDENTIFIER). Allows this method to understand how to
     * process the $customerIdentifier passed in, which is represented differently
     * in the LegacyDataStore and DataStore.
     *
     * @param mixed $customerIdentifier
     * The customer identifier.
     *
     * @return Model_Core_CustomerMap The customer map.
     * @throws Zend_Exception
     */
    public function getMap($customerIdentifierType, $customerIdentifier)
    {
        $select = $this->select();

        if($customerIdentifierType == Model_Core_Customer::IDENTIFIER) {
            $select->where('customer_id = ?', $customerIdentifier);
        }
        else if($customerIdentifierType == Model_Core_Customer::LEGACY_IDENTIFIER) {
        
            $select->where('legacy_customer_reference = ?', $customerIdentifier);
        }
        else {
            throw new Zend_Exception('Invalid customer type specified.');
        }
        
        $customerRow = $this->fetchRow($select);

        if (count($customerRow) > 0) {
            $customerMap = new Model_Core_CustomerMap();
            $customerMap->setId($customerRow->id);
            $customerMap->setIdentifier($customerRow->customer_id);
            $customerMap->setLegacyIdentifier($customerRow->legacy_customer_reference);
            return $customerMap;
        }
        else {
            return false;
        }
    }
    
    /**
     * Quick and dirty function to return all of the legacy reference numbers for a particular customer
     *
     * @param integer $customerID
     * @return array
     * @todo This shouldn't be called directly from retrieve quote, a manager is needed
     */
    public function getLegacyIDs($customerID) {
        $select = $this->select();
        $select->where('customer_id = ?', $customerID);
        $legacyRows = $this->fetchAll($select);
        $legacyIDs = array();

        foreach ($legacyRows as $legacyRow) {
            $legacyIDs[] = $legacyRow->legacy_customer_reference;
        }
        
        return $legacyIDs;
    }
}
