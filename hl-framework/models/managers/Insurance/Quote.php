<?php

/**
 * Class Manager_Insurance_Quote
 */
class Manager_Insurance_Quote
{
    /**
     * Letter type for sending a quote
     */
    const LETTER_TYPE_QUOTE = 'sendquote';

    /**
     * Letter type for sending an inception letter
     */
    const LETTER_TYPE_INCEPTION = 'inception';

    /**
     * Letter type for sending a renewal letter
     */
    const LETTER_TYPE_RENEWAL = 'renewal';

    /**
     * Letter type for sending a mid-term adjustment letter
     */
    const LETTER_TYPE_MTA = 'mta';

    /**
     * Letter type for sending a cancellation letter
     */
    const LETTER_TYPE_CANCELLATION = 'cancellation';

    /**
     * For when the method for sending the letter is email
     */
    const LETTER_METHOD_EMAIL = 'email';

    /**
     * For when the method for sending the letter is post
     */
    const LETTER_METHOD_POST = 'post';

    /**
     * For when the method for sending the letter is both post and email
     */
    const LETTER_METHOD_BOTH = 'both';

    /**
     * @var Model_Insurance_Quote
     */
    protected $_quoteModel;

    /**
     * @var Datasource_Insurance_Quotes
     */
    protected $_quoteDataSource;

    /**
     * Generic Quote Manager
     *
     * @param null $id
     * @param null $policyNumber
     * @param null $legacyCustomerReference
     * @param null $customerId
     * @throws Exception
     * @internal param \id $integer ID of an existing quote you want to open. If none provided a new quote is created
     */
	public function __construct($id = null, $policyNumber = null, $legacyCustomerReference = null, $customerId = null)
    {
		$this->_quoteDataSource = new Datasource_Insurance_Quotes();

		if (!is_null($id)) {
			// A quote ID has been passed in so we can load the quote from the quotes table
			$this->_quoteModel = $this->_quoteDataSource->getByID($id);
		}
        else {
			if (is_null($policyNumber) && is_null($legacyCustomerReference)) {
				// Create a new quote by saving an empty model - neat or dirty? you decide ;)
				$this->_quoteModel = $this->_quoteDataSource->save(new Model_Insurance_Quote());
				$this->_quoteModel->issuedDate = date('Y-m-d');
			}
            elseif (!is_null($policyNumber)) {
				// We've got a policy number instead of an ID - so let's use it
				$id = $this->_quoteDataSource->getIDByLegacyID($policyNumber);

                if (!$id) {
                    // Migrate the data
                    $this->legacyQuoteMigration($policyNumber, $customerId);
                }
                else {
                    $this->_quoteModel = $this->_quoteDataSource->getByID($id);
                }
			}
            elseif (!is_null($legacyCustomerReference)) {
				// We've got a legacy customer reference (yuck) - so use that
				$id = $this->_quoteDataSource->getIDByReferenceNumber($legacyCustomerReference);
				if (is_null($id)) throw new Exception("Quote not found");
				$this->_quoteModel = $this->_quoteDataSource->getByID($id);
			}
		}
	}

    /**
     * Performs a copy of the quote stored within the MYSQL4 db to MYSQL5.
     */
    protected function legacyQuoteMigration($policyNumber, $customerId)
    {
        // Do nothing, implement this method in sub classes.
    }

	/**
	 * Count how many products are on this quote
	 *
	 * @return int Count of products
	 */
	public function productCount()
    {
		$quoteProducts = new Datasource_Insurance_Quote_Products();
		$productCount = $quoteProducts->getCountByQuoteID($this->_quoteModel->ID);

		// Return product count
		return $productCount;
	}

    /**
     * This function lets you check to see if the current quote has a particular product in it
     *
     * @param int productID A valid product ID. Use the constants in this manager!
     * @throws Exception
     * @return boolean True or false depending on whether the product is in this quote
     *
     * Example: $quoteHasBuildingsCover = $quoteManager->hasProduct(Manager_Insurance_LandlordsPlus_Quote::BUILDING_COVER);
     */
	public function hasProduct($productID)
    {
		if (is_null($productID) || $productID == 0) {
			throw new Exception('Invalid product ID');
		}

		// First we need to get an array of product ID's that this quote contains
		$quoteProducts = new Datasource_Insurance_Quote_Products();
		$productArray = $quoteProducts->getProductsByQuoteID($this->_quoteModel->ID);

		// Search for needle in haystack and return true/false
		return (in_array($productID, $productArray));
	}

	/**
	 * Remove a product from the quote and clean up any meta data
	 *
	 * @param int productID ID of the product you want to remove
	 * @return boolean True or False depending on success
	 *
	 * Example: $result = $quoteManager->removeProduct(Manager_Insurance_LandlordsPlus_Quote::BUILDING_COVER);
	 */
	public function removeProduct($productID)
    {
		$quoteProducts = new Datasource_Insurance_Quote_Products();
		return $quoteProducts->remove($this->_quoteModel->ID, $productID);
	}

    /**
     * Returns an array of meta data for a specific product
     *
     * @param $productID
     * @return array Associative array of meta data
     */
	public function getProductMeta($productID)
    {
		$quoteProductMetas = new Datasource_Insurance_Quote_Product_Metas();
		return $quoteProductMetas->getByProductID($this->_quoteModel->ID, $productID);
	}

	/**
	 * Adds a property to the quote
	 *
	 * @param string postcode Postcode of the property
	 * @param string town
	 * @param string county
	 * @param string line1 First line of address
	 * @param int tenantTypeID Valid tenant type ID
	 * @param boolean agentManaged Is the property managed by an agent?
	 * @param int ownershipLengthID Valid ID for an ownership length
	 * @param int noClaimsYearsID Valid ID for a number of no claims years discount
	 * @param boolean excludeFloodCover Do you want to exclude flood cover from this property?
	 * @param string line2 Second line of address (optional)
	 * @param string line3 Third line of address (optional)
	 * @param string country Country (optional)
	 *
	 * Note: this is Landlords Insurance + so only one property is allowed per quote
	 */
	public function addProperty($postcode, $town, $county, $line1, $tenantTypeID, $agentManaged, 
								$ownershipLengthID, $noClaimsYearsID, $excludeFloodCover, 
								$line2 = null, $line3 = null, $country = null)
    {
		$quoteProperties = new Datasource_Insurance_Quote_Properties();

		if ($this->propertyCount() > 0) {
			// You can only have one property on an LI+ quote - so we remove the existing one before adding the new one
			$quoteProperties->removeAllByQuoteID($this->_quoteModel->ID);
		}

		return $quoteProperties->add($this->_quoteModel->ID, $postcode, $town, $county, $line1, $tenantTypeID, $agentManaged, $ownershipLengthID, $noClaimsYearsID, $excludeFloodCover, $line2, $line3, $country);
		$this->save();
	}

	/**
	 * Returns the number of properties attached to this quote (useful for Portfolio!)
	 *
	 * @return int properyCount
	 */
	public function propertyCount()
    {
		$quoteProperties = new Datasource_Insurance_Quote_Properties();
		return $quoteProperties->getCountByQuoteID($this->_quoteModel->ID);
	}

	/**
	 * Returns an array of properties attached to this quote
	 *
	 * @return array properties
	 */
	public function getProperties()
    {
		$quoteProperties = new Datasource_Insurance_Quote_Properties();
		return $quoteProperties->getByQuoteID($this->_quoteModel->ID);
	}

	/**
	 * Dirty function to return the whole quoteModel - useful for debugging
	 *
	 * @todo Remove this when system is stable
	 */
	public function getModel()
    {
		$this->_quoteModel->properties = $this->getProperties();
		return $this->_quoteModel;
	}

	/**
	 * Delete the quote
	 *
	 */
	public function delete()
    {
		$this->_quoteDataSource->deleteByID($this->_quoteModel->ID);
	}

	/**
	 * Save the quote
	 *
	 */
	public function save()
    {
		$this->_quoteDataSource->save($this->_quoteModel);
	}

	/**
     * Sets the policy term in the policyTerm table.
     */
    public function setPolicyTerm()
    {
        $policyTermDatasource = new Datasource_Insurance_Policy_Term();
       return $policyTermDatasource->insertPolicyTerm($this->_quoteModel);
    }

	public function setPropertyPostcode($postcode) { $this->_quoteModel->propertyPostcode = $postcode;}
    public function getPropertyPostcode() { return $this->_quoteModel->propertyPostcode; }

   	public function setStartDate($date) { $this->_quoteModel->startDate = $date; }
	public function getStartDate() { return $this->_quoteModel->startDate; }

	public function setEndDate($date) { $this->_quoteModel->endDate = $date; }
	public function getEndDate() { return $this->_quoteModel->endDate; }

	public function getID() { return $this->_quoteModel->ID; }
	public function getLegacyID() { return $this->_quoteModel->legacyID; }

	public function setLegacyCustomerReference($refNo) { $this->_quoteModel->legacyCustomerID = $refNo; }
	public function getLegacyCustomerReference() { return $this->_quoteModel->legacyCustomerID; }

	public function setAgentSchemeNumber($schemeNumber) { $this->_quoteModel->agentSchemeNumber = $schemeNumber; }
	public function getAgentSchemeNumber() { return $this->_quoteModel->agentSchemeNumber; }

	public function setPayFrequency($payFrequency) { $this->_quoteModel->payFrequency = $payFrequency; }
	public function getPayFrequency() { return $this->_quoteModel->payFrequency; }

	public function setPayMethod($payMethod) { $this->_quoteModel->payBy = $payMethod; }
	public function getPayMethod() { return $this->_quoteModel->payBy; }

	public function getPolicyNumber() { return $this->getLegacyID(); }

    public function getPolicyQuote()
    {
    	$quoteDatasource = new Datasource_Insurance_LegacyQuotes();
        $quote = $quoteDatasource->getByPolicyNumber($this->_quoteModel->legacyID);

    	return $quote->quote;
    }

    public function getPolicyName()
    {
    	$quoteDatasource = new Datasource_Insurance_LegacyQuotes();
        $quote = $quoteDatasource->getByPolicyNumber($this->_quoteModel->legacyID);

        return $quote->policyName;
    }

	// Because we(I) did not make shcedule abstract enough I need a a function to do somthing
	public function setPayBy($payMethod) { $this->_quoteModel->payBy = $payMethod; }
	public function setStatus($status) { $this->_quoteModel->status = $status; }
	public function getPayBy() { return $this->_quoteModel->payFrequency; }

	public function isValidValue($value)
    {
		if (is_null($value) || $value <= 0) {
			return false;
		}
		else {
			return true;
		}
	}

    /**
     * Send the quote documents
     *
     * @param string $policyNumber
     * @param string|null $method default null - one of LETTER_METHOD_* constants
     * @param string|null $email default null
     * @param string|null $csuid default null
     * @return bool
     */
    public static function sendQuote($policyNumber, $method=null, $email=null, $csuid=null)
    {
        return self::sendLetter($policyNumber, $method, self::LETTER_TYPE_QUOTE, $email, $csuid);
    }

    /**
     * Send the Inception documents
     *
     * @param string $policyNumber
     * @param string|null $method default null - one of LETTER_METHOD_* constants
     * @param string|null $email default null
     * @param string|null $csuid default null
     * @return bool
     */
    public static function sendInception($policyNumber, $method=null, $email=null, $csuid=null)
    {
        return self::sendLetter($policyNumber, $method, self::LETTER_TYPE_INCEPTION, $email, $csuid);
    }

    /**
     * Send the renewal documents
     *
     * @param string $policyNumber
     * @param string|null $method default null - one of LETTER_METHOD_* constants
     * @param string|null $email default null
     * @param string|null $csuid default null
     * @return bool
     */
    public static function sendRenewal($policyNumber, $method=null, $email=null, $csuid=null)
    {
        return self::sendLetter($policyNumber, $method, self::LETTER_TYPE_RENEWAL, $email, $csuid);
    }

    /**
     * Send the MTA documents
     *
     * @param string $policyNumber
     * @param string|null $method default null - one of LETTER_METHOD_* constants
     * @param string|null $email default null
     * @param string|null $csuid default null
     * @param array $extraData Array of extra data
     * @return bool
     */
    public static function sendMta($policyNumber, $method=null, $email=null, $csuid=null, $extraData = array())
    {
        return self::sendLetter($policyNumber, $method, self::LETTER_TYPE_MTA, $email, $csuid, $extraData);
    }

    /**
     * Send the cancellation documents
     *
     * @param string $policyNumber
     * @param string|null $method default null - one of LETTER_METHOD_* constants
     * @param string|null $email default null
     * @param string|null $csuid default null
     * @param array $extraData
     * @return bool
     */
    public static function sendCancellation($policyNumber, $method=null, $email=null, $csuid=null, $extraData = array())
    {
        return self::sendLetter($policyNumber, $method, self::LETTER_TYPE_CANCELLATION, $email, $csuid, $extraData);
    }

    /**
     * Sends the documents via the testletter.pl
     *
     * @param string $policyNumber
     * @param string $method 'email', 'post' or 'both'
     * @param string $letterType
     * @param string|null $email default null
     * @param string|null $csuid default null
     * @param array $extraData Extra data
     * @return bool
     */
    private static function sendLetter($policyNumber, $method, $letterType, $email, $csuid, $extraData = array())
    {
        $params = Zend_Registry::get('params');
        $homeletServer = $params->homelet->get('legacyDomain');
        $remoteHost = $homeletServer;
        $serviceName = $params->homelet->get('sendQuoteService');
        $letterSendingScriptPath = $remoteHost . '/'. $serviceName;

        $getString = '?policynumber=' . $policyNumber;
        $getString .= '&lettertype=' . $letterType;
        if ($method === null) {
            $getString .= '&method=' . self::LETTER_METHOD_EMAIL;
        }
        else {
            $getString .= '&method=' . $method;
        }
        if ($email !== null) {
            $getString .= '&email=' . $email;
        }
        if ($csuid !== null) {
            $getString .= '&csuid=' . $csuid;
        }

        foreach ($extraData as $key => $value) {
            $getString .= sprintf('&%s=%s', urlencode($key), urlencode($value));
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $letterSendingScriptPath . $getString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($ch);
        curl_close($ch);

        //Return true to indicate success.
        return true;
    }
}
