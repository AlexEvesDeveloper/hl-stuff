<?php

namespace RRP\Underwriting;

use RRP\DependencyInjection\LegacyContainer;
use RRP\Underwriting\Exception\UnknownUnderwritingTypeException;

/**
 * Class UnderwritingDecoratorFactory
 *
 * @package RRP\Underwriting
 * @author April Portus <april.portus@barbon.com>
 */
class UnderwritingDecoratorFactory
{
    /**
     * Underwriting decorator type for Rent Recovery Plus
     */
    const UNDERWRITING_RENT_RECOVERY_PLUS = 'RentRecoveryPlusAnswers';

    /**
     * Policy name for the underwriting decorator type for Rent Recovery Plus
     */
    const POLICY_NAME_RENT_RECOVERY_PLUS = 'rentrecoveryp';

    /**
     * Gets the Underwriting decorator
     *
     * @param string $UnderwritingType One of the self::UNDERWRITING_* identifiers
     * @param int $questionSetId
     * @param string $dateAnswered
     * @param string $policyNumber
     * @return mixed
     * @throws Exception\UnknownUnderwritingTypeException
     */
    public static function getDecorator($UnderwritingType, $questionSetId, $dateAnswered, $policyNumber)
    {
        $className = 'RRP\Underwriting\Decorators\\' . $UnderwritingType;

        switch ($UnderwritingType) {
            case self::UNDERWRITING_RENT_RECOVERY_PLUS:
                return new $className($questionSetId, $dateAnswered, $policyNumber, self::POLICY_NAME_RENT_RECOVERY_PLUS);

            default:
                throw new UnknownUnderwritingTypeException();
        }
    }

}