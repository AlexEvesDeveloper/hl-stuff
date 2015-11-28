<?php

namespace Barbon\HostedApi\AppBundle\Validator\Common\Constraints;

use Symfony\Component\Validator\Constraint;

class AddressHistoryDuration extends Constraint
{
    public $minYears = 3;
    public $minNum = 3;

    public $message = 'You require at least {{ minYears }} years or {{ minNum }} addresses in your address history, not including addresses after an international address';
}