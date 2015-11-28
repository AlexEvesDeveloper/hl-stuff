<?php

/**
 * Data source definition for the previousclaims table. This data source records
 * previous claims made by the customer (or potential customer) on previous
 * insurance policies.
 */
class Datasource_Insurance_PreviousClaims extends Zend_Db_Table_Multidb {

	protected $_multidb = 'db_legacy_homelet';
    protected $_name = 'previousclaims';


    /**
	 * Inserts previous claims into the dbase.
	 *
	 * Receives an array of Model_Insurance_PreviousClaim objects,
	 * each representing an instance where the customer has made a claim on a previous
	 * insurance policy, and attempts to insert them into the previousClaims table.
	 *
	 * @param array $claimsArray
	 * An array of Model_Insurance_PreviousClaim objects which provide
	 * information about each of the previous claims.
	 */
	public function insertPreviousClaims($previousClaimArray) {

		foreach($previousClaimArray as $previousClaim) {

			$this->insertPreviousClaim($previousClaim);
		}
    }


	/**
	 * Inserts a single previous claim into the dbase.
	 *
	 * Receives a single Model_Insurance_PreviousClaim object - which
	 * represents an instance where the customer has made a claim on a previous
	 * policy - and attempts to insert it intothe previousClaims table.
	 *
	 * @param Model_Insurance_PreviousClaim $previousClaim
	 * A Model_Underwriting_DomainObjects_PreviousClaim object which provide information
	 * about the previous claim to insert.
	 */
	public function insertPreviousClaim($previousClaim) {

		$data = array(
			'refno' => $previousClaim->getRefno(),
			'claimmonth' => $previousClaim->getClaimMonth(),
			'claimyear' => $previousClaim->getClaimYear(),
			'claimvalue' => $previousClaim->getClaimValue()->getValue(),
			'claimTypeID' => $previousClaim->getClaimType()->getClaimTypeID()
		);

		if (!$this->insert($data)) {
            // Failed insertion
            Application_Core_Logger::log("Can't insert previous claim in table {$this->_name}", 'error');
        }
    }


	/**
	 * Returns an array of Model_Insurance_PreviousClaim objects
	 * related to the customer refno passed in, or null if the customer has not
	 * specified any previous claims.
	 *
	 * @param string $customerRefno
	 * The customer refno which identifies the previous claims.
	 *
	 * @return mixed
	 * Returns an array of Model_Insurance_PreviousClaim objects, or
	 * null if there are no previous claims.
	 */
	public function getPreviousClaims($customerRefno) {

		$select = $this->select();
        $select->where('refno = ?', $customerRefno);
        $previousClaimArray = $this->fetchAll($select);

		$returnArray = array();
		$previousClaimTypeDatasource = new Datasource_Insurance_PreviousClaimTypes();
		foreach($previousClaimArray as $current) {

			$previousClaim = new Model_Insurance_PreviousClaim();
			$previousClaim->setRefno($current['refno']);
			$previousClaim->setClaimMonth($current['claimmonth']);
			
			$previousClaimType = $previousClaimTypeDatasource->getByClaimTypeId($current['claimTypeID']);
			$previousClaim->setClaimType($previousClaimType);

			//Put the claim value in a Zend_Currency object.
			$claimValue = new Zend_Currency(
				array(
					'value' => $current['claimvalue'],
					'precision' => 2
				));
			$previousClaim->setClaimValue($claimValue);

			$previousClaim->setClaimYear($current['claimyear']);
			$returnArray[] = $previousClaim;
		}


		//Finalise the return value consistent with this functions contract.
		if(empty($returnArray)) {

			$returnVal = null;
		}
		else {

			$returnVal = $returnArray;
		}

		return $returnVal;
	}
	
	
	/**
	 * Removes a specific previous claim.
	 *
	 * @todo
	 * MuntSeverity: 6/10
	 *
	 * @param Model_Insurance_PreviousClaim
	 * The PreviousClaim to delete.
	 *
	 * @return void
	 */
	public function removePreviousClaim($previousClaim) {

		//The values have to be exactly the same, so modify the month and
		//currency values.
		$month = $previousClaim->getClaimMonth();
		if($month < 10) {
			
			$month = '0' . $previousClaim->getClaimMonth();
		}

		$value = $previousClaim->getClaimValue()->getValue();
		if(!preg_match("/00$/", $value)) {
			
			$value = $value . '.00';
		}

		$where = $this->quoteInto(
			'refno = ? AND claimmonth = ? AND claimyear = ? AND claimvalue = ? AND claimTypeID = ? AND claimtype = ?',
			$previousClaim->getRefno(),
			$month,
			$previousClaim->getClaimYear(),
			$value,
			$previousClaim->getClaimType()->getClaimTypeID(),
			''
		);
		
        $this->delete($where);
	}


	public function removeAllPreviousClaims($customerRefno) {

		$where = $this->quoteInto('refno = ?', $customerRefno);
        $this->delete($where);
	}
}

?>