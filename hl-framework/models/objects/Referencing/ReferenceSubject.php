<?php

/**
 * Represents a reference subject within the system. A reference subject can be a
 * prospective tenant or a guarantor, or both. May be an individual, occasionally a company
 * (is in the case of a company reference).
 */
class Model_Referencing_ReferenceSubject extends Model_Abstract {
	
	/**
	 * The Reference identifier, to which this ReferenceSubject is linked.
	 *
	 * @var integer
	 */
	public $referenceId;
	
	/**
	 * The reference subject's name.
	 *
	 * @var Model_Core_Name
	 */
	public $name;
	
	/**
	 * The reference subject's contact details.
	 *
	 * @var Model_Core_ContactDetails
	 */
	public $contactDetails;
	
	/**
	 * The reference subject's date of birth.
	 *
	 * @var mixed
	 * Zend_Date if the dob is known, else is null.
	 */
	public $dob;
	
	/**
	 * Holds the reference subject type (tenant, guarantor or company).
	 *
	 * @var string
	 * Must correspond to one of the consts exposed by the Model_Referencing_ReferenceSubjectTypes class.
	 */
	public $type;
	
	/**
	 * Hods all the reference subject's current and previous residences.
	 *
	 * @var array
	 * An array of Model_Referencing_Residence objects.
	 */
	public $residences;
	
	/**
	 * Holds all the reference subject's occupations.
	 *
	 * @var array
	 * An array of Model_Referencing_Occupation objects.
	 */
	public $occupations;
	
	/**
	 * Holds whether the subject has adverse credit.
	 *
	 * @var boolean
	 */
	public $hasAdverseCredit;
	
	/**
	 * Holds the share of rent to be paid by the subject.
	 *
	 * @var mixed
	 * Zend_Currency if the share of rent is known, else is null.
	 */
	public $shareOfRent;
    
    /**
	 * Holds the bank account details of the tenant.
	 *
	 * @var Model_Referencing_BankAccount
	 * Encapsulates the bank account details.
	 */
	public $bankAccount;
    
	/**
	 * Holds whether the applicant is a foreign national.
	 * 
	 * @var boolean
	 * True if a foreign national, false otherwise.
	 */
	public $isForeignNational;
}

?>