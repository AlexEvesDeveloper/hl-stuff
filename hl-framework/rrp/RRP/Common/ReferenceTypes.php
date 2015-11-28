<?php

namespace RRP\Common;

/**
 * Class ReferenceTypes
 *
 * @package RRP\Common
 * @author April Portus <april.portus@barbon.com>
 */
class ReferenceTypes
{
    /**
     * Get Reference Types
     *
     * @return array
     */
    public static function getReferenceTypes()
    {
        return array(
            'HomeLetReference' => 'HomeLet Reference',
            'OtherCC' => 'Other Provider (credit check only)',
            'OtherFull' => 'Other Provider (full reference)',
        );
    }

    /**
     * Returns true if additional provider details are required
     *
     * @param $referenceType
     * @return bool
     */
    public static function isProviderRequired($referenceType)
    {
        if (
            ($referenceType == 'OtherCC') ||
            ($referenceType == 'OtherFull')
        ) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Returns true if the nil excess option is allowed
     *
     * @param $referenceType
     * @return bool
     */
    public static function isNilExcessAllowed($referenceType)
    {
        if (
            ( ! $referenceType) ||
            ($referenceType == 'Enhance') ||
            ($referenceType == 'Optimum') ||
            ($referenceType == 'OtherFull')
        ) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Returns true if it is a full reference
     *
     * @param $referenceType
     * @return bool
     */
    public static function isFullReference($referenceType)
    {
        if (
            ($referenceType == 'Enhance') ||
            ($referenceType == 'Optimum') ||
            ($referenceType == 'OtherFull')
        ) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Returns true if only monthly rental in Band A is allowed
     *
     * @param $referenceType
     * @return bool
     */
    public static function isOnlyAllowBandA($referenceType)
    {
        if (
            ($referenceType == 'Insight') ||
            ($referenceType == 'OtherCC')
        ) {
            return true;
        }
        else {
            return false;
        }
    }

}
