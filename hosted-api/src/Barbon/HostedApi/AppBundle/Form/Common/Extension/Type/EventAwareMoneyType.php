<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Extension\Type;

use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class EventAwareMoneyType extends MoneyType
{
    use EventAwareTypeTrait;
}