<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Extension\Type;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class EventAwareCollectionType extends CollectionType
{
    use EventAwareTypeTrait;
}