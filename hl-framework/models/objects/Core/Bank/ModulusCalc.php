<?php

/**
 * Determines if a bank account number is valid against a corresponding sorting code.
 */
class Model_Core_Bank_ModulusCalc {

	/**#@+
	 * Consts which should be used to identify the modulus to apply in the
	 * validation calculation.
	 */
	const MODULUS_10 = '10';
	const MODULUS_11 = '11';
	const DOUBLE_ALTERNATE = '10';
	/**#@-*/
	
	
	protected $_sortCode;
	protected $_accountNumber;
	protected $_modulo;
	protected $_multiplier;
	protected $_total;
	
    /**
     * Construct the correct sub class based on the modulo type
     *
     * @param string $sortCode Sortcode to check
     * @param string $accountNumber Accout no to check
     * @param string $modulo
     */
    public static function factory($sortCode, $accountNumber, $modulo, $multiplier)
    {
        switch ($multiplier->modulusCheck)
        {
            case 'MOD10':
                return new Model_Core_Bank_Modulus_Mod10($sortCode, $accountNumber, $modulo, $multiplier);
            
            case 'MOD11':
                return new Model_Core_Bank_Modulus_Mod11($sortCode, $accountNumber, $modulo, $multiplier);
            
            case 'DBLAL':
                return new Model_Core_Bank_Modulus_ModDblAlt($sortCode, $accountNumber, $modulo, $multiplier);
            
            default:
                // Unknown modulus type
                throw new Exception("Invalid modulus. Bank validation not possible.");
        }
    }
	
	public function __construct($sortCode, $accountNumber, $modulo, $multiplier) {
		
		$this->_sortCode = $sortCode;
		$this->_accountNumber = $accountNumber;
		$this->_modulo = $modulo;
		$this->_multiplier = $multiplier;
	}
	
	
	/**
	 * Returns the sortcode.
	 *
	 * @return string.
	 * The sortcode.
	 */
	public function getSortCode() {
		
		return $this->_sortCode;
	}
	
	
	/**
	 * Returns the bank account number.
	 *
	 * @return string.
	 * The bank account number.
	 */
	public function getAccountNumber() {
		
		return $this->_accountNumber;
	}
	
	
	/**
	 * Returns the modulo used in the validation calculation.
	 *
	 * @return integer.
	 * The modulo. Corresponds to one of the consts exposed by this class.
	 */
	public function getModulo() {
		
		return $this->_modulo;
	}
	
	
	/**
	 * Returns the Model_Core_Bank_SortCodeAccountMultipler.
	 *
	 * This is used to validate the SortCodeAccountMerge.
	 *
	 * @return Model_Core_Bank_SortCodeAccountMultipler
	 * The multiplier.
	 */
	public function getMultiplier() {
		
		return $this->_multiplier;
	}
	
	
	/**
	 * Returns the pre-validation calculation result.
	 *
	 * The pre-validation calculation result is that determined by the
	 * calculateTotal() method. This total will be used in the isValid() method
	 * as part of the validation process.
	 *
	 * @return integer
	 * The pre-validation total.
	 */
	public function getTotal() {
		
		return $this->_total;
	}
	
	
	/**
	 * Returns the sortcode and account number encapsulated in a merge object.
	 *
	 * @return Model_Core_Bank_SortCodeAccountMerge
	 * The sortcode and account number encapsulated into a single object for
	 * the purposes of comparison against a SortCodeAccountMultiplier.
	 */
	public function getSortCodeAccountMerge() {
		
		$sortCodeList = preg_split('//', $this->_sortCode, -1, PREG_SPLIT_NO_EMPTY);
		$accountNumberList = preg_split('//', $this->_accountNumber, -1, PREG_SPLIT_NO_EMPTY);
		
		$merge = new Model_Core_Bank_SortCodeAccountMerge();
		$merge->sortCodeU = $sortCodeList[0];
		$merge->sortCodeV = $sortCodeList[1];
		$merge->sortCodeW = $sortCodeList[2];
		$merge->sortCodeX = $sortCodeList[3];
		$merge->sortCodeY = $sortCodeList[4];
		$merge->sortCodeZ = $sortCodeList[5];
		$merge->accountNumberA = $accountNumberList[0];
		$merge->accountNumberB = $accountNumberList[1];
		$merge->accountNumberC = $accountNumberList[2];
		$merge->accountNumberD = $accountNumberList[3];
		$merge->accountNumberE = $accountNumberList[4];
		$merge->accountNumberF = $accountNumberList[5];
		$merge->accountNumberG = $accountNumberList[6];
		$merge->accountNumberH = $accountNumberList[7];
		
		return $merge;
	}

	
	/**
	 * Sets the sortcode
	 *
	 * @param mixed $sortCode
	 * The sortcode. Should be formatted. Can be a string or integer.
	 *
	 * @return void
	 */
	public function setSortCode($sortCode) {
		
		$this->_sortCode = $sortCode;
	}

	
	/**
	 * Sets the account number
	 *
	 * @param mixed $accountNumber
	 * The account number. Should be formatted. Can be a string or integer.
	 *
	 * @return void
	 */
	public function setAccountNumber($accountNumber) {

		$this->_accountNumber = $accountNumber;
	}
	
	
	/**
	 * Sets the modulo used in the validation process.
	 *
	 * @param integer $modulo
	 * Must correspond to a const exposed by this class.
	 */
	public function setModulo($modulo) {
		
		$this->_modulo = $modulo;
	}
	
	
	/**
	 * Sets the SortCodeAccountMultipler used in the validation process.
	 *
	 * @param Model_Core_Bank_SortCodeAccountMultipler
	 * Will be later compared against a SortCodeAccountMerge object to determine
	 * if the account number is valid against the sortcode.
	 */
	public function setMultiplier($multiplier) {
		
		$this->_multiplier = $multiplier;
	}
	
	
	/**
	 * Sets the pre-calculation total.
	 *
	 * @param integer $total
	 * The result of the pre-calculation comparison between a merge and multiplier
	 * object.
	 */
	public function setTotal($total) {
		
		$this->_total = $total;
	}
	
	
	/**
	 * Multiplies a SortCodeAccountMerge and a SortCodeAccountMultipler.
	 *
	 * The multiplication of the related elements in both of these objects is combined
	 * into a total, which will later be used in the isValid() method.
	 *
	 * @return void
	 */
	public function calculateTotal() 
	{
		
        //Prepare the calculation variables.
		$sortCodeAccountMerge = $this->getSortCodeAccountMerge();			
		
		//Calculate the total
		$total = 0;
		
		$total += ($sortCodeAccountMerge->sortCodeU * $this->_multiplier->sortCodeU);
		$total += ($sortCodeAccountMerge->sortCodeV * $this->_multiplier->sortCodeV);
		$total += ($sortCodeAccountMerge->sortCodeW * $this->_multiplier->sortCodeW);
		$total += ($sortCodeAccountMerge->sortCodeX * $this->_multiplier->sortCodeX);
		$total += ($sortCodeAccountMerge->sortCodeY * $this->_multiplier->sortCodeY);
		$total += ($sortCodeAccountMerge->sortCodeZ * $this->_multiplier->sortCodeZ);
		$total += ($sortCodeAccountMerge->accountNumberA * $this->_multiplier->accountNumberA);
		$total += ($sortCodeAccountMerge->accountNumberB * $this->_multiplier->accountNumberB);
		$total += ($sortCodeAccountMerge->accountNumberC * $this->_multiplier->accountNumberC);
		$total += ($sortCodeAccountMerge->accountNumberD * $this->_multiplier->accountNumberD);
		$total += ($sortCodeAccountMerge->accountNumberE * $this->_multiplier->accountNumberE);
		$total += ($sortCodeAccountMerge->accountNumberF * $this->_multiplier->accountNumberF);
		$total += ($sortCodeAccountMerge->accountNumberG * $this->_multiplier->accountNumberG);
		$total += ($sortCodeAccountMerge->accountNumberH * $this->_multiplier->accountNumberH);

		$this->_total = $total;
	}
	
	
	/**
	 * Determines if an account number is valid against a sorting code.
	 *
	 * Takes the total produced by the calculateTotal() method and applies the modulo
	 * to it. If no remainder, then the account number is valid. Otherwise is invalid.
	 *
	 * @return boolean
	 * True if the account number is valid, false otherwise.
	 */
	public function isValid() {
		
		return ($this->_total % $this->_modulo == 0) ? true : false;
	}
}

?>