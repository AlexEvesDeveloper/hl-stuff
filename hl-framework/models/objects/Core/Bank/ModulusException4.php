<?php

/**
 * VocaLink exception:
 *
 * Perform the standard modulus 11 check. After you have finished the check:
 * Ensure that the remainder is the same as the two-digit checkdigit – the checkdigit for exception 4 is
 * gh from the original account number.
 */
class Model_Core_Bank_ModulusException4 extends Model_Core_Bank_ModulusException {
	
	/**
	 * Full method details given in the superclass.
	 */
	public function isValidatedByException() {
		
		return true;
	}
	
	
	/**
	 * Full method details given in the superclass.
	 */
	public function isValid($modulusCalc) {
			
		$sortCodeAccountMerge = $modulusCalc->getSortCodeAccountMerge();	
		$checkDigits = $sortCodeAccountMerge->accountNumberG . $sortCodeAccountMerge->accountNumberH;
		$remainder = $modulusCalc->getTotal() % 11;
		if($remainder == $checkDigits) {
			$isValid = true;
		}
		else {
			$isValid = false;
		}
		
		return $isValid;
	}
}

?>