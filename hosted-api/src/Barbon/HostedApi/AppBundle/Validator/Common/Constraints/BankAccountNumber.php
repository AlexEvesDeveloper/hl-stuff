<?php

namespace Barbon\HostedApi\AppBundle\Validator\Common\Constraints;

use Symfony\Component\Validator\Constraints\Regex;

class BankAccountNumber extends Regex
{
    /**
     * @var string
     */
    const PATTERN = '/^\d{8}$/';

    /**
     * {@inheritdoc}
     */
    public function __construct($options = null)
    {
        if (is_array($options)) {
            $options = array_merge($options, array('pattern' => self::PATTERN));
        }
        else {
            $options = self::PATTERN;
        }

        parent::__construct($options);
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'Symfony\Component\Validator\Constraints\RegexValidator';
    }
}
