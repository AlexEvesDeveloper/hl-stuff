<?php

namespace Iris\Utility\Product;

/**
 * Interface ProductPriceInterface
 *
 * @package Iris\Utility\Product
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
interface ProductPriceInterface
{
    /**
     * Get product price
     *
     * @param int $agentSchemeNumber
     * @param int $productId
     * @param int $propertyLetType
     * @param int $rentGuaranteeOfferingType
     * @param float $shareOfRent
     * @param int $policyLengthInMonths
     * @return \Barbondev\IRISSDK\IndividualApplication\Product\Model\ProductPrice
     * @throws \Iris\Utility\Product\Exception\ProductPriceNotFoundException
     */
    public function getProductPrice(
        $agentSchemeNumber,
        $productId,
        $propertyLetType,
        $rentGuaranteeOfferingType,
        $shareOfRent,
        $policyLengthInMonths
    );
}