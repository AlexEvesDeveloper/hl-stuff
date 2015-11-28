<?php

/**
 * Represents a sortcode and account number combined into a single list of integers.
 * This can then be used against a SortCodeAccountMultiplier to determine if
 * the account number is valid against the sorting code.
 */
class Model_Core_Bank_SortCodeAccountMerge extends Model_Abstract {

	/**#@+
	 * @var string
	 */
	public $sortCodeU = 0;
	public $sortCodeV = 0;
	public $sortCodeW = 0;
	public $sortCodeX = 0;
	public $sortCodeY = 0;
	public $sortCodeZ = 0;
	public $accountNumberA = 0;
	public $accountNumberB = 0;
	public $accountNumberC = 0;
	public $accountNumberD = 0;
	public $accountNumberE = 0;
	public $accountNumberF = 0;
	public $accountNumberG = 0;
	public $accountNumberH = 0;
	/**#@-*/
}

?>