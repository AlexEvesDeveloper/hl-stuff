<?php

/**
 * Business rules class which provides underwriting loading services.
 * 
 * @todo
 * Replace the class constants with something better.
 */
class Manager_Insurance_LandlordsPlus_Loading {
    
	protected $_loadingModel;
	
	
	/**#@+
	 * Constants used to specify arguments to methods of this class.
	 * 
	 * @var integer
	 * 
	 * @todo
	 * Replace these constants with a better solution, possibly pulling
	 * the coverage identifiers from the new LI+ quote engine (not yet built
	 * at this time).
	 */
	const BUILDINGS = 1;
	const CONTENTS = 2;
	/**#@-*/
    
    
	/**
	 * Returns the flood loading for a particular cover.
	 * 
	 * @param integer $coverType
	 * Must correspond to one of the consts exposed by this class.
	 * 
	 * @param boolean $isGross
	 * True if the gross loading is required, false if the nett loading is required.
	 * 
	 * @return float
	 * The loading.
	 */
    public function getDefaultFloodLoading($coverType, $isGross = true) {
		
		$params = Zend_Registry::get('params');

		$loading = 0.0;
		if($coverType == self::BUILDINGS) {
			
			if($isGross === true) {
				
				$loading = $params->uw->ld->landlordsp->buildings->gross->flood;
			}
			else {
				
				$loading = $params->uw->ld->landlordsp->buildings->net->flood;
			}
		}
		else if($coverType == self::CONTENTS) {
			
			if($isGross === true) {
				
				$loading = $params->uw->ld->landlordsp->contents->gross->flood;
			}
			else {
				
				$loading = $params->uw->ld->landlordsp->buildings->net->flood;
			}
		}
		
		return $loading;
	}
	
	
	/**
	* Reflects in the datasource the loading(s) to be applied to a particular quote/policy.
	*
	* @param string $policyNumber
	* The quote or policy number.
	*
	* @param string $optionsdiscounts
	* The pipe-delimited loadings to insert into the record. This will reflect the
	* loading applied to the particular coverages.
	*
	* @param Zend_Date $date
	* The date on which the loading applies.
	*
	* @return boolean
	* Returns true if loading is successfully inserted.
	*/
	public function addLoadingHistory($policyNumber, $optionDiscounts, $date) {
		
		if(empty($this->_loadingModel)) {
			
			$this->_loadingModel = new Datasource_Insurance_LandlordsPlus_Loadings();
		}

		$this->_loadingModel->addLoadingHistory($policyNumber, $optionDiscounts, $date);
	}
}

?>