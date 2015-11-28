<?php

/**
 * Model definition for the UwAutoTerms table. 
 */
class Datasource_Insurance_LandlordsPlus_Terms extends Zend_Db_Table_Multidb {
    
	protected $_multidb = 'db_legacy_homelet';    
	protected $_name = 'UWAutoTerms';
    protected $_primary = 'PC_COMPRESS';
	
	
	/**
	 * Returns the flood risk score for a given postcode.
	 * 
	 * Used by the underwriting logics.
	 * 
	 * @param string $postCode
	 * The postcode to check.
	 * 
	 * @return integer
	 * Returns the flood score.
	 */
	public function getFloodRiskScore($postCode) {
        
        $select = $this->select();
        $select->where('PC_COMPRESS = ?', $this->_sitOnPostcodeUntilFlat($postCode));
		$result = $this->fetchRow($select);
		
		if(!empty($result)) {
			
			$score = $result->FLD_FLAG;
		}
		else {

			$score = 0;
		}
		
		return $score;
	}
	
	
	/**
	 * Returns the subsidence risk score for a given postcode.
	 * 
	 * Used by the underwriting logics.
	 * 
	 * @param string $postCode
	 * The postcode to check.
	 * 
	 * @return integer
	 * Returns the subsidence score.
	 */
	public function getSubsidenceRiskScore($postCode) {

		$select = $this->select();
        $select->where('PC_COMPRESS = ?', $this->_sitOnPostcodeUntilFlat($postCode));
		$result = $this->fetchRow($select);
		
		if(!empty($result)) {
			
			$score = $result->SUB_FLAG;
		}
		else {

			$score = 0;
		}
		
		return $score;
	}
	
	
	/**
	 * Identifies if the postcode exists in the underwriting terms datasource.
	 * 
	 * Previously known as getPostcodeExists(), but was renamed as this was a rather
	 * ambiguous name.
	 * 
	 * @param string $postCode
	 * The postcode to search the datasource for.
	 * 
	 * @return boolean
	 * Returns true if the postcode exists in the datasource - and there has underwriting
	 * terms applied to it - false otherwise.
	 */
	public function getPostcodeHasTerms($postCode) {
		
		$select = $this->select();
        $select->where('PC_COMPRESS = ?', $this->_sitOnPostcodeUntilFlat($postCode));
		$result = $this->fetchRow($select);
		
		if(empty($result)) {
			
			$termsExist = false;
		}
		else {

			$termsExist = true;
		}
		
		return $termsExist;
	}
	
	
	/**
	 * Removes spaces from postcodes.
	 * 
	 * @param string $postCode
	 * The postcode to format.
	 * 
	 * @return string
	 * Returns a postcode without the space.
	 */
	protected function _sitOnPostcodeUntilFlat($postCode) {
		
		return preg_replace("/ /", '', $postCode);
	}
}

?>