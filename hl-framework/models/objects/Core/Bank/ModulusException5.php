<?php

/**
 * VocaLink exception:
 *
 * Perform the first check (standard modulus check) except:
 * If the sorting code appears in the sortcode substitution datasource in the “Original s/c” column,
 * substitute it for the “substitute with” column (for check purposes only). If the sorting code is not found,
 * use the original sorting code.
 *
 * Note: The sorting code substitution table information above is also available as a text file (SCSUBTAB.txt).
 * Each original sorting code entry in the table above is represented as a line in the text file. The fields in the
 * file are a fixed length and are separated by a “space”. The fields in the text file read from left to right as
 * follows:
 * 
 * Original sorting code 6 characters
 * Substitute sorting code 6 characters.
 * 
 * For the standard check with exception 5 the checkdigit is g from the original account number.
 * After dividing the result by 11;
 * if the remainder=0 and g=0 the account number is valid
 * if the remainder=1 the account number is invalid
 * for all other remainders, take the remainder away from 11. If the number you get is the same as g
 * then the account number is valid.
 * 
 * Perform the second double alternate check, and for the double alternate check with exception 5 the checkdigit is h from the
 * original account number, except:
 * 
 * After dividing the result by 10;
 * if the remainder=0 and h=0 the account number is valid
 * for all other remainders, take the remainder away from 10. If the number you get is the same as h
 * then the account number is valid.
 */
class Model_Core_Bank_ModulusException5 extends Model_Core_Bank_ModulusException {
	
	/**
	 * Full method details given in the superclass.
	 */
	public function applyPreCheckModifications($modulusCalc) {
		
		$substituteSortCode = $this->_getSortCodeSubstitution($modulusCalc->getSortCode());
		if(!empty($substituteSortCode)) {
			
			$modulusCalc->setSortCode($substituteSortCode);
		}
		return $modulusCalc;
	}
	
	
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
		
		$isValid = false;
		$sortCodeAccountMerge = $modulusCalc->getSortCodeAccountMerge();		
		
		if($modulusCalc->getModulo() == Model_Core_Bank_ModulusCalc::MODULUS_11) {
			
			$remainder = $modulusCalc->getTotal() % 11;
			if($remainder == 0) {
				
				if($sortCodeAccountMerge->accountNumberG == 0) {
					
					$isValid = true;
				}
			}
			else if($remainder != 1) {
				
				$modifiedRemainder = 11 - $remainder;
				if($modifiedRemainder == $sortCodeAccountMerge->accountNumberG) {
					
					$isValid = true;
				}
			}
		}
		else {
			
			$remainder = $modulusCalc->getTotal() % 10;
			if($remainder == 0) {
				
				if($sortCodeAccountMerge->accountNumberH == 0) {
					
					$isValid = true;
				}
			}
			else {
				
				$modifiedRemainder = 10 - $remainder;
				if($modifiedRemainder == $sortCodeAccountMerge->accountNumberH) {
					
					$isValid = true;
				}
			}
		}
		
		return $isValid;
	}
	
	
	/**
     * Attempts to find a substitute sort code.
     * 
     * @param integer $sortcode
     * The sortcode to substitute
     * 
     * @return mixed
     * Returns the substitute sortcode, or null if no substitute is found.
     */
    protected function _getSortCodeSubstitution($sortCode) {

        $sortCodeSubstitution = new Datasource_Core_Bank_SortCodeSubstitution();
        $substitution = $sortCodeSubstitution->getSubstitution($sortCode);
        
        if(!empty($substitution)) {
            
            $substituteSortCode = $substitution->substituteSortCode;
        }
        else {
            
            $substituteSortCode = null;
        }
        
        return $substituteSortCode;
    }
}

?>