<?php

namespace RRP\Common;

/**
 * Class TenancyAgreementTypes
 *
 * @package RRP\Common
 * @author April Portus <april.portus@barbon.com>
 */
class TenancyAgreementTypes
{
    /**
     * Identifier for tenancy agreement type of assured shorthold tenancy
     */
    const ASSURED_SHORTHOLD_TENANCY_TYPE = 'AST';

    /**
     * Identifier for tenancies for type Company
     */
    const COMPANY_TENANCY_TYPE = 'Company';

    /**
     * Get Tenancy Agreement Types
     *
     * @return array
     */
    public static function getTenancyAgreementTypes()
    {
        return array(
            self::ASSURED_SHORTHOLD_TENANCY_TYPE => self::ASSURED_SHORTHOLD_TENANCY_TYPE,
            self::COMPANY_TENANCY_TYPE => self::COMPANY_TENANCY_TYPE,
        );
    }

    /**
     * Returns true if the tenancy agreement type is an Assured Shorthold Tenancy
     *
     * @param $tenancyAgreementType
     * @return bool
     */
    public static function isAssuredShortholdTenancy($tenancyAgreementType)
    {
        if ($tenancyAgreementType == self::ASSURED_SHORTHOLD_TENANCY_TYPE) {
            return true;
        }
        return false;
    }

    /**
     * Sets the tenancy agreement type according to whether it's an Assured Shorthold Tenancy
     *
     * @param $isAssuredShortholdTenancy
     * @return string one of the self::*_TYPE identifiers
     */
    public static function getIsAssuredShortholdTenancy($isAssuredShortholdTenancy)
    {
        if ($isAssuredShortholdTenancy) {
            return self::ASSURED_SHORTHOLD_TENANCY_TYPE;
        }
        return self::COMPANY_TENANCY_TYPE;
    }
}
