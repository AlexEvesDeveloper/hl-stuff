<?php

namespace Barbon\HostedApi\AppBundle\Event;

use Barbon\HostedApi\AppBundle\Form\Reference\Model\ReferencingApplication;
use Barbon\HostedApi\AppBundle\Form\Reference\Model\ReferencingCase;
use Barbon\HostedApi\AppBundle\Form\Reference\Model\ReferencingGuarantor;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class NewGuarantorReferenceEvent
 * @package Barbon\HostedApi\AppBundle\Event
 */
class NewGuarantorReferenceEvent extends Event
{
    /**
     * @var ReferencingCase
     */
    protected $case;

    /**
     * @var ReferencingApplication
     */
    protected $application;

    /**
     * @var ReferencingGuarantor
     */
    protected $guarantor;

    /**
     * @param ReferencingCase $case
     * @param ReferencingApplication $application
     * @param ReferencingGuarantor $guarantor
     */
    public function  __construct(ReferencingCase $case, ReferencingApplication $application, ReferencingGuarantor $guarantor)
    {
        $this->case = $case;
        $this->application = $application;
        $this->guarantor = $guarantor;
    }

    /**
     * @return ReferencingCase
     */
    public function getCase()
    {
        return $this->case;
    }

    /**
     * @return ReferencingApplication
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @return ReferencingGuarantor
     */
    public function getGuarantor()
    {
        return $this->guarantor;
    }
}