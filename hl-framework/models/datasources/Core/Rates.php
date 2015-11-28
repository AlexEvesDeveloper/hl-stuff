<?php
/**
* Model definition for the rates table
*
* @todo This is the new rates system - it's only partially complete. Needs finishing before use!
*/
class Datasource_Core_Rates extends Zend_Db_Table_Multidb {
    protected $_name = 'rates';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet';
    
    private $_agentRateSetID = 0;
    private $_riskArea = 1;
    private $_rateSetID;
    
    public function __construct($agentsRateSetID = null, $riskArea = null) {
        // Setup the local variables with the data passed in
        
        if (!is_null($agentsRateSetID)) $this->_agentRateSetID = $agentsRateSetID;
        if (!is_null($riskArea)) $this->_riskArea = $riskArea;
        
        // Using the agent rate set ID and the risk area - find out which rate set we need to use
        // TODO: Need to finish this.......!!!!!
        $rateSets = new Datasource_Core_RateSets();
		$this->_rateSetID = $rateSets->getID($this->_agentRateSetID, $this->_riskArea);
        
        parent::__construct();
    }
    
    /**
    * fetch a specific set of Rates by ID
    *
    * @param int $productID A valid homelet product ID
    * @param double $selectorValue
    * @return array
    */
    public function fetchRate($productID, $selectorValue) {
        $select = $this->select()
                       ->where('product_id = ?', $productID)
                       ->where('selector_minimum <= ?', $selectorValue)
                       ->where('selector_maximum >= ?', $selectorValue)
                       ->where('rate_set_id = ?', $this->_rateSetID);
        $row = $this->fetchRow($select);
		
        if ($row) {
            return $row->toArray();
        } else {
            return false;
        }
        
        
        /* OLD CODE - TODO: needs refactoring as the rates above won't have IPT or etc.. applied to them
        
        $taxDatasource = new Datasource_Core_Tax();
        $ipt = ($taxDatasource->getTaxByType('ipt') / 100) + 1;
        
        $fields = array(
                     'rateName'     => 'rateName',
                     'rateSetID'    => 'rateSetID',
                     'riskArea'     => 'riskArea');

        // This handels where the UnSpecified Personal Possessions SI may be zero, as this is not in the database
        // we nee to alter the query to specify '0' as UnSpecifiedPersonalPossessionsRate
        if($this->unspecifiedPersonalPossessionsSumInsured > 0){
            $fields['unspecifiedPersonalPossessions'] = new Zend_Db_Expr('round(possessionsp_band'.$this->_personalBand.',4)');
        }else{
            $fields['unspecifiedPersonalPossessions'] = new Zend_Db_Expr('0');
        }
        
        $fields['specifiedPersonalPossessions'] = new Zend_Db_Expr('round(specpossessionsp,4)');
        
        $fields['pedalCycles'] = new Zend_Db_Expr('round(pedalcyclesp,3)');
        
        // This handles where the Contents SI > than 15000 is NOT a band in the data but a
        // calculation based on another field contentstpb
        if($this->_contentsband == ""){
            $fields['contents'] = new Zend_Db_Expr('contentstpb');
        }else{
            $fields['contents'] = new Zend_Db_Expr('contentstpa_band'.$this->_contentsband);
        }
        
        $select = $this->select()
            ->from($this->_name, $fields)
            ->where('endDate >= NOW() OR endDate = "0000-00-00"')
            ->where('agentsRateID = ?', $this->_agentsRateID)
            ->where('riskArea = ?', $this->_riskArea);
        
        $row = $this->fetchRow($select);
        
        // Ordinarily we could just return this data - but unfortunately the rates in our databases weren't updated for 6% IPT
        // so now we have to fudge them to make everything work
        //   .. do you realise stuff like this actually keeps me awake at night? :(
        
        if ($row['rateSetID'] == 45) {
            $row['contents'] = round(($row['contents'] * 1.05) / $ipt, 4);
            $row['specifiedPersonalPossessions'] = round(($row['specifiedPersonalPossessions'] * 1.05) / $ipt, 4);
            $row['unspecifiedPersonalPossessions'] = round(($row['unspecifiedPersonalPossessions'] * 1.05) / $ipt, 4);
            //$premiums->contents = round($premiums->contents);
        }
        
        return $row->toArray();*/
    }
    
}