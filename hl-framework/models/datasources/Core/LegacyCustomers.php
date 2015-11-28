<?php

/**
* Model definition for the legacy customer table.
*/
class Datasource_Core_LegacyCustomers extends Zend_Db_Table_Multidb {
    
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_legacy_homelet';
    protected $_name = 'customer';
    protected $_primary = 'refno';
    /**#@-*/
    
    
    /**
     * Inserts a new customer record into the legacy customer table.
     *
     * This method receives the bare-minimum data necessary to insert a new
     * customer record in the legacy customer table.
     *
     * @param string $emailAddress
     * The customers email address.
     *
     * @param string $password
     * The customer's password.
     *
     * @param string $legacyIdentifier
     * The customer refno.
     *
     * @return void
     */
    public function insertCustomer($emailAddress, $password, $legacyIdentifier) {
    
        //First insert into the DataStore.
        $data = array(
            'email' => $emailAddress,
            'password' => $password,
            'refno' => $legacyIdentifier
        );

        $this->insert($data);
    }
    
    
    /**
     * Updates an existing customer record in the legacy customer table.
     *
     * @param Model_Core_Customer $customer
     * An up-to-date Customer object that will be used to update the corresponding
     * record in the data store.
     *
     * @return void
     */
    public function updateCustomer($customer) {
        
        //Retrieve the data from the Customer object and format where necessary
        //ready for insertion into the data storage.
        $legacyIdentifier = $customer->getIdentifier(
            Model_Core_Customer::LEGACY_IDENTIFIER);
        
        $address1 = $customer->getAddressLine(Model_Core_Customer::ADDRESSLINE1);
        $address2 = $customer->getAddressLine(Model_Core_Customer::ADDRESSLINE2);
        $address3 = $customer->getAddressLine(Model_Core_Customer::ADDRESSLINE3);
        
        $telephone1 = $customer->getTelephone(Model_Core_Customer::TELEPHONE1);
        $telephone2 = $customer->getTelephone(Model_Core_Customer::TELEPHONE2);
        
        if($customer->getIsForeignAddress()) {
            
            $isForeignAddress = 'yes';
        }
        else {
            
            $isForeignAddress = 'no';
        }
        
        $data = array(
            'password'          =>  $customer->getPassword(),
            'title'             =>  $customer->getTitle(),
            'firstname'         =>  $customer->getFirstName(),
            'lastname'          =>  $customer->getLastName(),
            'landname'          =>  $customer->getLandlordName(),
            'personaladdress1'  =>  $address1,
            'personaladdress3'  =>  $address2,
            'personaladdress5'  =>  $address3,
            'personalpostcode'  =>  $customer->getPostcode(),
            'country'           =>  $customer->getCountry(),
            'isForeignAddress'  =>  $isForeignAddress,
            'phone1'            =>  $telephone1,
            'phone2'            =>  $telephone2,
            'fax'               =>  $customer->getFax(),
            'email'             =>  $customer->getEmailAddress(),
            'occupation'        =>  $customer->getOccupation(),
            'date_of_birth_at'  =>  $customer->getDateOfBirthAt()
        );

        $where = $this->quoteInto('refno = ?', $legacyIdentifier);
        $this->update($data, $where);
    }

    /**
     * Updates the LGSagentschemenumber field in the table for the given reference number
     *
     * @param string $agentSchemeNumber
     * @param string $legacyIdentifier
     */
    public function updateCustomerAgentSchemeNumber($agentSchemeNumber, $legacyIdentifier)
    {
        $data = array(
            'LGSagentschemenumber' => $agentSchemeNumber,
        );

        $where = $this->quoteInto('refno = ?', $legacyIdentifier);
        $this->update($data, $where);
    }

/**
     * Advises whether the customer exists.
     *
     * @param string $identifier
     * Used to identify a matching customer record in the legacy customer table,
     * if one exists.
     *
     * @return boolean
     * Returns true if the customer exists, false otherwise.
     */
    public function getConfirmCustomerExists($identifier) {
        
        //Attempt to retrieve the customer record.
        $select = $this->select();
        $select->where('refno = ?', $identifier);
        $customerRow = $this->fetchRow($select);
        
        if(empty($customerRow)) {
            
            $returnVal = false;
        }
        else {
            
            $returnVal = true;
        }
        
        return $returnVal;
    }
    
    
    /**
     * An "extension" to the getCustomer functionality to allow us to search for a customer by email address and retrieve them
     *
     * @param string $emailAddress
     * The email address you are searching for
     * 
     * @return Model_Core_Customer
     */
    public function getByEmailAddress($emailAddress) {
        
        $select = $this->select();
        $select->where('email = ?', $emailAddress);
        $customerRow = $this->fetchRow($select);
        
        if (!is_null($customerRow)) {
            
            return $this->getCustomer($customerRow->refno);
        }
        
        return null;
    }
    
    /**
     * Returns all customers matching the email address passed in.
     *
     * @param string $emailAddress
     * The email address to search for.
     *
     * @return mixed
     * Returns an array of Model_Core_Customer objects, or null if no
     * customers found.
     */
    public function getAllByEmailAddress($emailAddress)
    {
        $select = $this->select();
        $select->where('email = ?', $emailAddress);
        $rows = $this->fetchAll($select);
        
        $customerArray = array();
        if (count($rows) > 0) {
            foreach($rows as $currentRow) {
                $customerArray[] = $this->getCustomer($currentRow->refno);
            }
        }
        
        if (empty($customerArray)) {
            return null;
        }
        else {
            return $customerArray;
        }
    }
    
    /**
     * Retrieves the specified customer record, encapsulates the details in a
     * Customer object and returns this.
     *
     * @param string $identifier
     * Identifies the customer record in the legacy customer table.
     *
     * @return Model_Core_Customer
     * The customer details encapsulated in a Customer object.#5 /home/benjamin.vickers/HomeLet-Framework/src/application/models/datasources/Core/LegacyCustomers.php(209): Zend_Db_Table_Abstract->fetchRow(Object(Zend_Db_Table_Select))

     *
     * @throws Zend_Exception
     * Throws a Zend_Exception if the customer record cannot be found.
     */
    public function getCustomer($identifier) {
        
        //Retrieve the customer record.
        $select = $this->select();
        $select->where('refno = ?', $identifier);
        $customerRow = $this->fetchRow($select);
        if(empty($customerRow)) {
            throw new Zend_Exception('Customer not found.');
        }
        
        
        //Populate the details into a Customer object.
        $customer = new Model_Core_Customer();
        $customer->setIdentifier(
            Model_Core_Customer::LEGACY_IDENTIFIER,
            $identifier);
        
        $customerMaps = new Datasource_Core_CustomerMaps();
        $customerMap = $customerMaps->getMap(Model_Core_Customer::LEGACY_IDENTIFIER, $identifier);
        
        if ($customerMap) {
            $customer->setIdentifier(
                Model_Core_Customer::IDENTIFIER,
                $customerMap->getIdentifier());
        }
        
        $customer->setTitle($customerRow->title);
        $customer->setFirstName($customerRow->firstname);
        $customer->setLastName($customerRow->lastname);
        
        $customer->setAddressLine(Model_Core_Customer::ADDRESSLINE1, $customerRow->personaladdress1);
        $customer->setAddressLine(Model_Core_Customer::ADDRESSLINE2, $customerRow->personaladdress3);
        $customer->setAddressLine(Model_Core_Customer::ADDRESSLINE3, $customerRow->personaladdress5);
        
        $customer->setPostCode($customerRow->personalpostcode);
        $customer->setCountry($customerRow->country);
        
        if($customerRow->isForeignAddress == 'no') {
            $customer->setIsForeignAddress(false);
        }
        else {
            $customer->setIsForeignAddress(true);          
        }
        
        $customer->setTelephone(Model_Core_Customer::TELEPHONE1, $customerRow->phone1);
        $customer->setTelephone(Model_Core_Customer::TELEPHONE2, $customerRow->phone2);
        $customer->setFax($customerRow->fax);
        
        $customer->setEmailAddress($customerRow->email);
        $customer->setPassword($customerRow->password);
        $customer->setOccupation($customerRow->occupation);
        $customer->setDateOfBirthAt($customerRow->date_of_birth_at);
        $customer->typeID = 2; // Default to a tenant
        
        return $customer;
    }
    
    
    /**
     * Removes a customer.
     *
     * @param Model_Core_Customer $customer
     * The customer to remove.
     *
     * @return void
     */
    public function removeCustomer($customer) {
        
        $identifier = $customer->getIdentifier(
            Model_Core_Customer::LEGACY_IDENTIFIER);
        $where = $this->quoteInto('refno = ?', $identifier);
        $this->delete($where);
    }
}

?>