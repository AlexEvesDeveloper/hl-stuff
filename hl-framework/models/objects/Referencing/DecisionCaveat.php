<?php

/**
 * Holds the details of a single decision caveat. 
 * 
 * A decision made on a reference may have a caveat, e.g. that a suitable guarantor be provided.
 * Objects of this class hold such details, together with the corresponding reason(s) for
 * the caveat.
 */
class Model_Referencing_DecisionCaveat extends Model_Abstract {
	
	/**
	 * Holds the decision caveat.
	 * 
	 * @var integer
	 * Must correspond to one of the consts exposed by the 
	 * Model_Referencing_DecisionCaveats class.
	 */
	public $caveat;
	
	/**
	 * Holds the reason for the decision caveat.
	 * 
	 * @var integer
	 * Must correspond to one of the consts exposed by the
	 * Model_Referencing_DecisionCaveatReasons class.
	 */
	public $caveatReason;
}

?>