<?php

/**
 * Represents a Tenancy Application Tracker (TAT) in the system.
 */
class Model_Referencing_Tat extends Model_Abstract {
	
	/**
	 * Indicates if the initial TAT invitation has been sent to the reference subject.
	 *
	 * @var boolean
	 */
	public $isInvitationSent;
	
	/**
	 * Contains the details of the reference subject.
	 *
	 * @var Model_Referencing_ReferenceSubject
	 * The prospective tenant or guarantor.
	 */
	public $referenceSubject;
	
	/**
	 * Holds the details of the property lease.
	 *
	 * @var Model_Referencing_PropertyLease
	 */
	public $propertyLease;
	
	/**
	 * Holds the overall TAT state.
	 *
	 * @var string
	 * Must correspond to one of the consts exposed by Model_Referencing_TatStates.
	 */
	public $enquiryStatus;
	
	/**
	 * Holds the reference status of the reference subject's current occupation.
	 *
	 * @var string
	 * Must correspond to one of the consts exposed by Model_Referencing_TatStates.
	 */
	public $currentOccupationReferenceStatus;
	
	/**
	 * Holds the reference status of the reference subject's second occupation.
	 *
	 * @var string
	 * Must correspond to one of the consts exposed by Model_Referencing_TatStates.
	 */
	public $secondOccupationReferenceStatus;
	
	/**
	 * Holds the reference status of the reference subject's future occupation.
	 *
	 * @var string
	 * Must correspond to one of the consts exposed by Model_Referencing_TatStates.
	 */
	public $futureOccupationReferenceStatus;
	
	/**
	 * Holds the reference status of the reference subject's current residence.
	 *
	 * @var string
	 * Must correspond to one of the consts exposed by Model_Referencing_TatStates.
	 */
	public $currentResidentialReferenceStatus;
	
	/**
	 * Contains the information missing on the reference.
	 *
	 * @var mixed
	 * An array of strings labelling the items missing from
	 * the reference, or null if no items are missing.
	 */
	public $missingInformation;
	
	/**
	 * Contains the TAT notification history.
	 *
	 * The history contains all the emails sent from the HRT to the
	 * reference subject's email address.
	 *
	 * @var mixed
	 * An array of Model_Referencing_TatNotification objects, or
	 * null if none sent.
	 */
	public $tatNotifications;
}

?>