<?php

/**
 * Class Datasource_Insurance_RentRecoveryPlus_RrpTenantReferences
 *
 * @author April Portus <april.portus@barbon.com>
 */
class Datasource_Insurance_RentRecoveryPlus_RrpTenantReferences extends Zend_Db_Table_Multidb
{
    /**
     * @var string table name
     */
    protected $_name = 'rrp_tenant_references';

    /**
     * @var string primary key
     */
    protected $_primary = 'id';

    /**
     * @var string database
     */
    protected $_multidb = 'db_legacy_homelet';

    /**
     * Saves the data to the rrp_tenant_references table
     *
     * @param Model_Insurance_RentRecoveryPlus_RrpTenantReference $rrpTenantReference
     * @return bool
     */
    public function save(Model_Insurance_RentRecoveryPlus_RrpTenantReference $rrpTenantReference)
    {
        $data = array(
            'policynumber'     => $rrpTenantReference->getPolicyNumber(),
            'reference_number' => $rrpTenantReference->getReferenceNumber(),
            'term_id'          => $rrpTenantReference->getTermId(),
            'mta_id'           => $rrpTenantReference->getMtaId(),
            'date_created_at'  => $rrpTenantReference->getDateCreatedAt(),
        );

        // Firstly we need to see if this already exists
        if ($rrpTenantReference->getId() > 0) {
            $select = $this->select();
            $select
                ->where('policynumber = ?', $rrpTenantReference->getPolicyNumber())
                ->where('id = ?', $rrpTenantReference->getId());
            $row = $this->fetchAll($select);
            if ($row) {
                $data['id'] = $rrpTenantReference->getId();
                $where = $this->_db->quoteInto('id = ?', $rrpTenantReference->getId());
                $this->update($data, $where);
                return true;
            }
        }

        // New quote so just insert
        if ( ! $this->insert($data)) {
            // Failed insertion
            Application_Core_Logger::log("Can't insert quote in table {$this->_name}", 'error');
            return false;
        }

        return true;
    }

    /**
     * Remove all tenant references
     *
     * @param string $policyNumber
     * @param string $dateCreatedAt
     * @param int $termId
     * @param int $mtaId
     */
    public function removeAllForPolicy($policyNumber, $dateCreatedAt, $termId, $mtaId)
    {
        $where = array(
            $this->quoteInto('policynumber = ?', $policyNumber),
            $this->quoteInto('date_created_at = ?', $dateCreatedAt),
            $this->quoteInto('term_id = ?', $termId),
            $this->quoteInto('mta_id = ?', $mtaId)
        );
        $this->delete($where);
    }

    /**
     * Gets the rrp_tenant_references record for the given policy number
     *
     * @param string $policyNumber
     * @return array of Model_Insurance_RentRecoveryPlus_RrpTenantReference
     */
    public function getRrpTenantReferencesForPolicy($policyNumber)
    {
        $select = $this->select()
            ->where('policynumber = ?', $policyNumber);

        $references = array();
        $rowSet = $this->fetchAll($select);
        foreach ($rowSet as $row) {
            $references[] = Model_Insurance_RentRecoveryPlus_RrpTenantReference::hydrateFromRow($row->toArray());
        }
        return $references;
    }

    /**
     * When the quote is accepted this changes the quote number to the policy number, and the termId is now known
     *
     * @param string $quoteNumber
     * @param string $policyNumber
     * @param int $termId
     * @return bool
     */
    public function accept($quoteNumber, $policyNumber, $termId)
    {
        $wasSuccessful = false;

        $data = array(
            'policynumber' => $policyNumber,
            'term_id' => $termId
        );

        // Firstly we need to see if this already exists
        $select=$this->select();
        $select->where('policynumber = ?', $quoteNumber);
        $rowSet = $this->fetchAll($select);

        if (count($rowSet) > 0) {

            // Already exists so we are doing an update
            $where = $this->_db->quoteInto('policynumber = ?', $quoteNumber);
            $this->update($data, $where);
            $wasSuccessful = true;
        }

        return $wasSuccessful;
    }
}