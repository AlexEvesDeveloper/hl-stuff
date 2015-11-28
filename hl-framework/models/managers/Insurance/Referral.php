<?php

/**
 * Business rules class which provides underwriting referrals services.
 */
abstract class Manager_Insurance_Referral {

	protected $_previousClaimsModel;


	/**
	 * Determines the reasons a quote / policy will require referral, if any.
	 *
	 * This method will perform a number of tests on the data associated with
	 * the policy / quote number passed in. If any of the tests determine that a
	 * referral will be required, then the reason will be added to an array which will
	 * later be returned to the calling code. The reasons will correspond to
	 * the const values exposed by this class.
	 *
	 * Note that this method will NOT refer the quote / policy.
	 *
	 * @param string $policyNumber
	 * The quote / policy number which will be checked for referral-triggering
	 * data.
	 *
	 * @return mixed
	 * Returns an array of referral reasons (if any), each element of which will be
	 * a string value. Returns null	if there are no reasons for referral.
	 *
	 * @todo Produce Referral objects and return these in an array, rather than strings.
	 */
    public abstract function getReferralReasons($policyNumber);


	/**
	 * Determines whether a quote / policy will require referral.
	 *
	 * This method will perform a number of tests on the data associated with
	 * the policy / quote number passed in. If any of the tests determine that a
	 * referral will be required, then this method will return true. Note that this
	 * method will NOT refer the quote / policy.
	 *
	 * @param string $policyNumber
	 * The quote / policy number which will be checked for referral-triggering
	 * data.
	 *
	 * @return boolean
	 * Returns true/false according to whether or not the quote / policy should
	 * be referred.
	 */
	public function getRequiresReferral($policyNumber) {

		$referralReasons = $this->getReferralReasons($policyNumber);

		if(empty($referralReasons)) {

			return false;
		}
		
		return true;
	}


	/**
     * Sets the paystatus to 'Referred'.
     *
     * Method responsible for setting the paystatus of the quote or policy
     * passed in to a value of 'Referred'. Does NOT perform any other related
     * actions, such as notifying underwriting.
     *
	 * @param string $policyNumber
	 * The full quote/policy number of the quote/policy to update.
     *
     * @return boolean
     * Returns true on successful update, false otherwise.
     */
    public abstract function setToRefer($policyNumber);
    }

?>