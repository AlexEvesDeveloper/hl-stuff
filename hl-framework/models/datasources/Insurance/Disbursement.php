<?php
/**
 * Model definition for the Transaction table
 *
 * Class Datasource_Insurance_Disbursement
 *
 * @author April Portus <april.portus@barbon.com>
 */
class Datasource_Insurance_Disbursement extends Zend_Db_Table_Multidb
{
    /**
     * @var string table name
     */
    protected $_name = 'disbursement';

    /**
     * @var string primary id
     */
    protected $_primary = 'whitelabelID';

    /**
     * @var string dstabase
     */
    protected $_multidb = 'db_legacy_homelet';

    /**
     * Gets a Disbursement record
     *
     * @param string $whiteLabelId
     * @param int $rateSetId
     * @param \DateTime|null $date
     * @return Model_Insurance_Disbursement|null
     */
    public function getDisbursement($whiteLabelId, $rateSetId, \DateTime $date=null)
    {
        if ( ! $date) {
            $date = new \DateTime();
        }
        $select = $this->select();
        $select
            ->where('whitelabelID = ?', $whiteLabelId)
            ->where('fromratesetid <= ?', (int) $rateSetId)
            ->where('toratesetid >= ?', (int) $rateSetId)
            ->where('start_date <= ?', $date->format('Y-m-d'))
            ->where('end_date >= ?', $date->format('Y-m-d'));
        $row = $this->fetchRow($select);

        // There should only be a single disbursement with the given id
        if (count($row) == 1) {
            $disbursement = new Model_Insurance_Disbursement();
            $disbursement
                ->setwhitelabelID($row->whitelabelID)
                ->setStartDate($row->start_date)
                ->setEndDate($row->end_date)
                ->setDisbBuildings($row->disbbuildings)
                ->setDisbLowCostBuildings($row->disblowcostbuildings)
                ->setDisbBuildingsAccidentalDamage($row->disbbuildingsaccidentaldamage)
                ->setDisbBuildingsNoExcess($row->disbbuildingsnoexcess)
                ->setDisbSubsidenceFund($row->disbsubsidencefund)
                ->setDisbLimitedContents($row->disblimitedcontents)
                ->setDisbContentsl($row->disbcontentsl)
                ->setDisbContentslAccidentalDamage($row->disbcontentslaccidentaldamage)
                ->setDisbContentslNoExcess($row->disbcontentslnoexcess)
                ->setDisbEmergencyAssistance($row->disbemergencyassistance)
                ->setDisbLegalExpenses($row->disblegalexpenses)
                ->setDisbRentGuarantee($row->disbrentguarantee)
                ->setDisbContentst($row->disbcontentst)
                ->setDisbPedalCycles($row->disbpedalcycles)
                ->setDisbSpecPossessions($row->disbspecpossessions)
                ->setDisbPossessions($row->disbpossessions)
                ->setDisbpi($row->disbpi)
                ->setDisbia($row->disbia)
                ->setLegalIpt($row->legalIPT)
                ->setDisno($row->disno)
                ->setFromRateSetId($row->fromratesetid)
                ->setToRateSetId($row->toratesetid)
                ->setDisbContentstp($row->disbcontentstp)
                ->setDisbPedalCyclesp($row->disbpedalcyclesp)
                ->setDisbSpecPossessionsp($row->disbspecpossessionsp)
                ->setDisbPossessionsp($row->disbpossessionsp)
                ->setDisbLiabilitytp($row->disbliabilitytp)
                ->setRrpiFullRef6m($row->rrpi_full_ref_6m)
                ->setRrpiFullRef12m($row->rrpi_full_ref_12m)
                ->setRrpiFullRef6m0xs($row->rrpi_full_ref_6m_0xs)
                ->setRrpiFullRef12m0xs($row->rrpi_full_ref_12m_0xs)
                ->setRrpiCreditCheck6m($row->rrpi_credit_check_6m)
                ->setRrpiCreditCheck12m($row->rrpi_credit_check_12m);
            return $disbursement;
        }
        return null;
    }

}