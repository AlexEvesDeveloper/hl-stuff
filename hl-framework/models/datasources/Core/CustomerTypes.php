<?php

/**
* Model definition for the customer types table. This table lists the valid
* types of customer for an insurance policy, such as 'tenant', 'landlord' and
* 'agent'.
*/
class Datasource_Core_CustomerTypes extends Zend_Db_Table_Multidb {
    
    /**#@+
     * Customer type attributes.
     */
    protected $_multidb = 'db_homelet';
    protected $_name = 'customer_types';
    protected $_primary = 'id';
    /**#@-*/
    
    
    /**
     * Gets the type identifier for the customer type label passed in.
     *
     * Receives a string label which represents a customer type. This MUST
     * correspond to on of the consts exposed by the
     * Model_Core_Customer class: AGENT,
     * LANDLORD, or TENANT.
     *
     * This will then be used to identify the integer id which uniquely identifies
     * the customer type in the database.
     *
     * @param integer $customerType
     * Specifies the customer type in a const exposed by the Customer class.
     *
     * @return integer
     * Returns the id of the customer type.
     *
     * @throws Zend_Exception
     * Throws a Zend_Exception if the $customerType cannot be found in the
     * database.
     */
    public function getTypeId($customerType) {
        
        $select = $this->select();
        $select->where('type = ?', $customerType);
        $customerTypeRow = $this->fetchRow($select);
        return $customerTypeRow->id;
    }
}

?>