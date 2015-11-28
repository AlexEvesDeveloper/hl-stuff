<?php

namespace Iris\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Regex;

/**
 * Class BankSortCode
 *
 * @package Iris\Validator\Constraints
 * @author Simon Paulger <simon.paulger@barbon.com>
 */
class BankSortCode extends Regex
{
    /**
     * @var string
     */
    const PATTERN = '/^\d{6}$/';

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
