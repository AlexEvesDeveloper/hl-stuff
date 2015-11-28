<?php

/**
 * Business rules class which provides underwriting previous claims services.
 */
class Manager_Insurance_PreviousClaims {

    protected $_previousClaimsModel;
	protected $_previousClaimTypesModel;


	/**
	 * Convenience function to insert previous claims into the data storage.
	 *
	 * Receives an array of Model_Insurance_PreviousClaim objects
	 * and attempts to insert them into the data storage. This action should
	 * ideally be done before the quote/policy is referred, but is not essential.
	 *
	 * @param array $claimsArray
	 * An array of Model_Insurance_PreviousClaim objects which
	 * provide information about each of the previous claims.
	 */
    public function insertPreviousClaims($previousClaimArray) {

        foreach($previousClaimArray as $previousClaim) {

            $this->insertPreviousClaim($previousClaim);
        }
    }


	/**
	 * Inserts a single previous claim into the data storage.
	 *
	 * Receives a single Model_Insurance_PreviousClaim object and
	 * attempts to insert it into the data storage. This action should
	 * ideally be done before the quote/policy is referred, but is not essential.
	 *
	 * @param Model_Insurance_PreviousClaim $previousClaim
	 * A Model_Insurance_PreviousClaim object which provides information
	 * about the previous claim to insert.
	 */
    public function insertPreviousClaim($previousClaim) {

        if(empty($this->_previousClaimsModel)) {

            $this->_previousClaimsModel = new Datasource_Insurance_PreviousClaims();
        }

        $this->_previousClaimsModel->insertPreviousClaim($previousClaim);
    }


	/**
     * Tests if a previous claim is already stored.
     *
	 * Returns true if the $previousClaim details are already stored, false otherwise.
	 * This is useful in ensuring the same details are not recorded multiple times.
	 *
	 * @param Model_Insurance_PreviousClaim
	 * The previous claim details.
	 *
	 * @return boolean
	 * True if the previous claim has already been stored, false otherwise.
	 */
    public function getIsPreviousClaimAlreadyStored($previousClaim) {

		$previousClaimsArray = $this->getPreviousClaims($previousClaim->getRefno());

        $returnVal = false;
        if(!empty($previousClaimsArray)) {

            //One or more previous claims are already stored against the customer
            //refno. Now check to see if the one passed in has already been stored.
            foreach($previousClaimsArray as $current) {

                if($previousClaim->equals($current)) {

                    $returnVal = true;
                    break;
                }
            }
        }

        return $returnVal;
    }


	/**
	 * Counts previous claims made by a customer.
	 *
	 * This method returns the number of previous claims stored in the data storage
	 * against the refNo provided.
	 *
	 * @param string $refNo
	 * The means by which the previous claims will be identifed in the data storage.
	 *
	 * @return integer
	 * The number of preivous claims for this refNo.
	 */
	public function countPreviousClaims($refNo) {

		if(empty($this->_previousClaimsModel)) {

            $this->_previousClaimsModel = new Datasource_Insurance_PreviousClaims();
        }

		$previousClaimsArray = $this->_previousClaimsModel->getPreviousClaims($refNo);
		if(!empty($previousClaimsArray)) {

			$returnVal = count($previousClaimsArray);
		}
		else {

			$returnVal = 0;
		}
		return $returnVal;
	}


	/**
	 * Returns an array of Model_Insurance_PreviousClaim objects
	 * related to the refNo passed in, or null if there are no previous claims.
	 *
	 * @param string $refNo
	 * The customer refno which identifies the previous claims.
	 *
	 * @return mixed
	 * Returns an array of Model_Insurance_PreviousClaim objects,
	 * or null if there are no previous claims.
	 */
	public function getPreviousClaims($refNo) {

		if(empty($this->_previousClaimsModel)) {

            $this->_previousClaimsModel = new Datasource_Insurance_PreviousClaims();
        }

        return $this->_previousClaimsModel->getPreviousClaims($refNo);
	}


	/**
	 * Returns all the previous claim types.
	 *
	 * This method reads in all the previous claim types recognised by the
	 * system and populates each one into a PreviousClaimType object. An
	 * array of these objects will then be returned.
	 *
	 * This could be used by client code to populate a drop down of options,
	 * allowing the user to select a type of claim they have previously made on
	 * another policy.
	 *
	 * The results can be filtered by supplying an argument to reflect this.
	 * The argument, if given, must correspond to one of the consts exposed by
	 * this class.
	 *
	 * @param string $productName
	 * Filters the results by product name. Must correspond to a const exposed by the
	 * Model_Insurance_ProductNames class.
	 *
	 * @return array
	 * An array of Model_Insurance_PreviousClaimType objects, each one encapsulating 
	 * a valid previous claim type.
	 */
	public function getPreviousClaimTypes($productName = null) {

		if(empty($this->_previousClaimTypesModel)) {

            $this->_previousClaimTypesModel = new Datasource_Insurance_PreviousClaimTypes();
        }

        return $this->_previousClaimTypesModel->getPreviousClaimTypes($productName);
	}


	/**
	 * Removes a specific previous claim.
	 *
	 * @param Model_Insurance_PreviousClaim
	 * Identifies the previous claim to remove.
	 */
	public function removePreviousClaim($previousClaim) {

		if(empty($this->_previousClaimsModel)) {

            $this->_previousClaimsModel = new Datasource_Insurance_PreviousClaims();
        }
		$this->_previousClaimsModel->removePreviousClaim($previousClaim);
	}


	/**
	 * Removes previous claims.
	 *
	 * This method removes all the previous claims associated with the refNo
	 * passed in.
	 *
	 * @param string $refNo
	 * The customer refno which identifies the previous claims.
	 */
	public function removeAllPreviousClaims($refNo) {

		if(empty($this->_previousClaimsModel)) {

            $this->_previousClaimsModel = new Datasource_Insurance_PreviousClaims();
        }
		$this->_previousClaimsModel->removeAllPreviousClaims($refNo);
	}
}

?>