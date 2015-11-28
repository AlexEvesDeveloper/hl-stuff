<?php

/**
 * VocaLink exception:
 *
 * Perform the double alternate check as described in section 3.1, page 8, except:
 * Add 27 to the total (ie before you divide by 10) (this is between steps 3 and 4 in figure 2,
 * page 8). This effectively places a financial institution number (580149) before the
 * sorting code and account number string which is subject to the alternate doubling as well.
 */
class Model_Core_Bank_ModulusException1 extends Model_Core_Bank_ModulusException {
	
	/**
	 * Full method details given in the superclass.
	 */
	public function applyPostCheckModifications($modulusCalc) {
		
		$total = $modulusCalc->getTotal();
		$total += 27;
		$modulusCalc->setTotal($total);
		return $modulusCalc;
	}
}

?>