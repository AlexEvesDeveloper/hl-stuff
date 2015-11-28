<?php

class Datasource_Insurance_LandlordsPlus_BuildingCover_Rates extends Zend_Db_Table_Multidb {
	protected $_multidb = 'db_homelet';    
	protected $_name = 'landlordsplus_building_cover_rates';
    protected $_primary = array('building_risk_area','build_year_id');
    
    /**
     * 
     */
    public function getByProperty($riskAreaID, $yearBuilt,$startDate) {
    	$buildYearID = 0;
		switch ($yearBuilt) {
			case 'Before 1850' :
				$buildYearID = 1;
				break;
			case '1850-1899' : 
				$buildYearID = 2;
				break;
			case '1900-1919' :
				$buildYearID = 3;
				break;
			case '1920-1945' :
				$buildYearID = 4;
				break;
			case '1946-1979' :
				$buildYearID = 5;
				break;
			case '1980-1990' :
				$buildYearID = 6;
				break;
			case '1991-2000' :
				$buildYearID = 7;
				break;
			case '2001+' :
				$buildYearID = 8;
				break;
		}

		$select = $this->select();
		$select->where('building_risk_area = ?', $riskAreaID);
		$select->where('build_year_id = ?', $buildYearID);
                $select->where('startDate <= ?',$startDate);
                $select->where('endDate is null or endDate >= ?',$startDate);
		
		$ratesRow = $this->fetchRow($select);
		if (count($ratesRow)>0) {
			return array (
				'net'					=>	(double)$ratesRow->net_rate,
				'gross'					=>	(double)$ratesRow->gross_rate,
				'netAccidentalDamage'	=>	(double)$ratesRow->net_accidental_damage_rate,
				'grossAccidentalDamage'	=>	(double)$ratesRow->gross_accidental_damage_rate,
				'netFlood'				=>	(double)$ratesRow->net_flood_rate,
				'grossFlood'			=>	(double)$ratesRow->gross_flood_rate
			);
		} else {
			throw new Exception('Failed to find a rate');
		}
    }    
    
}

?>
