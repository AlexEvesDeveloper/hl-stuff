<?php

/**
 * An enumerated type (or the PHP equivalent), providing constants that represent
 * who may log a Reference onto the system.
 */
class Model_Referencing_ReferenceUserTypes extends Model_Abstract {
	
	const AGENT = 1;
	const PRIVATE_LANDLORD = 2;
    const REFERENCE_SUBJECT = 3;
    const CSU = 4;
}

?>