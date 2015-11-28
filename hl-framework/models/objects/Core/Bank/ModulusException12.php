<?php

/**
 * VocaLink exception:
 *
 * These are for Nationwide Flex accounts. Where there is a 12 in the exception column for
 * the first check for a sorting code and a 13 in the exception column for the second check
 * for the same sorting code, if either check is successful the account number is deemed valid.
 */
class Model_Core_Bank_ModulusException12 extends Model_Core_Bank_ModulusException {
	
	/**
	 * Full method details given in the superclass.
	 */
	public function isSubsequentChecksRequired($isValid) {

		if($isValid) {
			
			$returnVal = false;
		}
		else {
			
			$returnVal = true;
		}
		return $returnVal;
	}
	
	
	/**
	 * Full method details given in the superclass.
	 */
	public function isOneValidCheckEnough() {
		
		return true;
	}
}

?>
