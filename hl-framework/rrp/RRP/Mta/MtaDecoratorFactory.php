<?php

namespace RRP\Mta;

use RRP\Mta\Exception\UnknownMtaException;

/**
 * Class MtaDecoratorFactory
 *
 * @package RRP\Mta
 * @author April Portus <april.portus@barbon.com>
 */
class MtaDecoratorFactory
{
    /**
     * RentRecoveryPlusMta decorator type
     */
    const RENT_RECOVERY_PLUS_MTA = 'RentRecoveryPlusMta';

    /**
     * Get the decorator
     *
     * @param string $mtaType
     * @return object
     * @throws UnknownMtaException
     */
    public static function getDecorator($mtaType)
    {
        $className = 'RRP\Mta\Decorators\\' . $mtaType;

        switch ($mtaType) {
            case self::RENT_RECOVERY_PLUS_MTA:
                return new $className();

            default:
                throw new UnknownMtaException();
        }
    }
}