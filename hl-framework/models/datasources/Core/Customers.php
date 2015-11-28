<?php

/**
* Model definition for the customer table.
*/
class Datasource_Core_Customers extends Zend_Db_Table_Multidb
{
    protected $_legacyTypeMap = array(
        'agent'       =>  1,
        'customer'    =>  2,
    );
    
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_homelet';
    protected $_name = 'customers';
    protected $_primary = 'id';
    /**#@-*/
    
    
    /**
     * Inserts a new customer record into the customers table.
     *
     * This method receives the bare-minimum data necessary to insert a new
     * customer record in the customers table.
     *
     * @param string $emailAddress
     * The customers email address.
     *
     * @param string $password
     * The customer's password.
     *
     * @param string $customerTypeID
     * Represents the type of customer (e.g. 'tenant', 'landlord' etc). This value
     * MUST correspond to one of the relevant consts exposed by the
     * Model_Core_Customer class: AGENT,
     * LANDLORD or TENANT.
     * 
     * @return object
     */
    public function insertCustomer($emailAddress, $password, $customerTypeID)
    {
        //First insert into the DataStore.
        $data = array(
            'email_address'     =>  $emailAddress,
            'password'          =>  $password,
            'type_id'           =>  $this->_legacyTypeMap[$customerTypeID],
            'registration_date' =>  new Zend_Db_Expr('CURDATE()'),
        );
        
        return $this->insert($data);
    }
    
    
    /**
     * Updates an existing customer record in the customers table.
     *
     * @param Model_Core_Customer $customer
     * An up-to-date Customer object that will be used to update the corresponding
     * record in the data store.
     *
     * @return void
     */
    public function updateCustomer($customer)
    {
        //Retrieve the data from the Customer object and format where necessary
        //ready for insertion into the data storage.
        $identifier = $customer->getIdentifier(Model_Core_Customer::IDENTIFIER);
        
        $address1 = $customer->getAddressLine(Model_Core_Customer::ADDRESSLINE1);
        $address2 = $customer->getAddressLine(Model_Core_Customer::ADDRESSLINE2);
        $address3 = $customer->getAddressLine(Model_Core_Customer::ADDRESSLINE3);
        
        $telephone1 = $customer->getTelephone(Model_Core_Customer::TELEPHONE1);
        $telephone2 = $customer->getTelephone(Model_Core_Customer::TELEPHONE2);
        
        if($customer->getIsForeignAddress()) {
            $isForeignAddress = 1;
        }
        else {
            $isForeignAddress = 0;
        }
        
        $landlordName = $customer->getLandlordName();
        if(empty($landlordName)) {
            
            $landlordName = '';
        }
        
        $data = array(
            'password'              =>  $customer->getPassword(),
            'title'                 =>  $customer->getTitle(),
            'first_name'            =>  $customer->getFirstName(),
            'last_name'             =>  $customer->getLastName(),
            'landname'              =>  $landlordName,
            'address1'              =>  $address1,
            'address2'              =>  $address2,
            'address3'              =>  $address3,
            'postcode'              =>  $customer->getPostcode(),
            'country'               =>  $customer->getCountry(),
            'foreign_address'       =>  $isForeignAddress,
            'telephone1'            =>  $telephone1,
            'telephone2'            =>  $telephone2,
            'email_address'         =>  $customer->getEmailAddress(),
            'occupation'            =>  $customer->getOccupation(),
            'type_id'               =>  (isset($this->_legacyTypeMap[$customer->typeID]) ?
                $this->_legacyTypeMap[$customer->typeID] : null),
        );

        // Check account loading flag
        if ($customer->getAccountLoadComplete() != null) {
            $data['account_load_complete'] = ($customer->getAccountLoadComplete() == true ? 1 : 0);
        }

        // Check email validation flag has been set to a boolean value.
        // This check must be performed as the customer manager sometimes
        // retrieves legacy customers which don't know about email validation
        // and runs this store method on the new customer record, resulting
        // in a reset of the validation flag.
        if ($customer->getEmailValidated() != null) {
            // Email validation flag has been set, stored within the record
            $data['email_validated'] = ($customer->getEmailValidated() == true ? 1 : 0);
        }

        $where = $this->quoteInto('id = ?', $identifier);
        $this->update($data, $where);
    }
    
    
    /**
     * An "extension" to the getCustomer functionality to allow us to search for a customer by email address and retrieve them
     *
     * @param string $emailAddress The email address you are searching for
     * @return Model_Insurance_Common_Customer
     */
    public function getByEmailAddress($emailAddress)
    {
        $select = $this->select();
        $select->where('email_address = ?', $emailAddress);
        
        $customerRow = $this->fetchRow($select);
        
        if (!is_null($customerRow)) return $this->getCustomer($customerRow->id);
        
        return null;
    }

    /**
     * Retrieves the specified customer record, encapsulates the details in a
     * Customer object and returns this.
     *
     * @param $identifier
     * @return param $identifier
     * Identifies the customer record in the customers table.
     *
     * @return \param The customer details encapsulated in a Customer object.
     */
    public function getCustomer($identifier)
    {
        //Retrieve the customer record.
        $select = $this->select();
        $select->where('id = ?', $identifier);
        $customerRow = $this->fetchRow($select);
        
        if ($customerRow) {
            //Populate the details into a Customer object.
            $customer = new Model_Core_Customer();
            $customer->setIdentifier(
                Model_Core_Customer::IDENTIFIER,
                $identifier);

            $customerMaps = new Datasource_Core_CustomerMaps();
            $customerMap = $customerMaps->getMap(
                Model_Core_Customer::IDENTIFIER,
                $identifier);

            if ($customerMap) {
                $customer->setIdentifier(
                    Model_Core_Customer::LEGACY_IDENTIFIER,
                    $customerMap->getLegacyIdentifier());
            }

            $customer->setTitle($customerRow->title);
            $customer->setFirstName($customerRow->first_name);
            $customer->setLastName($customerRow->last_name);
            $customer->setLandlordName($customerRow->landname);

            $customer->setAddressLine(
                Model_Core_Customer::ADDRESSLINE1,
                $customerRow->address1);

            $customer->setAddressLine(
                Model_Core_Customer::ADDRESSLINE2,
                $customerRow->address2);

            $customer->setAddressLine(
                Model_Core_Customer::ADDRESSLINE3,
                $customerRow->address3);

            $customer->setPostCode($customerRow->postcode);
            $customer->setCountry($customerRow->country);

            if($customerRow->foreign_address == 0) {
                $customer->setIsForeignAddress(false);
            }
            else {
                $customer->setIsForeignAddress(true);
            }

            $customer->setTelephone(
                Model_Core_Customer::TELEPHONE1,
                $customerRow->telephone1);

            $customer->setTelephone(
                Model_Core_Customer::TELEPHONE2,
                $customerRow->telephone2);

            $customer->setEmailAddress($customerRow->email_address);
            $customer->setPassword($customerRow->password);
            $customer->setOccupation($customerRow->occupation);
            $customer->setEmailValidated(($customerRow->email_validated == 1 ? true : false));
            $customer->setAccountLoadComplete(($customerRow->account_load_complete == 1 ? true : false));

            $customerTypeName = array_search($customerRow->type_id, $this->_legacyTypeMap);
            if ($customerTypeName !== false) {
                $customer->typeID = $customerTypeName;
            }

            return $customer;
        }
        else {
            return null;
        }
    }

    /**
     * Removes a customer.
     *
     * @param Model_Core_Customer $customer
     * The customer to remove.
     *
     * @return void
     */
    public function removeCustomer($customer)
    {
        $identifier = $customer->getIdentifier(Model_Core_Customer::IDENTIFIER);
        $where = $this->quoteInto('id = ?', $identifier);
        $this->delete($where);
    }

    /**
     * Return a list of customers whose accounts have not been activated
     *
     * @return array List of Model_Core_Customer objects
     */
    public function unActivatedCustomerAccounts()
    {
        $customers = array();
        $select = $this->select()
                       ->where('email_validated = 0')
                       ->where('registration_date = DATE_SUB(CURDATE(), INTERVAL 3 DAY)');

        $rowSet = $this->fetchAll($select);
        if (count($rowSet)) {
            foreach ($rowSet as $row) {
                // For each account not yet activated after 3 days, create a customer object
                $customers[] = $this->getCustomer($row['id']);
            }
        }

        return $customers;
    }
    
    /**
     * Updates an existing customer record in the customers table.
     *
     * @param Model_Core_Customer $customer, identyfier
     * An up-to-date Customer object that will be used to update the corresponding
     * record in the data store.
     *
     * @return void
     */
    public function updateCustomerByLegacy($customer,$identifier)
    {
        
        $address1 = $customer->getAddressLine(Model_Core_Customer::ADDRESSLINE1);
        $address2 = $customer->getAddressLine(Model_Core_Customer::ADDRESSLINE2);
        $address3 = $customer->getAddressLine(Model_Core_Customer::ADDRESSLINE3);
        
        $telephone1 = $customer->getTelephone(Model_Core_Customer::TELEPHONE1);
        $telephone2 = $customer->getTelephone(Model_Core_Customer::TELEPHONE2);
        
        if($customer->getIsForeignAddress()) {
            $isForeignAddress = 1;
        }
        else {
            $isForeignAddress = 0;
        }
        
        $landlordName = $customer->getLandlordName();
        if(empty($landlordName)) {
            
            $landlordName = '';
        }
        
        $data = array(
            'title'                 =>  $customer->getTitle(),
            'first_name'            =>  $customer->getFirstName(),
            'last_name'             =>  $customer->getLastName(),
            'landname'              =>  $landlordName,
            'address1'              =>  $address1,
            'address2'              =>  $address2,
            'address3'              =>  $address3,
            'postcode'              =>  $customer->getPostcode(),
            'country'               =>  $customer->getCountry(),
            'foreign_address'       =>  $isForeignAddress,
            'telephone1'            =>  $telephone1,
            'telephone2'            =>  $telephone2,
            'email_address'         =>  $customer->getEmailAddress(),
            'occupation'            =>  $customer->getOccupation(),
            'type_id'               =>  2
        );


        $where = $this->quoteInto('id = ?', $identifier);
        $this->update($data, $where);
    }
    
    /**
     * Retrieves a list of quotes for a particular customer
     *
     * @param Model_Core_Customer $customer
     * @return array
     * @todo: I don't think the way the customer manager has been laid out is right
     * it feels awkward putting this here. We need to re-think
     */
    public function getQuoteList($customer)
    {
        Zend_Debug::dump($customer);
    }
}

