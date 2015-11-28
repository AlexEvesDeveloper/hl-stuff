<?php

/**
 * Model definition for the rates table
 *
 * Class Datasource_Insurance_RentRecoveryPlus_Rates
 *
 * @author April Portus <april.portus@barbon.com>
 */
class Datasource_Insurance_RentRecoveryPlus_Rates extends Zend_Db_Table_Multidb
{
    /**
     * @var string table name
     */
    protected $_name = 'rates';

    /**
     * @var string primary key
     */
    protected $_primary = 'policyRateID';

    /**
     * @var string database
     */
    protected $_multidb = 'db_legacy_homelet';

    /**
     * Gets the rate set filtered by agents rate ID and risk area
     *
     * @param int $agentRatesID
     * @param int $riskArea
     * @param DateTime|null $date
     * @return array
     * @throws RuntimeException
     */
    public function getRateSet($agentRatesID, $riskArea, DateTime $date=null)
    {
        if ($date == null) {
            $date = new \DateTime();
        }
        $rrpRatesSelect = $this->select();
        $rrpRatesSelect->setIntegrityCheck(false);
        $rrpRatesSelect->from(
            array('r' => 'rates'),
            array(
                'rateSetID',
                'rentguaranteerrp_full_6m_band_a',
                'rentguaranteerrp_full_6m_band_b',
                'rentguaranteerrp_full_12m_band_a',
                'rentguaranteerrp_full_12m_band_b',
                'rentguaranteerrp_full_6m_nilexcess_band_a',
                'rentguaranteerrp_full_6m_nilexcess_band_b',
                'rentguaranteerrp_full_12m_nilexcess_band_a',
                'rentguaranteerrp_full_12m_nilexcess_band_b',
                'rentguaranteerrp_credit_6m_band_a',
                'rentguaranteerrp_credit_12m_band_a',
            )
        );
        $rrpRatesSelect->where('r.agentsRateID = ?', $agentRatesID);
        $rrpRatesSelect->where('r.riskarea = ?', $riskArea);
        $rrpRatesSelect->where('r.startDate <= ?', $date->format('Y-m-d'));
        $rrpRatesSelect->where('r.endDate >= ? OR r.endDate = \'0000-00-00\'', $date->format('Y-m-d'));
        $rrpRatesSelect->order('r.startDate DESC');
        $rrpRatesSelect->limit(1);
        $rrpRatesRow = $this->fetchRow($rrpRatesSelect);

        if ($rrpRatesRow !== null) {
            return $rrpRatesRow->toArray();
        }

        // Can't find rates - log a warning
        $message = sprintf(
            'No rates in table %s (agentsRateID = %d, riskArea = %d, date = %s)',
            $this->_name,
            $agentRatesID,
            $riskArea,
            $date->format('Y-m-d')
        );
        throw new \RuntimeException($message);
    }

    /**
     * Gets the rate set id for the given agent scheme number
     *
     * @param int $agentSchemeNumber
     * @param int $riskArea
     * @param DateTime|null $date
     * @return int
     * @throws RuntimeException
     */
    public function getRateSetIdForAgent($agentSchemeNumber, $riskArea, DateTime $date=null)
    {
        if ($date == null) {
            $date = new \DateTime();
        }
        $select = $this->select();
        $select
            ->setIntegrityCheck(false)
            ->from(
                array('r' => 'rates'),
                array('rateSetID')
            )
            ->join(
                array('a' => 'newagents'),
                'a.agentsRateId = r.agentsRateId',
                ''
            )
            ->where('r.riskarea = ?', $riskArea)
            ->where('r.startDate <= ?', $date->format('Y-m-d'))
            ->where('r.endDate >= ? OR r.endDate = \'0000-00-00\'', $date->format('Y-m-d'))
            ->where('a.agentSchemeNo = ?', $agentSchemeNumber)
        ;
        $rowSet = $this->fetchAll($select);

        if (count($rowSet) == 1) {
            $row = $rowSet->current();
            return $row->rateSetID;
        }
        $message = sprintf('Multiple rateSetId\'s found for agent %s', $agentSchemeNumber);
        throw new \RuntimeException($message);
    }
}
