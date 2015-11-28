<?php

/**
 * An enumerated type (or the PHP equivalent), providing constants that represent the
 * reasons for a decision caveat.
 */
class Model_Referencing_DecisionCaveatReasons extends Model_Abstract {

	const UNABLE_TO_LOCATE_SUBJECT = 1;
	const POOR_INCOME_RENT_RATIO = 2;
    const POOR_CREDIT_SCORE = 3;
    const CURRENT_OCCUPATION_ENDING = 4;
}

?>