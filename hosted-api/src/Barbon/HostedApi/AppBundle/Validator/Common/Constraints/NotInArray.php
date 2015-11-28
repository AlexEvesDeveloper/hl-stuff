<?php

namespace Barbon\HostedApi\AppBundle\Validator\Common\Constraints;

use Symfony\Component\Validator\Constraint;

class NotInArray extends Constraint
{
    /**
     * @var mixed[]
     */
    public $notInArray = array();

    /**
     * @var string Validation error message
     */
    public $notInArrayMessage = 'The value {{ value }} cannot be the same as {{ values }}';
}
