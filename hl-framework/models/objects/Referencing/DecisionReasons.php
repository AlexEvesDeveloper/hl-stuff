<?php

/**
 * An enumerated type (or the PHP equivalent), providing constants that represent the
 * reasons for a reference decision. Note that not all decisions have corresponding reasons.
 */
class Model_Referencing_DecisionReasons extends Model_Abstract {

	const DETRIMENTAL_LANDLORD_REFERENCE = 1;
	const RENT_PAID_IN_ADVANCE = 2;
	const ALL_CRITERIA_SATISFIED = 3;
}

?>