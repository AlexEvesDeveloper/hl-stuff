<?php
/**
 * Core rates manager
 *
 */
class Manager_Insurance_TenantsContentsPlus_Rates {
    private $_rateModel;
    
    private $_agentsRateID;
    private $_riskArea;
    private $_contentsSumInsured;
    private $_contentsBand;
    private $_personalPossessionsSumInsured;
    private $_personalPossessionsBand;
    private $_rateDatasource;
    private $_rateSetID;
    
    public function __construct($agentsRateID, $riskArea,$rateSetID) {
        $this->_agentsRateID = $agentsRateID;
        $this->_riskArea = $riskArea;
        $this->_rateSetID = $rateSetID;        
        $this->_rateDatasource = new Datasource_Insurance_TenantsContentsPlus_Rates();
        $this->_rateModel = $this->_rateDatasource->getRateSet($this->_agentsRateID, $this->_riskArea,$this->_rateSetID);
    }
    
    public function getRates() {
        return $this->_rateModel;
    }
    
    public function setCoverAmounts($contentsSumInsured, $unspecifiedPossessionsSumInsured) {
        $this->_contentsSumInsured = $contentsSumInsured;
        $this->_personalPossessionsSumInsured = $unspecifiedPossessionsSumInsured;
    }
    
    public function getContentsRate() {
        // TODO: This needs removing and replacing with a proper band lookup from the database
        switch($this->_contentsSumInsured) {
            case ($this->_contentsSumInsured > 15000) :
                return $this->_rateModel->contentstpb;
                break;
            case ($this->_contentsSumInsured <= 5000) :
                return $this->_rateModel->contentstpa_banda;
                break;
            case ($this->_contentsSumInsured <= 7500) :
                return $this->_rateModel->contentstpa_bandb;
                break;
            case ($this->_contentsSumInsured <= 10000) :
                return $this->_rateModel->contentstpa_bandc;
                break;
            default :
                return $this->_rateModel->contentstpa_bandd;
                break; 
        }
    }
    
    public function getUnspecifiedPossessionsRate() {
        // TODO: This needs removing and replacing with a proper band lookup from the database
        // TODO: Original code for this had a band 'N' for zero but I can't see how that would have ever worked!
        switch($this->_personalPossessionsSumInsured) {
            case 1000 :
                return $this->_rateModel->possessionsp_banda;
                break;
            case 2000 :
                return $this->_rateModel->possessionsp_bandb;
                break;
            case 4000 :
                return $this->_rateModel->possessionsp_bandc;
                break;
            case 6000 :
                return $this->_rateModel->possessionsp_bandd;
                break;
        }
    }
    
    public function getSetID() {
        return $this->_rateModel->rateSetID;
    }
    
    public function getPedalCyclesRate() {
        return $this->_rateModel->pedalcyclesp;
    }
    
    public function getSpecifiedPossessionsRate() {
        // This is bizarrely stored annually in the database when everything else is stored monthly! Yes... I know *sigh*
        return $this->_rateModel->specpossessionsp / 12;
    }
    
    /**
     * Gets relevant fees for this rate set
     *
     * @return Model_Insurance_Fee
     */
    public function getFees() {
        return $this->_rateDatasource->getFees($this->_rateModel->rateSetID);
    }
    
}
?>
