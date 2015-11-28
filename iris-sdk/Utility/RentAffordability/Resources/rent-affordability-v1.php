<?php

/**
 * Rent Affordability Service Description
 *
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
return array(
    'name' => 'Rent Affordability',
    'description' => 'Calculate rent affordability',
    'operations' => array(
        'CheckRentAffordability' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/util/rentaffordability',
            'summary' => 'Checks rent affordability based on monthly rent',
            'responseClass' => 'Barbondev\IRISSDK\Utility\RentAffordability\Model\RentAffordability',
            'parameters' => array(
                'monthlyRent' => array(
                    'location' => 'query',
                    'type' => 'numeric',
                ),
            ),
        ),
        'CheckTenantRentAffordability' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/util/rentaffordability/tenant',
            'summary' => 'Checks rent affordability based on tenant annual income',
            'responseClass' => 'Barbondev\IRISSDK\Utility\RentAffordability\Model\RentAffordability',
            'parameters' => array(
                'income' => array(
                    'location' => 'query',
                    'type' => 'numeric',
                ),
            ),
        ),
        'CheckGuarantorRentAffordability' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/util/rentaffordability/guarantor',
            'summary' => 'Checks rent affordability based on guarantor annual income',
            'responseClass' => 'Barbondev\IRISSDK\Utility\RentAffordability\Model\RentAffordability',
            'parameters' => array(
                'income' => array(
                    'location' => 'query',
                    'type' => 'numeric',
                ),
            ),
        ),
    ),
);