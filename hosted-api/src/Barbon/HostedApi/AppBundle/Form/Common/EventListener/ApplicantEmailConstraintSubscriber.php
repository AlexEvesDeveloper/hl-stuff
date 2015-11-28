<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\EventListener;

use Barbon\HostedApi\AppBundle\Validator\Common\Constraints\NotInArray;

final class ApplicantEmailConstraintSubscriber extends AbstractContactDetailsConstraintSubscriber
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->constraint = new NotInArray(array(
            'notInArrayMessage' => 'Email must not be the same as the prospective landlords email address',
        ));
    }
}