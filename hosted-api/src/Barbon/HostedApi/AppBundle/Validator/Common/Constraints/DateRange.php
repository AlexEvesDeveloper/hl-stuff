<?php

namespace Barbon\HostedApi\AppBundle\Validator\Common\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\MissingOptionsException;

class DateRange extends Constraint
{
    /**
     * @var string
     */
    public $minMessage = 'This date should be greater than {{ limit }}.';

    /**
     * @var string
     */
    public $maxMessage = 'This date should be less than {{ limit }}.';

    /**
     * @var string
     */
    public $invalidMessage = 'This value should be a valid date.';

    /**
     * @var \DateTime
     */
    public $min;

    /**
     * @var \DateTime
     */
    public $max;

    /**
     * Constructor
     *
     * @param array|null $options
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     */
    public function __construct($options = null)
    {
        parent::__construct($options);

        if (null === $this->min && null === $this->max) {
            throw new MissingOptionsException('Either option "min" or "max" must be given for constraint ' . __CLASS__, array('min', 'max'));
        }

        if (null !== $this->min) {
            $this->min = new \DateTime($this->min);
        }

        if (null !== $this->max) {
            $this->max = new \DateTime($this->max);
        }
    }
}