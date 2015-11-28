<?php

/**
 * An enumerated type (or the PHP equivalent), providing constants that represent the status of
 * TAT details.
 */
class Model_Referencing_TatStates extends Model_Abstract {
	
	/**#@+
	 * Represents the overall state of the TAT.
	 */
	const REFERENCE_COMPLETE = 'Complete';
	const REFERENCE_INPROGRESS = 'In progress';
	/**#@-*/
	
	
	/**#@+
	 * Represents the possible states of the individual TAT reference items.
	 */
	const REFERENCE_ITEM_COMPLETE = 'Received';
	const REFERENCE_ITEM_INPROGRESS = 'Pending';
	const REFERENCE_ITEM_NOTAPPLICABLE = 'n/a';
	/**#@-*/
}

?>