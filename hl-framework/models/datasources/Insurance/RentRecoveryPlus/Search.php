<?php

/**
 * Class Datasource_Insurance_RentRecoveryPlus_Search
 *
 * @author April Portus <april.portus@barbon.com>
 */
class Datasource_Insurance_RentRecoveryPlus_Search extends Zend_Db_Table_Multidb
{
    /**
     * Identifier for renewal invited
     */
    const RENEWAL_PREFERENCE_INVITED = 'Renewal Invited';

    /**
     * Identifier for assumptive renewal
     */
    const RENEWAL_PREFERENCE_ASSUMPTIVE = 'Assumptive Renewal';

    /**
     * Policy table name
     */
    const POLICY_TABLE_NAME = 'policy';

    /**
     * Policy table alias
     */
    const POLICY_TABLE_ALIAS = 'P';

    /**
     * Quote table name
     */
    const QUOTE_TABLE_NAME = 'quote';

    /**
     * Quote table alias
     */
    const QUOTE_TABLE_ALIAS = 'Q';

    /**
     * 'Insight RRP Policy' table name
     */
    const INSIGHT_TABLE_NAME = 'insight_rrp_policy';

    /**
     * 'Insight RRP Policy' table alias
     */
    const INSIGHT_TABLE_ALIAS = 'R';

    /**
     * Rent Recovery Plus table name
     */
    const RRP_TABLE_NAME = 'rent_recovery_plus';

    /**
     * Rent Recovery Plus table alias
     */
    const RRP_TABLE_ALIAS = 'I';

    /**
     * Landlord Interest table name
     */
    const LLI_TABLE_NAME = 'landlord_interest';

    /**
     * Landlord Interest table alias
     */
    const LLI_TABLE_ALIAS = 'L';

    /**
     * New Agents table name
     */
    const NEWAGENT_TABLE_NAME = 'newagents';

    /**
     * New Agents table alias
     */
    const NEWAGENT_TABLE_ALIAS = 'A';

    /**
     * Policy Term table name
     */
    const TERM_TABLE_NAME = 'policyTerm';

    /**
     * Policy Term table alias
     */
    const TERM_TABLE_ALIAS = 'T';

    /**
     * Renewal Preference table name
     */
    const RENEWAL_TABLE_NAME = 'renewal_preference';

    /**
     * Renewal Preference table alias
     */
    const RENEWAL_TABLE_ALIAS = 'W';

    /**
     * @var string
     */
    protected $_name = 'rent_recovery_plus';

    /**
     * @var string
     */
    protected $_primary = 'policynumber';

    /**
     * @var string
     */
    protected $_multidb = 'db_legacy_homelet';

    /**
     * @var array of table names keyed on self:*_ALIAS
     */
    private $tableNames;

    /**
     * @var int
     */
    private $agentSchemeNumber;

    /**
     * @var string
     */
    private $policyNumber;

    /**
     * @var string
     */
    private $landlordName;

    /**
     * @var string
     */
    private $propertyPostcode;

    /**
     * Constructor to initialise private variables
     */
    public function __construct()
    {
        $this->agentSchemeNumber = null;
        $this->tableNames = array();
        $this->tableNames[self::POLICY_TABLE_ALIAS] = self::POLICY_TABLE_NAME;
        $this->tableNames[self::QUOTE_TABLE_ALIAS] = self::QUOTE_TABLE_NAME;
        $this->tableNames[self::INSIGHT_TABLE_ALIAS] = self::INSIGHT_TABLE_NAME;
        $this->tableNames[self::RRP_TABLE_ALIAS] = self::RRP_TABLE_NAME;
        $this->tableNames[self::LLI_TABLE_ALIAS] = self::LLI_TABLE_NAME;
        $this->tableNames[self::NEWAGENT_TABLE_ALIAS] = self::NEWAGENT_TABLE_NAME;
        $this->tableNames[self::TERM_TABLE_ALIAS] = self::TERM_TABLE_NAME;
        $this->tableNames[self::RENEWAL_TABLE_ALIAS] = self::RENEWAL_TABLE_NAME;

        parent::__construct();
    }

    /**
     * Sets the search criteria
     *
     * @param int $agentSchemeNumber
     * @param string $policyNumber
     * @param string $landlordName
     * @param string $propertyPostcode
     * @return $this
     */
    public function setCriteria($agentSchemeNumber, $policyNumber, $landlordName, $propertyPostcode)
    {
        $this->agentSchemeNumber = $agentSchemeNumber;
        $this->policyNumber = $policyNumber;
        $this->landlordName = $landlordName;
        $this->propertyPostcode = $propertyPostcode;
        return $this;
    }

    /**
     * Searches for policies from the quote or policy tables (or both) depending on the type
     *
     * @param int $offset
     * @param int $limit
     * @param array $type - set of *_TABLE_ALIAS
     * @return array
     * @throws Exception
     */
    public function searchForPolicies($offset, $limit, $type=array(self::POLICY_TABLE_ALIAS,self::QUOTE_TABLE_ALIAS))
    {
        if ($this->agentSchemeNumber == null) {
            throw new Exception('Criteria not set');
        }
        $this->checkTypeValid($type);

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array(self::RRP_TABLE_ALIAS => $this->tableNames[self::RRP_TABLE_ALIAS]),
            $this->getFields($type)
        );
        $select = $this->addJoin($select, $type);
        $select = $this->addWhere($select, $type, $this->agentSchemeNumber);
        $select->limit($limit, $offset);
        $rowSet = $this->fetchAll($select);

        $results = array();
        foreach ($rowSet as $row) {
            $results[] = $row->toArray();
        }
        return $results;
    }

    /**
     * Searches for a single policy using the policy number
     *
     * @param int $agentSchemeNumber
     * @param string $policyNumber
     * @return array|null
     */
    public function searchForPolicyByNumber($agentSchemeNumber, $policyNumber)
    {
        $this->setCriteria($agentSchemeNumber, $policyNumber, '', '');
        $type = array(self::POLICY_TABLE_ALIAS,self::QUOTE_TABLE_ALIAS);

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array(self::RRP_TABLE_ALIAS => $this->tableNames[self::RRP_TABLE_ALIAS]),
            $this->getFields($type)
        );
        $select = $this->addJoin($select, $type);
        $select = $this->addWhere($select, $type, $this->agentSchemeNumber);
        $row = $this->fetchRow($select);

        if ($row) {
            return $row->toArray();
        }
        return null;
    }

    /**
     * Gets a array of policy numbers from the insight_rrp_policy by end date
     *
     * @param \DateTime $endDate
     * @param string $insightStatus
     * @param bool $isRenewalInvite
     * @return array
     */
    public function searchForInsightByEndDate($endDate, $insightStatus, $isRenewalInvite=null)
    {
        $type = array(self::INSIGHT_TABLE_ALIAS);
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array(self::RRP_TABLE_ALIAS => $this->tableNames[self::RRP_TABLE_ALIAS]),
            array(sprintf('%s.policynumber', self::INSIGHT_TABLE_ALIAS))
        );
        $select = $this->addJoin($select, $type);
        $select->joinLeft(
            array(self::RENEWAL_TABLE_ALIAS => $this->tableNames[self::RENEWAL_TABLE_ALIAS]),
            sprintf(
                '%s.policynumber = %s.policynumber',
                self::RENEWAL_TABLE_ALIAS,
                self::RRP_TABLE_ALIAS
            ),
            ''
        );
        $select->where(
            sprintf('%s.insight_status = ?', self::RRP_TABLE_ALIAS),
            $insightStatus
        );
        $select->where(
            sprintf('%s.payStatus != ?', self::INSIGHT_TABLE_ALIAS),
            Model_Insurance_LegacyPolicy::PAY_STATUS_CANCELLED
        );
        $select->where(
            sprintf('%s.enddate = ?', self::INSIGHT_TABLE_ALIAS),
            $endDate->format('Y-m-d')
        );

        if ($isRenewalInvite === null) {
            // If null the preference is irrelevant
        }
        else if ($isRenewalInvite) {
            $select->where(
                sprintf('%s.renewalPreference = ?', self::RENEWAL_TABLE_ALIAS),
                self::RENEWAL_PREFERENCE_INVITED
            );
        }
        else {
            $select->where(
                sprintf(
                    '%s.renewalPreference = ? OR %s.id IS NULL',
                    self::RENEWAL_TABLE_ALIAS,
                    self::RENEWAL_TABLE_ALIAS
                ),
                self::RENEWAL_PREFERENCE_ASSUMPTIVE
            );
        }

        $rowSet = $this->fetchAll($select);

        $results = array();
        foreach ($rowSet as $row) {
            $results[] = $row->policynumber;
        }
        return $results;
    }

    /**
     * Gets a array of email addresses for policies issued on the given date, excluding policies in the exclude array
     *
     * @param \DateTime $issueDate
     * @param bool $isInception
     * @param array $listOfExcludedPolicies
     * @return array
     */
    public function searchForSurveyEmails($issueDate, $isInception, $listOfExcludedPolicies)
    {
        $type = array(self::POLICY_TABLE_ALIAS);
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array(self::RRP_TABLE_ALIAS => $this->tableNames[self::RRP_TABLE_ALIAS]),
            array(
                sprintf('%s.email', self::LLI_TABLE_ALIAS),
                sprintf('%s.name AS agentName', self::NEWAGENT_TABLE_ALIAS),
                sprintf(
                    'concat(%s.firstname,\' \',%s.lastname) AS landlordName',
                    self::LLI_TABLE_ALIAS,
                    self::LLI_TABLE_ALIAS
                ),
                sprintf('%s.policyNumber', self::POLICY_TABLE_ALIAS),
                sprintf('MAX(%s.term) AS latestTerm', self::TERM_TABLE_ALIAS)
            )
        );
        $select = $this->addJoin($select, $type);
        $select->join(
            array(self::NEWAGENT_TABLE_ALIAS => $this->tableNames[self::NEWAGENT_TABLE_ALIAS]),
            sprintf(
                '%s.agentschemeno = %s.companyschemenumber',
                self::NEWAGENT_TABLE_ALIAS ,
                self::POLICY_TABLE_ALIAS
            ),
            ''
        );
        $select->join(
            array(self::TERM_TABLE_ALIAS => $this->tableNames[self::TERM_TABLE_ALIAS]),
            sprintf(
                '%s.policynumber = %s.policynumber',
                self::TERM_TABLE_ALIAS ,
                self::POLICY_TABLE_ALIAS
            ),
            ''
        );
        if ($isInception) {
            $select->where(sprintf('%s.issueDate = ?', self::POLICY_TABLE_ALIAS), $issueDate->format('Y-m-d'));
        }
        else {
            $select->where(sprintf('%s.startDate = ?', self::POLICY_TABLE_ALIAS), $issueDate->format('Y-m-d'));
        }
        $select->where(sprintf(
            '%s.endDate > DATE_ADD(%s.issueDate, INTERVAL 28 DAY)',
            self::POLICY_TABLE_ALIAS,
            self::POLICY_TABLE_ALIAS
        ));
        $select->where(
            sprintf('%s.payStatus NOT IN (?)',self::POLICY_TABLE_ALIAS),
            array(
                Model_Insurance_LegacyPolicy::PAY_STATUS_CANCELLED,
                Model_Insurance_LegacyPolicy::PAY_STATUS_RENEWAL_OVERDUE
            )
        );
        if (count($listOfExcludedPolicies) > 0) {
            $select->where(sprintf('%s.policyNumber NOT IN (?)', self::POLICY_TABLE_ALIAS), $listOfExcludedPolicies);
        }
        $select->group(sprintf('%s.policyNumber', self::POLICY_TABLE_ALIAS));
        if ($isInception) {
            $select->having('latestTerm = 1');
        }
        else {
            $select->having('latestTerm > 1');
        }

        $rowSet = $this->fetchAll($select);

        $results = array();
        foreach ($rowSet as $row) {
            $results[] = array(
                'emailAddress' => $row->email,
                'lettingAgent' => $row->agentName,
                'landlordName' => $row->landlordName,
                'policyNumber' => $row->policyNumber
            );
        }
        return $results;
    }

    /**
     * Searches the database to determine which in the list have have the survey complete date set
     *
     * @param array $listOfPolicies
     * @return array
     */
    public function searchForUncompletedSurveys($listOfPolicies)
    {
        $type = array(self::POLICY_TABLE_ALIAS);
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array(self::RRP_TABLE_ALIAS => $this->tableNames[self::RRP_TABLE_ALIAS]),
            array(
                sprintf('%s.email', self::LLI_TABLE_ALIAS),
                sprintf('%s.policyNumber', self::POLICY_TABLE_ALIAS)
            )
        );
        $select = $this->addJoin($select, $type);
        $select
            ->where(sprintf('%s.survey_completed_at IS NULL', self::RRP_TABLE_ALIAS))
            ->where(sprintf('%s.email IS NOT NULL', self::LLI_TABLE_ALIAS))
            ->where(sprintf('%s.policyNumber IS NOT NULL', self::POLICY_TABLE_ALIAS))
        ;
        if (count($listOfPolicies) > 0) {
            $select->where(sprintf('%s.policyNumber IN (?)', self::POLICY_TABLE_ALIAS), $listOfPolicies);
        }

        $rowSet = $this->fetchAll($select);

        $results = array();
        foreach ($rowSet as $row) {
            $results[] = array(
                'emailAddress' => $row->email,
                'policyNumber' => $row->policyNumber
            );
        }
        return $results;

    }

    /**
     * Finds the total count of policies that satisfy the search criteria
     *
     * @param array $type - set of *_TABLE_ALIAS
     * @throws Exception
     * @return int
     */
    public function getTotalCount($type=array(self::POLICY_TABLE_ALIAS,self::QUOTE_TABLE_ALIAS))
    {
        if ($this->agentSchemeNumber == null) {
            throw new Exception('Criteria not set');
        }
        $this->checkTypeValid($type);

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array(self::RRP_TABLE_ALIAS => $this->tableNames[self::RRP_TABLE_ALIAS]),
            array(sprintf('count(%s.policynumber) as num', self::RRP_TABLE_ALIAS))
        );
        $select = $this->addJoin($select, $type);
        $select = $this->addWhere($select, $type, $this->agentSchemeNumber);

        $row = $this->fetchRow($select);
        if ($row) {
            return $row->num;
        }
        return 0;
    }

    /**
     * This build up the field list for the summary
     * Note: This caters for the policy/quote tables join
     *
     * @param array $type - set of *_TABLE_ALIAS
     * @return array
     */
    private function getFields($type)
    {
        $returnFields = array(
            self::LLI_TABLE_ALIAS . '.title',
            self::LLI_TABLE_ALIAS . '.firstname',
            self::LLI_TABLE_ALIAS . '.lastname',
            self::LLI_TABLE_ALIAS . '.phone',
            self::LLI_TABLE_ALIAS . '.email'
        );
        /** @var array $typeFields - list of fields that exist in all of policy, quote and insight_rrp_policy */
        $typeFields =
            array(
                '@.policynumber' => 'policynumber',
                '@.policylength' => 'policylength',
                '@.propaddress1' => 'propaddress1',
                '@.propaddress3' => 'propaddress3',
                '@.propaddress5' => 'propaddress5',
                '@.proppostcode' => 'proppostcode',
                '@.startdate' => 'startdate',
                '@.cancdate' => 'cancdate',
                '@.paystatus' => 'paystatus',
                '@.amountscovered' => 'amountscovered',
                '@.policyoptions' => 'policyoptions',
            );

        if (count($type) == 3) {
            foreach ($typeFields as $field => $as) {
                $returnFields[] = sprintf(
                    'IFNULL(IFNULL(%s, %s), %s) AS %s',
                        str_replace('@', $type[1], $field),
                        str_replace('@', $type[1], $field),
                        str_replace('@', $type[2], $field),
                    $as
                );
            }
        }
        else if (count($type) == 2) {
            foreach ($typeFields as $field => $as) {
                $returnFields[] = sprintf(
                    'IFNULL(%s, %s) AS %s',
                        str_replace('@', $type[0], $field),
                        str_replace('@', $type[1], $field),
                    $as
                );
            }
        }
        else {
            foreach ($typeFields as $field => $as) {
                $returnFields[] = sprintf(
                    '%s AS %s',
                    str_replace('@', $type, $field),
                    $as
                );
            }
        }
        return $returnFields;
    }

    /**
     * Adds the join fields
     * Note: This caters for the policy/quote tables join
     *
     * @param object $select
     * @param array $type - set of *_TABLE_ALIAS
     * @return mixed
     */
    private function addJoin($select, $type)
    {
        foreach ($type as $individualType) {
            $select->joinleft(
                array($individualType => $this->tableNames[$individualType]),
                sprintf('%s.policynumber = %s.policynumber', self::RRP_TABLE_ALIAS, $individualType),
                ''
            );
        }
        $select->join(
            array(self::LLI_TABLE_ALIAS => $this->tableNames[self::LLI_TABLE_ALIAS]),
            sprintf('%s.policynumber = %s.policynumber', self::LLI_TABLE_ALIAS, self::RRP_TABLE_ALIAS),
            ''
        );
        return $select;
    }

    /**
     * Adds the where clauses
     *
     * @param object $select
     * @param array $type - set of *_TABLE_ALIAS
     * @param int $agentSchemeNumber
     * @return mixed
     */
    private function addWhere($select, $type, $agentSchemeNumber)
    {
        if ( ! empty($this->landlordName)) {
            $select->where(
                sprintf('concat(%s.firstname,\' \',%s.lastname) like ?', self::LLI_TABLE_ALIAS, self::LLI_TABLE_ALIAS),
                "%{$this->landlordName}%"
            );
        }
        if ( ! empty($this->policyNumber)) {
            $select->where(sprintf('%s.policynumber like ?', self::RRP_TABLE_ALIAS), "%{$this->policyNumber}%");
        }
        $select
            ->where(
                sprintf('%s.insight_status = ?', self::RRP_TABLE_ALIAS),
                Model_Insurance_RentRecoveryPlus_RentRecoveryPlus::INSIGHT_STATUS_IAS
            )
            ->where(
                $this->addWhereTypes('%s.companyschemenumber = ?', $type),
                $agentSchemeNumber
            )
            ->where(
                $this->addWhereTypes('%s.payStatus != ?', $type),
                Model_Insurance_LegacyPolicy::PAY_STATUS_CANCELLED
            );
        if (strlen($this->propertyPostcode) > 4) {
            $select->where($this->addWhereTypes('%s.proppostcode = ?', $type), $this->propertyPostcode);
        }
        else if ( ! empty($this->propertyPostcode)) {
            $select->where($this->addWhereTypes('%s.proppostcode like ?', $type), $this->propertyPostcode);
        }
        return $select;
    }

    /**
     * Takes a single where and turns it into a where for each type. For example:
     *      if $singleWhere = '%s.name = ?'
     *      and $type = array('P','Q','R')
     *      and $glue = ' OR '
     *      it returns 'P.name = ? OR Q.name = ? OR R.name = ?'
     *
     * @param string $singleWhere
     * @param array $type
     * @param string $glue
     * @return string
     */
    private function addWhereTypes($singleWhere, $type, $glue=' OR ')
    {
        // Use array_pad to duplicate the 'single where' and implode it with 'glue'
        $whereText = implode($glue, array_pad(array(), count($type), $singleWhere));
        /// ... then replace each %s with the elements from the type array
        return vsprintf($whereText, $type);
    }

    /**
     * Throws an exception if the type is invalid
     *
     * @param array $type - set of *_TABLE_ALIAS
     * @throws \InvalidArgumentException
     */
    private function checkTypeValid($type)
    {
        $allCounts = array_count_values($type);
        if (count($allCounts) <= 3) {
            $individualCount = array();
            foreach (array(self::POLICY_TABLE_ALIAS, self::QUOTE_TABLE_ALIAS, self::INSIGHT_TABLE_ALIAS) as $individualType) {
                if (array_key_exists($individualType, $allCounts)) {
                    $individualCount[$individualType] = $allCounts[$individualType];
                }
                else {
                    $individualCount[$individualType] = 0;
                }
            }
            if (
                $individualCount[self::POLICY_TABLE_ALIAS] <= 1 &&
                $individualCount[self::QUOTE_TABLE_ALIAS] <= 1 &&
                $individualCount[self::INSIGHT_TABLE_ALIAS] <= 1 &&
                array_sum($individualCount) == count($type)
            ) {
                // All is good with the world
                return;
            }
        }
        throw new \InvalidArgumentException('Invalid alias types');
    }
}