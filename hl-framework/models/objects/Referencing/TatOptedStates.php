<?php

/**
 * An enumerated type (or the PHP equivalent), providing constants that represent
 * whether an agent is opted in or out of the TAT service.
 */
class Model_Referencing_TatOptedStates extends Model_Abstract {
	
	/**#@+
	 * Represents whether agent is opted-in or out of the TAT service.
	 *
	 * @var string
	 */
	const OPTED_IN = 'in';
	const OPTED_OUT = 'out';
	/**#@-*/
}

?>