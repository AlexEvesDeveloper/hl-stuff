<?php

/**
 * Represents the name of an individual in the system. Can encapsulate names of
 * applicants, guarantors, referees, agent contacts - anywhere a name is used it can
 * be incorporated into this class for consistency.
 */
class Model_Core_Name extends Model_Abstract {
	
	/**
	 * Uniquely identifies the name in the system.
	 *
	 * @var integer
	 */
	public $id;
    
    /**
	 * Individual's title.
	 *
	 * @var string
	 * Must correspond to one of the consts exposed Model_Core_NameTitles.
	 */
	public $title;
	
	/**
	 * Individual's first name.
	 *
	 * @var string
	 */
    public $firstName;
	
	/**
	 * Individual's middle name.
	 *
	 * @var string
	 */
	public $middleName;
	
	/**
	 * Individual's last name.
	 *
	 * @var string
	 */
	public $lastName;
	
	/**
	 * Individual's maiden name.
	 *
	 * @var string
	 */
	public $maidenName;
}

?>