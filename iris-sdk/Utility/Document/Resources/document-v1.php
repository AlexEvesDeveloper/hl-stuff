<?php

/**
 * Document Service Description
 *
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
return array(
    'name' => 'Document',
    'description' => 'Document management system',
    'operations' => array(
        'GetDocument' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/util/document/{documentUuId}/download',
            'summary' => 'Get a single document',
            'responseClass' => 'Barbondev\IRISSDK\Utility\Document\Model\Document',
            'parameters' => array(
                'documentUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
        ),
    ),
);