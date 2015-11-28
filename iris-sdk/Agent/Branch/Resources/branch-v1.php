<?php

/**
 * Branch Service Description
 *
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
return array(
    'name' => 'Agent',
    'description' => 'Agent management',
    'operations' => array(
        'GetBranches' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/agent/branches',
            'summary' => 'Get agent branches',
            'responseClass' => 'Barbondev\IRISSDK\Agent\Branch\Model\Branch',
        ),
        'GetBranch' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/agent/branch/{agentBranchUuId}',
            'summary' => 'Get a single agent branch',
            'responseClass' => 'Barbondev\IRISSDK\Agent\Branch\Model\Branch',
            'parameters' => array(
                'agentBranchUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
        ),
        'UpdateBranch' => array(
            'httpMethod' => 'PUT',
            'uri' => '/referencing/v1/agent/branch/{agentBranchUuId}',
            'summary' => 'Update a single agent branch',
            'parameters' => array(
                'agentBranchUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
                'agentSchemeNumber' => array(
                    'name' => 'agentBranchId',
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                ),
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
                'correspondenceAddress' => array(
                    'location' => 'json',
                    'type' => 'object',
                    'instanceOf' => 'Barbondev\IRISSDK\Common\Model\Address',
                    'required' => true,
                ),
                'contactName' => array(
                    'location' => 'json',
                    'type' => 'string',
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
                ),
                'status' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                ),
                'websiteUrl' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'createdAt' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'hasSMS' => array(
                    'location' => 'json',
                    'type' => 'boolean',
                ),
                'hasMailer' => array(
                    'location' => 'json',
                    'type' => 'boolean',
                ),
                'isPremier' => array(
                    'location' => 'json',
                    'type' => 'boolean',
                ),
                'hasTenantMailerOptin' => array(
                    'location' => 'json',
                    'type' => 'boolean',
                ),
                'hasInterimReport' => array(
                    'location' => 'json',
                    'type' => 'boolean',
                ),
                'source' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                ),
                'generalEmail' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'referencingEmail' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'invoiceEmail' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'insuranceEmail' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'rentGuaranteeRenewalsEmail' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
            ),
        ),
        'GetBranchUsers' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/agent/branch/{agentBranchUuId}/agentusers',
            'summary' => 'Get users for an agent branch',
            'responseClass' => 'Barbondev\IRISSDK\Agent\User\Model\User',
            'parameters' => array(
                'agentBranchUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
        ),
        'CreateBranchUser' => array(
            'httpMethod' => 'POST',
            'uri' => '/referencing/v1/agent/branch/{agentBranchUuId}/agentuser',
            'summary' => 'Create new user at an agent branch',
            'responseClass' => 'Barbondev\IRISSDK\Agent\User\Model\User',
            'parameters' => array(
                'agentBranchUuId' => array(
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
    ),
);