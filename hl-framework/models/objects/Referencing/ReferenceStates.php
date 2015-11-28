<?php

/**
 * An enumerated type (or the PHP equivalent), providing constants that represent the 
 * possible Reference states.
 */
class Model_Referencing_ReferenceStates extends Model_Abstract {
	
	/**#@+
	 * The set of Reference states.
	 */
	const INPROGRESS = 1;
	const CANCELLED = 2;
	const COMPLETE = 3;
	const INCOMPLETE = 4;
	/**#@-*/
}

?>