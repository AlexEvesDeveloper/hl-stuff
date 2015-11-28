<?php

namespace RRP\Application;

use RRP\Application\Exception\UnknownApplicationException;

/**
 * Class ApplicationDecoratorFactory
 *
 * @package RRP\Application
 * @author April Portus <april.portus@barbon.com>
 */
class ApplicationDecoratorFactory
{
    /*
     * RentRecoveryPlusQuote decorator type
     */
    const RENT_RECOVERY_PLUS_QUOTE   = 'RentRecoveryPlusQuote';

    /*
     * RentRecoveryPlusPolicy decorator type
     */
    const RENT_RECOVERY_PLUS_POLICY  = 'RentRecoveryPlusPolicy';

    /*
     * RentRecoveryPlusInsight decorator type
     */
    const RENT_RECOVERY_PLUS_INSIGHT = 'RentRecoveryPlusInsight';

    /**
     * @param string $applicationType
     * @return object
     * @throws UnknownApplicationException
     */
    public static function getDecorator($applicationType)
    {
        $className = 'RRP\Application\Decorators\\' . $applicationType;

        switch ($applicationType) {
            case self::RENT_RECOVERY_PLUS_QUOTE:
            case self::RENT_RECOVERY_PLUS_POLICY:
            case self::RENT_RECOVERY_PLUS_INSIGHT:
                return new $className();

            default:
                throw new UnknownApplicationException();
        }
    }
}