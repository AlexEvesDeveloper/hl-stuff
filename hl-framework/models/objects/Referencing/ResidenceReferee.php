<?php

/**
 * Represents a referee providing a residence reference for a reference
 * subject (tenant, guarantor or company).
 */
class Model_Referencing_ResidenceReferee extends Model_Abstract {
    
    /**
	 * The residence identifier, linking the referee to the relevant residence.
	 *
	 * @var integer
	 */
	public $residenceId;
	
	/**
	 * The name of the referee.
	 *
	 * @var Model_Core_Name
	 */
	public $name;
	
	/**
	 * The referee contact details.
	 *
	 * @var Model_Core_ContactDetails
	 */
	public $contactDetails;
	
	/**
	 * The address of the referee.
	 *
	 * @var Model_Core_Address
	 */
	public $address;
	
	/**
	 * The type of residence referee.
	 *
	 * @var int
	 * Must correspond to one of the consts exposed by the
	 * Model_Referencing_ResidenceRefereeTypes class.
	 */
	public $type;
}

?>