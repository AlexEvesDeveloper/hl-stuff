<?php

namespace Barbondev\IRISSDK\Common\Exception;

use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use Guzzle\Service\Exception\ValidationException as GuzzleValidationException;
use Barbondev\IRISSDK\Common\Exception\DefaultExceptionInterface;

/**
 * Class ValidationException
 *
 * @package Barbondev\IRISSDK\Common\Exception
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class ValidationException extends GuzzleValidationException implements DefaultExceptionInterface
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
     * Get parameter error message by name
     *
     * @param string $name
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getErrorMessageByName($name)
    {
        if (!isset($this->errors[$name])) {
            throw new \InvalidArgumentException(
                sprintf('Property "%s" cannot be found in validation errors', $name));
        }

        return $this->errors[$name];
    }

    /**
     * Returns TRUE if exception has property errors
     *
     * @return bool
     */
    public function hasErrors()
    {
        return (is_array($this->errors)) && count($this->errors);
    }

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