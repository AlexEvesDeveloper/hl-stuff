<?php

/**
 * Generic modulus checking exception class.
 *
 * Encapsulates a modulus checking exception, which allows variations to the
 * standard validations that are applied during a account number validation.
 */
class Model_Core_Bank_ModulusException {
	
	/**
	 * The modulus checking number. Only two checks are applied, so this
	 * value should be either 1 or 2.
	 *
	 * @var integer
	 * The modulus checking number.
	 */
	protected $_checkNumber;
	
	
	public function __construct($checkNumber) {
		
		$this->_checkNumber = $checkNumber;
	}
	
	
	/**
	 * Indicates if a modulus check is required.
	 *
	 * Some account numbers cannot be checked, so this method should be used
	 * to identify this before commencing.
	 *
	 * @param Model_Core_Bank_ModulusCalc
	 * The modulus checking calculator.
	 *
	 * @return boolean
	 * Returns true by default.
	 */
	public function isCheckRequired($modulusCalc) {
		
		return true;
	}
	
	
	/**
	 * Applies modifications to the modulus checking calculator ahead of totals calculation.
	 *
	 * Not always used.
	 *
	 * @param Model_Core_Bank_ModulusCalc
	 * The modulus checking calculator.
	 *
	 * @return Model_Core_Bank_ModulusCalc
	 * Returns the unmodified modulus checking calculator by default. Subclasses
	 * should return the modified modulus checking calculator, where appropriate.
	 */
	public function applyPreCheckModifications($modulusCalc) {
		
		return $modulusCalc;
	}
	
	
	/**
	 * Applies modifications to the modulus checking calculator ahead of validation.
	 *
	 * Not always used.
	 *
	 * @param Model_Core_Bank_ModulusCalc
	 * The modulus checking calculator.
	 *
	 * @return Model_Core_Bank_ModulusCalc
	 * Returns the unmodified modulus checking calculator by default. Subclasses
	 * should return the modified modulus checking calculator, where appropriate.
	 */
	public function applyPostCheckModifications($modulusCalc) {
		
		return $modulusCalc;
	}
	
	
	/**
	 * Indicates if this class should validate the modulus check.
	 *
	 * By default the  modulus check is handled by the modulus checking calculator,
	 * however, under some circumstances it should be handled by subclasses
	 * of ModulusException.
	 *
	 * @return boolean
	 * Returns false by default. Subclasses should modify the return value where
	 * appropriate.
	 */
	public function isValidatedByException() {
		
		return false;
	}
	
	
	/**
	 * Used in conjunction with the isValidatedByException() above.
	 *
	 * If the ModulusException class is to perform final validation on the
	 * bank account number, then this method should be called, rather than
	 * that of the modulus checking calculator, which is the default.
	 *
	 * @param Model_Core_Bank_ModulusCalc
	 * The modulus checking calculator.
	 *
	 * @return boolean
	 * Returns true or false depending on whether the account number is valid
	 * against the sortcode. Default behaviour is to call the $modulusCalc->isValid().
	 */
	public function isValid($modulusCalc) {
		
		return $modulusCalc->isValid();
	}
	
	
	/**
	 * Advises whether additional checks are required.
	 *
	 * Some modulus checks only need one check to determine if the account number
	 * is valid or otherwise. This method can be used by subclasses to indicate
	 * this.
	 *
	 * @param boolean $isValid
	 * The result of the modulus check.
	 *
	 * @return boolean
	 * True if more checks are required, false otherwise. Returns true by default.
	 */
	public function isSubsequentChecksRequired($isValid) {
		
		return true;
	}
	
	
	/**
	 * Advises if one valid check out of two is enough.
	 *
	 * Some account numbers are defined as valid if they pass only one of their
	 * tests. Subclasses should override this method to indicate when this is
	 * appropriate.
	 *
	 * @return boolean
	 * Whether one check out of two is enough. Returns true by default.
	 */
	public function isOneValidCheckEnough() {
		
		return true;
	}
}

?>