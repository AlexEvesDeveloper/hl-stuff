<?php

/**
 * Referencing login manager class.
 */
class Manager_Referencing_Login
{
	/**
	 * Returns true or false as to whether the customer is logged in.
	 *
	 * @return boolean
	 * True or false according to whether the user is logged in.
	 */
	public function getIsUserLoggedIn()
    {
		$auth = Zend_Auth::getInstance();
		$auth->setStorage(new Zend_Auth_Storage_Session('homelet_customer'));

        if ($auth->hasIdentity()) {
            return true;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Logs-in the private landlord (PLL) referencing customer.
	 *
	 * @param string $emailAddress The unique customer email address
	 * @param string $password The customer's password.
	 * @return boolean Returns true if the user has been successfully logged in, false otherwise.
	 */
	public function logUserIn($emailAddress, $password)
    {
		$auth = Zend_Auth::getInstance();
		$auth->setStorage(new Zend_Auth_Storage_Session('homelet_customer'));
		
		$customerManager = new Manager_Referencing_Customer();
	    $adapter = $customerManager->getAuthAdapter(array(
            'email'     => $emailAddress,
            'password'  => $password
        ));

        $result = $auth->authenticate($adapter);
	    if ($result->isValid()) {
            $customer = $customerManager->getByEmailAddress($emailAddress);

            if ($customer->getEmailValidated() !== true) {
                $auth->clearIdentity();
                return false;
            }
            else {
                $storage = $auth->getStorage();
                $storage->write($adapter->getResultRowObject(array(
                    'title',
                    'first_name',
                    'last_name',
                    'email_address',
                    'id')));

                return true;
            }
	    }
        else {
            return false;
        }
	}
}
