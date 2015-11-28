<?php

namespace Barbondev\IRISSDK\Common\Exception;

use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;

/**
 * Class DefaultExceptionInterface
 *
 * @package Barbondev\IRISSDK\Common\Exception
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
interface DefaultExceptionInterface
{
    /**
     * Set request
     *
     * @param RequestInterface $request
     * @return $this
     */
    public function setRequest(RequestInterface $request);

    /**
     * Get request
     *
     * @return RequestInterface
     */
    public function getRequest();

    /**
     * Set response
     *
     * @param Response $response
     * @return $this
     */
    public function setResponse(Response $response);

    /**
     * Get response
     *
     * @return Response
     */
    public function getResponse();
}