<?php

/**
 * VocaLink exception:
 * 
 * Perform the check as specified, except if g=9 zeroise weighting positions u-b.
 */
class Model_Core_Bank_ModulusException7 extends Model_Core_Bank_ModulusException {
	
	/**
	 * Full method details given in the superclass.
	 */
	public function applyPreCheckModifications($modulusCalc) {
		
		$sortCodeAccountMerge = $modulusCalc->getSortCodeAccountMerge();
		
		if($sortCodeAccountMerge->accountNumberG == 9) {
			
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
			
		return $modulusCalc;
	}
}

?>