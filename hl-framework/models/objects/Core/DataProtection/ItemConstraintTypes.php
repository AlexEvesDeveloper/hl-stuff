<?php

/**
 * An enumerated type (or the PHP equivalent), providing constants that represent the
 * data protection item constraint types.
 */
class Model_Core_DataProtection_ItemConstraintTypes extends Model_Abstract {
	
	const MARKETING_BY_PHONEANDPOST = 1;
	const MARKETING_BY_THIRDPARTY = 2;
    const MARKETING_BY_SMSANDEMAIL = 3;
	const MARKETING_BY_NONDIGITAL_MEANS = 4;
	const MARKETING_BY_DIGITAL_MEANS = 5;
}

?>