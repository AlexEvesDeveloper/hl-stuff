<?php

/**
 * Represents a valid e-mail address in the system.  Can encapsulate type
 * checking in due course when getters and setters are in use.  Anywhere an
 * e-mail address is used it can be incorporated into this class for
 * consistency.
 *
 * @category   Model
 * @package    Model_Core
 * @subpackage EmailAddress
 */
class Model_Core_EmailAddress extends Model_Abstract {

	/**
	 * E-mail address.
	 *
	 * @var string A valid e-mail address.
	 */
	public $emailAddress;
}