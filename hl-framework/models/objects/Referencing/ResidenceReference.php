<?php

/**
 * Represents a reference recieved from a residence referee.
 */
class Model_Referencing_ResidenceReference extends Model_Abstract {
	
	/**
	 * The unique residence identifier.
	 *
	 * @var integer
	 */
	public $residenceId;
	
	/**
	 * Holds the details of how the reference was submitted.
	 *
	 * @var integer
	 * Must correspond to one of the consts exposed by the
	 * Model_Referencing_ReferenceSubmissionTypes class.
	 */
	public $submissionType;
	
	/**
	 * Indicates the reference subject's duration at the address, in months.
	 *
	 * @var integer
	 */
	public $durationAtAddress;
	
	/**
	 * Holds the monthly rental amount.
	 *
	 * @var Zend_Currency
	 */
	public $currentRent;
	
	/**
	 * Indicates whether or not the rent is paid promptly.
	 *
	 * @var boolean
	 */
	public $isRentPaidPromptly;
	
	/**
	 * Indicates whether or not the reference subject is *satisfactory*.
	 *
	 * Yes, its a subjective answer.
	 *
	 * @var boolean
	 */
	public $isSatisfactory;
	
	/**
	 * Indicates whether or not the reference subject is a good tenant.
	 *
	 * Indeed, its another subject answer.
	 *
	 * @var boolean
	 */
	public $isGoodTenant;
}

?>