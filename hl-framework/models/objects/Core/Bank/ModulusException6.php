<?php

/**
 * VocaLink exception:
 * 
 * Indicates that these sorting codes may contain foreign currency accounts which cannot be checked.
 * Perform the first and second checks, except:
 * If a = 4, 5, 6, 7 or 8, and g and h are the same, the accounts are for a foreign currency and the checks
 * cannot be used.
 */
class Model_Core_Bank_ModulusException6 extends Model_Core_Bank_ModulusException {
	
	/**
	 * Full method details given in the superclass.
	 */
	public function isCheckRequired($modulusCalc) {

		$sortCodeAccountMerge = $modulusCalc->getSortCodeAccountMerge();
		
		if(in_array($sortCodeAccountMerge->accountNumberA, array(4,5,6,7,8))) {
			
			if($sortCodeAccountMerge->accountNumberG == $sortCodeAccountMerge->accountNumberH) {

				throw new Zend_Exception("Unable to verify - foreign account.");

			}
		}
		
		return true;
	}
}

?>