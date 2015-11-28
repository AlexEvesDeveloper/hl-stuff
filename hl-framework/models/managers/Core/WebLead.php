<?php

/**
 * Manager class responsible for implementing WebLead-related business logic,
 * and for binding together the WebLead domain objects and datasources.
 */
class Manager_Core_WebLead {

	protected $_webLeadSummaries;
	protected $_webLeadBlobs;
	
	
	/**
	 * Creates a new WebLeadSummary, writes it to the datastore, and returns
	 * the object. The object will be blank except for its unique identifier.
	 *
	 * @return Model_Core_WebLeadSummary
	 * The newly created WebLead.
	 */
	public function createNewSummary() {
		
		//First insert a new, blank WebLeadSummary into the datastore.
		if(!isset($this->_webLeadSummaries)) {
			
			$this->_webLeadSummaries = new Datasource_Core_WebLeadSummaries();
		}
		$webLeadId = $this->_webLeadSummaries->insertSummary();
		
		
		//Populate the $webLeadId into a new WebLeadSummary domain object, then return
		//the object.
		$webLeadSummary = new Model_Core_WebLeadSummary();
		$webLeadSummary->webLeadSummaryId = $webLeadId;
		return $webLeadSummary;
	}
	
	
	/**
	 * Updates an existing WebLeadSummary in the data store.
	 *
	 * @param Model_Core_WebLeadSummary
	 * The WebLeadSummary object to be updated in the data store.
	 *
	 * @throws Zend_Exception
	 * Throws a Zend_Exception if the update did not affect one and only one
	 * entry in the datastore.
	 */
	public function updateSummary($webLeadSummary) {
		
		if(!isset($this->_webLeadSummaries)) {
			
			$this->_webLeadSummaries = new Datasource_Core_WebLeadSummaries();
		}
		
		$noOfRowsAffected = $this->_webLeadSummaries->updateSummary($webLeadSummary);
/*
 * Needs re-munting
 * 		if($noOfRowsAffected != 1) {
			
			throw new Zend_Exception('WebLeadSummary update operation failed.');
		}
*/
	}
	
	
	/**
	 * Identifies if a WebLeadSummary exists in the datasource.
	 *
	 * @param integer $webLeadSummaryId
	 * Specifies the WebLeadSummary to search for.
	 *
	 * @return boolean
	 * Returns true if the WebLeadSummary exists in the datasource, false otherwise.
	 */
	public function getSummaryExists($webLeadSummaryId) {
		
		if(!isset($this->_webLeadSummaries)) {
			
			$this->_webLeadSummaries = new Datasource_Core_WebLeadSummaries();
		}
		return $this->_webLeadSummaries->getSummaryExists($webLeadSummaryId);
	}
	
	
	/**
	 * Retrieves a Model_Core_WebLeadSummary from the datasource, specified by
	 * the identifier passed in.
	 *
	 * @param integer $webLeadSummaryId
	 * Specifies the WebLeadSummary to retrieve.
	 *
	 * @return Model_Core_WebLeadSummary
	 * The WebLeadSummary corresponding to the identifier passed in.
	 *
	 * @throws Zend_Exception
	 * Throws a Zend_Exception if the WebLeadSummary cannot be found.
	 */
	public function getSummary($webLeadSummaryId) {
	
		if(!isset($this->_webLeadSummaries)) {
			
			$this->_webLeadSummaries = new Datasource_Core_WebLeadSummaries();
		}
		return $this->_webLeadSummaries->getSummary($webLeadSummaryId);
	}
	
	
	/**
	 * Retrieves a WebLead ID corresponding to the quote number passed in.
	 *
	 * @param string $quoteNumber
	 * Specifies the WebLead identifier to retrieve.
	 *
	 * @return mixed
	 * The WebLead identifier, or null if one cannot be found.
	 */
	public function getSummaryId($quoteNumber) {
		
		if(!isset($this->_webLeadSummaries)) {
			
			$this->_webLeadSummaries = new Datasource_Core_WebLeadSummaries();
		}
		return $this->_webLeadSummaries->getSummaryId($quoteNumber);
	}
	
	/**
	 * Removes WebLeads from the data store, and corresponding WebLeadBlobs.
	 *
	 * @param integer $webLeadSummaryId
	 * The identifer against which corresponding WebLeads and WebLeadBlobs will
	 * be deleted.
	 */
	public function removeWebLead($webLeadSummaryId) {
	
		//Remove summary
		if(!isset($this->_webLeadSummaries)) {
			
			$this->_webLeadSummaries = new Datasource_Core_WebLeadSummaries();
		}
		$this->_webLeadSummaries->removeSummary($webLeadSummaryId);
		
		
		//Remove blobs.
		if(!isset($this->_webLeadBlobs)) {
			
			$this->_webLeadBlobs = new Datasource_Core_WebLeadBlobs();
		}

		$this->_webLeadBlobs->removeBlob($webLeadSummaryId, 1);
	}
	
	
	/**
	 * Creates a new WebLeadBlob in the datasource, then returns an object
	 * representation of this.
	 *
	 * @param integer $webLeadSummaryId
	 * The WebLeadSummary identifier, which the blob is linked to.
	 *
	 * @param integer $stepNumber
	 * The step number the blob will encapsulate. Must correspond to one of the
	 * consts exposed by Model_Core_WebLeadStep.
	 *
	 * @return Model_Core_WebLeadBlob
	 * The newly created WebLeadBlob, which has been inserted into the datasource.
	 *
	 * @throws Zend_Exception
	 * Throws a Zend_Exception if the $webLeadSummaryId does not identify a
	 * WebLeadSummary within the datasource.
	 */
	public function createNewBlob($webLeadSummaryId, $stepNumber) {
		
		//Ensure the WebLeadSummary object exists.
		if(!isset($this->_webLeadSummaries)) {
			
			$this->_webLeadSummaries = new Datasource_Core_WebLeadSummaries();
		}
		
		if(!$this->getSummaryExists($webLeadSummaryId)) {
			
			throw new Zend_Exception('Invalid WebLeadSummary identifier.');
		}
		
		
		//Insert the initial blob data into the datasource, then use it to
		//populate a corresponding domain object, which is then returned to
		//the caller.
		if(!isset($this->_webLeadBlobs)) {
			
			$this->_webLeadBlobs = new Datasource_Core_WebLeadBlobs();
		}
		$this->_webLeadBlobs->insertBlob($webLeadSummaryId, $stepNumber);
		
		$webLeadBlob = new Model_Core_WebLeadBlob();
		$webLeadBlob->webLeadSummaryId = $webLeadSummaryId;
		$webLeadBlob->stepNumber = $stepNumber;
		return $webLeadBlob;
	}
	
	
	/**
	 * Updates an existing WebLeadBlob.
	 *
	 * @param Model_Core_WebLeadBlob
	 * The WebLeadBlob object to be updated in the data source.
	 *
	 * @return integer
	 * The number of entries in the data source affected by the update.
	 */
	public function updateBlob($webLeadBlob) {
		
		if(!isset($this->_webLeadBlobs)) {
			
			$this->_webLeadBlobs = new Datasource_Core_WebLeadBlobs();
		}
		
		return $this->_webLeadBlobs->updateBlob($webLeadBlob);
	}
	
	
	/**
	 * Determines if a WebLeadBlob exists.
	 *
	 * @param integer $webLeadSummaryId
	 * The WebLeadSummary identifier, which the blob is linked to.
	 *
	 * @param integer $stepNumber
	 * The step number the blob encapsulates. Must correspond to one of the
	 * consts exposed by Model_Core_WebLeadStep.
	 *
	 * @return boolean
	 * Returns true if the WebLeadBlob exists, false otherwise.
	 */
	public function getBlobExists($webLeadSummaryId, $stepNumber) {
		
		try {
			
			$this->getBlob($webLeadSummaryId, $stepNumber);
			$returnVal = true;
		}
		catch(Zend_Exception $e) {
			
			$returnVal = false;
		}
		return $returnVal;
	}
	
	
	/**
	 * Retrieves a Model_Core_WebLeadBlob from the datasource, specified by
	 * the identifiers passed in.
	 *
	 * @param integer $webLeadSummaryId
	 * The WebLeadSummary identifier, which the blob is linked to.
	 *
	 * @param integer $stepNumber
	 * The step number the blob encapsulates. Must correspond to one of the
	 * consts exposed by Model_Core_WebLeadStep.
	 *
	 * @throws Zend_Exception
	 * Throws a Zend_Exception if the WebLeadBlob cannot be found.
	 */
	public function getBlob($webLeadSummaryId, $stepNumber) {
		
		if(!isset($this->_webLeadBlobs)) {
			
			$this->_webLeadBlobs = new Datasource_Core_WebLeadBlobs();
		}
		return $this->_webLeadBlobs->getBlob($webLeadSummaryId, $stepNumber);
	}

    /**
     * Intended to be run by cron.
     *
     * Looks through recent quotes to process mailers for.  Criteria to be satisfied: a) must be x minutes old (eg,
     * probably abandoned) (also should be no older than y to prevent historic quotes being pinged), b) user must have
     * reached at least step z, and c) no mailer has been sent.
     *
     * Values for x, y and z are set in parameters and are independent of the web lead view and status from IAS.
     *
     * @return void
     */
    public function sendMailers()
    {
        // Prepare web lead summary data source
        if (null == $this->_webLeadSummaries) {
            $this->_webLeadSummaries = new Datasource_Core_WebLeadSummaries();
        }

        // Fetch relevant parameters
        $params = Zend_Registry::get('params');
        $hourOffset = $params->weblead->hourOffset;
        $minLastUpdated = $params->weblead->mailer->abandonedAge; // In seconds
        $maxLastUpdated = $params->weblead->mailer->ignoredAge; // In seconds

        $mailerAtSteps = array(
            Model_Core_WebLeadProduct::TCIPLUS => array(
                'continueQuote' => explode(',', $params->weblead->mailer->tenant->continueQuote->steps), // Comma-separated lists
                'completeQuote' => explode(',', $params->weblead->mailer->tenant->completeQuote->steps),
            ),
            Model_Core_WebLeadProduct::LANDLORDSPLUS => array(
                'continueQuote' => explode(',', $params->weblead->mailer->landlord->continueQuote->steps),
                'completeQuote' => explode(',', $params->weblead->mailer->landlord->completeQuote->steps),
            ),
        );

        $mailerProduct = array(
            Model_Core_WebLeadProduct::TCIPLUS => 'tenant',
            Model_Core_WebLeadProduct::LANDLORDSPLUS => 'landlord',
        );

        $searchCriteria = array(
            'hourOffset' => $hourOffset,
            'minLastUpdated' => $minLastUpdated,
            'maxLastUpdated' => $maxLastUpdated,
            'products' => array(
                Model_Core_WebLeadProduct::TCIPLUS,
                Model_Core_WebLeadProduct::LANDLORDSPLUS
            ),
            'isMailerSent' => 0,
        );

        // Fetch summaries that match the criteria
        $webLeadSummaries = $this->_webLeadSummaries->searchActiveSummaries($searchCriteria);

        // Early exit if there's nothing to do
        if (count($webLeadSummaries) == 0) {
            return;
        }

        // Loop through summaries looking for those that meet a "mailer at step" requirement
        foreach($webLeadSummaries as $webLeadSummary) {
            $product = $webLeadSummary->product;
            $maxCompletedStep = $this->getBlobMaxStep($webLeadSummary->webLeadSummaryId);

            foreach ($mailerAtSteps[$product] as $mailerType => $mailerStepFilter) {
                if (in_array($maxCompletedStep, $mailerStepFilter)) {

                    // We need to send a mailer!
                    $mail = new Application_Core_Mail();

                    $link = str_replace('http:', 'https:', $params->homelet->domain) .
                        $params->weblead->mailer->retrieveRelativeUrl;
                    $link = str_replace(
                        array('[quoteNumber]', '[email]'),
                        array($webLeadSummary->quoteNumber, $webLeadSummary->emailAddress),
                        $link
                    );

                    $replacements = array(
                        'title' => $webLeadSummary->title,
                        'firstName' => $webLeadSummary->firstName,
                        'lastName' => $webLeadSummary->lastName,
                        'fullName' => "{$webLeadSummary->title} {$webLeadSummary->firstName} {$webLeadSummary->lastName}",
                        'quoteNumber' => $webLeadSummary->quoteNumber,
                        'link' => htmlentities($link),
                        'imageBaseUrl' => $params->weblead->mailer->imageBaseUrl,
                    );

                    $subjectLine = $params->weblead->mailer->{$mailerProduct[$product]}->$mailerType->subject;
                    foreach ($replacements as $key => $val) {
                        $subjectLine = str_replace("[{$key}]", $val, $subjectLine);
                    }

                    $replacements['pageTitle'] = $subjectLine;

                    // If this is a "Complete Quote" mailer then fetch the actual quote to get some values out of it
                    if ('completeQuote' == $mailerType) {
                        $replacements['annualPremium'] = '';
                        $replacements['monthlyPremium'] = '';
                        $replacements['expiryDate'] = '';

                        $quoteManager = new Manager_Insurance_LegacyQuote();
                        $quote = $quoteManager->getQuoteByPolicyNumber($webLeadSummary->quoteNumber);

                        if ($quote) {
                            if ('Annually' == $quote->payBy) {
                                $replacements['annualPremium'] = number_format($quote->quote, 2);
                                $replacements['monthlyPremium'] = number_format($quote->quote / 12, 2);
                            }
                            else {
                                $replacements['annualPremium'] = number_format($quote->quote * 12, 2);
                                $replacements['monthlyPremium'] = number_format($quote->quote, 2);
                            }
                            $replacements['expiryDate'] = $quote->getExpiresAt();
                        }
                    }

                    $template = $params->weblead->mailer->{$mailerProduct[$product]}->$mailerType->template;

                    $mail
                        ->setTo($webLeadSummary->emailAddress, $replacements['fullName'])
                        ->setFrom($params->weblead->mailer->fromAddress, $params->weblead->mailer->fromName)
                        ->setSubject($subjectLine)
                        ->applyTemplate($template, $replacements, true)
                    ;

                    $mail->send();

                    // Update web lead summary to mark mailer as sent
                    $webLeadSummary->isMailerSent = true;
                    $this->_webLeadSummaries->updateSummary($webLeadSummary);

                }
            }
        }
    }

    /**
     * For any particular web lead summary, find and return the last step number that a blob exists.
     *
     * @param int $webLeadSummaryId
     * @return int|null
     */
    public function getBlobMaxStep($webLeadSummaryId)
    {
        // Prepare web lead blob data source
        if (null == $this->_webLeadBlobs) {
            $this->_webLeadBlobs = new Datasource_Core_WebLeadBlobs();
        }

        return $this->_webLeadBlobs->getBlobMaxStep($webLeadSummaryId);
    }

    /**
     * Method to update the "mailer sent" status of any given web lead summary.
     *
     * @param string $quoteNumber
     * @param bool $isSent
     */
    public function setIsMailerSent($quoteNumber, $isSent = true)
    {
        // Prepare web lead summary data source
        if (null == $this->_webLeadSummaries) {
            $this->_webLeadSummaries = new Datasource_Core_WebLeadSummaries();
        }

        // Find web lead ID by quote number
        $webLeadId = $this->getSummaryId($quoteNumber);

        // Load up web lead summary
        $webLeadSummary = $this->getSummary($webLeadId);

        // Set its mailer status and save it back
        $webLeadSummary->isMailerSent = $isSent;
        $this->_webLeadSummaries->updateSummary($webLeadSummary);
    }
}