<?php

/**
 * Represents a prospective landlord within the system.
 */
class Model_Referencing_ProspectiveLandlord extends Model_Abstract {
	
	/**
	 * Uniquely identifies the prospective landlord in the system.
	 *
	 * @var integer
	 */
	public $id;
	
	/**
	 * The prospective landlords's name.
	 *
	 * @var Model_Core_Name
	 */
	public $name;
	
	/**
	 * The prospective landlord's address.
	 *
	 * @var Model_Core_Address
	 */
	public $address;
	
	/**
	 * The prospective landlord's contact details.
	 *
	 * @var Model_Core_ContactDetails
	 */
	public $contactDetails;
}

?>