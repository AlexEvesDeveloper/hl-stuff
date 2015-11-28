<?php

/**
 * Represents a ProgressItem within the system.
 *
 * ProgressItems are stored in an array in the the Progress class.
 *
 * Only APPLICABLE ProgressItems should be created for a reference. For example,
 * if the current reference is an Insight, then there is no need to
 * create a ProgressItem encapsulating a
 * Model_Referencing_ProgressItemVariables::OCCUPATION_DETAILS variable, as
 * we do not capture occupation details on an Insight.
 */
class Model_Referencing_ProgressItem extends Model_Abstract {
	
	/**
	 * Holds the progress item variable.
	 *
	 * MUST correspond to one of the consts exposed by the
	 * Model_Referencing_ProgressItemVariables class.
	 *
	 * @var integer
	 * Described above.
	 */
	public $itemVariable;
	
	/**
	 * Holds the state of the progress item.
	 *
	 * MUST correspond to one of the consts exposed by the
	 * Model_Referencing_ProgressItemStates class.
	 *
	 * @var integer
	 * Described above.
	 */
	public $itemState;
	
	/**
	 * Holds the time at which the progress item was completed.
	 *
	 * @var mixed
	 * Zend_Date if the item is complete, else is null.
	 */
	public $itemCompletionTimestamp;
}

?>