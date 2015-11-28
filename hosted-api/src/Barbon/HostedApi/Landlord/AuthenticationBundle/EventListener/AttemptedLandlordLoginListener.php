<?php

namespace Barbon\HostedApi\Landlord\AuthenticationBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Cache\Cache;
use Barbon\HostedApi\AppBundle\Service\Authentication\MacManager;
use Barbon\HostedApi\AppBundle\Exception\InvalidMacException;

/**
 * Listener to capture any attempted login from the landlord login form
 *
 * @author Alex Eves <alex.eves@barbon.com>
 */
class AttemptedLandlordLoginListener
{
    /**
     * @var MacManager
     */
    private $macManager;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * Constructor
     *
     * @param IrisClient $irisClient
     * @param MacManager $macManager
     */
    public function __construct(MacManager $macManager, Cache $cache)
    {
        $this->macManager = $macManager;
        $this->cache = $cache;
    }
    /**
     * Triggered on every request.
     *
     * @param GetResponseEvent $event
     *
     * @return void
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        // do nothing if this is not an attempted landlord login
        if ( ! $request->request->has('landlord_login_submit')) {
            return;
        } 

        if (($mac = $this->macManager->getFromSession()) === null) {
            throw new InvalidMacException('No MAC stored in the session');
        }

        if ($this->cache->fetch('mac-'.$mac) === false) {
            throw new InvalidMacException('Doctrine cache has expired');
        }
    }
}
