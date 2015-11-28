<?php

/**
 * Simple factory to return a ModulusException object.
 *
 * During modulus checking, various exceptions to the normal rules may apply.
 * The static factory method provided by this class will identify and return the
 * appropriate ModulusException object.
 */
class Model_Core_Bank_ModulusExceptionFactory {

	const MODULUS_EXCEPTION_01 = 1;
	const MODULUS_EXCEPTION_02 = 2;
	const MODULUS_EXCEPTION_03 = 3;
	const MODULUS_EXCEPTION_04 = 4;
	const MODULUS_EXCEPTION_05 = 5;
	const MODULUS_EXCEPTION_06 = 6;
	const MODULUS_EXCEPTION_07 = 7;
	const MODULUS_EXCEPTION_08 = 8;
	const MODULUS_EXCEPTION_09 = 9;
	const MODULUS_EXCEPTION_10 = 10;
	const MODULUS_EXCEPTION_11 = 11;
	const MODULUS_EXCEPTION_12 = 12;
	const MODULUS_EXCEPTION_13 = 13;
	const MODULUS_EXCEPTION_14 = 14;
	
	
	/**
	 * Returns an appropriate ModulusException object.
	 *
	 * @param integer $exceptionCode
	 * Identifies the modulus checking VocaLink exception.
	 *
	 * @param integer $modulo
	 * The modulus to be applied.
	 *
	 * @param integer $checkNumber
	 * Indicates which check is currently being applied (1 or 2).
	 *
	 * @return mixed
	 * Returns a Model_Core_Bank_ModulusException, if an applicable one can be
	 * found, null otherwise.
	 */
	public static function createModulusException($exceptionCode, $modulo, $checkNumber) {
		
		$exception = null;
		
		switch($exceptionCode) {
			
			case self::MODULUS_EXCEPTION_01:
				//Applies only to Double Alternate checking.
				if($modulo == Model_Core_Bank_ModulusCalc::DOUBLE_ALTERNATE) {
					
					$exception = new Model_Core_Bank_ModulusException1($checkNumber);
				}
				break;
			
			case self::MODULUS_EXCEPTION_02:
				//Applies only to Modulus 11 checking.
				if($modulo == Model_Core_Bank_ModulusCalc::MODULUS_11) {
					
					$exception = new Model_Core_Bank_ModulusException2($checkNumber);
				}
				break;
			
			case self::MODULUS_EXCEPTION_03:
				//Applies only to Double Alternate checking.
				if($modulo == Model_Core_Bank_ModulusCalc::DOUBLE_ALTERNATE) {
					
					$exception = new Model_Core_Bank_ModulusException3($checkNumber);
				}
				break;
			
			case self::MODULUS_EXCEPTION_04:
				//Applies only to Modulus 11 checking.
				if($modulo == Model_Core_Bank_ModulusCalc::MODULUS_11) {
					
					$exception = new Model_Core_Bank_ModulusException4($checkNumber);
				}
				break;
			
			case self::MODULUS_EXCEPTION_05:
				#throw new Zend_Exception('ModulusException5 not yet built.');
				$exception = new Model_Core_Bank_ModulusException5($checkNumber);
				break;
			
			case self::MODULUS_EXCEPTION_06:
				//Applies to all modulus checking
				$exception = new Model_Core_Bank_ModulusException6($checkNumber);
				break;
			
			case self::MODULUS_EXCEPTION_07:
				//Applies to all modulus checking
				$exception = new Model_Core_Bank_ModulusException7($checkNumber);
				break;
			
			case self::MODULUS_EXCEPTION_08:
				//Applies to all modulus checking
				$exception = new Model_Core_Bank_ModulusException8($checkNumber);
				break;
			
			case self::MODULUS_EXCEPTION_09:
				//Applies only to Modulus 11 checking.
				if($modulo == Model_Core_Bank_ModulusCalc::MODULUS_11) {
					
					//Use the same ModulusException as that which handles exception 2.
					$exception = new Model_Core_Bank_ModulusException2($checkNumber);
				}
				break;
			
			case self::MODULUS_EXCEPTION_10:
				//Applies to all modulus checking.
				$exception = new Model_Core_Bank_ModulusException10($checkNumber);
				break;
			
			case self::MODULUS_EXCEPTION_11:
				//Applies to all modulus checking. Use the same ModulusException as that
				//which handles exception 10.
				$exception = new Model_Core_Bank_ModulusException10($checkNumber);
				break;
			
			case self::MODULUS_EXCEPTION_12:
				//Applies to all modulus checking.
				$exception = new Model_Core_Bank_ModulusException12($checkNumber);
				break;
			
			case self::MODULUS_EXCEPTION_13:
				//Applies to all modulus checking. Use the same ModulusException as that
				//which handles exception 12.
				$exception = new Model_Core_Bank_ModulusException12($checkNumber);
				break;
			
			case self::MODULUS_EXCEPTION_14:
				//Applies to Modulus 11 checking only.
				if($modulo == Model_Core_Bank_ModulusCalc::MODULUS_11) {
					
					$exception = new Model_Core_Bank_ModulusException14($checkNumber);
				}
				break;
		}
		
		return $exception;
	}
}
?>