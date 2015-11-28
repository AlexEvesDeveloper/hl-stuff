<?php

/**
 * Agent Brand Service Description
 *
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
return array(
    'name' => 'Agent Brand',
    'description' => 'Agent and branch brand management',
    'operations' => array(
        'UpdateAgentBrand' => array(
            'httpMethod' => 'PUT',
            'uri' => '/referencing/v1/agent/brand',
            'summary' => 'Update brand for an agent',
            'parameters' => array(
                'brandName' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                ),
                'email' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'address' => array(
                    'location' => 'json',
                    'type' => 'object',
                    'instanceOf' => 'Barbondev\IRISSDK\Common\Model\Address',
                ),
                'phone' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'fax' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'websiteUrl' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'logo' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'options' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
            ),
        ),
        'UpdateAgentBranchBrand' => array(
            'httpMethod' => 'PUT',
            'uri' => '/referencing/v1/agent/branch/{agentBranchUuId}/brand',
            'summary' => 'Update brand for an agent',
            'parameters' => array(
                'agentBranchUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
                'brandName' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                ),
                'email' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'address' => array(
                    'location' => 'json',
                    'type' => 'object',
                    'instanceOf' => 'Barbondev\IRISSDK\Common\Model\Address',
                ),
                'phone' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'fax' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'websiteUrl' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'logo' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'options' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
            ),
        ),
        'GetAgentBrandLogo' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/agent/brand/logo/download',
            'summary' => 'Get agent brand logo',
            'responseClass' => 'Barbondev\IRISSDK\Agent\Brand\Model\Logo',
        ),
        'GetAgentBranchBrandLogo' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/agent/branch/{branchUuId}/brand/logo/download',
            'summary' => 'Get agent branch brand logo',
            'responseClass' => 'Barbondev\IRISSDK\Agent\Brand\Model\Logo',
            'parameters' => array(
                'branchUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
        ),
    ),
);
