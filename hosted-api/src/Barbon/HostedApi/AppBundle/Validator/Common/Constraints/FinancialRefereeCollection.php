<?php

namespace Barbon\HostedApi\AppBundle\Validator\Common\Constraints;

use Symfony\Component\Validator\Constraint;

class FinancialRefereeCollection extends Constraint
{
    /**
     * @var int
     */
    public $minReferees = 0;

    /**
     * @var int
     */
    public $maxCurrentReferees = 2;

    /**
     * @var int
     */
    public $maxFutureReferees = 1;

    /**
     * @var string
     */
    public $tooFewRefereesMessage = 'You must have at least {{ minReferees }} referees.';

    /**
     * @var string
     */
    public $tooManyCurrentRefereesMessage = 'You cannot have more than {{ maxCurrentReferees }} current referees.';

    /**
     * @var string
     */
    public $tooManyFutureRefereesMessage = 'You cannot have more than {{ maxFutureReferees }} future referees.';
}
