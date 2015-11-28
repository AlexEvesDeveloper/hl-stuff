<?php

/**
 * Product Service Description
 *
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
return array(
    'name' => 'Product',
    'description' => 'Referencing products',
    'operations' => array(
        'GetProducts' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/individual/products',
            'summary' => 'Get all referencing products for the current agent',
            'responseClass' => 'Barbondev\IRISSDK\IndividualApplication\Product\Model\Product',
            'parameters' => array(
                'rentGuaranteeOfferingType' => array(
                    'location' => 'query',
                    'type' => 'integer',
                    'required' => true,
                ),
                'propertyLettingType' => array(
                    'location' => 'query',
                    'type' => 'integer',
                    'required' => true,
                ),
            ),
        ),
        'GetProductPrice' => array(
            'httpMethod' => 'GET',
            'uri' => '/referencing/v1/individual/product/price',
            'summary' => 'Get price information for a single product',
            'responseClass' => 'Barbondev\IRISSDK\IndividualApplication\Product\Model\ProductPrice',
            'parameters' => array(
                'productId' => array(
                    'location' => 'query',
                    'type' => 'integer',
                    'required' => true,
                ),
                'agentSchemeNumber' => array(
                    'location' => 'query',
                    'type' => 'integer',
                    'required' => true,
                ),
                'propertyLetType' => array(
                    'location' => 'query',
                    'type' => 'integer',
                    'required' => true,
                ),
                'rentGuaranteeOfferingType' => array(
                    'location' => 'query',
                    'type' => 'integer',
                    'required' => true,
                ),
                'shareOfRent' => array(
                    'location' => 'query',
                    'type' => 'numeric',
                    'required' => true,
                ),
                'policyLengthInMonths' => array(
                    'location' => 'query',
                    'type' => 'integer',
                    'required' => true,
                ),
                'guarantorSequenceNumber' => array(
                    'location' => 'query',
                    'type' => 'integer',
                    'required' => true,
                ),
                'isRenewal' => array(
                    'location' => 'query',
                    'type' => 'integer',
                    'required' => true,
                ),
            ),
        ),
    ),
);