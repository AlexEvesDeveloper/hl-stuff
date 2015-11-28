<?php

/**
 * Encapsulates the details of a reference subject's residence.
 */
class Model_Referencing_Residence extends Model_Abstract {
	
    /**
	 * The unique residence identifier.
	 *
	 * @var integer
	 */
	public $id;
    
    /**
	 * The reference identifier, linking the Reference to this residence.
	 *
	 * @var integer
	 * The unique internal reference identifier.
	 */
	public $referenceId;
    
    /**
	 * Identifies the residence in a timeline.
	 *
	 * @var integer
	 * Must corresond to one of the consts exposed by the
	 * Model_Referencing_ResidenceChronology class.
	 */
	public $chronology;
	
	/**
	 * The address of the residence.
	 *
	 * @var Model_Core_Address
	 */
	public $address;
	
	/**
	 * Indicates the reference subject's duration at the address, in months.
	 *
	 * @var integer
	 */
	public $durationAtAddress;
	
	/**
	 * Holds the residential status of the residence.
	 *
	 * @var string
	 * Must correspond to one of the consts exposed by the
	 * Model_Referencing_ResidenceStatus class.
	 */
	public $status;
	
	/**
	 * Holds details of the residential referee.
	 *
	 * @var Model_Referencing_ResidenceReferee
	 */
	public $refereeDetails;
	
	/**
	 * Holds details of the residential reference.
	 *
	 * @var Model_Referencing_ResidenceReference
	 */
	public $referencingDetails;
}

?>