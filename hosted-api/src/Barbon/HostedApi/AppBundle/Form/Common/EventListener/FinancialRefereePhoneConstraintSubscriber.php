<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\EventListener;

use Barbon\HostedApi\AppBundle\Validator\Common\Constraints\NotInArray;

final class FinancialRefereePhoneConstraintSubscriber extends AbstractContactDetailsConstraintSubscriber
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->constraint = new NotInArray(array(
            'notInArrayMessage' => 'Telephone number must not be the same as an applicants telephone number',
        ));
    }
}
