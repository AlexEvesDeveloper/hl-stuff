<?php

namespace Barbondev\IRISSDK\System\Agent;

use Barbondev\IRISSDK\AbstractClient;
use Barbondev\IRISSDK\Common\Client\ClientBuilder;
use Barbondev\IRISSDK\Common\Enumeration\ClientOptions;
use Barbondev\IRISSDK\Common\Exception\DefaultException;
use Barbondev\IRISSDK\Common\Exception\ValidationException;
use Barbondev\IRISSDK\Common\Exception\AuthenticationException;
use Barbondev\IRISSDK\System\Agent\Model\BranchAuthorisation;

/**
 * Class SystemAgentClient
 *
 * @package Barbondev\IRISSDK\System\Agent
 * @author Paul Swift <paul.swift@barbon.com>
 *
 * @method \Guzzle\Http\Message\Response requestPasswordReset(array $args = array())
 * @method \Guzzle\Http\Message\Response updatePassword(array $args = array())
 */
class SystemAgentClient extends AbstractClient
{
    /**
     * Factory client
     *
     * @param array $config
     * @return SystemAgentClient
     */
    public static function factory($config = array())
    {
        return ClientBuilder::factory(__CLASS__)
            ->setConfig($config)
            ->setConfigDefaults(array(
                ClientOptions::SERVICE_DESCRIPTION => __DIR__ . '/Resources/system-agent-v%s.php',
            ))
            ->build()
            ;
    }

    /**
     * Authenticate an agent
     *
     * @param array $args
     * @return BranchAuthorisation
     * @throws \Barbondev\IRISSDK\Common\Exception\AuthenticationException
     */
    public function authenticate(array $args)
    {
        $request = $this->post(
            sprintf(
                '/referencing/v1/system/agentbranch/%s/authenticate',
                $args['agentSchemeNumber']
            )
        );

        $body = json_encode(array(
            'username' => $args['username'],
            'password' => $args['password']
        ));
        $contentType = 'application/json';

        $request->setBody($body, $contentType);

        try {
            $result = $request->send();
        }
        catch (DefaultException $e) {
            throw new AuthenticationException($e->getMessage());
        }
        catch (ValidationException $e) {
            throw new AuthenticationException($e->getMessage());
        }

        if (200 != $result->getStatusCode()) {

            throw new AuthenticationException(
                'Error status ' . $result->getStatusCode()
            );

        } else {

            // Authentication successful, instantiate authorization object with
            //   returned JSON data

            $authData = $result->json();

            $authorisation = new BranchAuthorisation();
            $authorisation
                ->setConsumerKey($authData['consumerKey'])
                ->setConsumerSecret($authData['consumerSecret'])
                ->setAgentBranchUuid($authData['agentBranchId'])
            ;

        }

        return $authorisation;
    }
}