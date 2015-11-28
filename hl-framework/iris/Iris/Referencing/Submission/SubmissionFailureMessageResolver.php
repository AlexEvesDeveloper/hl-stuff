<?php

namespace Iris\Referencing\Submission;

/**
 * Class SubmissionFailureMessageResolver
 *
 * @package Iris\Referencing\Submission
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class SubmissionFailureMessageResolver
{
    /**
     * IRIS error response codes
     * @todo: move these to a namespace in the IRIS SDK
     */
    const IRIS_ERROR_AGENT_NOT_PERMITTED = 1001;
    const IRIS_ERROR_VALIDATION_FAILURE = 1011;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * Constructor
     *
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Resolves an IRIS error code into a customer friendly message
     *
     * @param int $irisErrorCode
     * @return string
     */
    public function getFailureMessage($irisErrorCode)
    {
        $message = $this->twig->render('general.html.twig');

        switch ($irisErrorCode) {

            case self::IRIS_ERROR_AGENT_NOT_PERMITTED:
                $message = $this->twig->render('agent-not-permitted.html.twig');
                break;

            case self::IRIS_ERROR_VALIDATION_FAILURE:
                $message = $this->twig->render('validation-failure.html.twig');
                break;
        }

        return $message;
    }
}