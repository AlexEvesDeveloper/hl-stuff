<?php

/**
 * Holds a reference's status.
 * 
 * A reference's status is comprised of the reference state (defined in
 * Model_Referencing_ReferenceStates) and, optionally, the reason for the reference
 * state (defined in Model_Referencing_ReferenceStateReasons)/
 */
class Model_Referencing_ReferenceStatus extends Model_Abstract {
	
    /**
	 * The unique, internal reference identifier.
	 * 
	 * @var integer
	 */
	public $referenceId;

    /**
	 * Holds the reference state.
	 * 
	 * @var integer
	 * Must correspond to one of the consts exposed by the
	 * Model_Referencing_ReferenceStates class.
	 */
	public $state = Model_Referencing_ReferenceStates::INPROGRESS;
	
	
	/**
	 * Holds the reason for the current reference state.
	 * 
	 * This is optional, and may not be set, the reason being that some status'
	 * do not require a reason (INPROGRESS, for example).
	 * 
	 * @var mixed
	 * Either an integer corresponding to one of the consts exposed by the
	 * Model_Referencing_ReferenceStateReasons class, or null.
	 */
	public $reasonForState;
}

?>