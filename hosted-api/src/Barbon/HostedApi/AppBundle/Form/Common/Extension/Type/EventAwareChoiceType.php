<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Extension\Type;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EventAwareChoiceType extends ChoiceType
{
    use EventAwareTypeTrait;
}