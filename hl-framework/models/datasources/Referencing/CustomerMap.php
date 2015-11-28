<?php

/**
* Model definition for the customer_map datasource.
*/
class Datasource_Referencing_CustomerMap extends Zend_Db_Table_Multidb {

    protected $_multidb = 'db_referencing';
    protected $_name = 'customer_types_map';
    protected $_primary = 'reference_id';
    
    /**
     * Updates or inserts a Reference customer into the datasource.
     *
     * Provided as an 'upsert' because calling code may not know if the Reference
     * customer already exists in the datasource.
     *
     * @param Model_Referencing_Reference
     * The Reference started by the customer to be inserted. This is provided
     * so that the Reference identifier can be used.
     *
     * @return void
     */
    public function upsertReferenceCustomer($reference)
    {
        $select = $this->select();
        $select->where('reference_id = ?', $reference->internalId);
        $referenceCustomerRow = $this->fetchRow($select);
        
        if(empty($referenceCustomerRow)) {
            $this->_insertReferenceCustomer($reference);
        }
        else {
            $this->_updateReferenceCustomer($reference);
        }
    }


    /**
     * Inserts a new Reference customer.
     *
     * @param Model_Referencing_Reference
     * The Reference linking to the customer which needs to be inserted. This is
     * provided so that the Reference identifier can be used.
     *
     * @return void
     */
    protected function _insertReferenceCustomer($reference)
    {
        $data = array(
            'reference_id' => $reference->internalId,
            'customer_type_id' => $reference->customer->customerType,
            'customer_id' => $reference->customer->customerId
        );
        
        $this->insert($data);
    }
    
    
    /**
     * Updates an existing Reference customer.
     *
     * @param Model_Referencing_Reference
     * The Reference linking to the customer to be updated. This is
     * provided so that the Reference identifier can be used.
     *
     * @return void
     */
    protected function _updateReferenceCustomer($reference)
    {
        $data = array(
            'customer_type_id' => $reference->customer->customerType,
            'customer_id' => $reference->customer->customerId
        );
        
        $where = $this->quoteInto('reference_id = ?', $reference->internalId);
        $this->update($data, $where);
    }
    
    
    /**
     * Retrieves a Model_Referencing_CustomerMap object.
     *
     * @param integer $referenceId The unique Reference identifier.
     * @return mixed A Model_Referencing_CustomerMap, or null if none found.
     */
    public function getReferenceCustomer($referenceId)
    {
        $select = $this->select();
        $select->where('reference_id = ?', $referenceId);
        $referenceCustomerRow = $this->fetchRow($select);
        
        if(empty($referenceCustomerRow)) {
            Application_Core_Logger::log(get_class() . '::' . __FUNCTION__ . ': Unable to find CustomerMap.');
            $returnVal = null;
        }
        else {
            //Populate the details into an CustomerMap object.
            $referenceCustomer = new Model_Referencing_CustomerMap();
            $referenceCustomer->customerType = $referenceCustomerRow->customer_type_id;
            $referenceCustomer->customerId = $referenceCustomerRow->customer_id;
            $returnVal = $referenceCustomer;
        }
        
        return $returnVal;
    }
}
