<?php

/**
 * An enumerated type (or the PHP equivalent), providing constants that represent the
 * relative importance of a ReferenceSubject's occupation, grouped by chronology.
 * 
 * For example, a reference subject may have three current occupations. 'Current' 
 * determines the chronology, and within this chronology the occupations will be 
 * further labelled according to importance (usually determined by income amount 
 * in descending order).
 */
class Model_Referencing_OccupationImportance extends Model_Abstract {
	
	const FIRST = 1;
	const SECOND = 2;
	const THIRD = 3;
}

?>