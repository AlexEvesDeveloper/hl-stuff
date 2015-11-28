<?php

namespace Barbon\HostedApi\AppBundle\Event\Listener;

use Barbon\HostedApi\AppBundle\Event\NewReferenceFinishEvent;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class IndividualPaymentListener
 * @package Barbon\HostedApi\AppBundle\Event\Listener
 */
class SessionCleanUpListener
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @param NewReferenceFinishEvent $event
     */
    public function cleanUpSession(NewReferenceFinishEvent $event)
    {
        // todo: remove hardcoded key names for a more elegant solution.
        if ($this->session->has('submitted-case')) {
            $this->session->remove('submitted-case');
        }

        if ($this->session->has('submitted-guarantor')) {
            $this->session->remove('submitted-guarantor');
        }
    }
}