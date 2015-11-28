<?php

/**
 * Class for updating a customers personal details
 */
class Service_Core_UpdatePersonalDetails {
    public function updatePersonalDetails($email, $title, $firstName, $lastName)
    {
        $customerManager = new Manager_Core_Customer();
        $customer = $customerManager->getCustomerByEmailAddress($email);

        if ($customer) {
            // Customer found in web database, update their personal details
            $customer->setTitle($title);
            $customer->setFirstName($firstName);
            $customer->setLastName($lastName);
            $customerManager->updateCustomer($customer);

            return 1;
        }
        else {
            // No customer found, update nothing
            return 0;
        }
    }
}
