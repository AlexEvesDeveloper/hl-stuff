<?php

namespace Barbon\HostedApi\AppBundle\Event;

use Barbon\HostedApi\AppBundle\Form\Reference\Model\ReferencingCase;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class NewReferenceSuccessEvent
 * @package Barbon\HostedApi\AppBundle\Event
 */
class NewReferenceSuccessEvent extends Event
{
    /**
     * @var ReferencingCase
     */
    protected $case;

    /**
     * @param ReferencingCase $case
     */
    public function  __construct(ReferencingCase $case)
    {
        $this->case = $case;
    }

    /**
     * @return ReferencingCase
     */
    public function getCase()
    {
        return $this->case;
    }
}