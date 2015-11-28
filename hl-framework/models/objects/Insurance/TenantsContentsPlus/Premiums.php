<?php
class Model_Insurance_TenantsContentsPlus_Premiums extends Model_Abstract {
    public $contents                = 0; // Monthly Contents Premium
    public $pedalCycles             = 0; // Monthly Pedal Cycles Premium
    public $specifiedPossessions    = 0; // Monthly Specified Possessiosn Premium
    public $unspecifiedPossessions  = 0; // Monthly Un-Spec Possessions Premium
    
    public $annualContents                = 0; // Annual Contents Premium
    public $annualPedalCycles             = 0; // Annual Pedal Cycles Premium
    public $annualSpecifiedPossessions    = 0; // Annual Specified Possessiosn Premium
    public $annualUnspecifiedPossessions  = 0; // Annual Un-Spec Possessions Premium
    
    public $total                   = 0; // Monthly Total Premium
    public $annualTotal             = 0; // Annual Total Premium
    public $iptAmount               = 0; // Monthly IPT Amount
    public $annualIptAmount         = 0; // Annual IPT Amount
}
?>