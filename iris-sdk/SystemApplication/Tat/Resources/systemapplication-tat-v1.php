<?php

/**
 * System Application Service Description
 *
 * @author Paul Swift <paul.swift@barbon.com>
 */
return array(
    'name' => 'System Tenant Application Tracker',
    'description' => 'Tenant application tracker services used by the system',
    'operations' => array(
        'GetTatStatus' => array(
            'httpMethod' => 'POST',
            'uri' => '/referencing/v1/system/tatForm/status',
            'summary' => 'Get tenant application tracker status information',
            'responseClass' => 'Barbondev\IRISSDK\SystemApplication\Tat\Model\TatStatus',
            'parameters' => array(
                'agentSchemeNumber' => array(
                    'location' => 'json',
                    'type' => 'integer',
                    'required' => true,
                ),
                'applicationReferenceNumber' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                ),
                'applicantBirthDate' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
        ),
    ),
);