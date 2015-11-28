<?php

/**
 * Class Datasource_Insurance_RentRecoveryPlus_RentRecoveryPlus
 *
 * @author April Portus <april.portus@barbon.com>
 */
class Datasource_Insurance_RentRecoveryPlus_RentRecoveryPlusMta extends Zend_Db_Table_Multidb
{
    /**
     * @var string table name
     */
    protected $_name = 'rent_recovery_plus_mta';

    /**
     * @var string primary key
     */
    protected $_primary = 'mta_id';

    /**
     * @var string database
     */
    protected $_multidb = 'db_legacy_homelet';

    /**
     * Creates a new record in the rent_recovery_plus_mta table
     *
     * @param Model_Insurance_RentRecoveryPlus_RentRecoveryPlusMta $rrpMta
     * @param int $mtaId
     * @return bool
     */
    public function create($rrpMta, $mtaId)
    {
        $isSuccessful = true;

        $data = array(
            'mta_id'                           => $mtaId,
            'policynumber'                     => $rrpMta->getPolicyNumber(),
            'claim_info'                       => $rrpMta->getClaimInfo(),
        );

        // New quote so just insert
        if (!$this->insert($data)) {
            // Failed insertion
            Application_Core_Logger::log("Can't insert quote in table {$this->_name}", 'error');
            $isSuccessful = false;
        }

        return $isSuccessful;
    }

    /**
     * Get the data for the given mtaId
     *
     * @param string $mtaId
     * @return Model_Insurance_RentRecoveryPlus_RentRecoveryPlusMta|null
     */
    public function getRentRecoveryPlusMta($mtaId)
    {
        $select = $this->select()
            ->where('mta_id = ?', $mtaId);

        $row = $this->fetchRow($select);
        if ($row) {
            return Model_Insurance_RentRecoveryPlus_RentRecoveryPlusMta::hydrate($row->toArray());
        }
        return null;
    }
}