<?php
class Datasource_Insurance_DSI_Areas extends Zend_Db_Table_Multidb {
	protected $_multidb = 'db_homelet';    
	protected $_name = 'dsi_areas';
    protected $_primary = 'postcode_area';
	
	/**
	 * 
	 */
	public function getAreaIDByPostcode($postcode) {
		$area = Application_Core_Postcode::getAreaCode($postcode);
		$select = $this->select()->where('postcode_area = ?', $area);
		
		$areaRow = $this->fetchRow($select);
		if (count($areaRow)>0) {
			return (int)$areaRow['dsi_area_id'];
		}
	}
}
?>