<?php

namespace Iris\Referencing\Submission;

use Barbondev\IRISSDK\Common\ClientRegistry\Context\ContextInterface;
use Barbondev\IRISSDK\Common\Exception\DefaultException;

/**
 * Class AbstractSubmitter
 *
 * @package Iris\Referencing\Submission
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
abstract class AbstractSubmitter
{
    /**
     * @var ContextInterface
     */
    protected $context;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     */
    public function __construct(ContextInterface $context)
    {
        $this->context = $context;
    }

    /**
     * Handle the exception in the event of failure to submit. Either
     * Redirects to a failure message page or throws a \Zend_Exception
     *
     * @param DefaultException $exception
     * @param string|null $redirectToUrlOnFailure
     * @throws \Zend_Exception
     * @return void
     */
    protected function handleErrorOnSubmission(DefaultException $exception, $redirectToUrlOnFailure)
    {
        // todo: this logging is temporary, so we can diagnose
        error_log(sprintf('SUBMISSION FAILURE: REQ: %s', (string) $exception->getRequest()));
        error_log(sprintf('SUBMISSION FAILURE: RES: %s', (string) $exception->getResponse()));

        if (null !== $redirectToUrlOnFailure) {
            $this->redirectToUrl(str_replace('{error_code}', $exception->getCode(), $redirectToUrlOnFailure));
            exit;
        }

        throw new \Zend_Exception(sprintf('Failed to submit case/application: %s', (string) $exception->getResponse()));
    }

    /**
     * Crude way of redirecting
     *
     * @param string $url
     */
    protected function redirectToUrl($url)
    {
        header(sprintf("Location: %s\n", $url));
    }
}