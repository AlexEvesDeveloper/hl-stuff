<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Extension\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;

class EventAwareTextType extends TextType
{
    use EventAwareTypeTrait;
}