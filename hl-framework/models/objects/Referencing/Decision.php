<?php

/**
 * Holds the details of a reference decision.
 * 
 * Note that not all references have decisions.
 */
class Model_Referencing_Decision extends Model_Abstract {

	/**
	 * The unique, internal Reference identifier.
	 * 
	 * @var integer
	 */
	public $referenceId;
	
	/**
	 * The actual reference decision.
	 *
	 * @var integer
	 * Must correspond to one of the consts exposed by the 
	 * Model_Referencing_Decisions class.
	 */
	public $decision;
	
	/**
	 * The reason for the decision.
	 * 
	 * Not all decisions have a corresponding reason. This is provided only
	 * where the reason may want to be known. E.g. If the decision is
	 * NOT_ACCEPTABLE, then it would be useful to set this variable.
	 * 
	 * @var mixed
	 * An array of Model_Referencing_DecisionReasons, or null if not set.
	 */
	public $decisionReasons;
	
	/**
	 * Holds a array of decision caveats.
	 * 
	 * A decision may have zero or more caveats - these should be stored
	 * in this variable.
	 * 
	 * @var mixed
	 * An array of Model_Referencing_DecisionCaveat objects, or null
	 * if not set.
	 */
	public $caveats;
}

?>