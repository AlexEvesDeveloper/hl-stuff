<?php

/**
 * Agent Service Description
 *
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
return array(
    'name' => 'Agent Branch Users',
    'description' => 'Agent branch user management',
    'operations' => array(
        'GetUser' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/agentuser/{agentUserUuId}',
            'summary' => 'Get agent branch user',
            'responseClass' => 'Barbondev\IRISSDK\Agent\User\Model\User',
            'parameters' => array(
                'agentUserUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
        ),
        'UpdateUser' => array(
            'httpMethod' => 'PUT',
            'uri' => '/referencing/v1/agentuser/{agentUserUuId}',
            'summary' => 'Update agent branch user',
            'parameters' => array(
                'agentUserUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
                'name' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'username' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'password' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'email' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'hasReports' => array(
                    'location' => 'json',
                    'type' => 'boolean',
                ),
                'hasAccounts' => array(
                    'location' => 'json',
                    'type' => 'boolean',
                ),
                'status' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'isExternalNewsEnabled' => array(
                    'location' => 'json',
                    'type' => 'boolean',
                ),
            ),
        ),
        'DeleteUser' => array(
            'httpMethod' => 'DELETE',
            'uri' => '/referencing/v1/agentuser/{agentUserUuId}',
            'summary' => 'Delete an agent branch user',
            'parameters' => array(
                'agentUserUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
        ),
    ),
);