<?php

class Model_Core_WebLead extends Model_Abstract {

	/**
	 * Unique weblead identifier. Integer.
	 */
	public $quoteId;
	
	/**
	 * Weblead's title (e.g. Mr, Mrs etc). String.
	 */
	public $title;
	
	/**
	 * Weblead's first name. String.
	 */
	public $firstName;
	
	/**
	 * Weblead's last name. String.
	 */
	public $lastName;
	
	/**
	 * Telephone number. String.
	 */
	public $contactNumber;
	
	/**
	 * Email address. String.
	 */
	public $emailAddress;
	
	/**
	 * When the weblead was first initiated. Zend_Date.
	 */
	public $startTime;
	
	/**
	 * When the weblead was last updated. Zend_Date.
	 */
	public $lastUpdated;
	
	/**
	 * Data protected ticked. Boolean.
	 */
	public $dpa;
	
	/**
	 * Status of the weblead. Model_Core_WebLeadStatus.
	 */
	public $status;
	
	/**
	 * Status of the weblead. Model_Core_WebLeadProduct.
	 */
	public $product;
	
	/**
	 * Insurance quote number issued to the weblead. String.
	 */
	public $quoteNumber;
	
	/**
	 * Promotion code supplied by the weblead. String.
	 */
	public $promotionCode;
	
	/**
	 * UNSURE. Boolean.
	 */
	public $followUp;
	
	/**
	 * The weblead's referer. Typically a website. String.
	 */
	public $referer;
	
	/**
	 * Date when the weblead was completed. Zend_Date.
	 */
	public $completed;
}

?>