<?php

namespace Barbon\HostedApi\AppBundle\Event;

use Barbon\HostedApi\AppBundle\Form\Reference\Model\ReferencingCase;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class NewMultiReferenceEvent
 * @package Barbon\HostedApi\AppBundle\Event
 */
class NewMultiReferenceEvent extends Event
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