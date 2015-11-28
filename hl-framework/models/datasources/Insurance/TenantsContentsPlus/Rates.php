<?php
/**
* Model definition for the rates table
*
* @todo There is a lot of stuff in here that needs tidying up
*/
class Datasource_Insurance_TenantsContentsPlus_Rates extends Zend_Db_Table_Multidb {
    protected $_name = 'rates';
    protected $_primary = 'policyRateID';
    protected $_multidb = 'db_legacy_homelet';
    
    /**
     * Gets the rate set filtered by agents rate ID and risk area
     *
     * @param int $agentRateID
     * @param int $riskArea
     * @return Model_Insurance_Rate
     */
    public function getRateSet ($agentRateID, $riskArea, $rateSetID) {
       $rateModel = new Model_Insurance_Rate();
       if ($rateSetID == 0 || $rateSetID == -1) {
		$select = $this->select()
		->where('endDate >= NOW() OR endDate = "0000-00-00"')
		->where('agentsRateID = ?', $agentRateID)
		->where('riskarea = ?', $riskArea);
       } else {
		$select = $this->select()
		->where('rateSetID = ?', $rateSetID)
		->where('riskarea = ?', $riskArea);
       }

        $rate = $this->fetchRow($select);
        
        if (count($rate) > 0) {
        
            $rateModel->rateSetID = $rate->rateSetID;
            $rateModel->rateName = $rate->rateName;
            $rateModel->riskArea = $rate->riskarea;
            $rateModel->contentstpb = $rate->contentstpb;
            $rateModel->contentstpa_banda = $rate->contentstpa_banda;
            $rateModel->contentstpa_bandb = $rate->contentstpa_bandb;
            $rateModel->contentstpa_bandc = $rate->contentstpa_bandc;
            $rateModel->contentstpa_bandd = $rate->contentstpa_bandd;
            $rateModel->possessionsp_banda = $rate->possessionsp_banda;
            $rateModel->possessionsp_bandb = $rate->possessionsp_bandb;
            $rateModel->possessionsp_bandc = $rate->possessionsp_bandc;
            $rateModel->possessionsp_bandd = $rate->possessionsp_bandd;
            $rateModel->pedalcyclesp = $rate->pedalcyclesp;
            $rateModel->specpossessionsp = $rate->specpossessionsp;
            
            //The TCI+ rates were originally written for IPT at 5%, as this was something that hadn't
            //changed in over a thousand years. However, IPT suddenly started to change every few months,
            //switching restlessly between 5% and 6%, and so the best developers in the land were gathered
            //together to devise an engineering solution to accommodate this...
            $taxDatasource = new Datasource_Core_Tax();
			$tax = $taxDatasource->getTaxbyType('ipt');
            if ($tax['rate'] == 6) {
                
                //Behold repugnance:
                $fudge = 1.05/1.06;
                $rateModel->contentstpa_banda = round($rateModel->contentstpa_banda * $fudge, 4);
                $rateModel->contentstpa_bandb = round($rateModel->contentstpa_bandb * $fudge, 4);
                $rateModel->contentstpa_bandc = round($rateModel->contentstpa_bandc * $fudge, 4);
                $rateModel->contentstpa_bandd = round($rateModel->contentstpa_bandd * $fudge, 4);
                $rateModel->contentstpb = round($rateModel->contentstpb * $fudge, 6);
                $rateModel->possessionsp_banda = round($rateModel->possessionsp_banda * $fudge, 4);
                $rateModel->possessionsp_bandb = round($rateModel->possessionsp_bandb * $fudge, 4);
                $rateModel->possessionsp_bandc = round($rateModel->possessionsp_bandc * $fudge, 4);
                $rateModel->possessionsp_bandd = round($rateModel->possessionsp_bandd * $fudge, 4);
                $rateModel->pedalcyclesp = round($rateModel->pedalcyclesp * $fudge, 4);
                $rateModel->specpossessionsp = round($rateModel->specpossessionsp * $fudge, 4);
            }
        } else {
            // Can't find rates - log a warning
            Application_Core_Logger::log("Can't find rates in table {$this->_name} (agentsRateID = {$agentRateID}, riskarea = {$riskArea})", 'warning');
        }
        
        return $rateModel;
    }

    /**
     * Gets relevant fees for a given rate set
     *
     * @param int $rateSetID rate set ID
     *
     * @return Model_Insurance_Fee
     */
    public function getFees($rateSetID) {
        $feeModel = new Model_Insurance_Fee();
        
        // Fields we want
        $fields = array(
            'tenantspMonthlyFee'    => 'tenantspMonthlyFee',
            'MonthlyFeeSP'          => 'tenantspMonthlyFeeSP',
            'adminFee'              => 'adminFee',
            'sixMonthFee'           => 'tenantspsixMonthFee',
            'cancellationFee'       => 'cancellationFee');
        
        $select = $this->select()
            ->from($this->_name, $fields)
            ->where('rateSetID = ?', $rateSetID);
          //  ->where('endDate = ?', 0);
        
        $feeRow = $this->fetchRow($select);
        if (!empty($feeRow)) {
            $feeModel->adminFee = (double)$feeRow->adminFee;
            $feeModel->cancellationFee = (double)$feeRow->cancellationFee;
            $feeModel->monthlyFeeSP = (double)$feeRow->MonthlyFeeSP;
            $feeModel->sixMonthFee = (double)$feeRow->sixMonthFee;
            $feeModel->tenantspMonthlyFee = (double)$feeRow->MonthlyFeeSP;
            return $feeModel;
        } else {
            // Could not find a usable fee
            Application_Core_Logger::log("Fee for rateSetID {$rateSetID} Not found in table {$this->_name}", 'warning');
            return false;
        }
    }

}
