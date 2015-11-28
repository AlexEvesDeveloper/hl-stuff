<?php

namespace Barbon\HostedApi\AppBundle\Service\Iris;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class IrisClientFactory
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var Container
     */
    protected $container;

    const SYSTEM_USER = 'system';
    const AGENT_USER = 'agent';
    const LANDLORD_USER = 'landlord';
    const USER_TYPES = 'system|agent|landlord';

    /**
     * Constructor
     *
     * @param RequestStack $requestStack
     * @param Container $container
     */
    public function __construct(RequestStack $requestStack, Container $container)
    {
        $this->requestStack = $requestStack;
        $this->container = $container;
    }

    /**
     * Get an IrisClient object based on the HTTP Request
     *
     * @return IrisClient
     */
    public function getIrisClient()
    {
        switch ($this->getUserTypeByRequestUri()) {
            case self::SYSTEM_USER:
                return $this->container->get('barbon.iris_rest_client.client.iris_system_client');
            case self::AGENT_USER:
                return $this->container->get('barbon.iris_rest_client.client.iris_agent_client');
            case self::LANDLORD_USER:
                return $this->container->get('barbon.iris_rest_client.client.iris_landlord_client');
            default:
                // for some reason, the dev profiler routes come in here
                return $this->container->get('barbon.iris_rest_client.client.iris_system_client');
        } 
    }

    /**
     * Determine the type of User that is needed for the current request, based on the URL 
     *
     * @return string
     */
    private function getUserTypeByRequestUri()
    {
        if (is_null($this->requestStack->getCurrentRequest())) {
            return;
        }
        
        $urlStack = explode('/', $this->requestStack->getCurrentRequest()->getRequestUri());
        $userTypes = explode('|', self::USER_TYPES);

        foreach ($urlStack as $urlStub) {
            if (in_array($urlStub, $userTypes)) {
                return $urlStub;
            }
        }
    }
}
