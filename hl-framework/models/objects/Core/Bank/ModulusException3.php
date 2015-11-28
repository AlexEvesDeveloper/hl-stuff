<?php

/**
 * VocaLink exception:
 *
 * If c = 6 or 9 the double alternate check does not need to be carried out.
 */
class Model_Core_Bank_ModulusException3 extends Model_Core_Bank_ModulusException {
	
	/**
	 * Full method details given in the superclass.
	 */
	public function isCheckRequired($modulusCalc) {

        $sortCodeAccountMerge = $modulusCalc->getSortCodeAccountMerge();
        
        if(($sortCodeAccountMerge->accountNumberC == 6) || ($sortCodeAccountMerge->accountNumberC == 9)) {
            
            $returnVal = false;
        }
        else {
            
            $returnVal = true;
        }
		
        return $returnVal;
	}
	
	public function isOneValidCheckEnough()
	{
		return true;
	}
}

?>