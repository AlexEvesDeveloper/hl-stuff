<?php

/**
 * Represents a category mapping between an Agent object, a Core_ContactDetails
 * object and a Core_Address object.
 *
 * @category   Model
 * @package    Model_Core
 * @subpackage Agent
 */
class Model_Core_Agent_ContactMap extends Model_Abstract {

    /**
     * The category of the e-mail address.
     *
     * @var Model_Core_Agent_ContactMapCategory Indicates the contact category.
     */
	public $category;

	/**
     * The physical address.
     *
     * @var Model_Core_Address Indicates the physical address.
     */
	public $address;

    /**
     * The phone numbers.
     *
     * @var Model_Core_ContactDetails Indicates the phone and fax number.
     */
	public $phoneNumbers;
	
}