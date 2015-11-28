<?php
/**
 * Class for remotely customer partial registration
 *
 */
class Service_Core_PartialRegistration {

    /**
     * check customer registration and carry on the registration process if it is not completed 
     *
     * Returns True if valid, false otherwise.
     *
     * @param string $email_address
     *
     * @return int
     */
    public function checkRegister($refno, $email, $isChangeEmail) 
    {
        (string)$refno=preg_replace('/X/','',$refno);
        $customermgr = new Manager_Core_Customer();
        $customer = $customermgr->getCustomerByEmailAddress($email);
        $params = Zend_Registry::get('params');
        $mac = new Application_Core_Security($params->myhomelet->activation_mac_secret, false);
        $digest = $mac->generate(array('email' => $email));
        $activationLink = 'refno=' . $refno . '&' . 'email=' . $email . '&' . 'mac=' . $digest;

        $customerMap = new Datasource_Core_CustomerMaps();  
        if ($customer) {

            if (!$customerMap->getMap(Model_Core_Customer::LEGACY_IDENTIFIER, $refno)) {
                $customermgr->linkLegacyToNew($refno, $customer->getIdentifier(Model_Core_Customer::IDENTIFIER));
            }
              
            if (!$customer->getEmailValidated()) {
                $mail = new Application_Core_Mail();
                $mail->setTo($email, null);
                $mail->setFrom('hello@homelet.co.uk', 'HomeLet');
                $mail->setSubject('My HomeLet account validation');
                $mail->applyTemplate('core/account-validation',
                    array(
                        'activationLink' => $activationLink,
                        'homeletWebsite' => $params->homelet->domain,
                        'firstname'      => $customer->getFirstName(),
                        'templateId'     => 'HL2442 12-12',
                        'heading'        => 'Validating your My HomeLet account',
                        'imageBaseUrl' => $params->weblead->mailer->imageBaseUrl,
                    ),
                    false,
                    '/email-branding/homelet/portal-footer.phtml',
                    '/email-branding/homelet/portal-header.phtml');

                $mail->applyTextTemplate('core/account-validationtxt',
                    array('activationLink' => $activationLink,
                          'homeletWebsite' => $params->homelet->domain,
                          'firstname'      => $customer->getFirstName(),
                          'templateId'     => 'HL2442 12-12',
                          'heading'        => 'Validating your My HomeLet account'),
                    false,
                    '/email-branding/homelet/portal-footer-txt.phtml',
                    '/email-branding/homelet/portal-header-txt.phtml');

                // Send email
                $mail->send();

                return 1;
            }
            else {
                return 0; 
            }
        }   
        else {

            if($isChangeEmail){
                $cMap=$customerMap->getMap(Model_Core_Customer::LEGACY_IDENTIFIER, $refno);
                if ($cMap) {
                   $customer=$customermgr->getCustomer(Model_Core_Customer::IDENTIFIER, $cMap->getIdentifier()); 
                   $customer->setEmailAddress($email);
                   $customermgr->updateCustomer($customer);
                   $legacyids = $customerMap->getLegacyIDs($customer->getIdentifier());
                   foreach ($legacyids as $legacyid) {
                    if($legacyid != $refno){
                        $customer=$customermgr->getCustomer(Model_Core_Customer::LEGACY_IDENTIFIER, $legacyid);
                        $customer->setEmailAddress($email);
                        $customermgr->updateCustomer($customer);
                    }
                   }
                   return 0;
                }
            }   
            $oldCustomer = $customermgr->getCustomer(Model_Core_Customer::LEGACY_IDENTIFIER, $refno);

            $mail = new Application_Core_Mail();
            $mail->setTo($email, null);
            $mail->setFrom('hello@homelet.co.uk', 'HomeLet');
            $mail->setSubject("Don't forget to register your My HomeLet account");
            $mail->applyTemplate('core/partial-registration',
                array(
                    'activationLink' => $activationLink,
                    'homeletWebsite' => $params->homelet->domain,
                    'firstname'      => $oldCustomer->getFirstName(),
                    'templateId'     => 'HL2469 12-12',
                    'heading'        => 'Get even more with your My HomeLet account',
                    'imageBaseUrl' => $params->weblead->mailer->imageBaseUrl,
                ),
                false,
                '/email-branding/homelet/portal-footer.phtml',
                '/email-branding/homelet/portal-header.phtml');

            $mail->applyTextTemplate('core/partial-registrationtxt',
                array('activationLink' => $activationLink,
                      'homeletWebsite' => $params->homelet->domain,
                      'firstname'      => $oldCustomer->getFirstName(),
                      'templateId'     => 'HL2469 12-12',
                      'heading'        => 'Get even more with your My HomeLet account'),
                false,
                '/email-branding/homelet/portal-footer-txt.phtml',
                '/email-branding/homelet/portal-header-txt.phtml');

            // Send email
            $mail->send();

            return 2;
        }
    }
}

