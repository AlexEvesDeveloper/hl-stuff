<?php

/**
 * VocaLink exception:
 * 
 * Perform the check as specified, except substitute the sorting code with 090126,
 * for check purposes only.
 */
class Model_Core_Bank_ModulusException8 extends Model_Core_Bank_ModulusException {
	
	/**
	 * Full method details given in the superclass.
	 */
	public function applyPreCheckModifications($modulusCalc) {
		
		$modulusCalc->setSortCode('090126');		
		return $modulusCalc;
	}
}

?>