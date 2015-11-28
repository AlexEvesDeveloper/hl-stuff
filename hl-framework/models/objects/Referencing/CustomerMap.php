<?php

/**
 * Holds customer details for a reference.
 */
class Model_Referencing_CustomerMap extends Model_Abstract
{
	/**
	 * Holds the type of Reference customer.
	 *
	 * @var integer
     * Must correspond to one of the consts exposed by the Model_Referencing_CustomerTypes class.
	 */
	public $customerType;
    
    /**
	 * Holds the customer identifier.
	 *
     * If the customer is an agent, then this will hold the agent scheme number. If the customer is
     * a private landlord, then this will hold the unique customer id.
     *
	 * @var mixed
	 */
    public $customerId;

    /**
     * Holds the legacy customer identifier.
     *
     * If the customer is a private landlord, this will hold the unique legacy insurance customer
     * refno.
     *
     * @var string
     */
    public $legacyCustomerId;
}
