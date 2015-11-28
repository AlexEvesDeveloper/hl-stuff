<?php

namespace Barbon\HostedApi\SecurityBundle\Authentication\Handler;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Custom handler to detect idle sessions, and logout user if necessary
 *
 * @author Alex Eves <alex.eves@barbon.com>
 */ 
class IdleSessionHandler
{
    protected $session;
    protected $securityContext;
    protected $router;
    protected $authorizationChecker;
    protected $maxIdleTime;

    /**
     * Constructor.
     *
     * @param SessionInterface $session
     * @param SecurityContextInterface $securityContext
     * @param RouterInterface $router
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param integer $maxIdleTime
     *
     */
    public function __construct(
        SessionInterface $session, 
        SecurityContextInterface $securityContext, 
        RouterInterface $router, 
        AuthorizationCheckerInterface $authorizationChecker, 
        $maxIdleTime
    ) {
        $this->session = $session;
        $this->securityContext = $securityContext;
        $this->router = $router;
        $this->authorizationChecker = $authorizationChecker;
        $this->maxIdleTime = $maxIdleTime;   
    }

    /**
     * Triggered on every request.
     *
     * See if the time since last active is larger than the maximum idle time that is allowed
     *
     * @param GetResponseEvent $event
     *
     * @return void
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        // only need to evalute the original request from the browser
        if ( ! $event->isMasterRequest()) {
            return;
        }

        // do nothing if user is not logged in
        if ($this->securityContext->getToken() !== null
            && ! $this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return;
        }

        // do nothing if the post contains a systemKey and systemSecret
        if ($event->getRequest()->request->has('systemKey') &&
            $event->getRequest()->request->has('systemSecret')) {
            return;
        }

        // assume an infinite session time is allowed if the parameter is <= 0 or not set, by doing nothing
        if ($this->maxIdleTime <= 0 || $this->maxIdleTime === null) {
            return;
        }

        // just incase a session hasn't started, otherwise it simply returns true
        $this->session->start();

        // have we been inactive for longer than the allowed time limit..?
        $timeSinceActive = time() - $this->session->getMetadataBag()->getLastUsed();

        if ($timeSinceActive > $this->maxIdleTime) {



            // log the user out
            $this->securityContext->setToken(null);

            // issue a message for the login page
            $this->session->getFlashBag()->set('session_timeout', 'Your session has expired due to inactivity.');

            // redirect to login
            $event->setResponse(
                new RedirectResponse($this->router->generate('barbon_hostedapi_app_index_index'))
            );
        }
    }
}
