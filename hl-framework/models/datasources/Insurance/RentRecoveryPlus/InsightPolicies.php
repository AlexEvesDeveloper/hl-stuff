<?php

/**
 * Class Datasource_Insurance_RentRecoveryPlus_InsightPolicies
 *
 * @author April Portus <april.portus@barbon.com>
 */
class Datasource_Insurance_RentRecoveryPlus_InsightPolicies extends Datasource_Insurance_LegacyPolicies
{
    /**
     * @var string table name
     */
    protected $_name = 'insight_rrp_policy';

    /**
     * @var string primary key
     */
    protected $_primary = 'policynumber';

    /**
     * @var string database
     */
    protected $_multidb = 'db_legacy_homelet';
}
