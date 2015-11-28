<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\EventListener;

use Barbon\HostedApi\AppBundle\Validator\Common\Constraints\NotInArray;

interface NotInArrayConstraintSubscriber
{
    /**
     * Add a value that the field must not match
     *
     * @param $value
     */
    public function addValue($value);

    /**
     * Get the constraint
     *
     * @return NotInArray
     */
    public function getConstraint();
}
