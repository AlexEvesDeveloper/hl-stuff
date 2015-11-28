<?php
	/**
	* Objects of this class can be used to store details of a Bank Interest on a policy.
	* @author John Burrin
	* @since 1.3
	* 
	*/

class Model_Insurance_BankInterest {
	
	/**
	* int(11) PRIMARY KEY 
	*/
	public $interestID;
	
	/**
	* varchar(50), Policy reference number
	*/
	public $refno;
	
	/**
	* varchar(50) Policy number
	*/
	public $policynumber = "";
	
	/**
	* varchar(50) Bank name
	*/
	public $bankname;
	
	/**
	* varchar(50) Bank address
	*/
	public $bankaddress1;
	
	/**
	* varchar(50)
	*/
	public $bankaddress2;
	
	/**
	* varchar(50)
	*/
	public $bankaddress3;
	
	/**
	* varchar(50)
	*/
	public $bankaddress4;
	
	/**
	* varchar(50) Postcode
	*/
	public $bankpostcode;

	/**
	* varchar(50) Account number at the bank
	*/
	public $accountnumber;

	/**
	* varchar(50) Link to portfolio_properties
	*/
	public $propertyId;
}
?>