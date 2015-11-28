<?php

/**
 * Referencing Case Service Description
 *
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
return array(
    'name' => 'Referencing Case',
    'description' => 'Referencing case for an individual',
    'operations' => array(
        'SubmitReferencingCase' => array(
            'httpMethod' => 'PUT',
            'uri' => '/referencing/v1/individual/case/{referencingCaseUuId}/submit',
            'summary' => 'Submit existing individual referencing case',
            'parameters' => array(
                'referencingCaseUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
        ),
        'GetReferencingCase' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/individual/case/{referencingCaseUuId}',
            'summary' => 'Get existing individual referencing case',
            'responseClass' => 'Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ReferencingCase',
            'parameters' => array(
                'referencingCaseUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
        ),
        'GetApplications' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/individual/case/{referencingCaseUuId}/applications',
            'summary' => 'Get applications for a case',
            'responseClass' => 'Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication',
            'parameters' => array(
                'referencingCaseUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
        ),
        'CreateReferencingCase' => array(
            'httpMethod' => 'POST',
            'uri' => '/referencing/v1/individual/case',
            'summary' => 'Create new individual referencing case',
            'responseClass' => 'Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ReferencingCase',
            'parameters' => array(
                'address' => array(
                    'location' => 'json',
                    'type' => 'object',
                    'instanceOf' => 'Barbondev\IRISSDK\Common\Model\Address',
                ),
                'totalRent' => array(
                    'location' => 'json',
                    'type' => 'numeric',
                ),
                'tenancyStartDate' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'tenancyTerm' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'numberOfTenants' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'propertyType' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'propertyLetType' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'rentGuaranteeOfferingType' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'prospectiveLandlord' => array(
                    'location' => 'json',
                    'type' => 'object',
                    'instanceOf' => 'Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ProspectiveLandlord',
                ),
                'propertyBuiltInRangeType' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'numberOfBedrooms' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
            ),
        ),
        'UpdateReferencingCase' => array(
            'httpMethod' => 'PUT',
            'uri' => '/referencing/v1/individual/case/{referencingCaseUuId}',
            'summary' => 'Create new individual referencing case',
            'parameters' => array(
                'referencingCaseUuId' => array(
                    'location' => 'uri',
                    'type' => 'string',
                ),
                'address' => array(
                    'location' => 'json',
                    'type' => 'object',
                    'instanceOf' => 'Barbondev\IRISSDK\Common\Model\Address',
                ),
                'totalRent' => array(
                    'location' => 'json',
                    'type' => 'numeric',
                ),
                'tenancyStartDate' => array(
                    'location' => 'json',
                    'type' => 'string',
                ),
                'tenancyTerm' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'numberOfTenants' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'propertyType' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'propertyLetType' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'rentGuaranteeOfferingType' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'prospectiveLandlord' => array(
                    'location' => 'json',
                    'type' => 'object',
                    'instanceOf' => 'Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ProspectiveLandlord',
                ),
                'propertyBuiltInRangeType' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
                'numberOfBedrooms' => array(
                    'location' => 'json',
                    'type' => 'integer',
                ),
            ),
        ),
    ),
);
