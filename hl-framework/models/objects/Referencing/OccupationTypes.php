<?php

/**
 * An enumerated type (or the PHP equivalent), providing constants that represent the
 * ReferenceSubject's occupation type.
 */
class Model_Referencing_OccupationTypes extends Model_Abstract {

	const EMPLOYMENT = 1;
	const CONTRACT = 2;
	const SELFEMPLOYMENT = 3;
	const INDEPENDENT = 4;
	const RETIREMENT = 5;
	const STUDENT = 6;
	const UNEMPLOYMENT = 7;
    const OTHER = 8;
}

?>