<?php

/**
 * Domain object which represents a single WebLeadSummary, the high level record
 * of a web-based sales lead.
 */
class Model_Core_WebLeadSummary extends Model_Abstract {

	/**
	 * Unique WebLeadSummary identifier. Integer.
	 */
	public $webLeadSummaryId;
	
	/**
	 * WebLead's title (e.g. Mr, Mrs etc). String.
	 */
	public $title;
	
	/**
	 * WebLead's first name. String.
	 */
	public $firstName;
	
	/**
	 * WebLead's last name. String.
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
	 * When the WebLead was first initiated. Zend_Date.
	 */
	public $startTime;
	
	/**
	 * When the WebLead was last updated. Zend_Date.
	 */
	public $lastUpdatedTime;
	
	/**
	 * Status of the WebLead. Model_Core_WebLeadStatus.
	 */
	public $status = Model_Core_WebLeadStatus::IN_PROGRESS;
	
	/**
	 * Status of the WebLead. Model_Core_WebLeadProduct.
	 */
	public $product = Model_Core_WebLeadProduct::NONE;
	
	/**
	 * Insurance quote number issued to the WebLead. String.
	 */
	public $quoteNumber;
	
	/**
	 * Promotion code supplied by the WebLead. String.
	 */
	public $promotionCode;
	
	/**
	 * Flag for chasing the lead. Boolean.
	 */
	public $followUp = false;
	
	/**
	 * The WebLead's referer. Typically a website. String.
	 */
	public $referer;
	
	/**
	 * Date when the WebLead was completed. Zend_Date.
	 */
	public $completedTime;

    /**
     * @var bool Flag for whether a cron-triggered mailer has been sent.
     */
    public $isMailerSent = false;
}

?>