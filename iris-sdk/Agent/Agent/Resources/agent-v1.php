<?php

/**
 * Agent Service Description
 *
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
return array(
    'name' => 'Agent',
    'description' => 'Agent management',
    'operations' => array(
        'GetAgent' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/agent',
            'summary' => 'Get agent',
            'responseClass' => 'Barbondev\IRISSDK\Agent\Agent\Model\Agent',
        ),
        'CreateAgentUser' => array(
            'httpMethod' => 'POST',
            'uri' => '/referencing/v1/agent/branch/{agentBranchUuId}/agentuser',
            'summary' => 'Create a new agent user',
            'responseClass' => 'Barbondev\IRISSDK\Agent\Agent\Model\AgentUser',
            'parameters' => array(
                'agentBranchUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
                'name' => array(
                    'location' => 'json',
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
                'email' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                ),
                'hasReports' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                ),
                'hasAccounts' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                ),
                'status' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                ),
                'isExternalNewsEnabled' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
        ),
        'UpdateAgent' => array(
            'httpMethod' => 'PUT',
            'uri' => '/referencing/v1/agent',
            'summary' => 'Update agent',
            'parameters' => array(
                'name' => array(
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
                'email' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                ),
                'phone' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                ),
                'fax' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
        ),
    ),
);