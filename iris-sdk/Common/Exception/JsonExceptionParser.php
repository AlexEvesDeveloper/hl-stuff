<?php

namespace Barbondev\IRISSDK\Common\Exception;

use Barbondev\IRISSDK\Common\Exception\ExceptionParserInterface;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;

/**
 * Class JsonExceptionParser
 *
 * @package Barbondev\IRISSDK\Common\Exception
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class JsonExceptionParser implements ExceptionParserInterface
{
    /**
     * {@inheritdoc}
     */
    public function parse(RequestInterface $request, Response $response)
    {
        $data = array(
            'type' => $response->isClientError() ? ExceptionParserInterface::TYPE_CLIENT : ExceptionParserInterface::TYPE_SERVER,
            'code' => null,
            'message' => null,
            'errors' => array(),
            'data' => json_decode($response->getBody(true), true),
        );

        if (isset($data['data']['errorCode'])) {
            $data['code'] = $data['data']['errorCode'];
        }

        if (isset($data['data']['message'])) {
            $data['message'] = $data['data']['message'];
        }

        if (isset($data['data']['errors']) && is_array($data['data']['errors'])) {

            $data['errors'] = array();

            foreach ($data['data']['errors'] as $name => $message) {
                $data['errors'][] = array(
                    $name => trim($message),
                );
            }
        }

        return $data;
    }
}