<?php

/**
 * Address Finder Service Description
 *
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
return array(
    'name' => 'Address Finder',
    'description' => 'Find an address based on postcode',
    'operations' => array(
        'FindAddress' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/util/lookup/postcode/{postcode}',
            'summary' => 'Find an address based on postcode',
            'responseClass' => 'Barbondev\IRISSDK\Utility\AddressFinder\Model\PafAddress',
            'parameters' => array(
                'postcode' => array(
                    'location' => 'uri',
                    'type' => 'string',
                ),
            ),
        ),
    ),
);