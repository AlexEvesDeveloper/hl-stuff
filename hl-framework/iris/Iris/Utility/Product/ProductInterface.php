<?php

namespace Iris\Utility\Product;

use Barbondev\IRISSDK\IndividualApplication\Product\Model\Product as ProductModel;

/**
 * Interface ProductInterface
 *
 * @package Iris\Utility\Product
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
interface ProductInterface
{
    /**
     * Get products
     *
     * @param int $rentGuaranteeOfferingType
     * @param int $propertyLettingType
     * @return \Guzzle\Common\Collection
     */
    public function getProducts($rentGuaranteeOfferingType, $propertyLettingType);

    /**
     * Get a single product based on product ID
     *
     * @param int $rentGuaranteeOfferingType
     * @param int $propertyLettingType
     * @param int $productId
     * @return ProductModel
     * @throws \Iris\Utility\Product\Exception\ProductNotFoundException
     */
    public function getProduct($rentGuaranteeOfferingType, $propertyLettingType, $productId);
}