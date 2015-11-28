<?php

/**
 * Lookup Service Description
 *
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
return array(
    'name' => 'Lookup',
    'description' => 'Reference the master lookup service',
    'operations' => array(
        'GetLookup' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/system/lookupdata',
            'summary' => 'Get all lookup sections and items',
            'responseClass' => 'Barbondev\IRISSDK\System\Lookup\Model\Lookup',
        ),
    ),
);