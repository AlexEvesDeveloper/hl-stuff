<?php

/**
 * Encapsulates the details of a single sortcode/account multiplier. Used
 * to validate bank account numbers against sorting codes as part of a direct
 * comparison against a Model_Core_Bank_SortCodeAccountMerge.
 */
class Model_Core_Bank_SortCodeAccountMultipler extends Model_Abstract {

	/**#@+
	 * @var string
	 */
	public $startSortCode = 0;
	public $endSortCode = 0; //string(10)
	/**#@-*/
	
	
	/**
	 * @var integer
	 * Permitted values are 'MOD10','MOD11','DBLAL'
	 */
	public $modulusCheck = 'MOD10';
	
	
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
	public $exceptionCode = 0;
	/**#@-*/
}

?>