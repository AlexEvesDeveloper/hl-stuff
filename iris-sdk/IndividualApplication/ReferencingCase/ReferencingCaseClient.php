<?php

namespace Barbondev\IRISSDK\IndividualApplication\ReferencingCase;

use Barbondev\IRISSDK\AbstractClient;
use Barbondev\IRISSDK\Common\Client\ClientBuilder;
use Barbondev\IRISSDK\Common\Enumeration\ClientOptions;

/**
 * Class ReferencingCaseClient
 *
 * @package Barbondev\IRISSDK\IndividualApplication\ReferencingCase
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 *
 * @method \Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ReferencingCase getReferencingCase(array $args = array())
 * @method \Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ReferencingCase createReferencingCase(array $args = array())
 * @method \Guzzle\Common\Collection getApplications(array $args = array())
 * @method \Guzzle\Http\Message\Response updateReferencingCase(array $args = array())
 * @method \Guzzle\Http\Message\Response submitReferencingCase(array $args = array())
 */
class ReferencingCaseClient extends AbstractClient
{
    /**
     * Factory client
     *
     * @param array $config
     * @return ReferencingCaseClient
     */
    public static function factory($config = array())
    {
        return ClientBuilder::factory(__CLASS__)
            ->setConfig($config)
            ->setConfigDefaults(array(
                ClientOptions::SERVICE_DESCRIPTION => __DIR__ . '/Resources/referencing-case-v%s.php',
            ))
            ->build()
        ;
    }
}