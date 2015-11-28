<?php

/**
 * Wraps around the legacy referencing_uk.landlordref table.
*/
class Datasource_ReferencingLegacy_LandlordRef extends Zend_Db_Table_Multidb {    
    
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_legacy_referencing';
    protected $_name = 'landlordref';
    protected $_primary = 'refno';
    /**#@-*/
    
    
	/**
	 * Inserts a landlordref record in the legacy datasource.
	 *
	 * Landlordref records hold details of prospective landlords (nicely duplicated
	 * from the customer table) and the current landlord (if applicable).
	 *
	 * @param Model_Core_Name
	 * The LL name.
	 * 
	 * @param Model_Core_Address
	 * The LL address.
	 *
	 * @param Model_Core_ContactDetails
	 * The LL contact details.
	 *
	 * @return integer
	 * Returns the newly created primary key identfier.
	 */
	public function insertLandlordRef($name, $address, $contactDetails, $type = Model_Referencing_ResidenceRefereeTypes::PRIVATE_LANDLORD) {

		switch($type) {
			
			case Model_Referencing_ResidenceRefereeTypes::PRIVATE_LANDLORD:
				$convertedType = 'Landlord';
				break;
			case Model_Referencing_ResidenceRefereeTypes::LETTING_AGENT:
				$convertedType = 'Letting/Estate Agent';
				break;
			case Model_Referencing_ResidenceRefereeTypes::MANAGING_AGENT:
				$convertedType = 'Managing Agent';
				break;
			case Model_Referencing_ResidenceRefereeTypes::SOLICITOR:
				$convertedType = 'Solicitor';
				break;
			default:
				$convertedType = 'Letting/Estate Agent';
		}
		
		$data = array(
			'type' => $convertedType,
			'name' => $name->firstName . ' ' . $name->lastName,
			'address1' => $address->addressLine1,
			'address2' => empty($address->addressLine2) ? '' : $address->addressLine2,
			'town' => empty($address->town) ? '' : $address->town,
			'postcode' => $address->postCode,
			'telday' => empty($contactDetails->telephone1) ? '' : $contactDetails->telephone1,
			'fax' => empty($contactDetails->fax1) ? '' : $contactDetails->fax1,
			'televe' => empty($contactDetails->telephone2) ? '' : $contactDetails->telephone2,
			'email' => empty($contactDetails->email1) ? '' : $contactDetails->email1
		);
        return $this->insert($data);
    }
}

?>