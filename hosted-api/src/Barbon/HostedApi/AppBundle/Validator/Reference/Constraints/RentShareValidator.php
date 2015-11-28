<?php

namespace Barbon\HostedApi\AppBundle\Validator\Reference\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class RentShareValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        // Confirm the rent share is not blank
        if ( ! isset($value)) {
            $this->context->addViolation($constraint->blankMessage);
        }
        
        // Confirm the rent share is greater than or equal to 0
        if (0 > $value) {
            $this->context->addViolation($constraint->invalidMessage);
        }
        
        // Confirm the rent share matches float formatting constraints
        if ( ! preg_match('/^([0-9]+\.[0-9]{0,2}|[0-9]+)$/', $value)) {
            $this->context->addViolation($constraint->invalidFloatMessage);
        }

        // Confirm the rent share is less than the total rent
        if ($value > $constraint->totalRent) {
            $this->context->addViolation($constraint->moreThanTotalRentMessage);
        }
    }
}
