<?php

namespace Barbon\HostedApi\AppBundle\Event;

use Barbon\HostedApi\AppBundle\Form\Reference\Model\ReferencingCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class ConfirmMultiReferenceEvent
 * @package Barbon\HostedApi\AppBundle\Event
 */
class ConfirmMultiReferenceEvent extends GetResponseEvent
{
    /**
     * @var ReferencingCase
     */
    private $case;

    /**
     * @var array
     */
    private $references;

    /**
     * @param HttpKernelInterface $kernel
     * @param RequestStack $requestStack
     */
    public function  __construct(HttpKernelInterface $kernel, RequestStack $requestStack)
    {
        parent::__construct($kernel, $requestStack->getCurrentRequest(), HttpKernelInterface::MASTER_REQUEST);
    }

    /**
     * @return ReferencingCase
     */
    public function getCase()
    {
        return $this->case;
    }

    /**
     * @param ReferencingCase $case
     * @return $this
     */
    public function setCase(ReferencingCase $case)
    {
        $this->case = $case;
        return $this;
    }

    /**
     * @return array
     */
    public function getReferences()
    {
        return $this->references;
    }

    /**
     * @param array $references
     * @return $this
     */
    public function setReferences(array $references)
    {
        $this->references = $references;
        return $this;
    }

}