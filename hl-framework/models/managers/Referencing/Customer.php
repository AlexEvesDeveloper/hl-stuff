<?php

/**
* Business rules class providing PLL customer object services.
* 
* @todo
* Merge this manager and the CustomerMap manager.
*/
class Manager_Referencing_Customer extends Zend_Db_Table_Abstract
{
    protected $_customerModel;
    
    public function __construct()
    {
        // Make this model use the new homelet database connection
        $this->_db = Zend_Registry::get('db_homelet');
        $this->_customerModel = new Datasource_Core_Customers();
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
     * Creates a new customer in the LegacyDataStore and returns an object representation of this.
     * 
     * @param string $emailAddress The customer's email address. No validation checking is performed on this,
     *          other than to check for a non-empty string.
     * @return Model_Core_Customer Returns a Customer object encapsulating the customer details.
     * @throws Zend_Exception Throws a Zend_Exception if parameters are missing.
     */
    public function createNewCustomer($emailAddress)
    {
        // Validate the data passed in.
        if (empty($emailAddress)) {
            throw new Zend_Exception('Required parameters missing');
        }
        
        // Save the customer into the LegacyDataStore.
        $passwordUtil = new Application_Core_Password();
        $password = $passwordUtil->generate();
        $identifier = $this->_customerModel->insertCustomer($emailAddress, $password, Model_Core_Customer::CUSTOMER);

        // Encapsulate the customer details in a Model_Core_Customer object and return
        $customer = new Model_Core_Customer();
        $customer->setIdentifier(Model_Core_Customer::IDENTIFIER, $identifier);
        $customer->setEmailAddress($emailAddress);
        $customer->setPassword($password);
        return $customer;
    }

    /**
     * Updates an existing customer in the LegacyDataStore.
     *
     * @param Model_Core_Customer $customer A Customer object containing all the latest details for that customer.
     * @return void
     * @throws Zend_Exception Throws a Zend_Exception if the customer cannot be updated in the data stores.
     */
    public function updateCustomer($customer)
    {
        $this->_customerModel->updateCustomer($customer);
    }

    /**
     * Retrieves and returns a customer.
     *
     * @param mixed $customerIdentifier The customer identifier.
     * @return Model_Core_Customer Returns the matching customer.
     * @throws Zend_Exception Throws a Zend_Exception if the $customerIdentifierType is invalid, or
     *          if the customer cannot be found.
     */
    public function getCustomer($customerIdentifier)
    {
        return $this->_customerModel->getCustomer($customerIdentifier);
    }
    
    /**
     * Retrieves all customers matching the email address passed in.
     *
     * @param string $emailAddress Email address to search for
     * @return mixed An array of Model_Core_Customer objects, or null if no customers found.
     */
    public function getByEmailAddress($emailAddress)
    {
        return $this->_customerModel->getByEmailAddress($emailAddress);
    }
}
