<?php

class Datasource_Insurance_LandlordsPlus_ContentsCover_Rates extends Zend_Db_Table_Multidb {
	protected $_multidb = 'db_homelet';    
	protected $_name = 'landlordsplus_contents_cover_rates';
    protected $_primary = 'contents_risk_area';
    
    /**
     * 
     */
    public function getByProperty($riskAreaID,$startDate) {
    	
		$select = $this->select();
		$select->where('contents_risk_area = ?', $riskAreaID);
                $select->where('startDate <= ?',$startDate);
                $select->where('endDate is null or endDate >= ?',$startDate);
		
		$ratesRow = $this->fetchRow($select);
		if (count($ratesRow)>0) {
			return array (
				'net'	=>	(double)$ratesRow->net_rate,
				'gross'	=>	(double)$ratesRow->gross_rate,
				'netAccidentalDamage'	=>	(double)$ratesRow->net_accidental_damage_rate,
				'grossAccidentalDamage'	=>	(double)$ratesRow->gross_accidental_damage_rate,
				'netFlood'		=> (double)$ratesRow->net_flood_rate,
				'grossFlood'	=> (double)$ratesRow->gross_flood_rate
			);
		} else {
			throw new Exception('Failed to find a rate');
		}
    }    
    
}

?>
