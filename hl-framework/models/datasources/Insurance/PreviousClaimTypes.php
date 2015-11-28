<?php

/**
 * Data source definition for the previous claim types table. This table stores
 * information that a user may specify when incepting a policy, such as where they
 * have made a previous claim for an 'Impact or Collision Any Other Cause (not Aircraft)'
 */
class Datasource_Insurance_PreviousClaimTypes extends Zend_Db_Table_Multidb {

	protected $_multidb = 'db_legacy_homelet';	
	protected $_name = 'claimTypes';
	protected $_primary = 'claimTypeID';


	public function getByClaimTypeId($id) {
		
		$select = $this->select();
        $select->where('claimTypeID = ?', $id);
		$claimTypeRow = $this->fetchRow($select);
		
		if(!empty($claimTypeRow)) {
			
			$returnVal = new Model_Insurance_PreviousClaimType(
				$claimTypeRow->claimTypeID,
    			$claimTypeRow->claimType,
    			$claimTypeRow->claimTypeText);
		}
		else {
			
			$returnVal = null;
		}
		return $returnVal;
	}
	
	/**
	 * Returns all the previous claim types.
	 *
	 * This method reads in all the previous claim types recognised by the
	 * system and populates each one into a PreviousClaimType object. An
	 * array of these objects will then be returned. The results can be filtered
	 * by supplying an argument to reflect this.
	 *
	 * @param string $productName
	 * Filters the results by product name. Must correspond to a const exposed by the
	 * Model_Insurance_ProductNames class.
	 *
	 * @return array
	 * An array of PreviousClaimType objects, each one encapsulating a valid previous
	 * claim type.
	 */
	public function getPreviousClaimTypes($productName = null) {

        $select = $this->select();

		//Prepare the where clause, if applicable.
		if(!empty($productName)) {

			switch($productName) {

				case Model_Insurance_ProductNames::TENANTCONTENTSPLUS:
					$fieldValue = 'tenants';
					break;

				case Model_Insurance_ProductNames::LANDLORDSPLUS:
					$fieldValue = 'landlords';
					break;
				
				case Model_Insurance_ProductNames::PORTFOLIOINSURANCE:
					$fieldValue = 'portfolio';
					break;

				default:
					throw new Zend_Exception(get_class() > __FUNCTION__ . ': argument supplied is invalid.');
			}
			$select->where('claimType = ?', $fieldValue);
		}

        $claimTypesAll = $this->fetchAll($select);

        if (count($claimTypesAll) > 0) {
    		//Populate the data retrieved into PreviousClaimType objects.
    		$returnArray = array();
            foreach ($claimTypesAll as $currentClaimType) {
    
                $returnArray[] = new Model_Insurance_PreviousClaimType(
    				$currentClaimType['claimTypeID'],
    				$currentClaimType['claimType'],
    				$currentClaimType['claimTypeText']);
            }
            return $returnArray;
        } else {
            // Can't get previous claim types - log a warning
            Application_Core_Logger::log("Can't get previous claim types from table {$this->_name} (filterBy = {$productName})", 'warning');
        }
	}
}

?>