<?php

/**
 * Represents a PropertyLease within the system. Every reference revolves around the property
 * the applicant intends to lease. Note that many references can link to the same
 * property lease, which is why this object itself does not point to a specific
 * reference ID.
 */
class Model_Referencing_PropertyLease extends Model_Abstract {
	
	/**
	 * Uniquely identifies the property lease in the system.
	 *
	 * @var integer
	 */
	public $id;
	
	/**
	 * Holds the prospective landlord details.
	 *
	 * @var Model_Referencing_ProspectiveLandlord
	 */
	public $prospectiveLandlord;
	
	/**
	 * The address of the property.
	 *
	 * @var Model_Core_Address
	 */
	public $address;
	
	/**
	 * The rental amount per month.
	 *
	 * @var mixed
	 * Zend_Currency if the total rent is known, else is null.
	 */
	public $rentPerMonth;
	
	/**
	 * The tenancy start date.
	 *
	 * var Zend_Date
	 */
	public $tenancyStartDate;
	
	/**
	 * The duration of the tenancy.
	 *
	 * @var integer
	 */
	public $tenancyTerm;
	
	/**
	 * The number of tenants to move into the property.
	 *
	 * @var integer
	 */
	public $noOfTenants;
	
	/**
	 * Encapsulates the property aspect details.
	 * 
	 * Property aspects include property type, number of bedrooms, property
	 * age, property build type and property let types. These details
	 * are used to build the Rental Price Index.
	 *
	 * @var mixed
	 * An array of Model_Referencing_PropertyAspects_PropertyAspectItem objects,
	 * or null if not set.
	 */
	public $propertyAspects;
}

?>