<?php

/**
* Model definition for the ReferenceSubject datasource.
*/
class Datasource_Referencing_ReferenceSubject extends Zend_Db_Table_Multidb {

    protected $_multidb = 'db_referencing';
    protected $_name = 'reference_subject';
    protected $_primary = 'reference_id';
	
	/**
     * Creates a new, empty ReferenceSubject and corresponding record in the datasource.
     *
     * @param integer $referenceId
     * Identifies the Reference to link the ReferenceSubject against.
     *
     * @return Model_Referencing_ReferenceSubject
     * Returns the newly created, empty ReferenceSubject.
     */
    public function insertPlaceholder($referenceId) {
    
        $this->insert(array('reference_id' => $referenceId));
		
		$referenceSubject = new Model_Referencing_ReferenceSubject();
        $referenceSubject->referenceId = $referenceId;
        return $referenceSubject;
    }
    
    /**
     * Updates an existing ReferenceSubject.
     *
     * @param Model_Referencing_ReferenceSubject $referenceSubject
     * The ReferenceSubject to update in the datasource.
     *
     * @return void
     */
    public function updateReferenceSubject($referenceSubject) {		
		
		if(empty($referenceSubject)) {
			
			return;
		}
		
		
		//Update linked name details, if given.
		if(empty($referenceSubject->name)) {
            
            $nameId = null;
        }
        else {

            //Obtain the $nameId for storage in this datasource.
            $nameId = $referenceSubject->name->id;
            
            //Update linked type.
			$namesDatasource = new Datasource_Core_Names();
            $namesDatasource->updateName($referenceSubject->name);
        }
		
		
		//Update linked contact details, if given.
		if(empty($referenceSubject->contactDetails)) {
            
            $contactId = null;
        }
        else {
            
            //Obtain the $contactId for storage in this datasource.
            $contactId = $referenceSubject->contactDetails->id;
            
            //Update linked type.
			$contactDatasource = new Datasource_Core_ContactDetails();
            $contactDatasource->updateContactDetails($referenceSubject->contactDetails);
        }
		
		
		//Translate the 'hasAdverseCredit'
		if(empty($referenceSubject->hasAdverseCredit)) {
			
			$hasAdverseCredit = 0;
		}
		else {
			
			if($referenceSubject->hasAdverseCredit) {
				
				$hasAdverseCredit = 1;
			}
			else {
				
				$hasAdverseCredit = 0;
			}
		}
		
		
		//Translate 'isForeignNational'
		if(empty($referenceSubject->isForeignNational)) {
			
			$isForeignNational = 0;
		}
		else {
			
			if($referenceSubject->isForeignNational) {
				
				$isForeignNational = 1;
			}
			else {
				
				$isForeignNational = 0;
			}
		}
		
		
		//Update...
		$data = array(
            'name_id' => $nameId,
            'contact_id' => $contactId,
			'dob' => empty($referenceSubject->dob) ? null : $referenceSubject->dob->toString(Zend_Date::ISO_8601),
            'type_id' => $referenceSubject->type,
			'has_adverse_credit' => $hasAdverseCredit,  
			'rent_share' => empty($referenceSubject->shareOfRent) ? null : $referenceSubject->shareOfRent->getValue(),
			'is_foreign_national' => $isForeignNational
		);
        
        $where = $this->quoteInto('reference_id = ?', $referenceSubject->referenceId);
        $this->update($data, $where);
    }
	
    /**
     * Retrieves the specified ReferenceSubject.
     *
     * @param integer $referenceId
     * The unique integer Reference identifier.
     *
     * @return mixed
     * The ReferenceSubject details encapsulated in a Model_Referencing_ReferenceSubject
     * object, or null if the reference subject cannot be found.
     */
    public function getByReferenceId($referenceId) {
        
        $select = $this->select();
        $select->where('reference_id = ?', $referenceId);
        $referenceSubjectRow = $this->fetchRow($select);
        
        if(empty($referenceSubjectRow)) {

            Application_Core_Logger::log(get_class() . '::' . __FUNCTION__ . ': Unable to find ReferenceSubject.');
            $returnVal = null;
        }
        else {

            $referenceSubject = new Model_Referencing_ReferenceSubject();
            $referenceSubject->referenceId = $referenceSubjectRow->reference_id;
			
            if(!empty($referenceSubjectRow->name_id)) {
                
                $namesDatasource = new Datasource_Core_Names();
                $referenceSubject->name = $namesDatasource->getById($referenceSubjectRow->name_id);
            }
            
            if(!empty($referenceSubjectRow->contact_id)) {
                
                $contactDatasource = new Datasource_Core_ContactDetails();
                $referenceSubject->contactDetails = $contactDatasource->getById($referenceSubjectRow->contact_id);
            }
            
			if(!empty($referenceSubjectRow->dob)) {
				
				if($referenceSubjectRow->dob != '0000-00-00') {
				
					$referenceSubject->dob = new Zend_Date($referenceSubjectRow->dob, Zend_Date::ISO_8601);
				}
			}
			
			if(!empty($referenceSubjectRow->type_id)) {
				
				$referenceSubject->type = $referenceSubjectRow->type_id;
			}
            
			if(empty($referenceSubjectRow->has_adverse_credit) || $referenceSubjectRow->has_adverse_credit == 0) {
				
				$referenceSubject->hasAdverseCredit = false;
			}
			else {
				
				$referenceSubject->hasAdverseCredit = true;
			}
            
			if(!empty($referenceSubjectRow->rent_share)) {
					
				$referenceSubject->shareOfRent = new Zend_Currency(
					array(
						'value' => $referenceSubjectRow->rent_share,
						'precision' => 0
					)
				);
			}
			
			if(empty($referenceSubjectRow->is_foreign_national) || $referenceSubjectRow->is_foreign_national == 0) {
				
				$referenceSubject->isForeignNational = false;
			}
			else {
				
				$referenceSubject->isForeignNational = true;
			}
			
            $returnVal = $referenceSubject;
        }
        
        return $returnVal;
    }

	/**
     * Deletes an existing ReferenceSubject.
     *
     * @param Model_Referencing_ReferenceSubject
     * The ReferenceSubject to delete.
     *
     * @return void
     */
	public function deleteReferenceSubject($referenceSubject) {
		
		$where = $this->quoteInto('reference_id = ? ', $referenceSubject->referenceId);
        $this->delete($where);
	}
}

?>