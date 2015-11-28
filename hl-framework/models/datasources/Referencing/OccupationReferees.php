<?php

/**
* Model definition for the occupation referees datasource.
*/
class Datasource_Referencing_OccupationReferees extends Zend_Db_Table_Multidb {
    
    protected $_multidb = 'db_referencing';
    protected $_name = 'occupation_referee';
    protected $_primary = 'occupation_id';
    
	/**
     * Creates a new, empty OccupationalReferee and corresponding record in the datasource.
     *
     * @param integer $occupationId
     * A unique Occupation identifier. This is used to link the new OccupationalReferee
     * record to the corresponding Occupation record.
     *
     * @return Model_Referencing_OccupationalReferee
     * Returns the newly created, empty OccupationalReferee.
     */
    public function createReferee($occupationId) {
    
	    $this->insert(array('occupation_id' => $occupationId));
		
        $referee = new Model_Referencing_OccupationReferee();
		$referee->occupationId = $occupationId;
        return $referee;
    }
    
    
	/**
	 * Updates an existing OccupationalReferee in the datasource.
	 *
	 * @param Model_Referencing_OccupationalReferee
	 * The OccupationalReferee to update.
	 *
	 * @return void
	 */
    public function updateReferee($occupationalReferee) {
        
		if(empty($occupationalReferee)) {
			
			return;
		}
		
		//Update linked name details, if given.
		if(empty($occupationalReferee->name)) {
            
            $nameId = null;
        }
        else {

            //Obtain the $nameId for storage in this datasource.
            $nameId = $occupationalReferee->name->id;
            
            //Update linked type.
			$namesDatasource = new Datasource_Core_Names();
            $namesDatasource->updateName($occupationalReferee->name);
        }
		
		
		//Update linked contact details, if given.
		if(empty($occupationalReferee->contactDetails)) {
            
            $contactId = null;
        }
        else {
            
            //Obtain the $contactId for storage in this datasource.
            $contactId = $occupationalReferee->contactDetails->id;
            
            //Update linked type.
			$contactDatasource = new Datasource_Core_ContactDetails();
            $contactDatasource->updateContactDetails($occupationalReferee->contactDetails);
        }
		
		
		//Update linked address details, if given.
		if(empty($occupationalReferee->address)) {
            
            $addressId = null;
        }
        else {
            
            //Obtain the $addressId for storage in this datasource.
            $addressId = $occupationalReferee->address->id;
            
            //Update linked type.
			$addressDatasource = new Datasource_Core_Addresses();
            $addressDatasource->updateAddress($occupationalReferee->address);
        }
		
		
		//Update...
		$data = array(
            'name_id' => empty($occupationalReferee->name) ? null : $occupationalReferee->name->id,
            'contact_id' => empty($occupationalReferee->contactDetails) ? null : $occupationalReferee->contactDetails->id,
			'address_id' => empty($occupationalReferee->address) ? null : $occupationalReferee->address->id,
			'referee_position' => $occupationalReferee->position,
			'organisation_name' => $occupationalReferee->organisationName
        );
        
        $where = $this->quoteInto('occupation_id = ?', $occupationalReferee->occupationId);
        $this->update($data, $where);
    }
    
    
    /**
     * Retrieves the OccupationalReferee for a specific Occupation.
     *
     * @param integer $occupationId
     * The unique Occupation identifier.
     *
     * @return mixed
     * Returns a Model_Referencing_OccupationalReferee object, or null if no
     * referee is found.
     */
    public function getByOccupationId($occupationId) {
		
		if(empty($occupationId)) {
			
			return null;
		}
		
		
		$select = $this->select();
		$select->where('occupation_id = ?', $occupationId);
		$refereeRow = $this->fetchRow($select);
		
		if(empty($refereeRow)) {

			$returnVal = null;
		}
		else {

			$occupationalReferee = new Model_Referencing_OccupationReferee();
			$occupationalReferee->occupationId = $refereeRow->occupation_id;
			
			if(!empty($refereeRow->name_id)) {
                
                $namesDatasource = new Datasource_Core_Names();
                $occupationalReferee->name = $namesDatasource->getById($refereeRow->name_id);
            }
            
            if(!empty($refereeRow->contact_id)) {
                
                $contactDatasource = new Datasource_Core_ContactDetails();
                $occupationalReferee->contactDetails = $contactDatasource->getById($refereeRow->contact_id);
            }
			
			if(!empty($refereeRow->address_id)) {

                $addressDatasource = new Datasource_Core_Addresses();
                $occupationalReferee->address = $addressDatasource->getById($refereeRow->address_id);
            }
			
			$occupationalReferee->organisationName = $refereeRow->organisation_name;
			$occupationalReferee->position = $refereeRow->referee_position;
			$returnVal = $occupationalReferee;
		}
		
		return $returnVal;
    }
}

?>