<?php

/**
 * Represents the progress of a reference within the system.
 */
class Model_Referencing_Progress extends Model_Abstract {
	
	/**
	 * The Reference identifier, to which this progress object is linked.
	 *
	 * @var integer
	 */
	public $referenceId;
	
	/**
	 * Holds an array of ProgressItems APPLICABLE to the reference.
	 * 
	 *
	 * Each item of the array must be a Model_Referencing_ProgressItem
	 * instance.
	 *
	 * @var array
	 * The array of APPLICABLE ProgressItems.
	 */
	public $items;
}

?>