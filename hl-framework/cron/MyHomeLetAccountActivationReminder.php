<?php

final class Cron_MyHomeLetAccountActivationReminder
{
    public function run()
    {
        $params = Zend_Registry::get('params');
        $customersDataSource = new Datasource_Core_Customers();
        $customers = $customersDataSource->unActivatedCustomerAccounts();

        foreach ($customers as $customer) {
            $customer->sendAccountValidationEmail(
                'Don\'t forget to validate your My HomeLet account',
                'Don&rsquo;t forget to validate your My HomeLet account...',
                'core/account-validation',
                'core/account-validationtxt',
                'HL2469 12-12'
            );
        }
    }
}

