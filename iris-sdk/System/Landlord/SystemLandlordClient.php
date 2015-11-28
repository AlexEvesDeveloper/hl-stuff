<?php

namespace Barbondev\IRISSDK\System\Landlord;

use Barbondev\IRISSDK\AbstractClient;
use Barbondev\IRISSDK\Common\Client\ClientBuilder;
use Barbondev\IRISSDK\Common\Enumeration\ClientOptions;
use Barbondev\IRISSDK\Common\Exception\DefaultException;
use Barbondev\IRISSDK\Common\Exception\ValidationException;
use Barbondev\IRISSDK\Common\Exception\AuthenticationException;
use Barbondev\IRISSDK\Common\Model\Authorisation;

/**
 * Class SystemLandlordClient
 *
 * @package Barbondev\IRISSDK\System\Landlord
 * @author Paul Swift <paul.swift@barbon.com>
 *
 * @method \Guzzle\Http\Message\Response registerLandlord(array $args = array())
 * @method \Barbondev\IRISSDK\Common\Model\Authorisation authenticate(array $args = array())
 * @method \Guzzle\Http\Message\Response requestPasswordReset(array $args = array())
 * @method \Guzzle\Http\Message\Response updatePassword(array $args = array())
 */
class SystemLandlordClient extends AbstractClient
{
    /**
     * Factory client
     *
     * @param array $config
     * @return SystemLandlordClient
     */
    public static function factory($config = array())
    {
        return ClientBuilder::factory(__CLASS__)
            ->setConfig($config)
            ->setConfigDefaults(array(
                ClientOptions::SERVICE_DESCRIPTION => __DIR__ . '/Resources/system-landlord-v%s.php',
            ))
            ->build()
        ;
    }

    public function authenticate(array $args)
    {
        $request = $this->post(
            '/referencing/v1/system/landlords/authenticate'
        );

        $body = json_encode(array(
            'email' => $args['email'],
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

            $authorisation = new Authorisation();
            $authorisation->setConsumerKey(
                $authData['consumerKey']
            );
            $authorisation->setConsumerSecret(
                $authData['consumerSecret']
            );

        }

        return $authorisation;
    }
}