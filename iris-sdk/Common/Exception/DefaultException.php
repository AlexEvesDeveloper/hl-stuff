<?php

namespace Barbondev\IRISSDK\Common\Exception;

use Barbondev\IRISSDK\Common\Exception\DefaultExceptionInterface;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use Guzzle\Common\Exception\RuntimeException;

/**
 * Class DefaultException
 *
 * @package Barbondev\IRISSDK\Common\Exception
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class DefaultException extends RuntimeException implements DefaultExceptionInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * {@inheritdoc}
     */
    public function setRequest(RequestInterface $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * {@inheritdoc}
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse()
    {
        return $this->response;
    }
}