<?php

/**
* Model definition for the ResidentialReferee datasource.
*/
class Datasource_Referencing_ResidenceReferees extends Zend_Db_Table_Multidb {
    
    protected $_multidb = 'db_referencing';
    protected $_name = 'residence_referee';
    protected $_primary = 'residence_id';
    
	/**
     * Creates a new, empty ResidentialReferee and corresponding record in the datasource.
     *
     * @param integer $residenceId
     * A unique Residence identifier. This is used to link the new ResidentialReferee
     * record to the corresponding Residence record.
     *
     * @return Model_Referencing_ResidentialReferee
     * Returns the newly created, empty ResidentialReferee.
     */
    public function createReferee($residenceId) {
    
	    $this->insert(array('residence_id' => $residenceId));
		
        $residence = new Model_Referencing_ResidenceReferee();
		$residence->residenceId = $residenceId;
        return $residence;
    }

	/**
	 * Updates an existing ResidentialReferee in the datasource.
	 *
	 * @param Model_Referencing_ResidentialReferee
	 * The ResidentialReferee to update.
	 *
	 * @return void
	 */
    public function updateReferee($residentialReferee) {
        
		if(empty($residentialReferee)) {
			
			return;
		}
		
		//Update linked name details, if given.
		if(empty($residentialReferee->name)) {
            
            $nameId = null;
        }
        else {

            //Obtain the $nameId for storage in this datasource.
            $nameId = $residentialReferee->name->id;
            
            //Update linked type.
			$namesDatasource = new Datasource_Core_Names();
            $namesDatasource->updateName($residentialReferee->name);
        }
		
		
		//Update linked contact details, if given.
		if(empty($residentialReferee->contactDetails)) {
            
            $contactId = null;
        }
        else {
            
            //Obtain the $contactId for storage in this datasource.
            $contactId = $residentialReferee->contactDetails->id;
            
            //Update linked type.
			$contactDatasource = new Datasource_Core_ContactDetails();
            $contactDatasource->updateContactDetails($residentialReferee->contactDetails);
        }
		
		
		//Update linked address details, if given.
		if(empty($residentialReferee->address)) {
            
            $addressId = null;
        }
        else {
            
            //Obtain the $addressId for storage in this datasource.
            $addressId = $residentialReferee->address->id;
            
            //Update linked type.
			$addressDatasource = new Datasource_Core_Addresses();
            $addressDatasource->updateAddress($residentialReferee->address);
        }
		
		
		//Update...
		$data = array(
            'name_id' => empty($residentialReferee->name) ? null : $residentialReferee->name->id,
			'address_id' => empty($residentialReferee->address) ? null : $residentialReferee->address->id,
            'contact_id' => empty($residentialReferee->contactDetails) ? null : $residentialReferee->contactDetails->id,
			'type_id' => $residentialReferee->type
        );
        
        $where = $this->quoteInto('residence_id = ?', $residentialReferee->residenceId);
        $this->update($data, $where);
    }
    
	/**
     * Retrieves the ResidentialReferee for a specific Residence.
     *
     * @param integer $residenceId
     * The unique Residence identifier.
     *
     * @return mixed
     * Returns a Model_Referencing_ResidentialReferee object, or null if no
     * referee is found.
     */
    public function getByResidenceId($residenceId) {
		
		if(empty($residenceId)) {
			
			return null;
		}
		
		
		$select = $this->select();
		$select->where('residence_id = ?', $residenceId);
		$refereeRow = $this->fetchRow($select);
		
		if(empty($refereeRow)) {
			
			$returnVal = null;
		}
		else {
			
			$residentialReferee = new Model_Referencing_ResidenceReferee();
			$residentialReferee->residenceId = $refereeRow->residence_id;
			$residentialReferee->type = $refereeRow->type_id;
			
			if(!empty($refereeRow->name_id)) {
                
                $namesDatasource = new Datasource_Core_Names();
                $residentialReferee->name = $namesDatasource->getById($refereeRow->name_id);
            }
            
            if(!empty($refereeRow->contact_id)) {
                
                $contactDatasource = new Datasource_Core_ContactDetails();
                $residentialReferee->contactDetails = $contactDatasource->getById($refereeRow->contact_id);
            }
			
			if(!empty($refereeRow->address_id)) {

                $addressDatasource = new Datasource_Core_Addresses();
                $residentialReferee->address = $addressDatasource->getById($refereeRow->address_id);
            }
			
			$returnVal = $residentialReferee;
		}
		
		return $returnVal;
    }
}

?>