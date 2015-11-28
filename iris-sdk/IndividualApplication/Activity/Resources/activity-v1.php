<?php

/**
 * Activity Service Description
 *
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
return array(
    'name' => 'Activity',
    'description' => 'Activity stream for applications',
    'operations' => array(
        'GetActivities' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/individual/application/{referencingApplicationUuId}/activities',
            'summary' => 'Get all activities associated with an application',
            'responseClass' => 'Barbondev\IRISSDK\IndividualApplication\Activity\Model\Activity',
            'parameters' => array(
                'referencingApplicationUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
        ),
    ),
);