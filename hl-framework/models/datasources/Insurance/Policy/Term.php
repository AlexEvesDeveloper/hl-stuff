<?php

/**
 * Data source definition for the legacy policyTerm table. This data source records
 * the length of a given policy.
 *
 * Class Datasource_Insurance_Policy_Term
 */
class Datasource_Insurance_Policy_Term extends Zend_Db_Table_Multidb
{
    /**
     * @var string database
     */
    protected $_multidb = 'db_legacy_homelet';

    /**
     * @var string primary id
     */
    protected $_primary = 'id';

    /**
     * @var string table name
     */
    protected $_name = 'policyTerm';

    /**
     * Inserts the policy term into the database.
     *
     * Receives a single Model_Insurance_Policy or Model_Insurance_LegacyQuote object - which
     * should contain the full data describing the
     * policy - and attempts to insert it into the policyTerm table.
     *
     * @param Model_Insurance_Policy|Model_Insurance_Quote $policy
     * A Model_Insurance_Policy or Model_Insurance_Quote object which provides information
     * about the policy term to insert.
     * @param float|null $netPremium Net premium or net set if null
     * @return mixed
     */
    public function insertPolicyTerm($policy, $netPremium=null)
    {
        $optionalFields = null;
        if ($netPremium !== null) {
            $optionalFields = array('netPremium' => $netPremium);
        }
        return $this->updatePolicyTerm($policy, 1, $optionalFields);
    }

    /**
     * Updates the term in the policy term table, or creates a new one if it doesn't exist
     *
     * @param object $policy
     * @param int $termNumber
     * @param $optionalFields array|null array of additional fields
     * @return null|int
     */
    public function updatePolicyTerm($policy, $termNumber, $optionalFields=null)
    {
        $select = $this->select();
        $select->where('policynumber = ?', $policy->policyNumber);
        $row = $this->fetchRow($select);

        $termPremium = $policy->premium;
        $termIPT = $policy->ipt;
        if ($policy->payBy == 'Monthly') {
            $factor = (float) $policy->policyLength;
            $termPremium = round($factor * (float)$policy->premium, 2);
            $termIPT = round($factor * (float)$policy->ipt, 2);
        }
        $termGrossPremium = $termPremium + $termIPT;

        $data = array(
            'policynumber' => $policy->policyNumber,
            'policylength' => $policy->policyLength,
            'termPremium' => $termPremium,
            'termIPT' => $termIPT,
            'termGrossPremium' => $termGrossPremium,
            'termStartdate' => $policy->startDate,
            'termEnddate' => $policy->endDate,
            'term' => $termNumber,
            'timeCreated' => date('Y-m-d H:i:s')
        );

        if ($optionalFields !== null) {
            $data = array_merge($data, $optionalFields);
        }

        if (count($row) > 0) {
            // Already exists so we are doing an update
            $where = $this->_db->quoteInto('policynumber = ?', $policy->policyNumber);
            $this->update($data, $where);

            return $row->id;
        }

        // New quote so just insert
        return $this->insert($data);
    }

    /**
     * Getts the policy term row from the database
     *
     * @param string $policynumber
     * @param string $policystartdate
     * @return mixed
     */
    public function getPolicyTerm($policynumber, $policystartdate)
    {
        $select = $this->select()
            ->where('policynumber = ?', $policynumber)
            ->where('termStartdate = ?', $policystartdate);
        $row = $this->fetchRow($select);

        return $row;
    }

    /**
     * Changes the record in the database when the quote is accepted into a policy
     *
     * @param string $quoteNumber
     * @param string|null $policyNumber
     * @return mixed
     */
    public function changeQuoteToPolicy($quoteNumber, $policyNumber = null)
    {

        //If policyNumber is empty then assume the QHLI should be replaced with PHLI.
        if (empty($policyNumber)) {

            $policyNumber = preg_replace('/^Q/', 'P', $quoteNumber);
        }

        $where = $this->quoteInto('policynumber = ?', $quoteNumber);
        $updatedData = array('policynumber' => $policyNumber);
        return $this->update($updatedData, $where);
    }
}
