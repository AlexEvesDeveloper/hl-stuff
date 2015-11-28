<?php

/**
 * System Landlord Service Description
 *
 * @author Paul Swift <paul.swift@barbon.com>
 */
return array(
    'name' => 'System Landlord',
    'description' => 'Landlord services used by the system',
    'operations' => array(
        'RegisterLandlord' => array(
            'httpMethod' => 'POST',
            'uri' => '/referencing/v1/system/landlord',
            'summary' => 'Register a direct landlord against a specified domain',
            'parameters' => array(
                'title' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                ),
                'firstName' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                ),
                'lastName' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                ),
                'email' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                ),
                'password' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                ),
                'securityQuestion' => array(
                    'location' => 'json',
                    'type' => 'integer',
                    'required' => true,
                ),
                'securityAnswer' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                ),
                'address' => array(
                    'location' => 'json',
                    'type' => 'object',
                    'instanceOf' => 'Barbondev\IRISSDK\Common\Model\Address',
                ),
            ),
        ),
        'Authenticate' => array(
            'httpMethod' => 'POST',
            'uri' => '/referencing/v1/system/landlords/authenticate',
            'summary' => 'Checks authentication of direct landlord from credentials',
            'responseClass' => 'Barbondev\IRISSDK\Common\Model\Authorisation',
            'parameters' => array(
                'email' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                ),
                'password' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
        ),
        'RequestPasswordReset' => array(
            'httpMethod' => 'POST',
            'uri' => '/referencing/v1/system/landlords/resetpassword',
            'summary' => 'Initiates direct landlord password reset request',
            'parameters' => array(
                'email' => array(
                    'location' => 'postField',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
        ),
        'UpdatePassword' => array(
            'httpMethod' => 'POST',
            'uri' => '/referencing/v1/system/securelink/{linkRef}/landlord/updatepassword',
            'summary' => 'Executes direct landlord password reset request',
            'parameters' => array(
                'linkRef' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
                'password' => array(
                    'location' => 'postField',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
        ),
    ),
);