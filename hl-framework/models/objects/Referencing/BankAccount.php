<?php

/**
 * Encapsulates a reference subject's bank account details.
 */
class Model_Referencing_BankAccount {

    /**
	 * Identifies the reference to which the bank account is related.
	 *
	 * @var integer
	 * Must correspond to the unique internal Reference identifer.
	 */
    public $referenceId;
    
	/**
	 * Holds the bank account number.
	 *
	 * @var string
	 */
	public $accountNumber;
	
	/**
	 * Holds the bank sort code.
	 *
	 * @var string
	 */
	public $sortCode;
	
	/**
	 * Indicates if the bank account details are valid.
	 *
	 * @var boolean
	 */
    public $isValidated;
}

?>