<?php

namespace Iris\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Regex;

/**
 * Class Postcode
 *
 * @package Iris\Validator\Constraints
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 *
 * @Annotation
 */
class Postcode extends Regex
{
    /**
     * Postcode match pattern (production)
     */
    const PRODUCTION_PATTERN = '/^([A-PR-UWYZ]([0-9]{1,2}|([A-HK-Y][0-9]|[A-HK-Y][0-9]([0-9]|[ABEHMNPRV-Y]))|[0-9][A-HJKS-UW])\ [0-9][ABD-HJLNP-UW-Z]{2}|(GIR\ 0AA)|(SAN\ TA1)|(BFPO\ (C\/O\ )?[0-9]{1,4})|((ASCN|BBND|[BFS]IQQ|PCRN|STHL|TDCU|TKCA)\ 1ZZ)|1001)$/';

    /**
     * Postcode match pattern (development). Includes "X9" postcodes
     */
    const DEVELOPMENT_PATTERN = '/^([A-PR-UWXYZ]([0-9]{1,2}|([A-HK-Y][0-9]|[A-HK-Y][0-9]([0-9]|[ABEHMNPRV-Y]))|[0-9][A-HJKS-UW])\ [0-9][ABD-HJLNP-UW-Z]{2}|(GIR\ 0AA)|(SAN\ TA1)|(BFPO\ (C\/O\ )?[0-9]{1,4})|((ASCN|BBND|[BFS]IQQ|PCRN|STHL|TDCU|TKCA)\ 1ZZ)|1001)$/';

    /**
     * {@inheritdoc}
     */
    public function __construct($options = null)
    {
        $pattern = ('production' == APPLICATION_ENV) ? self::PRODUCTION_PATTERN : self::DEVELOPMENT_PATTERN;

        if (is_array($options)) {
            $options = array_merge($options, array('pattern' => $pattern));
        }
        else {
            $options = $pattern;
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