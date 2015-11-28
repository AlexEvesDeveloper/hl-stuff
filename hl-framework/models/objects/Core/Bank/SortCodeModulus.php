<?php

/**
 * Represents a sortcode modulus in the system.
 */
class Model_Core_Bank_SortCodeModulus extends Model_Abstract {

	public $startSortCode = 0; //string(10)
	public $endSortCode = 0; //string(10)
	public $modulusCheck = 'MOD10'; //enum('MOD10','MOD11','DBLAL')
	public $sortCodeU = 0; //string(4)
	public $sortCodeV = 0; //string(4)
	public $sortCodeW = 0; //string(4)
	public $sortCodeX = 0; //string(4)
	public $sortCodeY = 0; //string(4)
	public $sortCodeZ = 0; //string(4)
	public $accountNumberA = 0; //string(4)
	public $accountNumberB = 0; //string(4)
	public $accountNumberC = 0; //string(4)
	public $accountNumberD = 0; //string(4)
	public $accountNumberE = 0; //string(4)
	public $accountNumberF = 0; //string(4)
	public $accountNumberG = 0; //string(4)
	public $accountNumberH = 0; //string(4)
	public $exceptionCode = 0; //string(4)
}

?>