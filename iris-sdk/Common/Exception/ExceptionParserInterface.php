<?php

namespace Barbondev\IRISSDK\Common\Exception;

use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;

/**
 * Class ExceptionParserInterface
 *
 * @package Barbondev\IRISSDK\Common\Exception
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
interface ExceptionParserInterface
{
    /**
     * Exception type constants
     */
    const TYPE_CLIENT = 'client';
    const TYPE_SERVER = 'server';

    /**
     * Parse a server exception into an array containing
     * the following keys
     *
     * - type:      Client or server exception
     * - code:      System error code
     * - message:   System error message
     * - errors:    Array of child errors relating to specific request params
     * - data:      Parsed set of error data from response, e.g. array
     *
     * @param RequestInterface $request
     * @param Response $response
     * @return array
     */
    public function parse(RequestInterface $request, Response $response);
}