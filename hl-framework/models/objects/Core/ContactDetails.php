<?php

/**
 * Represents the contact details of an entity within the system. The entity can be an
 * individual, referee, letting agent etc. Anywhere contact details are used they
 * can be incorporated into this class for consistency.
 */
class Model_Core_ContactDetails extends Model_Abstract {
	
	/**
	 * Uniquely identifies the contact details in the system.
	 *
	 * @var integer
	 */
	public $id;
    
    /**
	 * First, primary or daytime telephone number.
	 *
	 * @var string
	 */
	public $telephone1;
	
	/**
	 * Secondary, cellular or evening telephone number.
	 *
	 * @var string
	 */
	public $telephone2;
	
	/**
	 * First or primary email address.
	 *
	 * @var string
	 */
	public $email1;
	
	/**
	 * Secondary email address.
	 *
	 * @var string
	 */
	public $email2;
	
	/**
	 * First or primary fax number.
	 *
	 * @var string
	 */
	public $fax1;
	
	/**
	 * Secondary fax number.
	 *
	 * @var string
	 */
	public $fax2;
	
	/**
	 * Create some public getters
	 */
	
	/**
	 * 
	 * Gets the 
	 * @param unknown_type $index
	 */
	public function getTelephone(){
		// No I don't like this either, but it's a bit late to refactor poor ojects now
		return array(
			'telephone1' => $this->telephone1,
			'telephone2' => $this->telephone2);
	}
	

	public function getFax(){
		// No I don't like this either, but it's a bit late to refactor poor ojects now
		return array(
					'fax1' => $this->fax1,
					'fax2' => $this->fax2);
		}
		
	public function getEmail($index){
		// No I don't like this either, but it's a bit late to refactor poor ojects now
		return array(
						'email1' => $this->email1,
						'email2' => $this->email2);
	}		
}

?>