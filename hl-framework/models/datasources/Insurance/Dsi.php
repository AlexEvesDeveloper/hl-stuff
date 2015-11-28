<?php
class Datasource_Insurance_Dsi extends Zend_Db_Table_Multidb {
	protected $_multidb = 'db_homelet';    
	protected $_name = 'dsi_value_matrix';
    protected $_primary = array('dsi_area_id', 'dsi_year_built_id', 'dsi_property_type', 'dsi_bedroom_quantity_id', 'business_type_id');

	public function getValue($areaID, $yearBuilt, $propertyType, $bedroomQuantity) {
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
		
		$propertyTypeID = 0;
		switch ($propertyType) {
			case 'Bungalow' :
				$propertyTypeID = 1;
				break;
			case 'Detached' :
				$propertyTypeID = 2;
				break;
			case 'Semi-Detached' :
				$propertyTypeID = 3;
				break;
			case 'Terraced' :
				$propertyTypeID = 4;
				break;
			case 'Other' :
				$propertyTypeID = 5;
				break; 
		}
		
		$bedroomQuantityID = 0;
		switch ($bedroomQuantity) {
			case '1' : 
				$bedroomQuantityID = 1;
				break;
			case '2' : 
				$bedroomQuantityID = 2;
				break;
			case '3' : 
				$bedroomQuantityID = 3;
				break;
			case '4' : 
				$bedroomQuantityID = 4;
				break;
			case '5' : 
				$bedroomQuantityID = 5;
				break;
			case '6' : 
				$bedroomQuantityID = 6;
				break;
		}
		
		$businessTypeID = 1; // Fixed for inception. 2 is for renewals
		
		$select = $this->select();
		$select->where('dsi_area_id = ?', $areaID);
		$select->where('dsi_year_built_id = ?', $buildYearID);
		$select->where('dsi_property_type_id = ?', $propertyTypeID);
		$select->where('dsi_bedroom_quantity_id = ?', $bedroomQuantityID);
		$select->where('business_type_id = ?', $businessTypeID);
		$select->where('(end_date > ? OR end_date is null)', date('Y-m-d'));
		
		$dsiRow = $this->fetchRow($select);
		if (count($dsiRow)>0) {
			return array(
				'rebuildValue'			=>	$dsiRow->dsi_value,
				'areaID'				=>	$dsiRow->dsi_area_id,
				'yearBuiltID'			=>  $dsiRow->dsi_year_built_id,
				'dsiPropertyTypeID'		=>	$dsiRow->dsi_property_type_id,
				'dsiBedroomQuantityID'	=>	$dsiRow->dsi_bedroom_quantity_id
			);
		} else {
			return array(
				'rebuildValue'			=>	null,
				'areaID'				=>	$areaID,
				'yearBuiltID'			=>  $buildYearID,
				'dsiPropertyTypeID'		=>	$propertyTypeID,
				'dsiBedroomQuantityID'	=>	$bedroomQuantityID
			);
		}
	}
}
?>