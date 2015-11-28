<?php

/**
 * An enumerated type (or the PHP equivalent), providing constants that represent the reasons for
 * a Reference state.
 */
class Model_Referencing_ReferenceStateReasons extends Model_Abstract {
	
	/**#@+
	 * The set of Reference states.
	 */
	const AWAITING_TENANT_COMPLETION = 1;
	const AWAITING_FURTHER_INFORMATION = 2;
	/**#@-*/
}

?>