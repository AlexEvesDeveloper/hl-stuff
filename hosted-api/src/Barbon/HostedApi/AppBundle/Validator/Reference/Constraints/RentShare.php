<?php

namespace Barbon\HostedApi\AppBundle\Validator\Reference\Constraints;

use Symfony\Component\Validator\Constraint;

class RentShare extends Constraint
{
    public $totalRent = 0;
    
    public $blankMessage = 'This value cannot be blank';
    public $invalidMessage = 'This value cannot be invalid';
    public $invalidFloatMessage = 'This value must either have 2 decimal places or a whole number, e.g. "15000.00" or "15000"';
    public $moreThanTotalRentMessage = 'This value cannot be more than the total rent';
}
