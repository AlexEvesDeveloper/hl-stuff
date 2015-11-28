<?php

/**
 * An enumerated type (or the PHP equivalent), providing constants that represent the
 * progress items that may occur during the lifetime of a reference.
 */
class Model_Referencing_ProgressItemVariables extends Model_Abstract {

	/**#@+
	 * Note that customer details are not a progress item variable, because customer details
	 * are required to log a reference, which mean they are always 'complete'.
	 */
	const STARTED = 1;
	const PROPERTY_DETAILS_SUBMITTED = 2;
	const PROPERTY_LANDLORD_DETAILS_SUBMITTED = 3;
	const REFERENCE_SUBJECT_DETAILS_SUBMITTED = 4;
	const RESIDENTIAL_DETAILS_SUBMITTED = 5;
	const RESIDENTIAL_REFEREE_DETAILS_SUBMITTED = 6;
	const OCCUPATION_DETAILS_SUBMITTED = 7;
	const TERMS_AGREED = 8;
	const PAYMENT_ARRANGED = 9;
	const CREDIT_DATA_REQUESTED = 10;
	const RESIDENTIAL_REFEREE_LETTER_SENT = 11;
	const CURRENT_OCCUPATION_REFEREE_LETTER_SENT = 12;
	const SECOND_OCCUPATION_REFEREE_LETTER_SENT = 13;
	const FUTURE_OCCUPATION_REFEREE_LETTER_SENT = 14;
	const REFERENCE_SUBJECT_LETTER_SENT = 15;
	const RESIDENTIAL_REFERENCE_RECEIVED = 16;
	const OCCUPATION_REFERENCES_RECEIVED = 17;
	const INTERIM_REPORT_BUILT = 18;
    const INTERIM_REPORT_SENT = 19;
	const FINAL_REPORT_BUILT = 20;
    const FINAL_REPORT_SENT = 21;
	const FINISHED = 22;
	const REOPENED = 23;
	/**#@-*/
}

?>