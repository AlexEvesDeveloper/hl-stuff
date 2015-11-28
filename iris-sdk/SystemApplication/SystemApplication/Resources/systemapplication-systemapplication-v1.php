<?php

/**
 * System Application Service Description
 *
 * @author Paul Swift <paul.swift@barbon.com>
 */
return array(
    'name' => 'System Application',
    'description' => 'Referencing application services used by the system',
    'operations' => array(
        'ValidateLink' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/system/securelink/{linkRef}/validate',
            'summary' => 'Checks if a form link is valid',
            'parameters' => array(
                'linkRef' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
        ),
        'ValidateReviewLink' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/system/securelink/review/{linkRef}/validate',
            'summary' => 'Checks if a form link is valid for final review',
            'parameters' => array(
                'linkRef' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
        ),
        'PaymentOrder' => array(
            'httpMethod' => 'POST',
            'uri' => '/referencing/v1/system/application/{applicationUuId}/payment/order',
            'summary' => 'Create an application payment order',
            'responseClass' => 'Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\PaymentOrder',
            'parameters' => array(
                'applicationUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
                'paymentTypes' => array(
                    'location' => 'json',
                    'type' => 'array',
                    'required' => true,
                ),
                'redirectOnSuccessUrl' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => false,
                ),
                'redirectOnFailureUrl' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => false,
                ),
            ),
        ),
        'PaymentStatus' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/system/application/{applicationUuId}/payment-status',
            'summary' => 'Checks the status of a payment for a referencing application',
            'responseClass' => 'Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\PaymentStatus',
            'parameters' => array(
                'applicationUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
        ),
        'CreateReferencingApplicationNote' => array(
            'httpMethod' => 'POST',
            'uri' => '/referencing/v1/system/application/{applicationUuId}/note',
            'summary' => 'Add a new note to an application',
            'responseClass' => 'Barbondev\IRISSDK\IndividualApplication\Note\Model\Note',
            'parameters' => array(
                'applicationUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
                'note' => array(
                    'location' => 'postField',
                    'type' => 'string',
                    'required' => true,
                ),
                'emailAssessor' => array(
                    'location' => 'postField',
                    'type' => 'string',
                ),
            ),
        ),
        'UpdateReferencingApplicationNote' => array(
            'httpMethod' => 'PUT',
            'uri' => '/referencing/v1/system/application/{applicationUuId}/note/{noteId}',
            'summary' => 'Update an existing note for an application',
            'parameters' => array(
                'applicationUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
                'noteId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
                'note' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                )
            ),
        ),
        'SubmitApplication' => array(
            'httpMethod' => 'PUT',
            'uri' => '/referencing/v1/system/application/{referencingApplicationUuId}/submit',
            'summary' => 'Submit an application after data entry',
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
            'uri' => '/referencing/v1/system/applications',
            'summary' => 'Find individual referencing applications for entire domain',
            'responseClass' => 'Barbondev\IRISSDK\SystemApplication\SystemApplication\Model\ReferencingApplicationFindResults',
            'parameters' => array(
                'applicantFirstName' => array(
                    'location' => 'query',
                    'type' => 'string',
                ),
                'applicantLastName' => array(
                    'location' => 'query',
                    'type' => 'string',
                ),
                'applicantMiddleName' => array(
                    'location' => 'query',
                    'type' => 'string',
                ),
                'applicantDateOfBirth' => array(
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
        'GetReferencingApplication' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/system/securelink/{linkRef}/application',
            'summary' => 'Retrieves the applicant details',
            'responseClass' => 'Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication',
            'parameters' => array(
                'linkRef' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
        ),
        'GetReferencingFinancialReferee' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/system/securelink/{linkRef}/financialReferee',
            'summary' => 'Retrieves a financial referee\'s details',
            'responseClass' => 'Barbondev\IRISSDK\Common\Model\FinancialReferee',
            'parameters' => array(
                'linkRef' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
        ),
        'GetReferencingCase' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/system/case/{referencingCaseUuId}',
            'summary' => 'Retrieves the case details',
            'responseClass' => 'Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ReferencingCase',
            'parameters' => array(
                'referencingCaseUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
        ),
        'SubmitContract' => array(
            'httpMethod' => 'POST',
            'uri' => '/referencing/v1/system/securelink/{linkRef}/referee/financial/contract',
            'summary' => 'Stores the response from the contract employer for contract-based employed applicants',
            'parameters' => array(
                'linkRef' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
                'contractDuration' => array(
                    'location' => 'json',
                    'type' => 'integer',
                    'required' => true,
                ),
                'employmentEndDate' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'isIncomeStable' => array(
                    'location' => 'json',
                    'type' => 'boolean',
                    'required' => true,
                ),
                'refereeName' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                ),
                'employmentStartDate' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                ),
                'jobTitle' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                ),
                'employmentType' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'annualIncome' => array(
                    'location' => 'json',
                    'type' => 'numeric',
                    'required' => true,
                ),
                'commission' => array(
                    'location' => 'json',
                    'type' => 'numeric',
                    'required' => true,
                ),
                'jobType' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'refereeIpAddress' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
            ),
        ),
        'SubmitSelfEmployed' => array(
            'httpMethod' => 'POST',
            'uri' => '/referencing/v1/system/securelink/{linkRef}/referee/financial/selfemployed',
            'summary' => 'Stores the response from the accountant for self-employed applicants',
            'parameters' => array(
                'linkRef' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
                'isAccountancyServiceProvided' => array(
                    'location' => 'json',
                    'type' => 'boolean',
                    'required' => true,
                ),
                'durationInAccountancyService' => array(
                    'location' => 'json',
                    'type' => 'integer',
                    'required' => true,
                ),
                'refereeName' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                ),
                'annualIncome' => array(
                    'location' => 'json',
                    'type' => 'numeric',
                    'required' => true,
                ),
                'refereeIpAddress' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
            ),
        ),
        'SubmitEmployed' => array(
            'httpMethod' => 'POST',
            'uri' => '/referencing/v1/system/securelink/{linkRef}/referee/financial/employed',
            'summary' => 'Stores the response from the employer for employed applicants',
            'parameters' => array(
                'linkRef' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
                'employmentStartDate' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                ),
                'employmentType' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'annualIncome' => array(
                    'location' => 'json',
                    'type' => 'numeric',
                    'required' => true,
                ),
                'isIncomeStable' => array(
                    'location' => 'json',
                    'type' => 'boolean',
                    'required' => true,
                ),
                'employmentEndDate' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'refereeName' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                ),
                'jobTitle' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'jobType' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'commission' => array(
                    'location' => 'json',
                    'type' => 'numeric',
                ),
                'contractDuration' => array(
                    'location' => 'json',
                    'type' => 'numeric',
                ),
                'refereeIpAddress' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
            ),
        ),
        'SubmitRetired' => array(
            'httpMethod' => 'POST',
            'uri' => '/referencing/v1/system/securelink/{linkRef}/referee/financial/retired',
            'summary' => 'Stores the response from the pension provider for retired applicants',
            'parameters' => array(
                'linkRef' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
                'annualIncome' => array(
                    'location' => 'json',
                    'type' => 'numeric',
                    'required' => true,
                ),
                'refereeName' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                ),
                'refereeIpAddress' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
            ),
        ),
        'SubmitLetting' => array(
            'httpMethod' => 'POST',
            'uri' => '/referencing/v1/system/securelink/{linkRef}/referee/letting',
            'summary' => 'Stores the response from the letting referee',
            'parameters' => array(
                'linkRef' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
                'hasRentPaidPromptly' => array(
                    'location' => 'json',
                    'type' => 'boolean',
                    'required' => true,
                ),
                'isSatisfied' => array(
                    'location' => 'json',
                    'type' => 'boolean',
                    'required' => true,
                ),
                'isGoodTenant' => array(
                    'location' => 'json',
                    'type' => 'boolean',
                    'required' => true,
                ),
                'refereeName' => array(
                    'location' => 'json',
                    'type' => 'string',
                    'required' => true,
                ),
                'applicantStayDuration' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'monthlyRent' => array(
                    'location' => 'json',
                    'type' => 'numeric',
                ),
                'refereeIpAddress' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
            ),
        ),
        'UpdateApplicant' => array(
            'httpMethod' => 'PUT',
            'uri' => '/referencing/v1/system/securelink/{linkRef}/applicant',
            'summary' => 'Updates the applicant details',
            'parameters' => array(
                'linkRef' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
                'applicationId' => array(
                    'location' => 'json',
                    'type' => 'string',
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
                    'type' => 'integer',
                ),
                'hasEmploymentChanged' => array(
                    'name' => 'employmentChange',
                    'location' => 'json',
                    'type' => 'boolean',
                ),
                'caseId' => array(
                    'location' => 'json',
                    'type' => 'string'
                ),
                'canContactApplicantByPhoneAndPost' => array(
                    'location' => 'json',
                    'type' => 'boolean'
                ),
                'canContactApplicantBySMSAndEmail' => array(
                    'location' => 'json',
                    'type' => 'boolean'
                ),
            ),
        ),
        'GetDocuments' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/system/application/{referencingApplicationUuId}/documents',
            'summary' => 'Get referencing application documents',
            'responseClass' => 'Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\Document',
            'parameters' => array(
                'referencingApplicationUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
        ),
        'GetAgentBranch' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/system/securelink/{linkRef}/agent-branch',
            'summary' => 'Get an agent branch from a valid link',
            'parameters' => array(
                'linkRef' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
        ),
    ),
);
