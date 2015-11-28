<?php

/**
 * VocaLink exception:
 *
 * Only occurs for some standard modulus 11 checks, when there is a 2 in the exception column for the first
 * check for a sorting code and a 9 in the exception column for the second check for the same sorting code.
 * This is used specifically for LTSB euro accounts.
 * 
 * Perform the standard check as described in section 3.2, page 9, except:
 * If a != 0 and g != 9, substitute the weight specified in the table with
 * u v w x y z a b c d e f g h
 * 0 0 1 2 5 3 6 4 8 7 10 9 3 1
 * 
 * If a != 0 and g = 9, substitute the weight specified in the table with
 * u v w x y z a b c d e f g h
 * 0 0 0 0 0 0 0 0 8 7 10 9 3 1
 * 
 * If the first row with exception 2 passes the standard modulus 11 check, you do not need to carry out the
 * second check (ie it is deemed to be a valid sterling account).
 * 
 * All LTSB euro accounts are held at sorting code 30-96-34, however customers may perceive that
 * their euro account is held at the branch where sterling accounts are held and thus quote a sorting
 * code other than 30-96-34. The combination of the “sterling” sorting code and “euro” account number
 * will cause the first standard modulus 11 check to fail. In such cases, carry out the second modulus
 * 11 check, substituting the sorting code with 309634 and the appropriate weighting. If this check
 * passes it is deemed to be a valid euro account.
 */
class Model_Core_Bank_ModulusException2 extends Model_Core_Bank_ModulusException {
    
    /**
	 * Full method details given in the superclass.
	 * 
	 * 
	 */
	public function applyPreCheckModifications($modulusCalc) {
        
        $sortCodeAccountMerge = $modulusCalc->getSortCodeAccountMerge();
        $multiplier = $modulusCalc->getMultiplier();
        
        if(($sortCodeAccountMerge->accountNumberA != 0) && ($sortCodeAccountMerge->accountNumberG != 9)) {
            
            $multiplier->sortCodeU = 0;
            $multiplier->sortCodeV = 0;
            $multiplier->sortCodeW = 1;
            $multiplier->sortCodeX = 2;
            $multiplier->sortCodeY = 5;
            $multiplier->sortCodeZ = 3;
            $multiplier->accountNumberA = 6;
            $multiplier->accountNumberB = 4;
            $multiplier->accountNumberC = 8;
            $multiplier->accountNumberD = 7;
            $multiplier->accountNumberE = 10;
            $multiplier->accountNumberF = 9;
            $multiplier->accountNumberG = 3;
            $multiplier->accountNumberH = 1;
        }
        else if(($sortCodeAccountMerge->accountNumberA != 0) && ($sortCodeAccountMerge->accountNumberG == 9)) {

            $multiplier->sortCodeU = 0;
            $multiplier->sortCodeV = 0;
            $multiplier->sortCodeW = 0;
            $multiplier->sortCodeX = 0;
            $multiplier->sortCodeY = 0;
            $multiplier->sortCodeZ = 0;
            $multiplier->accountNumberA = 0;
            $multiplier->accountNumberB = 0;
            $multiplier->accountNumberC = 8;
            $multiplier->accountNumberD = 7;
            $multiplier->accountNumberE = 10;
            $multiplier->accountNumberF = 9;
            $multiplier->accountNumberG = 3;
            $multiplier->accountNumberH = 1;
        }
        
        $modulusCalc->setMultiplier($multiplier);
        return $modulusCalc;
	}
    
    
    /**
	 * Full method details given in the superclass.
	 */
    public function applyPostCheckModifications($modulusCalc) {

        if(!$modulusCalc->isValid()) {
            //If the first check fails, carry out a second check with a sortcode substitute.
            $modulusCalc->setSortCode('309634');
            $modulusCalc->calculateTotal();
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