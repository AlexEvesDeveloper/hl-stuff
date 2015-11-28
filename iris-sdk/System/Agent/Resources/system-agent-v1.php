<?php

/**
 * System Agent Service Description
 *
 * @author Paul Swift <paul.swift@barbon.com>
 */
return array(
    'name' => 'System Agent',
    'description' => 'Agent services used by the system',
    'operations' => array(
        'Authenticate' => array(
            'httpMethod' => 'POST',
            'uri' => '/referencing/v1/system/agentbranch/{agentSchemeNumber}/authenticate',
            'summary' => 'Checks authentication of agent user from credentials',
            'parameters' => array(
                'agentSchemeNumber' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
                'username' => array(
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
            'uri' => '/referencing/v1/system/agentbranch/{agentSchemeNumber}/users/resetpassword',
            'summary' => 'Initiates password reset request',
            'parameters' => array(
                'agentSchemeNumber' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
                'username' => array(
                    'location' => 'postField',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
        ),
        'UpdatePassword' => array(
            'httpMethod' => 'PUT',
            'uri' => '/referencing/v1/system/securelink/{linkRef}/agentuser/updatepassword',
            'summary' => 'Executes password reset request',
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