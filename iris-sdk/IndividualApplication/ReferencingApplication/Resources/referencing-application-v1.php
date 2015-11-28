<?php

/**
 * Referencing Application Service Description
 *
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
return array(
    'name' => 'Referencing Application',
    'description' => 'Referencing application for an individual',
    'operations' => array(
        'SubmitReferencingApplication' => array(
            'httpMethod' => 'PUT',
            'uri' => '/referencing/v1/individual/application/{referencingApplicationUuId}/submit',
            'summary' => 'Submit an individual referencing application',
            'parameters' => array(
                'referencingApplicationUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
        ),
        'GetReferencingApplication' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/individual/application/{referencingApplicationUuId}',
            'summary' => 'Get individual referencing application',
            'responseClass' => 'Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication',
            'parameters' => array(
                'referencingApplicationUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
        ),
        'GetReportNotifications' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/individual/application/recent/document',
            'summary' => 'Get recently uploaded reports to DMS',
            'responseClass' => 'Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReportNotification',
        ),
        'GetProgress' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/individual/application/{referencingApplicationUuId}/progress',
            'summary' => 'Get individual referencing application progress',
            'responseClass' => 'Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\Progress',
            'parameters' => array(
                'referencingApplicationUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
        ),
        'GetDocuments' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/individual/application/{referencingApplicationUuId}/documents',
            'summary' => 'Get individual referencing application documents',
            'responseClass' => 'Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\Document',
            'parameters' => array(
                'referencingApplicationUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
        ),
        'ResendCompletionEmailToApplicant' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/individual/application/{referencingApplicationUuId}/applicationEmail/resend',
            'summary' => 'Resend the completion prompt email to the applicant',
            'parameters' => array(
                'referencingApplicationUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
        ),
        'FindReferencingApplications' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/individual/applications',
            'summary' => 'Find individual referencing applications',
            'responseClass' => 'Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplicationFindResults',
            'parameters' => array(
                'referenceNumber' => array(
                    'location' => 'query',
                    'type' => 'string',
                ),
                'applicantFirstName' => array(
                    'location' => 'query',
                    'type' => 'string',
                ),
                'applicantLastName' => array(
                    'location' => 'query',
                    'type' => 'string',
                ),
                'propertyAddress' => array(
                    'location' => 'query',
                    'type' => 'string',
                ),
                'propertyTown' => array(
                    'location' => 'query',
                    'type' => 'string',
                ),
                'propertyPostcode' => array(
                    'location' => 'query',
                    'type' => 'string',
                ),
                'applicationStatus' => array(
                    'location' => 'query',
                    'type' => 'string',
                ),
                'productType' => array(
                    'location' => 'query',
                    'type' => 'string',
                ),
                'offset' => array(
                    'location' => 'query',
                    'type' => 'integer',
                ),
                'numberOfRecords' => array(
                    'location' => 'query',
                    'type' => 'integer',
                ),
            ),
        ),
        'CancelReferencingApplication' => array(
            'httpMethod' => 'DELETE',
            'uri' => '/referencing/v1/individual/application/{referencingApplicationUuId}/cancel',
            'summary' => 'Cancel an individual referencing application',
            'parameters' => array(
                'referencingApplicationUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
        ),
        'CreateReferencingApplication' => array(
            'httpMethod' => 'POST',
            'uri' => '/referencing/v1/individual/case/{referencingCaseUuId}/application',
            'summary' => 'Create individual referencing application',
            'responseClass' => 'Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication',
            'parameters' => array(
                'referencingCaseUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
                'productId' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'title' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'firstName' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'middleName' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'lastName' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'otherName' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'email' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'birthDate' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'employmentStatus' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'residentialStatus' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'grossIncome' => array(
                    'location' => 'json',
                    'type' => 'numeric',
                ),
                'hasCCJ' => array(
                    'location' => 'json',
                    'type' => 'boolean',
                ),
                'phone' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'mobile' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'bankAccount' => array(
                    'location' => 'json',
                    'type' => 'object',
                    'instanceOf' => 'Barbondev\IRISSDK\Common\Model\BankAccount',
                ),
                'isRentPaidInAdvance' => array(
                    'location' => 'json',
                    'type' => 'boolean',
                ),
                'financialReferees' => array(
                    'location' => 'json',
                    'type' => 'array',
                ),
                'addressHistories' => array(
                    'location' => 'json',
                    'type' => 'array',
                ),
                'rentShare' => array(
                    'location' => 'json',
                    'type' => 'numeric',
                ),
                'completionMethod' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'lettingReferee' => array(
                    'location' => 'json',
                    'type' => 'object',
                    'instanceOf' => 'Barbondev\IRISSDK\Common\Model\LettingReferee',
                ),
                'signaturePreference' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'applicationType' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'policyLength' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'canEmploymentChange' => array(
                    'location' => 'json',
                    'type' => 'boolean',
                ),
                'canContactApplicantByPhoneAndPost' => array(
                    'location' => 'json',
                    'type' => 'boolean',
                ),
                'canContactApplicantBySMSAndEmail' => array(
                    'location' => 'json',
                    'type' => 'boolean',
                ),
                'obtainFinancialReference' => array(
                    'location' => 'json',
                    'type' => 'boolean',
                ),
            ),
        ),
        'UpdateReferencingApplication' => array(
            'httpMethod' => 'PUT',
            'uri' => '/referencing/v1/individual/application/{referencingApplicationUuId}',
            'summary' => 'Update an individual referencing application',
            'parameters' => array(
                'referencingApplicationUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
                'productId' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'title' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'firstName' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'middleName' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'lastName' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'otherName' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'email' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'birthDate' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'employmentStatus' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'residentialStatus' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'grossIncome' => array(
                    'location' => 'json',
                    'type' => 'numeric',
                ),
                'hasCCJ' => array(
                    'location' => 'json',
                    'type' => 'boolean',
                ),
                'phone' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'mobile' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'bankAccount' => array(
                    'location' => 'json',
                    'type' => 'object',
                    'instanceOf' => 'Barbondev\IRISSDK\Common\Model\BankAccount',
                ),
                'isRentPaidInAdvance' => array(
                    'location' => 'json',
                    'type' => 'boolean',
                ),
                'financialReferees' => array(
                    'location' => 'json',
                    'type' => 'array',
                ),
                'addressHistories' => array(
                    'location' => 'json',
                    'type' => 'array',
                ),
                'rentShare' => array(
                    'location' => 'json',
                    'type' => 'numeric',
                ),
                'completionMethod' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'lettingReferee' => array(
                    'location' => 'json',
                    'type' => 'object',
                    'instanceOf' => 'Barbondev\IRISSDK\Common\Model\LettingReferee',
                ),
                'signaturePreference' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'applicationType' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'policyLength' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'canEmploymentChange' => array(
                    'location' => 'json',
                    'type' => 'boolean',
                ),
                'canContactApplicantByPhoneAndPost' => array(
                    'location' => 'json',
                    'type' => 'boolean',
                ),
                'canContactApplicantBySMSAndEmail' => array(
                    'location' => 'json',
                    'type' => 'boolean',
                ),
                'obtainFinancialReference' => array(
                    'location' => 'json',
                    'type' => 'boolean',
                ),
            ),
        ),
        'UpdateReferencingApplicationEmail' => array(
            'httpMethod' => 'PUT',
            'uri' => '/referencing/v1/individual/application/{referencingApplicationUuId}/email',
            'summary' => 'Update an individual referencing application email address',
            'parameters' => array(
                'referencingApplicationUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
                'email' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
            ),
        ),
        'CreateReferencingGuarantorApplication' => array(
            'httpMethod' => 'POST',
            'uri' => '/referencing/v1/individual/application/{referencingApplicationUuId}/guarantor',
            'summary' => 'Create individual referencing guarantor application',
            'responseClass' => 'Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication',
            'parameters' => array(
                'referencingApplicationUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
                'productId' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'title' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'firstName' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'middleName' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'lastName' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'otherName' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'email' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'birthDate' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'employmentStatus' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'residentialStatus' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'grossIncome' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'hasCCJ' => array(
                    'location' => 'json',
                    'type' => 'boolean',
                ),
                'phone' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'mobile' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'bankAccount' => array(
                    'location' => 'json',
                    'type' => 'object',
                    'instanceOf' => 'Barbondev\IRISSDK\Common\Model\BankAccount',
                ),
                'isRentPaidInAdvance' => array(
                    'name' => 'rentPaidInAdvance',
                    'location' => 'json',
                    'type' => 'boolean',
                ),
                'financialReferees' => array(
                    'location' => 'json',
                    'type' => 'array',
                ),
                'addressHistories' => array(
                    'location' => 'json',
                    'type' => 'array',
                ),
                'rentShare' => array(
                    'location' => 'json',
                    'type' => 'numeric',
                ),
                'completionMethod' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'lettingReferee' => array(
                    'location' => 'json',
                    'type' => 'object',
                    'instanceOf' => 'Barbondev\IRISSDK\Common\Model\LettingReferee',
                ),
                'signaturePreference' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'applicationType' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'policyLength' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'hasEmploymentChanged' => array(
                    'name' => 'employmentChange',
                    'location' => 'json',
                    'type' => 'boolean',
                ),
                'canContactApplicantByPhoneAndPost' => array(
                    'location' => 'json',
                    'type' => 'boolean',
                ),
                'canContactApplicantBySMSAndEmail' => array(
                    'location' => 'json',
                    'type' => 'boolean',
                ),
                'obtainFinancialReference' => array(
                    'location' => 'json',
                    'type' => 'boolean',
                ),
            ),
        ),
    ),
);
