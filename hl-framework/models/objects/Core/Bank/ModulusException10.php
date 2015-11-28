<?php

/**
 * VocaLink exception:
 *
 * These exceptions are for some LTSB accounts. If there is a 10 in the exception column for
 * the first check for a sorting code and an 11 in the exception column for the second check
 * for the same sorting code, if either check is successful the account number is deemed valid.
 * For the exception 10 check, if ab = 09 or 99 and g=9, zeroise weighting positions u-b.
 */
class Model_Core_Bank_ModulusException10 extends Model_Core_Bank_ModulusException {
	
	/**
	 * Full method details given in the superclass.
	 */
	public function applyPreCheckModifications($modulusCalc) {
		
		$sortCodeAccountMerge = $modulusCalc->getSortCodeAccountMerge();
		
		if(in_array($sortCodeAccountMerge->accountNumberA, array(0,9)) && $sortCodeAccountMerge->accountNumberB == 9) {
			
			if($sortCodeAccountMerge->accountNumberG == 9) {
				
				//Zeroise weighting positions u through to b.
				$multiplier = $modulusCalc->getMultiplier();
				
				$multiplier->sortCodeU = 0;
				$multiplier->sortCodeV = 0;
				$multiplier->sortCodeW = 0;
				$multiplier->sortCodeX = 0;
				$multiplier->sortCodeY = 0;
				$multiplier->sortCodeZ = 0;
				$multiplier->accountNumberA = 0;
				$multiplier->accountNumberB = 0;
				
				$modulusCalc->setMultiplier($multiplier);
			}
		}
			
		return $modulusCalc;
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
	
	
	/**
	 * Full method details given in the superclass.
	 */
	public function isOneValidCheckEnough() {
		
		return false;
	}
}

?>