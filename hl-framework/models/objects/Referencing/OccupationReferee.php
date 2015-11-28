<?php

/**
 * Represents a referee providing an occupation reference for a ReferenceSubject.
 */
class Model_Referencing_OccupationReferee extends Model_Abstract {
    
    /**
	 * The unique occupation identifier, to which this referee is linked.
	 *
	 * @var integer
	 */
	public $occupationId;
	
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
	 * The referee address (usually the organisation address).
	 *
	 * @var Model_Core_Address
	 */
	public $address;
	
	/**
	 * The referee's position within the organisation.
	 * 
	 * @var string
	 */
	public $position;
	
	/**
	 * The name of the organisation.
	 *
	 * @var string
	 */
	public $organisationName;
}

?>