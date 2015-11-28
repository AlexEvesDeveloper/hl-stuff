<?php
/**
 * Object Data modle to describe the rates Table
 *
 * This is a FULL mapping of the rates table, I suspect a large proportion of these fields may be redundant
 * Please document tham as you use the and we'll be able to identify obsolete ones
 */

class Model_Insurance_Rate extends Model_Abstract {
    /**
     * Rate Id Primary key auto inc.
     */
    public $policyRateID;
    
    /**
     * Rate Name - simple name describing the rate
     */
    public $rateName;
    
    /**
     * Risk area the rate applies to, default 0
     */
    public $riskArea;
    
    /**
     * Rate start date - indexed, default 0000-00-00 
     */
    public $startDate;
    
    /**
     * Rate end date - indexed, default 0000-00-00
     */
    public $endDate;
    
    
    /**
     * Rate set Id - Indexed, default 0
     */
    public $rateSetID;    
    
    /**
     * Contents rate if not in a band
     */
    public $contentstpb;
    
    /**
     * Tenants Contents+ 
     *
     * Bands a - d
     */
    public $possessionsp_banda;
    public $possessionsp_bandb;
    public $possessionsp_bandc;
    public $possessionsp_bandd;
    
    public $contentstpa_banda;
    public $contentstpa_bandb;
    public $contentstpa_bandc;
    public $contentstpa_bandd;
    
    /**
     * Pedalcycle rate for Tenants Contents+
     */
    public $pedalcyclesp;
    
    /**
     * Specified possessions Rate for Tenants Contents+
     */
    public $specpossessionsp;

    /**
    * Tenants Contents+ Monthly Fee
    */
    public $tenantspMonthlyFee;
    
    /**
    * Tenants Contents+ Six Month Policy Fee
    */
    public $tenantspsixMonthFee;
    
    public $tenantsMonthlyFeeSP;
    
    /**
    * Tenants Contents+ Monthly Admin fee
    */
    public $tenantspMonthlyFeeSP;
    
    public $lowcostlandlordsMonthlyFeeSP;
    public $landlordsMonthlyFeeSP;
    
    public $renewaStartDate;
    public $renewaEndDate;
    public $buildings;
    public $buildingsAccidentalDamage;
    public $buildingsNeExcess;
    public $specialbuildings;
    public $specialbuildings2;
    public $lowCostBuildings;
    public $contentsl;
    public $contentslAccidentalDamage;
    public $contentslNoExcess;
    public $limitedcontents;
    public $emergencyassistance;
    public $legalexpenses;
    public $rentguarantee;
    public $contentsa_banda;
    public $contentsa_bandb;
    public $contentsa_bandc;
    public $contentsa_bandd;
    public $contentsb;
    public $possessions_banda;
    public $possessions_bandb;
    public $possessions_bandc;
    public $possessions_bandd;
    public $pedalcycles;
    public $specpossessions;
    public $monthlyFee;
    public $adminFee;
    public $sixMonthFee;
    public $lowcostlandlordsMonthlyFee;
    public $landlordsMonthlyFee;
    public $tenantsmonthlyFee;
    public $legalexpensesMonthlyFee;
    public $singlePageUnderwritingQuestionSetID;
    public $displayOrder;
}
?>