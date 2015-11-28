<?php

/**
 * Class Datasource_Insurance_RentRecoveryPlus_RentRecoveryPlus
 *
 * @author April Portus <april.portus@barbon.com>
 */
class Datasource_Insurance_RentRecoveryPlus_RentRecoveryPlus extends Zend_Db_Table_Multidb
{
    /**
     * @var string table name
     */
    protected $_name = 'rent_recovery_plus';

    /**
     * @var string primary key
     */
    protected $_primary = 'policynumber';

    /**
     * @var string database
     */
    protected $_multidb = 'db_legacy_homelet';

    /**
     * Saves the data to the rent_recovery_plus table
     *
     * @param Model_Insurance_RentRecoveryPlus_RentRecoveryPlus $rrp
     * @return bool
     */
    public function save(Model_Insurance_RentRecoveryPlus_RentRecoveryPlus $rrp)
    {
        $isSuccessful = true;

        // Firstly we need to see if this already exists
        $select=$this->select();
        $select->where('policynumber = ?', $rrp->getPolicyNumber());
        $row = $this->fetchRow($select);

        $data = array(
            'policynumber'                       => $rrp->getPolicyNumber(),
            'reference_type'                     => $rrp->getReferenceType(),
            'other_provider'                     => $rrp->getOtherProvider(),
            'existing_policy_ref'                => $rrp->getExistingPolicyRef(),
            'is_existing_policy_to_be_cancelled' => $rrp->getIsExistingPolicyToBeCancelled(),
            'cancellation_objections'            => $rrp->getCancellationObjections(),
            'property_let_type'                  => $rrp->getPropertyLetType(),
            'has_landlord_permission'            => $rrp->getHasLandlordPermission(),
            'property_deposit'                   => $rrp->getPropertyDeposit(),
            'has_nil_deposit_insurance'          => $rrp->getHasNilDepositInsurance(),
            'tenancy_start_at'                   => $rrp->getTenancyStartAt(),
            'claim_info'                         => $rrp->getClaimInfo(),
            'is_continuation_policy'             => $rrp->getIsContinuationPolicy(),
        );

        if (count($row) > 0) {
            // Already exists so we are doing an update
            $where = $this->_db->quoteInto('policynumber = ?', $rrp->getPolicyNumber());
            $this->update($data, $where);
        }
        else {
            // New quote so just insert
            if ( ! $this->insert($data)) {
                // Failed insertion
                Application_Core_Logger::log("Can't insert quote in table {$this->_name}", 'error');
                $isSuccessful = false;
            }
        }

        return $isSuccessful;
    }

    /**
     * When the quote is accepted this changes the quote number to the policy number
     *
     * @param string $quoteNumber
     * @param string $policyNumber
     * @param null|string $insightStatus
     * @return bool
     */
    public function accept($quoteNumber, $policyNumber, $insightStatus=null)
    {
        $wasSuccessful = false;

        $data['policynumber'] = $policyNumber;
        if ($insightStatus) {
            $data['insight_status'] = $insightStatus;
        }

        // Firstly we need to see if this already exists
        $select=$this->select();
        $select->where('policynumber = ?', $quoteNumber);
        $row = $this->fetchRow($select);

        if (count($row) == 1) {

            // Already exists so we are doing an update
            $where = $this->_db->quoteInto('policynumber = ?', $quoteNumber);
            $this->update($data, $where);
            $wasSuccessful = true;
        }

        return $wasSuccessful;
    }

    /**
     * Gets the rent_recovery_plus record for the given policy number
     *
     * @param string $policyNumber
     * @return Model_Insurance_RentRecoveryPlus_RentRecoveryPlus|null
     */
    public function getRentRecoveryPlus($policyNumber)
    {
        $select = $this->select()
            ->where('policynumber = ?', $policyNumber);

        $row = $this->fetchRow($select);
        if ($row) {
            return Model_Insurance_RentRecoveryPlus_RentRecoveryPlus::hydrateFromRow($row->toArray());
        }
        return null;
    }

    /**
     * Sets the survey complete data for the give policy
     *
     * @param string $policyNumber
     * @param string $completeDate
     * @return bool
     */
    public function setSurveyCompletedAt($policyNumber, $completeDate)
    {
        // Firstly we need to see if this already exists
        $select = $this->select();
        $select->where('policynumber = ?', $policyNumber);
        $row = $this->fetchRow($select);

        if (count($row) > 0) {
            // Already exists so we are doing an update
            $data = array('survey_completed_at' => $completeDate);
            $where = $this->_db->quoteInto('policynumber = ?', $policyNumber);
            $this->update(
                $data,
                $where
            );
            return true;
        }

        // Can't update if it doesn't exist
        return false;
    }
}