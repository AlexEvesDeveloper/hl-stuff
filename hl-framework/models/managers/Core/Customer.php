<?php

/**
* Business rules class providing customer object services.
*/
class Manager_Core_Customer extends Zend_Db_Table_Abstract {

    /**#@+
     * References to the same customer stored in the DataStore, and the LegacyDataStore.
     */
    protected $_customerModel; 
    protected $_legacyCustomerModel;
    protected $_securityAnswerModel;
    /**#@-*/

    
    public function __construct() {
        
        // Make this model use the new homelet database connection
        $this->_db = Zend_Registry::get('db_homelet');
        
        $this->_customerModel = new Datasource_Core_Customers();
        $this->_legacyCustomerModel = new Datasource_Core_LegacyCustomers();
        $this->_securityAnswerModel = new Datasource_Core_SecurityAnswer();
    }
    
    
    /**
     * This function returns an auth adapter for the login systems
     *
     * This function takes a params array (which should be login form values)
     * and creates a zend auth adapter linked to the correct database
     * and users table. If the params array has come from a login form and has
     * a username and password fields it will set them as the identity
     * and credentials in the auth adapter so that we can check to see if they
     * are valid
     *
     * @param array params
     * @return Zend_Auth_Adapter_DbTable
     *
     * @todo
     * High coupling. De-munt this.
     */
    public function getAuthAdapter(array $params)
    {
        $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Registry::get('db_homelet'));
        $authAdapter
            ->setTableName('customers')
            ->setIdentityColumn('email_address')
            ->setCredentialColumn('password')
            ->setCredentialTreatment('?');
        
        $authAdapter->setIdentity($params['email']);
        $authAdapter->setCredential($params['password']);
        
        return $authAdapter;
    }
    
    
    /**
     * This function returns an auth adapter for the login systems based on
     * customers existing in the legacy homelet db.
     *
     * This function takes a params array (which should be login form values)
     * and creates a zend auth adapter linked to the correct database
     * and users table. If the params array has come from a login form and has
     * a username and password fields it will set them as the identity
     * and credentials in the auth adapter so that we can check to see if they
     * are valid
     *
     * @param array params
     * @return Zend_Auth_Adapter_DbTable
     */
    public function getLegacyAuthAdapter(array $params) {
        
        $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Registry::get('db_legacy_homelet'));
        $authAdapter
            ->setTableName('customer')
            ->setIdentityColumn('email')
            ->setCredentialColumn('password')
            ->setCredentialTreatment('?');
        
        $authAdapter->setIdentity($params['email']);
        $authAdapter->setCredential($params['password']);
        
        return $authAdapter;
    }
    
    /**
     * Creates a new customer in both the DataStore and the LegacyDataStore,
     * and returns an object representation of this. The object will encapsulate both
     * the customer identifier, and the legacy customer identifier.
     * 
     * @param string $emailAddress
     * The customer's email address. No validation checking is performed on this,
     * other than to check for a non-empty string.
     *
     * @param integer $customerType
     * Indicates the customer type, typically a tenant, landlord or agent. Must
     * correspond to a relevant const exposed by the Model_Core_Customer
     * class: AGENT, LANDLORD or TENANT.
     *
     * @param boolean $legacyOnly
     * If this is set to true then the customer record is only created in the legacy system!
     *
     * @return Model_Core_Customer
     * Returns a Customer object encapsulating the customer details.
     *
     * @throws Zend_Exception
     * Throws a Zend_Exception if parameters are missing, or if the $customerType
     * is invalid.
     *
     * @todo
     * Email the customer to let them know an account has been created and tell
     * them there randomly generated password. Not sure if we need to get email
     * content written by compliance or marketing?
     */
    public function createNewCustomer($emailAddress, $customerType, $legacyOnly = false) {

        //Validate the data passed in.
        if (empty($emailAddress) || empty($customerType)) {
            
            throw new Zend_Exception('Required parameters missing');
        }

        switch($customerType) {
            case Model_Core_Customer::AGENT:
            case Model_Core_Customer::CUSTOMER:
                //All is well.
                break;
            default:
                throw new Zend_Exception('Invalid customer type specified.');
        }

        //Save the customer into the DataStore and LegacyDataStore. To do this
        //first obtain the email address, password, customer type (tenant, landlord,
        //agent) and an unused legacy identifier (customerRefno).
        $passwordUtil = new Application_Core_Password();
        $password = $passwordUtil->generate();

        $utils = new Application_Core_Utilities();
        while(true) {
            
            $legacyIdentifier = $utils->_generateRefno();
            if($this->_legacyCustomerModel->getConfirmCustomerExists($legacyIdentifier)) {
                
                continue;
            }
            
            //A unique identifier has been found.
            break;
        }

        //And create:
        $this->_legacyCustomerModel->insertCustomer($emailAddress, $password, $legacyIdentifier);
        if (!$legacyOnly) {
            $identifier = $this->_customerModel->insertCustomer($emailAddress, $password, $customerType);

            //Next link the LegacyDataStore and the DataStore.
            $customerMap = new Datasource_Core_CustomerMaps();
            $customerMap->insertMap($legacyIdentifier, $identifier);
        }

        //Finally, encapsulate the customer details in a Model_Insurance_Common_Customer_DomainObjects_Customer
        //object and return.
        $customer = new Model_Core_Customer();
        if (!$legacyOnly) { $customer->setIdentifier(Model_Core_Customer::IDENTIFIER, $identifier); }
        $customer->setIdentifier(Model_Core_Customer::LEGACY_IDENTIFIER, $legacyIdentifier);
        $customer->setEmailAddress($emailAddress);
        $customer->setPassword($password);

        return $customer;
    }

    /**
     * Links a legacy customer to a new customer record
     *
     * @param integer $legacyRefNo The reference number for the legacy customer
     * @param integer $newIdentifier The ID of the new customer. If this is null then a new customer
     *      record is generated first and then linked
     * @param string $newType If creating a new customer record this indicates the customer type, typically a
     *      customer or agent. Must correspond to a relevant const exposed by the
     *      Model_Insurance_Common_Customer_DomainObjects_Customer
     * @return Model_Core_Customer Returns the customer model
     */
    public function linkLegacyToNew($legacyRefNo, $newIdentifier = null, $newType = null)
    {
        if (is_null($newIdentifier)) {
        	$legacyCustomer = $this->getCustomer(Model_Core_Customer::LEGACY_IDENTIFIER, $legacyRefNo);
            
            // Double check to make sure there isn't actually already a customer entry
            // if there is we don't want to create another one!
            $newIdentifier = $legacyCustomer->getIdentifier(Model_Core_Customer::IDENTIFIER);
        }
        
        if (is_null($newIdentifier)) {
            // No new customer ID specified so we need to create a new customer
            $password = Application_Core_Password::generate();
            $customerID = $this->_customerModel->insertCustomer($legacyCustomer->getEmailAddress(), $password, $newType);
        	
            // Link the new and legacy customers together
            $customerMap = new Datasource_Core_CustomerMaps();
            $customerMap->insertMap($legacyRefNo, $customerID);
        
            $newCustomer = $this->getCustomer(Model_Core_Customer::IDENTIFIER, $customerID);
            
            // Because we've had to create a new customer we also need to update the address details and etc.. using the legacy data
            // To do this we get the legacy customer object - set the new ID and save it.
            $legacyCustomer->setIdentifier(Model_Core_Customer::IDENTIFIER, $customerID);
            $legacyCustomer->typeID = $newCustomer->typeID;
            $legacyCustomer->setPassword($newCustomer->getPassword()); // make the passwords match
            
            $this->updateLegacyCustomer($legacyCustomer);
        }
        else {
            $customerID = $newIdentifier;
            
            // Link the new and legacy customers together
            $customerMap = new Datasource_Core_CustomerMaps();
            $customerMap->insertMap($legacyRefNo, $customerID);
            
            // We also need to make the password on the legacy account match the password on the new account - or MADNESS ENSUES!!!!
            $newCustomer = $this->getCustomer(Model_Core_Customer::IDENTIFIER, $customerID);
            $legacyCustomer = $this->getCustomer(Model_Core_Customer::LEGACY_IDENTIFIER, $legacyRefNo);
            
            $legacyCustomer->setPassword($newCustomer->getPassword());
            $legacyCustomer->typeID = $newCustomer->typeID;
            $this->updateLegacyCustomer($legacyCustomer);
        }
        
        return $customerID;
    }

    /**
     * Update the legacy customer only. This is because we know we have only legacy data
     * to update and don't need to update the new customer with the same data every time
     * we link a policy together.
     *
     * @param $customer Customer model
     */
    public function updateLegacyCustomer($customer)
    {
        $this->_legacyCustomerModel->updateCustomer($customer);
    }

    /**
     * Updates an existing customer in the DataStore and the LegacyDataStore.
     *
     * @param Model_Core_Customer $customer
     * A Customer object containing all the latest details for that customer.
     *
     * @return void
     * 
     * @throws Zend_Exception
     * Throws a Zend_Exception if the customer cannot be updated in the data
     * stores.
     */
    public function updateCustomer($customer) {

        $this->_legacyCustomerModel->updateCustomer($customer);
        
        // We only need to update the NEW customer model if the legacy one is actually linked to one.
        if ($customer->getIdentifier(Model_Core_Customer::IDENTIFIER)) {
            $this->_customerModel->updateCustomer($customer);

            // Update the customers security question preferences but only if
            // they have been set. This is because legacy customer records can be used to create
            // new customer records but these records are unaware of the security question/answer
            // fields.
            if ($customer->getSecurityQuestion() != null && $customer->getSecurityAnswer() != null) {
                $this->_securityAnswerModel->updateCustomerSecurityAnswer(
                    $customer->getIdentifier(Model_Core_Customer::IDENTIFIER),
                    $customer->getSecurityQuestion(),
                    $customer->getSecurityAnswer()
                );
            }
        }
    }

    /**
     * Allows the LGSagentschemenumber field to be updated for the given reference number
     *
     * @param string $agentSchemeNumber
     * @param string $legacyIdentifier
     */
    public function updateCustomerAgentSchemeNumber($agentSchemeNumber, $legacyIdentifier)
    {
        $this->_legacyCustomerModel->updateCustomerAgentSchemeNumber($agentSchemeNumber, $legacyIdentifier);
    }

    /**
     * Retrieves and returns a customer.
     *
     * @param integer $customerIdentifierType
     * Must correspond to a relevant const exposed by the DomainObjects_Customer class
     * (LEGACY_IDENTIFIER or IDENTIFIER). Allows this method to understand how to
     * process the $customerIdentifier passed in, which is represented differently
     * in the LegacyDataStore and DataStore.
     *
     * @param mixed $customerIdentifier
     * The customer identifier.
     *
     * @return Model_Core_Customer
     * Returns the matching customer.
     *
     * @throws Zend_Exception
     * Throws a Zend_Exception if the $customerIdentifierType is invalid, or
     * if the customer cannot be found.
     */
    public function getCustomer($customerIdentifierType, $customerIdentifier)
    {
        if($customerIdentifierType == Model_Core_Customer::LEGACY_IDENTIFIER) {
            return $this->_legacyCustomerModel->getCustomer($customerIdentifier);
        }
        else if($customerIdentifierType == Model_Core_Customer::IDENTIFIER) {
            $customer = $this->_customerModel->getCustomer($customerIdentifier);
            $securityQuestionData = $this->_securityAnswerModel->getCustomerSecurityAnswer($customerIdentifier);

            if (is_array($securityQuestionData)) {
                // Set the security details into the customer object
                $customer->setSecurityQuestion($securityQuestionData[0]);
                $customer->setSecurityAnswer($securityQuestionData[1]);
            }

            // If we have a legacy record mapped to this customer, get the DoB from it and put it in the returned object
            if ($customer->getIdentifier(Model_Core_Customer::LEGACY_IDENTIFIER)) {

                $legacyCustomerRecord = $this->_legacyCustomerModel->getCustomer(
                    $customer->getIdentifier(Model_Core_Customer::LEGACY_IDENTIFIER)
                );

                $customer->setDateOfBirthAt($legacyCustomerRecord->getDateOfBirthAt());
            }

            return $customer;
        }
    }
    
    /**
     * Attempts to retrieve a customer by their email address
     *
     * @param string $emailAddress
     * Email address to search for
     * 
     * @return mixed
     * Returns either a Model_Core_Customer object or null if no customer found
     */
    public function getCustomerByEmailAddress ($emailAddress) {
        
        $customerModel = $this->_customerModel;
        $customer = $customerModel->getByEmailAddress($emailAddress);
    
        // If we've got a customer model return it - otherwise return null
        if ($customer) {
            $securityQuestionData = $this->_securityAnswerModel->getCustomerSecurityAnswer(
                $customer->getIdentifier(Model_Core_Customer::IDENTIFIER));

            if (is_array($securityQuestionData)) {
                // Set the security details into the customer object
                $customer->setSecurityQuestion($securityQuestionData[0]);
                $customer->setSecurityAnswer($securityQuestionData[1]);
            }

            // If we have a legacy record mapped to this customer, get the DoB from it and put it in the returned object
            if ($customer->getIdentifier(Model_Core_Customer::LEGACY_IDENTIFIER)) {

                $legacyCustomerRecord = $this->_legacyCustomerModel->getCustomer(
                    $customer->getIdentifier(Model_Core_Customer::LEGACY_IDENTIFIER)
                );

                $customer->setDateOfBirthAt($legacyCustomerRecord->getDateOfBirthAt());
            }

            return $customer;
        }
        
        return null;
    }
    
    /**
     * Attempts to retrieve a customer by their email address from the legacy db.
     *
     * @param string $emailAddress
     * Email address to search for
     * 
     * @return mixed
     * Returns either a Model_Core_Customer object or null if no customer found
     */
    public function getLegacyCustomerByEmailAddress($emailAddress)
    {
        $customerModel = $this->_legacyCustomerModel;
        $customer = $customerModel->getByEmailAddress($emailAddress);
    
        // If we've got a customer model return it - otherwise return null
        if ($customer) {
            return $customer;
        }
        else {
            return null;
        }
    }

    /**
     * Generates a sudo email address to uniquely identify agents but ensure common reference number is used
     *
     * @param int $agentSchemeNumber
     * @return string email address
     */
    public static function generateAgentSudoEmailAddress($agentSchemeNumber)
    {
        return sprintf('%s@agents.homelet.com', $agentSchemeNumber);
    }

    /**
     * Attempts to retrieve all customers from the old insurance database for a
     * given email address.
     *
     * @param $emailAddress Customer email address
     * @return mixed
     */
    public function getAllLegacyCustomersByEmailAddress($emailAddress)
    {
        return $this->_legacyCustomerModel->getAllByEmailAddress($emailAddress);
    }
    
    /**
     * Removes the $customer from the customer tables in the DataStore and the
     * LegacyData store. Will not remove any other records associated with the customer.
     *
     * @param Model_Core_Customer $customer
     * The customer to remove.
     *
     * @return void
     */
    public function removeCustomer($customer) {
        
        $this->_customerModel->removeCustomer($customer);
        $this->_legacyCustomerModel->removeCustomer($customer);
    }
    
    
    /**
     * Not implemented in this release.
     */
    public function getIsNoteAlreadyStored($customerNote) {
        
        throw new Zend_Exception(get_class() . __FUNCTION__ . ": not yet implemented.");       
    }
    
    
    /**
     * Not implemented in this release.
     */
    public function insertNote($customerNote) {
        
        throw new Zend_Exception(get_class() . __FUNCTION__ . ": not yet implemented.");        
    }
    
    public function createCustomerFromLegacy($emailAddress, $legacyIdentifier) {

        //Validate the data passed in.
        if (empty($emailAddress) || empty($legacyIdentifier)) {
            
            throw new Zend_Exception('Required parameters missing');
        }

        //Save the customer into the DataStore and LegacyDataStore. To do this
        //first obtain the email address, password, customer type (tenant, landlord,
        //agent) and an unused legacy identifier (customerRefno).
        $passwordUtil = new Application_Core_Password();
        $password = $passwordUtil->generate();
        
        
        //And create:
            $identifier = $this->_customerModel->insertCustomer($emailAddress, $password, Model_Core_Customer::CUSTOMER);
            
            //Next link the LegacyDataStore and the DataStore.
            $customerMap = new Datasource_Core_CustomerMaps();
            $customerMap->insertMap($legacyIdentifier, $identifier);
        
        //Finally, encapsulate the customer details in a Model_Insurance_Common_Customer_DomainObjects_Customer
        //object and return.
        $customer = new Model_Core_Customer();
        $customer->setIdentifier(Model_Core_Customer::IDENTIFIER, $identifier); 
        $customer->setIdentifier(Model_Core_Customer::LEGACY_IDENTIFIER, $legacyIdentifier);
        $customer->setEmailAddress($emailAddress);
        $customer->setPassword($password);

        return $customer;
    }
    
    public function updateCustomerByLegacy($customer,$identifier)
    {
        $this->_customerModel->updateCustomerByLegacy($customer,$identifier); 
    }
}
    
?>
