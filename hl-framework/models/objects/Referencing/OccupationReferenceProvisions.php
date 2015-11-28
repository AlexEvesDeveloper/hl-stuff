<?php

/**
 * An enumerated type (or the PHP equivalent), providing constants that represent the
 * methods by which an occupation reference can be provided.
 */
class Model_Referencing_OccupationReferenceProvisions extends Model_Abstract {
	
	const EMPLOYMENT_REFERENCE = 1;
	const CONTRACT_REFERENCE = 2;
	const ACCOUNTANT_REFERENCE = 3;
	const SA302_FORMS = 4;
	const PENSION_ADMINISTRATOR_REFERENCE = 5;
	const PENSION_STATEMENTS = 6;
	const BANK_STATEMENTS = 7;
	const PAY_SLIPS = 8;
}

?>