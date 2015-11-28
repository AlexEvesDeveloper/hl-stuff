<?php

/**
 * An enumerated type (or the PHP equivalent), providing constants that represent the
 * product variables applicable to the different referencing products.
 */
class Model_Referencing_ProductVariables extends Model_Abstract {

    const RENT_GUARANTEE = 1;
    const NON_RENT_GUARANTEE = 2;
    const LEGAL_EXPENSES = 3;
    const NON_LEGAL_EXPENSES = 4;
    const INTERNATIONAL = 5;
    const NATIONAL = 6;
    const EXCESS_APPLIES = 7;
    const NO_EXCESS = 8;
	const EXCESS_NOT_APPLICABLE = 9;
    const CREDIT_REFERENCE = 10;
    const FULL_REFERENCE = 11;
    const VARIABLE_DURATION = 12;
    const FIXED_DURATION = 13;
}

?>