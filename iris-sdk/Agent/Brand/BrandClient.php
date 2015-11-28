<?php

namespace Barbondev\IRISSDK\Agent\Brand;

use Barbondev\IRISSDK\AbstractClient;
use Barbondev\IRISSDK\Common\Client\ClientBuilder;
use Barbondev\IRISSDK\Common\Enumeration\ClientOptions;
use Guzzle\Http\Mimetypes;

/**
 * Class BrandClient
 *
 * @package Barbondev\IRISSDK\Agent\Brand
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 *
 * @method \Guzzle\Http\Message\Response updateAgentBrand(array $args = array())
 * @method \Guzzle\Http\Message\Response updateAgentBranchBrand(array $args = array())
 * @method \Barbondev\IRISSDK\Agent\Brand\Model\Logo getAgentBrandLogo(array $args = array())
 * @method \Barbondev\IRISSDK\Agent\Brand\Model\Logo getAgentBranchBrandLogo(array $args = array())
 */
class BrandClient extends AbstractClient
{
    /**
     * Factory client
     *
     * @param array $config
     * @return BrandClient
     */
    public static function factory($config = array())
    {
        return ClientBuilder::factory(__CLASS__)
            ->setConfig($config)
            ->setConfigDefaults(array(
                ClientOptions::SERVICE_DESCRIPTION => __DIR__ . '/Resources/brand-v%s.php',
            ))
            ->build()
        ;
    }

    /**
     * Update an agent brand logo
     *
     * Example arguments
     * <code>
     * Array (
     *     file: /tmp/file
     *     fileName: image.jpeg
     *     description: My lovely picture
     * )
     * </code>
     *
     * @param array $args
     * @return \Guzzle\Http\Message\Response
     */
    public function updateAgentBrandLogo(array $args = array())
    {
        $request = $this->put('/referencing/v1/agent/brand/logo');

        $request->setPostField('fileName', $args['fileName']);
        $request->setPostField('description', $args['description']);
        $request->setPostField('category', '4'); // todo: Change with API spec, see TBL (being removed)

        $request->addPostFile(
            'file',
            $args['file'],
            Mimetypes::getInstance()->fromFilename(basename($args['file']))
        );

        return $request->send();
    }

    /**
     * Update an agent branch brand logo
     *
     * Example arguments
     * <code>
     * Array (
     *     agentBranchUuId: 1053a4b8-bfa5-94af-bf1b-d842d44f8
     *     file: /tmp/file
     *     fileName: image.jpeg
     *     description: My lovely picture
     * )
     * </code>
     *
     * @param array $args
     * @return \Guzzle\Http\Message\Response
     */
    public function updateAgentBranchBrandLogo(array $args = array())
    {
        $request = $this->put(sprintf('/referencing/v1/agent/branch/%s/brand/logo', $args['agentBranchUuId']));

        $request->setPostField('fileName', $args['fileName']);
        $request->setPostField('description', $args['description']);
        $request->setPostField('category', '4'); // todo: Change with API spec, see TBL (being removed)

        $request->addPostFile(
            'file',
            $args['file'],
            Mimetypes::getInstance()->fromFilename(basename($args['file']))
        );

        return $request->send();
    }
}
