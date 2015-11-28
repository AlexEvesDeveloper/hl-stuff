<?php

namespace Iris\Utility\Product;

use Barbondev\IRISSDK\Common\ClientRegistry\Context\ContextInterface;
use Desarrolla2\Cache\Cache;
use Iris\Utility\Product\Exception\ProductNotFoundException;

/**
 * Class Product
 *
 * @package Iris\Utility\Product
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class Product implements ProductInterface
{
    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param \Desarrolla2\Cache\Cache $cache
     */
    public function __construct(ContextInterface $context, Cache $cache)
    {
        $this->context = $context;
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function getProducts($rentGuaranteeOfferingType, $propertyLettingType)
    {
        $parameters = array(
            'rentGuaranteeOfferingType' => (int) $rentGuaranteeOfferingType,
            'propertyLettingType' => (int) $propertyLettingType,
        );

        // Always merge context parameters as this cache is unique per agent branch
        $cacheKey = $this->buildCacheKey(array_merge($parameters, $this->context->getParameters()));

        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        /** @var \Guzzle\Common\Collection $products */
        $products = $this->context->getProductClient()->getProducts($parameters);

        $this->cache->set($cacheKey, $products, 500); // Longer TTL for products

        return $products;
    }

    /**
     * Build cache key
     *
     * @param array $parameters
     * @return string
     */
    private function buildCacheKey(array $parameters)
    {
        return sha1(implode('', $parameters) . __CLASS__);
    }

    /**
     * {@inheritdoc}
     */
    public function getProduct($rentGuaranteeOfferingType, $propertyLettingType, $productId)
    {
        $products = $this->getProducts($rentGuaranteeOfferingType, $propertyLettingType);

        /** @var \Barbondev\IRISSDK\IndividualApplication\Product\Model\Product $product */
        foreach ($products as $product) {
            if ($product->getId() == $productId) {
                return $product;
            }
        }

        throw new ProductNotFoundException(
            sprintf(
                'Product not found given the rentGuaranteeOfferingType %s, propertyLettingType %s and productId %s',
                $rentGuaranteeOfferingType,
                $propertyLettingType,
                $productId
            )
        );
    }
}