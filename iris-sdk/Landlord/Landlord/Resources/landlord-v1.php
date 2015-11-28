<?php

/**
 * Landlord Service Description
 *
 * @author Paul Swift <paul.swift@barbon.com>
 */
return array(
    'name' => 'Landlord',
    'description' => 'Landlord services used by direct landlords',
    'operations' => array(
        'GetLandlord' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/landlord',
            'summary' => 'Retrieve a direct landlord profile',
            'responseClass' => 'Barbondev\IRISSDK\Landlord\Landlord\Model\Landlord',
        ),
        'UpdateLandlord' => array(
            'httpMethod' => 'PUT',
            'uri' => '/referencing/v1/landlord',
            'summary' => 'Update a direct landlord profile',
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
                    'required' => true,
                ),
                'dayPhone' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'eveningPhone' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'occupation' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'foreigner' => array(
                    'location' => 'json',
                    'type' => 'boolean',
                ),
            ),
        ),
    ),
);