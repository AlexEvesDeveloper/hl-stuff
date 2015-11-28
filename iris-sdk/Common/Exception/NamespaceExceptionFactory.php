<?php

namespace Barbondev\IRISSDK\Common\Exception;

use Barbondev\IRISSDK\Common\Exception\ExceptionFactoryInterface;
use Barbondev\IRISSDK\Common\Exception\ExceptionParserInterface;
use Barbondev\IRISSDK\Common\Exception\InternalServerErrorException;
use Barbondev\IRISSDK\Common\Exception\ValidationException;
use Barbondev\IRISSDK\Common\Exception\ForbiddenException;
use Barbondev\IRISSDK\Common\Exception\NotFoundException;
use Barbondev\IRISSDK\Common\Utility\ValidationErrorParser;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use Psr\Log\LoggerInterface;

/**
 * Class NamespaceExceptionFactory
 *
 * @package Barbondev\IRISSDK\Common\Exception
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class NamespaceExceptionFactory implements ExceptionFactoryInterface
{
    /**
     * @var ExceptionParserInterface
     */
    protected $parser;

    /**
     * @var string
     */
    protected $defaultExceptionClass;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Constructor
     *
     * @param ExceptionParserInterface $parser
     * @param string $defaultExceptionClass
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        ExceptionParserInterface $parser,
        $defaultExceptionClass = 'Barbondev\IRISSDK\Common\Exception\DefaultException',
        LoggerInterface $logger = null
    ) {
        $this->parser = $parser;
        $this->defaultExceptionClass = $defaultExceptionClass;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function fromResponse(RequestInterface $request, Response $response)
    {
        if ($this->logger) {

            $this->logger->error('Request: {request}', array(
                'request' => (string) $request,
            ));

            $this->logger->error('Response: {response}', array(
                'response' => (string) $response,
            ));
        }

        $parts = $this
            ->parser
            ->parse($request, $response)
        ;

        // Default Exception
        $exception = new $this->defaultExceptionClass(
            $parts['message'],
            $parts['code']
        );

        if (preg_match('/5[0-9]{2}/', $response->getStatusCode())) {

            // For 500 range exceptions (internal server error)
            $exception = new InternalServerErrorException(
                $parts['message'],
                $parts['code']
            );

        }
        elseif ('400' == $response->getStatusCode() || '422' == $response->getStatusCode()) {

            // Validation exception
            $exception = new ValidationException(
                $parts['message'],
                $parts['code']
            );

            if (!empty($parts['errors']) && is_array($parts['errors'])) {

                $errors = array();

                foreach ($parts['errors'] as $error) {
                    $errors[key($error)] = current($error);
                }

                $parser = new ValidationErrorParser();

                $exception
                    ->setErrors($parser->parse($errors))
                ;
            }
        }
        elseif ('403' == $response->getStatusCode()) {

            // For 403 forbidden exceptions
            $exception = new ForbiddenException(
                $parts['message'],
                $parts['code']
            );

        }
        elseif ('404' == $response->getStatusCode()) {

            // For 404 not found exceptions
            $exception = new NotFoundException(
                $parts['message'],
                $parts['code']
            );

        }
        elseif (
            '406' == $response->getStatusCode() &&
            '1012' == $parts['code']
        ) {

            // For 406 Not Acceptable exceptions with a 1012 already submitted error code
            $exception = new AlreadySubmittedException(
                $parts['message'],
                $parts['code']
            );
        }
        elseif (preg_match('/4[0-9]{2}/', $response->getStatusCode())) {

            // For 400 range exceptions (auth, etc.)

            // TODO: Add exception

        }
        elseif (preg_match('/3[0-9]{2}/', $response->getStatusCode())) {

            // For 300 range exceptions (redirection, etc.)

            // TODO: Add exception

        }

        $exception
            ->setRequest($request)
            ->setResponse($response)
        ;

        return $exception;
    }
}