<?php

namespace Barbon\HostedApi\AppBundle\Event;

use Barbon\HostedApi\AppBundle\Form\Reference\Model\ReferencingGuarantor;
use Barbon\HostedApi\AppBundle\Form\Reference\Model\ReferencingCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class ConfirmGuarantorReferenceEvent
 * @package Barbon\HostedApi\AppBundle\Event
 */
class ConfirmGuarantorReferenceEvent extends GetResponseEvent
{
    /**
     * @var ReferencingCase
     */
    private $case;

    /**
     * @var ReferencingGuarantor
     */
    private $reference;

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
     * @return ReferencingGuarantor
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param ReferencingGuarantor $reference
     * @return $this
     */
    public function setReference(ReferencingGuarantor $reference)
    {
        $this->reference = $reference;
        return $this;
    }
}