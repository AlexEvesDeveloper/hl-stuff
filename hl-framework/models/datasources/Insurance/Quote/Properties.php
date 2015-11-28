<?php

class Datasource_Insurance_Quote_Properties extends Zend_Db_Table_Multidb {
	protected $_multidb = 'db_homelet';    
	protected $_name = 'quote_properties';
    protected $_primary = 'id';

	/**
	 * 
	 */
	public function removeAllByQuoteID($quoteID) {
		$where = $this->quoteInto('quote_id = ?', $quoteID);
		$this->delete($where);
	}
	
	/**
	 * 
	 */
	public function getCountByQuoteID($quoteID) {
		$select = $this->select()->where('quote_id = ?', $quoteID);
		$select->from($this->_name, array('property_count' => 'COUNT(*)'));
		
		$row = $this->fetchRow($select);
		return ($row->property_count);
	}
	
	/**
	 * Add a property to a quote
	 */
	public function add($quoteID, $postcode, $town, $county, $line1, $tenantTypeID, $agentManaged, $ownershipLengthID, $noClaimsYearsID, $excludeFloodCover, $line2, $line3, $country) {
		
        
        $data = array (
			'quote_id'				=> $quoteID,
			'postcode'				=> $postcode,
			'town'					=> $town,
			'county'				=> $county,
			'line_1'				=> $line1,
			'tenant_type_id'		=> $tenantTypeID,
			'letting_agent_managed'	=> $agentManaged?1:0,
			'ownership_length_id'	=> $ownershipLengthID,
			'no_claims_years_id'	=> $noClaimsYearsID,
			'exclude_flood_cover'	=> $excludeFloodCover?1:0
		);
		
		if (!is_null($line2)) $data['line_2'] = $line2;
		if (!is_null($line3)) $data['line_2'] = $line3;
		if (!is_null($country)) $data['country'] = $country;
		//Zend_Debug::dump($data);die();
        //throw new Zend_Exception('Invalid customer type specified.');
		return $this->insert($data);
	}
	
	/**
	 * Get all properties for a specific quote
	 */
	public function getByQuoteID($quoteID) {
		$select = $this->select()->where('quote_id = ?', $quoteID);
		
		$propertyRows = $this->fetchAll($select);
		if (count($propertyRows)>0) {
			return $propertyRows->toArray();
		}
	}
}
?>