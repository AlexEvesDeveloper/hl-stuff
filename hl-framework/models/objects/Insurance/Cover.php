<?php

/**
 * Holds an Insurance cover.
 *
 */
class Model_Insurance_Cover extends Model_Abstract {
	public $policyOptionID; 				// Legacy OptionID
	public $sumInsured = 0;					// sum insured
	public $grosspremium = 0;				// annually gross premium
	public $premium = 0;					// annually premium
	public $netpremium = 0;			        // annually net premium
	public $ipt = 0;                        // annually tax

	}
?>