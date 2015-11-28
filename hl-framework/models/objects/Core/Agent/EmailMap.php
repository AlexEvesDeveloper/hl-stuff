<?php

/**
 * Represents a category mapping between an Agent object and a Core_Email
 * object.
 *
 * @category   Model
 * @package    Model_Core
 * @subpackage Agent
 */
class Model_Core_Agent_EmailMap extends Model_Abstract {

    /**
     * The category of the e-mail address.
     *
     * @var Model_Core_Agent_EmailMapCategory Indicates the e-mail address category.
     */
	public $category;

	/**
     * The e-mail address.
     *
     * @var Model_Core_EmailAddress Indicates the e-mail address.
     */
	public $emailAddress;

}