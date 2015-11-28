<?php

namespace Barbondev\IRISSDK\IndividualApplication\Product;

use Barbondev\IRISSDK\AbstractClient;
use Barbondev\IRISSDK\Common\Client\ClientBuilder;
use Barbondev\IRISSDK\Common\Enumeration\ClientOptions;

/**
 * Class ProductClient
 *
 * @package Barbondev\IRISSDK\IndividualApplication\Product
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 *
 * @method \Guzzle\Common\Collection getProducts(array $args = array())
 * @method \Barbondev\IRISSDK\IndividualApplication\Product\Model\ProductPrice getProductPrice(array $args = array())
 */
class ProductClient extends AbstractClient
{
    /**
     * Factory client
     *
     * @param array $config
     * @return ProductClient
     */
    public static function factory($config = array())
    {
        return ClientBuilder::factory(__CLASS__)
            ->setConfig($config)
            ->setConfigDefaults(array(
                ClientOptions::SERVICE_DESCRIPTION => __DIR__ . '/Resources/product-v%s.php',
            ))
            ->build()
        ;
    }
}