<?php

/**
 * Holds an Insurance quote.
 *
 */
class Model_Insurance_Quote extends Model_Abstract {
	public $ID; 							// ID
	public $legacyID = '';					// Legacy ID
	public $typeID = 2;						// Type ID (Always 2 for Landlords Insurance Plus)
	public $customerID = 0;					// ID of the corresponding customer record
	public $legacyCustomerID = '';			// Legacy ID for the customer
	public $agentSchemeNumber = '1403796';  // Agent Scheme Number if property is agent managed - defaults to us
	
	public $issuedDate;						// Date quote was issued
	public $startDate;						// Commencement date
	public $endDate;						// End date
	public $status = 'QUOTE';				// Always 'QUOTE' until converted to a policy
	
	public $properties = array();			// Array of properties associated with quote (This should allow portfolio to work in this structure)
	
	public $payBy = 'DD';
	public $payFrequency = 'MONTHLY';
	public $policyNumber;
	public $premium=0.00;
	public $ipt=0.00;
	public $policyLength=12;
}
?>