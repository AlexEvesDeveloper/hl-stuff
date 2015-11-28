<?php

/**
 * Represents a sortcode substitution in the system. Some sortcodes need to
 * be substituted prior to modulus checking, as specified by VocaLink.
 */
class Model_Core_Bank_SortCodeSubstitution extends Model_Abstract {

	/**
	 * Identifies the substitution in the datasource.
	 * 
	 * @var integer
	 */
	public $sortCodeSubstitutionID;
	
	
	/**
	 * Identifies a sortcode to replace.
	 * 
	 * @var string
	 * The existing sortcode to replace.
	 */
	public $sortCodeToReplace;
	
	
	/**
	 * Identifies the sortcode that should be used in place of the original.
	 *
	 * @var string
	 * The replacement sortcode.
	 */
	public $substituteSortCode;
}

?>