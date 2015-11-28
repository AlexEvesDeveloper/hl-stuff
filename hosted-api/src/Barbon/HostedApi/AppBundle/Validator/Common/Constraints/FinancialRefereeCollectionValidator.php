<?php

namespace Barbon\HostedApi\AppBundle\Validator\Common\Constraints;

use Barbon\HostedApi\AppBundle\Form\Common\Enumerations\FinancialRefereeStatus;
use Barbon\HostedApi\AppBundle\Form\Common\Model\FinancialReferee;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class FinancialRefereeCollectionValidator extends ConstraintValidator
{
    /**
     * {@inheritDoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof FinancialRefereeCollection) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\FinancialRefereeCollection');
        }

        if (null === $value) {
            return;
        }

        if (!is_array($value) && !($value instanceof \Traversable && $value instanceof \ArrayAccess)) {
            throw new UnexpectedTypeException($value, 'array or Traversable and ArrayAccess');
        }

        // Counters
        $currentRefereeCount = 0;
        $futureRefereeCount = 0;

        foreach ($value as $index => $financialReferee) {
            if ($financialReferee instanceof FinancialReferee) {
                // Check status
                switch ($financialReferee->getFinancialRefereeStatus()) {
                    case FinancialRefereeStatus::CURRENT_REFEREE:
                    case FinancialRefereeStatus::SECOND_REFEREE:
                        $currentRefereeCount++;
                        break;

                    case FinancialRefereeStatus::FUTURE_REFEREE:
                        $futureRefereeCount++;
                        break;
                }
            }
            else {
                // Invalid type
                throw new UnexpectedTypeException($financialReferee, 'Barbon\HostedApi\AppBundle\Form\Common\Model\FinancialReferee');
            }
        }

        if ($currentRefereeCount + $futureRefereeCount < $constraint->minReferees) {
            // Too few referees
            $this->context->addViolation($constraint->tooFewRefereesMessage, array(
                '{{ minReferees }}' => $constraint->minReferees
            ));
        }

        if ($currentRefereeCount > $constraint->maxCurrentReferees) {
            // Too many current referees
            $this->context->addViolation($constraint->tooManyCurrentRefereesMessage, array(
                '{{ maxCurrentReferees }}' => $constraint->maxCurrentReferees
            ));
        }

        if ($futureRefereeCount > $constraint->maxFutureReferees) {
            // Too many future referees
            $this->context->addViolation($constraint->tooManyFutureRefereesMessage, array(
                '{{ maxFutureReferees }}' => $constraint->maxFutureReferees
            ));
        }
    }
}
