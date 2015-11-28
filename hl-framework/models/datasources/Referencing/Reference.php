<?php

/**
* Model definition for the Reference datasource. The Reference links together all aspects of the
* referencing process. The Reference identifier can be used to identify all related data,
* not just that in the Reference datasource.
*/
class Datasource_Referencing_Reference extends Zend_Db_Table_Multidb {    
    
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_referencing';
    protected $_name = 'reference';
    protected $_primary = 'id';
    /**#@-*/
    

    /**
     * Inserts a new, empty Reference into the datasource and returns a corresponding object.
     *
     * This method will allocate unique internal and external Reference identifiers
     * to the new Reference.
     *
	 * @return Model_Referencing_Reference
	 * Holds the details of the newly inserted Reference.
     */
    public function createReference() {
		
		$muntManager = new Manager_ReferencingLegacy_Munt();
		$reference = $muntManager->createReference();
        
        //Minimal insertion
        $data = array(
            'id' => $reference->internalId,
            'external_id' => $reference->externalId);
        
        if (!$this->insert($data)) {
            
            // Failed insertion
            Application_Core_Logger::log("Can't create record in table {$this->_name}", 'error');
            $returnVal = null;
        }
        else {
            
            $returnVal = $reference;
        }
        
        return $returnVal;
    }
    
    
    /**
     * Updates an existing Reference.
     *
     * @param Model_Referencing_Reference
     * The Reference object to update in the datasource.
     *
     * @return void
     */
    public function updateReference($reference) {

        $data = array(
            'external_id' => $reference->externalId,
            'completion_method_id' => empty($reference->completionMethod) ? null : $reference->completionMethod
        );
        
        
        $where = $this->quoteInto('id = ?', $reference->internalId);
        $this->update($data, $where);
    }
    
    
    /**
     * Retrieves the specified Reference.
     *
     * @param mixed $referenceId
     * The unique Reference identifier. May be the IRN (internal refno) or the ERN (external
     * refno).
     *
     * @return mixed
     * The Reference details, encapsulated in a Model_Referencing_Reference object,
     * or null if the Reference cannot be found.
     */
    public function getReferenceObject($referenceId) {

        $select = $this->select();
		
		$referenceIdString = (string)$referenceId;
        if(ctype_digit($referenceIdString)) {
            
            //All the characters in $enquiryId are numeric
            $select->where('id = ?', $referenceId);
        }
        else {
            
            $select->where('external_id = ?', $referenceIdString);
        }
        $referenceRow = $this->fetchRow($select);
        
        
        if(empty($referenceRow)) {
            
            Application_Core_Logger::log(get_class() . '::' . __FUNCTION__ . ': Unable to find Reference.');
            $returnVal = null;
        }
        else {

            //Populate the details into an Enquiry object.
            $reference = new Model_Referencing_Reference();
            $reference->internalId = $referenceRow->id;
            $reference->externalId = $referenceRow->external_id;
            $reference->completionMethod = $referenceRow->completion_method_id;
            $returnVal = $reference;
        }
        
        return $returnVal;
    }
    
    
    /**
     * Utility method to get the IRN associated with the ERN passed in.
     *
     * The IRN is the internal Reference identifier, containing only digits. The ERN
     * is the external Reference (Enquiry) identifier, containing a period and optionally a
     * forward slash.
     *
     * @param string $ern
     * The external Reference (Enquiry) identifier.
     *
     * @return mixed
     * Returns the internal Reference identifier (IRN) as an integer, if found.
     * Otherwise returns null.
     */
    public function getInternalIdentifier($ern) {
        
        $select = $this->select(array('id'));
        $select->where('external_id = ?', $ern);
        $referenceRow = $this->fetchRow($select);
        
        if(empty($referenceRow)) {
            
            Application_Core_Logger::log(get_class() . '::' . __FUNCTION__ . ': Invalid external identifier.');
            $returnVal = null;
        }
        else {
            
            $returnVal = $referenceRow->id;
        }
        
        return $returnVal;
    }
    
    
    /**
     * Utility method to get the ERN associated with the IRN passed in.
     *
     * The ERN is the external Reference (Enquiry) identifier and is represented as a string,
     * as it may contain a period and forward slash. The IRN is the internal
     * Reference identifier, and contains only digits.
     *
     * @param integer $irn
     * The internal Reference identifier.
     *
     * @return mixed
     * Returns the external Reference (Enquiry) identifier (ERN) as a string, if found.
     * Otherwise returns null.
     */
    public function getExternalIdentifier($irn) {
        
        $select = $this->select(array('external_id'));
        $select->where('id = ?', $irn);
        $referenceRow = $this->fetchRow($select);
        
        if(empty($referenceRow)) {
            
            Application_Core_Logger::log(get_class() . '::' . __FUNCTION__ . ': Invalid internal identifier.');
            $returnVal = null;
        }
        else {
            
            $returnVal = $referenceRow->external_id;
        }
        
        return $returnVal;
    }

    /**
     * Get all reference numbers for a customer id
     *
     * @param int $customerId Customer id
     * @return array Collection of reference numbers
     */
    public function getAllReferenceIds($customerId)
    {
        $results = array();
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(array('r' => $this->_name), array())
            ->columns(array('r.*'))
            ->join(array('m' => 'customer_types_map'), 'm.reference_id = r.id')
            ->where('customer_id = ?', $customerId);

        $rowSet = $this->fetchAll($select);
        if (count($rowSet) > 0) {
            foreach ($rowSet as $row) {
                $results[] = $row->external_id;
            }
        }

        return $results;
    }
}
