<?php

/**
 * Encapsulates a single quote/policy document.
 */
class Model_Insurance_Document extends Model_Abstract
{
	/**
	 * Uniquely identifies the document in the data store.
	 *
	 * @var integer
	 */
	public $request_id;
	
	/**
	 * The quote/policynumber associated with the document.
	 *
	 * @var string
	 */
	public $policy_number;
	
	/**
	 * The CSU who created the document, if applicable.
	 *
	 * @var integer
	 */
	public $csuid;
	
	/**
	 * Identifiers the templates this document is comprised of.
	 *
	 * @var string
	 *
	 * @todo
	 * Refactor this, certainly into an array, possibly an array of letter
	 * template objects.
	 */
	public $template_name;
	
	/**
	 * Unique request hash
	 *
	 * @var string
	 */
	public $request_hash;
	
	/***
	 * Holds the time the document was sent
	 *
	 * @var Zend_Date
	 */
	public $send_datetime;
	
	/***
	 * The target type associated with the document.
	 *
	 * @var Zend_Date
	 */
	public $addresse;

    /**
     * Document category type name
     *
     * @var string
     */
    public $catType;

    /**
     * Customer friendly document description
     *
     * @var string
     */
    public $customerDescription;

    /**
     * Document send method
     *
     * @var string
     */
    public $send_method;
}
