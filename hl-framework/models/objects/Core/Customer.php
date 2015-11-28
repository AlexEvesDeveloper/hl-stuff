<?php

/**
 * Represents a customer in the system.
 */
class Model_Core_Customer extends Model_Abstract {

	/**#@+
     * Constants that should be used to specify the customer type.
     */
    const AGENT = 'agent';
    const CUSTOMER = 'customer';
    /**#@-*/

	
	/**#@+
     * Constants that should be used to specify the identifier used when retrieving
     * a customer object.
     */
	const IDENTIFIER = 1;
    const LEGACY_IDENTIFIER = 2;
    /**#@-*/
    
	
	/**@#+
	 * Constants that should be used to specify the address line number to
	 * retrieve in the relevant get().
	 */
	const ADDRESSLINE1 = 1;
	const ADDRESSLINE2 = 2;
	const ADDRESSLINE3 = 3;
	/**@#-*/
	
	
	/**@#+
	 * Constants that should be used to specify which telephone number to retrieve
	 * in the relevant get().
	 */
	const TELEPHONE1 = 1;
	const TELEPHONE2 = 2;
	/**@#-*/

    /**@#+
     * Constants for default validation e-mail strings.
     */
    const VALIDATION_SUBJECT = 'My HomeLet account validation';
    const VALIDATION_HEADING = 'Validating your My HomeLet account';
    const VALIDATION_TEMPLATE = 'core/account-validation';
    const VALIDATION_TEMPLATETXT = 'core/account-validationtxt';
    const VALIDATION_TEMPLATEID = 'HL2442 12-12';
    /**@#-*/

	
	/**#@+
	 * Customer attributes.
	 */
	protected $_id;
	protected $_legacyId;
	protected $_title;
	protected $_firstName;
	protected $_lastName;
    protected $_landlordName;
	
	protected $_addressLine1;
	protected $_addressLine2;
	protected $_addressLine3;
	protected $_postCode;
	protected $_country;
	protected $_isForeignAddress;
	
	protected $_telephone1;
	protected $_telephone2;
    protected $_fax;
	protected $_emailAddress;
	protected $_password;

    protected $_securityquestion = null;
    protected $_securityanswer = null;
	
	protected $_occupation;

    protected $_accountLoadComplete = null;
    protected $_emailValidated = null;

    /**
     * @var null|string Represents a date of birth or NULL
     */
    protected $_date_of_birth_at = null;
	/**#@-*/
	
	public $typeID;
	
	/**
	 * Gets the customer identifier.
	 *
	 * @param integer $customerIdentifierType
     * Must correspond to a relevant const exposed by the this class: IDENTIFIER or
     * LEGACY_IDENTIFIER. Allows this method to understand which customer
     * identifier to return, either the LegacyDataStore identifier, or the
     * DataStore identifier.
     *
     * @return mixed
     * Returns the customer identifier, which is either an integer for the
     * DataStore, or a string for the LegacyDataStore.
     *
     * @throws Zend_Exception
     * Throws a Zend_Exception if the customer type is invalid.
	 */
	public function getIdentifier($customerIdentifierType) {
		
		if($customerIdentifierType == self::LEGACY_IDENTIFIER) {
			
			$returnVal = $this->_legacyId;
		}
		else if($customerIdentifierType == self::IDENTIFIER) {

			$returnVal = $this->_id;
		}
		else {
			
			throw new Zend_Exception('Invalid customer identifier type.');
		}
		
		return $returnVal;
	}

	
	/**
	 * Gets the customer title.
	 *
	 * @return string
	 * The customers title.
	 */
	public function getTitle() {
		
		return $this->_title;
	}
	
	
	/**
	 * Gets the customer's first name.
	 *
	 * @return string
	 * The customer's first name.
	 */
	public function getFirstName() {
		
		return $this->_firstName;
	}
	
	
	/**
	 * Gets the customer's last name.
	 *
	 * @return string
	 * The customer's last name.
	 */
	public function getLastName() {
		
		return $this->_lastName;
	}
	
	
	/**
	 * Provided to support the rotting legacy data structure.
	 *
	 * @return string
	 * Returns the landlord name. Don't ask me how this differs from the customer firstname and lastname.
	 */
    public function getLandlordName() {
    
        return $this->_landlordName;
    }
	
	
	/**
	 * Gets a line of the customer's address.
	 *
	 * @param $addressLineNumber
	 * Must correspond to a const exposed by this class: ADDRESSLINE1, ADDRESSLINE2,
	 * or ADDRESSLINE3. This will determine which line of the address is returned.
	 *
	 * @return string
	 * A line of the customers address.
	 *
     * @throws Zend_Exception
     * Throws a Zend_Exception if the $addressLineNumber is invalid.
	 */
	public function getAddressLine($addressLineNumber) {
		
		if($addressLineNumber == self::ADDRESSLINE1) {
			
			$returnVal = $this->_addressLine1;
		}
		else if($addressLineNumber == self::ADDRESSLINE2) {

			$returnVal = $this->_addressLine2;
		}
		else if($addressLineNumber == self::ADDRESSLINE3) {

			$returnVal = $this->_addressLine3;
		}
		else {
			
			throw new Zend_Exception('Invalid address line number.');
		}
		
		return $returnVal;
	}
	
	
	/**
	 * Gets the customer's postcode.
	 *
	 * @return string
	 * The customer's postcode.
	 */
	public function getPostCode() {
		
		return $this->_postCode;
	}
	
	
	/**
	 * Gets the customer's country.
	 *
	 * @return string
	 * The customer's country.
	 */
	public function getCountry() {
		
		return $this->_country;
	}
	
	
	/**
	 * Returns whether the customer's address is overseas or not.
	 *
	 * @return boolean
	 * True if the customer's address is overseas, false otherwise.
	 */
	public function getIsForeignAddress() {
		
		return $this->_isForeignAddress;
	}
	
	
	/**
	 * Gets one of the customer's phone numbers.
	 * 
	 * @param $which
	 * Must correspond to a const exposed by this class: TELEPHONE1 or TELEPHONE2.
	 * This will determine which phone numnber is returned.
	 *
	 * @return string
	 * One of the customer's phone numbers.
	 *
     * @throws Zend_Exception
     * Throws a Zend_Exception if $which is invalid.
	 */
	public function getTelephone($which) {
		
		if($which == self::TELEPHONE1) {
			
			$returnVal = $this->_telephone1;
		}
		else if($which == self::TELEPHONE2) {

			$returnVal = $this->_telephone2;
		}
		else {
			
			throw new Zend_Exception('Invalid telephone number requested.');
		}
		
		return $returnVal;
	}
	
	
	/**
	 * Returns thre fax number.
	 *
	 * @return mixed
	 * The fax number as a string, or null if no fax number set.
	 */
    public function getFax() {
        
        return $this->_fax;
    }
	
	
	/**
	 * Gets the customer's email address.
	 *
	 * @return string
	 * The customer's email address.
	 */
	public function getEmailAddress() {
		
		return $this->_emailAddress;
	}
	
	
	/**
	 * Gets the customer's password.
	 *
	 * @return string
	 * The customer's password.
	 */
	public function getPassword() {
		
		return $this->_password;
	}

    /**
     * Get the customers security question option
     */
    public function getSecurityQuestion() {
        return $this->_securityquestion;
    }

    /**
     * Set the customers security answer for their chosen option
     */
    public function getSecurityAnswer() {
        return $this->_securityanswer;
    }


    /**
	 * Gets the customer's occupation.
	 *
	 * @return string
	 * The customer's occupation.
	 */
	public function getOccupation() {
		
		return $this->_occupation;
	}

    /**
     * Return the validated status of the account
     *
     * @return bool|null Account validated status
     */
    public function getEmailValidated() {
        return $this->_emailValidated;
    }
	
	/**
	 * Sets the customer identifier.
	 *
	 * @param integer $customerIdentifierType
     * Must correspond to a relevant const exposed by the this class: IDENTIFIER or
     * LEGACY_IDENTIFIER. This will allows the method to understand which
     * customer identifier to set, either the LegacyDataStore identifier, or the
     * DataStore identifier.
     *
     * @param mixed $identifier
     * The customer identifier.
     *
     * @throws Zend_Exception
     * Throws a Zend_Exception if the $customerIdentifierType is invalid.
	 */
	public function setIdentifier($customerIdentifierType, $identifier) {
		
		if($customerIdentifierType == self::LEGACY_IDENTIFIER) {
			
			$this->_legacyId = $identifier;
		}
		else if($customerIdentifierType == self::IDENTIFIER) {

			$this->_id = $identifier;
		}
		else {
			
			throw new Zend_Exception('Invalid customer identifier type.');
		}
	}
	
	
	/**
	 * Sets the customer's title.
	 *
	 * @param string $title The customers title.
	 * @return void
	 */
	public function setTitle($title) {
		
		$this->_title = $title;
	}
	
	
	/**
	 * Sets the customer's first name.
	 *
	 * @param string $firstName The customers first name.
	 * @return void
	 */
	public function setFirstName($firstName) {
		
		$this->_firstName = $firstName;
	}
	
	
	/**
	 * Sets the customer's last name.
	 *
	 * @param string $lastName The customers last name.
	 * @return void
	 */
	public function setLastName($lastName) {
		
		$this->_lastName = $lastName;
	}
	
	
	/**
	 * Provided to support the rotting legacy data structures.
	 *
	 * @param string $landlordName The landlord name. Don't ask me how this differs
     *      from the customer first name and last name.
	 * @return void
	 */
    public function setLandlordName($landlordName) {
    
        $this->_landlordName = $landlordName;
    }


    /**
     * Sets a line of the customer's address.
     *
     * @param integer $addressLineNumber Must correspond to one of the consts exposed by this class:
     *      ADDRESSLINE1, ADDRESSLINE2 or ADDRESSLINE3.
     * @param string $addressLine The line of the address.
     * @return void
     * @throws Zend_Exception
     */
	public function setAddressLine($addressLineNumber, $addressLine) {
		
		if($addressLineNumber == self::ADDRESSLINE1) {
			
			$this->_addressLine1 = $addressLine;
		}
		else if($addressLineNumber == self::ADDRESSLINE2) {

			$this->_addressLine2 = $addressLine;
		}
		else if($addressLineNumber == self::ADDRESSLINE3) {

			$this->_addressLine3 = $addressLine;
		}
		else {
			
			throw new Zend_Exception('Invalid address line number.');
		}
	}
	
	
	/**
	 * Sets the customer's postcode.
	 *
	 * @param string $postCode
	 * The customers postcode.
	 *
	 * @return void
	 */
	public function setPostCode($postCode) {
		
		$this->_postCode = $postCode;
	}
	
	
	/**
	 * Sets the customer's country.
	 *
	 * @param string $country
	 * The customers country.
	 *
	 * @return void
	 */
	public function setCountry($country) {
		
		$this->_country = $country;
	}
	
	
	/**
	 * Sets whether the customer's address is an overseas address.
	 *
	 * @param boolean $isForeignAddress
	 * True if the customer's address is overseas, false otherwise.
	 *
	 * @return void
	 */
	public function setIsForeignAddress($isForeignAddress) {
		
		if(!is_bool($isForeignAddress)) {
			
			return new Zend_Exception('Invalid type passed in.');
		}
		$this->_isForeignAddress = $isForeignAddress;
	}
	
	
	/**
	 * Sets one of the customer's telephone numbers.
	 *
	 * @param integer $which Must correspond to one of the consts exposed by this class: TELEPHONE1 or TELEPHONE2.
	 * @param string $number The telephone number.
	 *
	 * @return void
     * @throws Zend_Exception
	 */
	public function setTelephone($which, $number) {
		
		if($which == self::TELEPHONE1) {
			
			$this->_telephone1 = $number;
		}
		else if($which == self::TELEPHONE2) {

			$this->_telephone2 = $number;
		}
		else {
			
			throw new Zend_Exception('Invalid telephone number requested.');
		}
	}
	
	
	/**
	 * Sets the fax number.
	 *
	 * @param string $fax
	 * The fax number.
	 *
	 * @return void
	 */
    public function setFax($fax) {
    
        $this->_fax = $fax;
    }
	
	
	/**
	 * Sets the customer's email address.
	 *
	 * @param string $emailAddress
	 * The customer's email address.
	 *
	 * @return void
	 */
	public function setEmailAddress($emailAddress) {
		
		$this->_emailAddress = $emailAddress;
	}
	
	
	/**
	 * Sets the customer's password.
	 *
	 * @param string $password The customer's password.
	 * @return void
	 */
	public function setPassword($password) {
		$this->_password = $password;
	}

    /**
     * Set the customers security question option
     *
     * @param $securityquestion Security question id
     */
    public function setSecurityQuestion($securityquestion) {
        $this->_securityquestion = $securityquestion;
    }

    /**
     * Set the customers security answer for their chosen option
     *
     * @param $securityanswer Security question answer
     */
    public function setSecurityAnswer($securityanswer) {
        $this->_securityanswer = $securityanswer;
    }

    /**
     * Sends a single email to a customer to provide a validation URL for activating a registered My HomeLet account.
     *
     * @param string $subject
     * @param string $heading
     * @param string $template
     * @param string $templateTxt
     * @param string $templateId
     * @return void
     */
    public function sendAccountValidationEmail(
        $subject = self::VALIDATION_SUBJECT,
        $heading = self::VALIDATION_HEADING,
        $template = self::VALIDATION_TEMPLATE,
        $templateTxt = self::VALIDATION_TEMPLATETXT,
        $templateId = self::VALIDATION_TEMPLATEID
    )
    {
        $params = Zend_Registry::get('params');

        // Create sign-up completion email
        $mail = new Application_Core_Mail();
        $mail->setTo($this->getEmailAddress(), null);
        $mail->setFrom('hello@homelet.co.uk', 'HomeLet');
        $mail->setSubject($subject);

        //  Generate activation link
        $mac = new Application_Core_Security($params->myhomelet->activation_mac_secret, false);
        $digest = $mac->generate(array('email' => $this->getEmailAddress()));

        $activationLink = sprintf('email=%s&mac=%s', urlencode($this->getEmailAddress()), $digest);

        // Apply template
        $mail->applyTemplate($template,
            array(
                'activationLink' => $activationLink,
                'homeletWebsite' => $params->homelet->domain,
                'firstname'      => $this->getFirstName(),
                'templateId'     => $templateId,
                'heading'        => $heading,
                'imageBaseUrl' => $params->weblead->mailer->imageBaseUrl,
            ),
            false,
            '/email-branding/homelet/portal-footer.phtml',
            '/email-branding/homelet/portal-header.phtml');

        $mail->applyTextTemplate($templateTxt,
            array('activationLink' => $activationLink,
                'homeletWebsite' => $params->homelet->domain,
                'firstname'      => $this->getFirstName(),
                'templateId'     => $templateId,
                'heading'        => $heading),
            false,
            '/email-branding/homelet/portal-footer-txt.phtml',
            '/email-branding/homelet/portal-header-txt.phtml');

        // Send email
        $mail->send();
    }

	/**
	 * Resets the customer's password
	 *
	 * @return void
	 */
	public function resetpassword() {
		$this->_password = Application_Core_Password::generate();
        $params = Zend_Registry::get('params');

		// Email the customer with the new password
		$metaData = array(
			'name'		        => $this->getFirstName(),
			'email'		        => $this->getEmailAddress(),
			'password'	        => $this->_password,
            'homeletWebsite'    => $params->homelet->domain,
            'templateId'        => 'HL2485 12-12',
            'heading'           => 'We’ve reset your password for you',
            'imageBaseUrl' => $params->weblead->mailer->imageBaseUrl,
		);
		
		$emailer = new Application_Core_Mail();
		$emailer->setTo($this->getEmailAddress(), $this->getFirstName() . ' ' . $this->getLastName())
				->setSubject('HomeLet - Your Password')
				->applyTemplate('core_resetpassword',
                    $metaData,
                    false,
                    '/email-branding/homelet/portal-footer.phtml',
                    '/email-branding/homelet/portal-header.phtml');
		$emailer->send();
	}

	
	/**
	 * Sets the customer's occupation.
	 *
	 * @param string $occupation
	 * The customer's occupation.
	 *
	 * @return void
	 */
	public function setOccupation($occupation)
    {
		$this->_occupation = $occupation;
	}

    /**
     * Set the customer account validation flag
     *
     * @param bool $emailValidated Email validated flag
     */
    public function setEmailValidated($emailValidated)
    {
        $this->_emailValidated = $emailValidated;
    }

    /**
     * Set the account load complete flag
     *
     * @param $accountLoadComplete
     */
    public function setAccountLoadComplete($accountLoadComplete)
    {
        $this->_accountLoadComplete = $accountLoadComplete;
    }

    /**
     * Get the account load complete flag
     *
     * @return int
     */
    public function getAccountLoadComplete()
    {
        return $this->_accountLoadComplete;
    }

    /**
     * Get the date of birth
     *
     * @return null|string
     */
    public function getDateOfBirthAt()
    {
        return $this->_date_of_birth_at;
    }

    /**
     * Set the date of birth, must be NULL or be SQL DATE format (yyyy-mm-dd)
     *
     * @param null|string $dateOfBirth
     */
    public function setDateOfBirthAt($dateOfBirth)
    {
        $this->_date_of_birth_at = $dateOfBirth;
    }

	
	
	/**
	 * Identifies if this customer is the same as the customer passed in.
	 *
	 * This method will identify a customer as equal to another customer if the
	 * datas are the same.
	 *
	 * @param Model_Core_Customer $otherCustomer
	 * The customer to compare this customer against.
	 *
	 * @return boolean
	 * Returns true if the customers are equal, false otherwise.
	 *
	 * @todo
	 * This method is incomplete.
	 */
	public function equals($otherCustomer) {
		if($this->_id != $otherCustomer->getIdentifier(self::IDENTIFIER)) {
			return false;
		}

		if($this->_legacyId != $otherCustomer->getIdentifier(self::LEGACY_IDENTIFIER)) {
			return false;
		}

		if(strcasecmp($this->_title, $otherCustomer->getTitle()) != 0) {
			return false;
		}

		if(strcasecmp($this->_title, $otherCustomer->getFirstName()) != 0) {
			return false;
		}

		if(strcasecmp($this->_lastName, $otherCustomer->getLastname()) != 0) {
			return false;
		}

		if(strcasecmp($this->_addressLine1, $otherCustomer->getAddressLine(self::ADDRESSLINE1)) != 0) {
			return false;
		}

		if(strcasecmp($this->_addressLine2, $otherCustomer->getAddressLine(self::ADDRESSLINE2)) != 0) {
			return false;
		}

		if(strcasecmp($this->_addressLine3, $otherCustomer->getAddressLine(self::ADDRESSLINE3)) != 0) {
			return false;
		}

		if(strcasecmp($this->_postCode, $otherCustomer->getPostCode()) != 0) {
			return false;
		}

		if(strcasecmp($this->_country, $otherCustomer->getCountry()) != 0) {
			return false;
		}

		if($this->_isForeignAddress != $otherCustomer->getIsForeignAddress()) {
			return false;
		}

		if($this->_telephone1 != $otherCustomer->getTelephone(self::TELEPHONE1)) {
			return false;
		}

		if($this->_telephone2 != $otherCustomer->getTelephone(self::TELEPHONE2)) {
			return false;
		}

        if($this->_fax != $otherCustomer->getFax()) {
			return false;
		}

		if(strcasecmp($this->_emailAddress, $otherCustomer->getEmailAddress()) != 0) {
			return false;
		}

		if($this->_password != $otherCustomer->getPassword()) {
			return false;
		}

        if ($this->_securityquestion != $otherCustomer->getSecurityQuestion()) {
            return false;
        }

        if ($this->_securityanswer != $otherCustomer->getSecurityAnswer()) {
            return false;
        }

		if(strcasecmp($this->_occupation, $otherCustomer->getOccupation()) != 0) {
			return false;
		}

        if ($this->_accountLoadComplete != $otherCustomer->getAccountLoadComplete()) {
            return false;
        }

        if ($this->_emailValidated != $otherCustomer->getEmailValidated()) {
            return false;
        }
		
		return true;
	}
}
