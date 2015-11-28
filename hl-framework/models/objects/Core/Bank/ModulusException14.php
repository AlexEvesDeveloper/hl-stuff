<?php

/**
 * VocaLink exception:
 *
 * Perform the modulus 11 check as normal:
 * If the check passes (that is, there is no remainder), then the account number should be considered
 * valid. Do not perform the second check
 * If the first check fails, then the second check must be performed as specified below.
 *
 * Second check:
 * If the 8th digit of the account number (reading from left to right) is not 0, 1 or 9 then the account
 * number fails the second check and is not a valid Coutts account number
 * If the 8th digit is 0, 1 or 9, then remove the digit from the account number and insert a 0 as the 1st
 * digit for check purposes only
 * Perform the modulus 11 check on the modified account number using the same weightings as
 * specified in the table (that is, 0 0 0 0 0 0 8 7 6 5 4 3 2 1):
 * If there is no remainder, then the account number should be considered valid
 * If there is a remainder, then the account number fails the second check and is not a valid Coutts
 * account number.
 */
class Model_Core_Bank_ModulusException14 extends Model_Core_Bank_ModulusException {
	
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
		
		// Sets returnVal to true is this is first check and mod11 passes
		if($this->_checkNumber == 1 && $modulusCalc->isValid()) {

			//First check uses the default.
			$returnVal = true;
		}
		else {
			
			//Second check.
			$accountNumberList = preg_split('//', $modulusCalc->getAccountNumber(), -1, PREG_SPLIT_NO_EMPTY);
			if(count($accountNumberList) >= 8) {
				
				if(in_array($accountNumberList[7], array(2, 3, 4, 5, 6, 7, 8))) {
					
					$returnVal = false;
				}
				else {
					
					//Modify the accountNumber by removing the last digit and inserting a new digit
					//at the beginning.
					array_pop($accountNumberList);
					array_unshift($accountNumberList, 0);
					
					//Combine $accountNumberList into an integer so that it can be re-added to the ModulusCalc.
					$accountNumber = '';
					for($i = 0; $i < count($accountNumberList); $i++) {
						
						$accountNumber .= $accountNumberList[$i];
					}
					
					$modulusCalc->setAccountNumber($accountNumber);
					$modulusCalc->calculateTotal();
					$returnVal = $modulusCalc->isValid();
				}
			}
		}
		
		return $returnVal;
	}
	
	
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
}

?>