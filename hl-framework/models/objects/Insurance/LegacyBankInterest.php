<?php

/**
* Objects of this class can be used to store details of a Bank Interest on a policy.
*/
class Model_Insurance_LegacyBankInterest {
	
    protected $_interestId;
	protected $_refno;
    protected $_policynumber;
    protected $_bankname;
    protected $_bankAddress;
    protected $_accountnumber;

    
    public function __construct() {
    	
        $this->_interestId = null;
    	$this->_refno = "";
        $this->_policynumber = "";
        $this->_bankname = "";
        $this->_bankAddress = new Model_Core_Address();
        $this->_accountnumber = "";
    }
    
    /**
     * Returns the unique bank interest identifier.
     * 
     * @return mixed
     * The unique bank interest identifier as an integer, or null if not set.
     */
    public function getInterestId() {
    	
    	return $this->_interestId;
    }

    /**
     * Returns the policy reference number associated with the bank interest.
     *
     * @return string
     * Returns the reference number.
     */
    public function getRefno() {
    	
        return $this->_refno;
    }
    
    /**
    * Returns the quote/policy number associated with the bank interest.
    *
    * @return string
    * The quote/policynumber.
    */
    public function getPolicyNumber() {
    	
        return $this->_policynumber;
    }
    
    /**
    * Returns the bank name.
    *
    * @return string.
    * The bank name.
    */
    public function getBankName() {
    	
        return $this->_bankname;
    }
    
    /**
     * Returns the bank address.
     *
     * @return Model_Core_Address.
     * The bank address.
     */
    public function getBankAddress() {
    	
    	return $this->_bankAddress;
    }

    /**
     * Returns the account number.
     *
     * @return string.
     * The account number.
     */
    public function getAccountNumber() {
    	
        return $this->_accountnumber;
    }
    
    /**
     * Sets the unique bank interest identifier.
     * 
     * @param mixed $interestId;
     * The unique bank interest identifier as an integer, or null.
     * 
     * @return void
     */
    public function setInterestId($interestId) {
    	
    	$this->_interestId = $interestId;
    }

    /**
     * Sets the policy reference number associated with the bank interest.
     * 
     * @param string $refno
     * The policy reference number.
     */
    public function setRefno($refno) {
    	
        $this->_refno = $refno;
    }
    
    /**
     * Sets the quote/policy number associated with the bank interest.
     * 
     * @param string $policynumber
     * The quote/policy number.
     */
    public function setPolicyNumber($policynumber) {
    	
        $this->_policynumber = $policynumber;
    }
    
    /**
     * Sets the bank name.
     * 
     * @param string $bankname
     * The bank name.
     */
    public function setBankName($bankname) {
    	
        $this->_bankname = $bankname;
    }
    
    /**
     * Sets the bank address.
     * 
     * @param Model_Core_Address $address
     * Object encapsulating the bank address details.
     */
    public function setBankAddress($address) {
    	
    	$this->_bankAddress = $address;
    }

    /**
     * Sets the account number associated with the bank interest.
     * 
     * @param mixed $accountnumber
     * The bank account number.
     */
    public function setAccountNumber($accountnumber) {
    	
        $this->_accountnumber = $accountnumber;
    }
}

?>