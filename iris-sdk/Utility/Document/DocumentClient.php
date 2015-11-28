<?php

namespace Barbondev\IRISSDK\Utility\Document;

use Barbondev\IRISSDK\AbstractClient;
use Barbondev\IRISSDK\Common\Client\ClientBuilder;
use Barbondev\IRISSDK\Common\Enumeration\ClientOptions;

/**
 * Class DocumentClient
 *
 * @package Barbondev\IRISSDK\Utility\Document
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 *
 * @method \Barbondev\IRISSDK\Utility\Document\Model\Document getDocument(array $args = array())
 */
class DocumentClient extends AbstractClient
{
    /**
     * Factory client
     *
     * @param array $config
     * @return DocumentClient
     */
    public static function factory($config = array())
    {
        return ClientBuilder::factory(__CLASS__)
            ->setConfig($config)
            ->setConfigDefaults(array(
                ClientOptions::SERVICE_DESCRIPTION => __DIR__ . '/Resources/document-v%s.php',
            ))
            ->build()
        ;
    }
}