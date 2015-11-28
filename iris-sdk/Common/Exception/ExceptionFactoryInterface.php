<?php

namespace Barbondev\IRISSDK\Common\Exception;

use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;

/**
 * Class ExceptionFactoryInterface
 *
 * @package Barbondev\IRISSDK\Common\Exception
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
interface ExceptionFactoryInterface
{
    /**
     * Returns an IRIS specific exception
     *
     * @param RequestInterface $request
     * @param Response $response
     * @return \Barbondev\IRISSDK\Common\Exception\IRISExceptionInterface
     */
    public function fromResponse(RequestInterface $request, Response $response);
}