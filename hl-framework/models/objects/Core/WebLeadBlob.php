<?php

/**
 * Domain object which represents a single WebLead blob. A blob is a bunch of
 * form data scraped from the web page and stored without processing.
 */
class Model_Core_WebLeadBlob extends Model_Abstract {

	/**
	 * Unique WebLead identifier. Integer.
	 */
	public $webLeadSummaryId;
	
	/**
	 * WebLead quote step number. Integer.
	 */
	public $stepNumber;
	
	/**
	 * The blob, raw form data from the page the user is on.
	 */
	public $blob;
	
	/**
	 * Checksum run against the blob.
	 */
	public $blobChecksum;
}

?>