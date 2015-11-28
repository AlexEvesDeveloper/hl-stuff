<?php

namespace Iris\Utility\Product;

use Barbondev\IRISSDK\Common\ClientRegistry\Context\ContextInterface;
use Barbondev\IRISSDK\Common\Exception\NotFoundException;
use Desarrolla2\Cache\Cache;
use Iris\Utility\Product\Exception\ProductPriceNotFoundException;

/**
 * Class ProductPrice
 *
 * @package Iris\Utility\Product
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class ProductPrice implements ProductPriceInterface
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
    public function getProductPrice(
        $agentSchemeNumber,
        $productId,
        $propertyLetType,
        $rentGuaranteeOfferingType,
        $shareOfRent,
        $policyLengthInMonths
    ) {

        $parameters = array(
            'productId' => (int)$productId,
            'agentSchemeNumber' => (int)$agentSchemeNumber,
            'propertyLetType' => (int)$propertyLetType,
            'rentGuaranteeOfferingType' => (int)$rentGuaranteeOfferingType,
            'shareOfRent' => (float)$shareOfRent,
            'policyLengthInMonths' => (int)($policyLengthInMonths ?: 0),
            'guarantorSequenceNumber' => (int)0,
            'isRenewal' => (int)false,
        );

        // Always merge context parameters as this cache is unique per agent branch
        $cacheKey = $this->buildCacheKey(array_merge($parameters, $this->context->getParameters()));

        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        /** @var \Barbondev\IRISSDK\IndividualApplication\Product\Model\ProductPrice $productPrice */
        try {
            $productPrice = $this->context->getProductClient()->getProductPrice($parameters);
        }
        catch (NotFoundException $e) {
            throw new ProductPriceNotFoundException(
                sprintf('Product price not found using parameters: ', print_r($parameters, true))
            );
        }

        $this->cache->set($cacheKey, $productPrice, 120); // Short TTL for this cache

        return $productPrice;
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
}