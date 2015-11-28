<?php

class Model_Insurance_Disbursement
{
    /**
     * Band A is in slot 1 or the composites string
     */
    const BAND_A = 1;

    /**
     * Band B is in slot 2 or the composites string
     */
    const BAND_B = 2;

    /**
     * Band C is in slot 3 or the composites string
     */
    const BAND_C = 3;

    /**
     * Band D is in slot 4 or the composites string
     */
    const BAND_D = 4;

    /**
     * Disbursements delimiter in composites string
     */
    const COMPOSITE_DELIMITER = '|';

    /**
     * @var string
     */
     private $whiteLabelID;

    /**
     * @var string
     */
     private $startDate;

    /**
     * @var string
     */
     private $endDate;

    /**
     * @var string
     */
     private $disbBuildings;

    /**
     * @var string
     */
     private $disbLowCostBuildings;

    /**
     * @var float
     */
     private $disbBuildingsAccidentalDamage;

    /**
     * @var float
     */
     private $disbBuildingsNoExcess;

    /**
     * @var float
     */
     private $disbSubsidenceFund;

    /**
     * @var float
     */
     private $disbLimitedContents;

    /**
     * @var float
     */
     private $disbContentsl;

    /**
     * @var float
     */
     private $disbContentslAccidentalDamage;

    /**
     * @var float
     */
     private $disbContentslNoExcess;

    /**
     * @var float
     */
     private $disbEmergencyAssistance;

    /**
     * @var float
     */
     private $disbLegalExpenses;

    /**
     * @var float
     */
     private $disbRentGuarantee;

    /**
     * @var string
     */
     private $disbContentst;

    /**
     * @var string
     */
     private $disbPedalCycles;

    /**
     * @var string
     */
     private $disbSpecPossessions;

    /**
     * @var string
     */
     private $disbPossessions;

    /**
     * @var float
     */
     private $disbPi;

    /**
     * @var string
     */
     private $disbIa;

    /**
     * @var double
     */
     private $legalIpt;

    /**
     * @var int
     */
     private $disno;

    /**
     * @var int
     */
     private $fromRateSetId;

    /**
     * @var int
     */
     private $toRateSetId;

    /**
     * @var string
     */
     private $disbContentstp;

    /**
     * @var string
     */
     private $disbPedalCyclesp;

    /**
     * @var string
     */
     private $disbSpecPossessionsp;

    /**
     * @var string
     */
     private $disbPossessionsp;

    /**
     * @var float
     */
     private $disbLiabilitytp;

    /**
     * @var string
     */
     private $rrpiFullRef6m;

    /**
     * @var string
     */
     private $rrpiFullRef12m;

    /**
     * @var string
     */
     private $rrpiFullRef6m0xs;

    /**
     * @var string
     */
     private $rrpiFullRef12m0xs;

    /**
     * @var string
     */
     private $rrpiCreditCheck6m;

    /**
     * @var string
     */
     private $rrpiCreditCheck12m;

    /**
     * Gets the Disbursement for Buildings
     * @return string
     */
    public function getDisbBuildings()
    {
        return $this->disbBuildings;
    }

    /**
     * Sets the Disbursement for Buildings
     *
     * @param string $disbBuildings
     * @return $this
     */
    public function setDisbBuildings($disbBuildings)
    {
        $this->disbBuildings = $disbBuildings;
        return $this;
    }

    /**
     * Gets the Disbursement for Buildings Accidental Damage
     *
     * @return float
     */
    public function getDisbBuildingsAccidentalDamage()
    {
        return $this->disbBuildingsAccidentalDamage;
    }

    /**
     * Sets the Disbursement for Building Accidental Damage
     *
     * @param float $disbBuildingsAccidentalDamage
     * @return $this
     */
    public function setDisbBuildingsAccidentalDamage($disbBuildingsAccidentalDamage)
    {
        $this->disbBuildingsAccidentalDamage = $disbBuildingsAccidentalDamage;
        return $this;
    }

    /**
     * Gets the Disbursement for Buildings No Excess
     *
     * @return float
     */
    public function getDisbBuildingsNoExcess()
    {
        return $this->disbBuildingsNoExcess;
    }

    /**
     * Sets the Disbursement for Buildings No Excess
     *
     * @param float $disbBuildingsNoExcess
     * @return $this
     */
    public function setDisbBuildingsNoExcess($disbBuildingsNoExcess)
    {
        $this->disbBuildingsNoExcess = $disbBuildingsNoExcess;
        return $this;
    }

    /**
     * Gets the Disbursement for Contentsl
     *
     * @return float
     */
    public function getDisbContentsl()
    {
        return $this->disbContentsl;
    }

    /**
     * Sets the Disbursement for Contentsl
     *
     * @param float $disbContentsl
     * @return $this
     */
    public function setDisbContentsl($disbContentsl)
    {
        $this->disbContentsl = $disbContentsl;
        return $this;
    }

    /**
     * Gets the Disbursement for Contentsl Accidental Damage
     *
     * @return float
     */
    public function getDisbContentslAccidentalDamage()
    {
        return $this->disbContentslAccidentalDamage;
    }

    /**
     * Sets the Disbursement for Contentsl Accidental Damage
     *
     * @param float $disbContentslAccidentalDamage
     * @return $this
     */
    public function setDisbContentslAccidentalDamage($disbContentslAccidentalDamage)
    {
        $this->disbContentslAccidentalDamage = $disbContentslAccidentalDamage;
        return $this;
    }

    /**
     * Gets the Disbursement for Contentsl No Excess
     *
     * @return float
     */
    public function getDisbContentslNoExcess()
    {
        return $this->disbContentslNoExcess;
    }

    /**
     * Sets the Disbursement for Contentsl No Excess
     *
     * @param float $disbContentslNoExcess
     * @return $this
     */
    public function setDisbContentslNoExcess($disbContentslNoExcess)
    {
        $this->disbContentslNoExcess = $disbContentslNoExcess;
        return $this;
    }

    /**
     * Gets the Disbursement for Contentst
     *
     * @return string
     */
    public function getDisbContentst()
    {
        return $this->disbContentst;
    }

    /**
     * Sets the Disbursement for Contentst
     *
     * @param string $disbContentst
     * @return $this
     */
    public function setDisbContentst($disbContentst)
    {
        $this->disbContentst = $disbContentst;
        return $this;
    }

    /**
     * Gets the Disbursement for Contentstp
     *
     * @return string
     */
    public function getDisbContentstp()
    {
        return $this->disbContentstp;
    }

    /**
     * Sets the Disbursement for Contentstp
     *
     * @param string $disbContentstp
     * @return $this
     */
    public function setDisbContentstp($disbContentstp)
    {
        $this->disbContentstp = $disbContentstp;
        return $this;
    }

    /**
     * Gets the Disbursement for Emergency Assistance
     *
     * @return float
     */
    public function getDisbEmergencyAssistance()
    {
        return $this->disbEmergencyAssistance;
    }

    /**
     * Sets the Disbursement for  Emergency Assistance
     *
     * @param float $disbEmergencyAssistance
     * @return $this
     */
    public function setDisbEmergencyAssistance($disbEmergencyAssistance)
    {
        $this->disbEmergencyAssistance = $disbEmergencyAssistance;
        return $this;
    }

    /**
     * Gets the DisbIa
     *
     * @return string
     */
    public function getDisbIa()
    {
        return $this->disbIa;
    }

    /**
     * Sets the DisbIa
     *
     * @param string $disbIa
     * @return $this
     */
    public function setDisbIa($disbIa)
    {
        $this->disbIa = $disbIa;
        return $this;
    }

    /**
     * Gets the Disbursement for Legal Expenses
     *
     * @return float
     */
    public function getDisbLegalExpenses()
    {
        return $this->disbLegalExpenses;
    }

    /**
     * Sets the Disbursement for Legal Expenses
     *
     * @param float $disbLegalExpenses
     * @return $this
     */
    public function setDisbLegalExpenses($disbLegalExpenses)
    {
        $this->disbLegalExpenses = $disbLegalExpenses;
        return $this;
    }

    /**
     * Gets the Disbursement for Liabilitytp
     *
     * @return float
     */
    public function getDisbLiabilitytp()
    {
        return $this->disbLiabilitytp;
    }

    /**
     * Sets the Disbursement for Liabilitytp
     *
     * @param float $disbLiabilitytp
     * @return $this
     */
    public function setDisbLiabilitytp($disbLiabilitytp)
    {
        $this->disbLiabilitytp = $disbLiabilitytp;
        return $this;
    }

    /**
     * Gets the Disbursement for Limited Contents
     *
     * @return float
     */
    public function getDisbLimitedContents()
    {
        return $this->disbLimitedContents;
    }

    /**
     * Sets the Disbursement for Limited Contents
     *
     * @param float $disbLimitedContents
     * @return $this
     */
    public function setDisbLimitedContents($disbLimitedContents)
    {
        $this->disbLimitedContents = $disbLimitedContents;
        return $this;
    }

    /**
     * Gets the Disbursement for Low Cost Buildings
     *
     * @return string
     */
    public function getDisbLowCostBuildings()
    {
        return $this->disbLowCostBuildings;
    }

    /**
     * Sets the Disbursement for Low Cost Buildings
     *
     * @param string $disbLowCostBuildings
     * @return $this
     */
    public function setDisbLowCostBuildings($disbLowCostBuildings)
    {
        $this->disbLowCostBuildings = $disbLowCostBuildings;
        return $this;
    }

    /**
     * Gets the Disbursement for Pedal Cycles
     *
     * @return string
     */
    public function getDisbPedalCycles()
    {
        return $this->disbPedalCycles;
    }

    /**
     * Sets the Disbursement for Pedal Cycles
     *
     * @param string $disbPedalCycles
     * @return $this
     */
    public function setDisbPedalCycles($disbPedalCycles)
    {
        $this->disbPedalCycles = $disbPedalCycles;
        return $this;
    }

    /**
     * Gets the Disbursement for Pedal Cyclesp
     *
     * @return string
     */
    public function getDisbPedalCyclesp()
    {
        return $this->disbPedalCyclesp;
    }

    /**
     * Sets the Disbursement for Pedal Cyclesp
     *
     * @param string $disbPedalCyclesp
     * @return $this
     */
    public function setDisbPedalCyclesp($disbPedalCyclesp)
    {
        $this->disbPedalCyclesp = $disbPedalCyclesp;
        return $this;
    }

    /**
     * Gets the DisbPi
     *
     * @return float
     */
    public function getDisbPi()
    {
        return $this->disbPi;
    }

    /**
     * Sets the DisbPi
     *
     * @param float $disbPi
     * @return $this
     */
    public function setDisbPi($disbPi)
    {
        $this->disbPi = $disbPi;
        return $this;
    }

    /**
     * Gets the Disbursement for Possessions
     *
     * @return string
     */
    public function getDisbPossessions()
    {
        return $this->disbPossessions;
    }

    /**
     * Sets the Disbursement for Possessions
     *
     * @param string $disbPossessions
     * @return $this
     */
    public function setDisbPossessions($disbPossessions)
    {
        $this->disbPossessions = $disbPossessions;
        return $this;
    }

    /**
     * Gets the Disbursement for Possessionsp
     *
     * @return string
     */
    public function getDisbPossessionsp()
    {
        return $this->disbPossessionsp;
    }

    /**
     * Sets the Disbursement for Possessionsp
     *
     * @param string $disbPossessionsp
     * @return $this
     */
    public function setDisbPossessionsp($disbPossessionsp)
    {
        $this->disbPossessionsp = $disbPossessionsp;
        return $this;
    }

    /**
     * Gets the Disbursement for Rent Guarantee
     *
     * @return float
     */
    public function getDisbRentGuarantee()
    {
        return $this->disbRentGuarantee;
    }

    /**
     * Sets the Disbursement for Rent Guarantee
     *
     * @param float $disbRentGuarantee
     * @return $this
     */
    public function setDisbRentGuarantee($disbRentGuarantee)
    {
        $this->disbRentGuarantee = $disbRentGuarantee;
        return $this;
    }

    /**
     * Gets the Disbursement for Spec Possessions
     *
     * @return string
     */
    public function getDisbSpecPossessions()
    {
        return $this->disbSpecPossessions;
    }

    /**
     * Sets the Disbursement for Spec Possessions
     *
     * @param string $disbSpecPossessions
     * @return $this
     */
    public function setDisbSpecPossessions($disbSpecPossessions)
    {
        $this->disbSpecPossessions = $disbSpecPossessions;
        return $this;
    }

    /**
     * Gets the Disbursement for Spec Possessionsp
     *
     * @return string
     */
    public function getDisbSpecPossessionsp()
    {
        return $this->disbSpecPossessionsp;
    }

    /**
     * Sets the Disbursement for Spec Possessionsp
     *
     * @param string $disbSpecPossessionsp
     * @return $this
     */
    public function setDisbSpecPossessionsp($disbSpecPossessionsp)
    {
        $this->disbSpecPossessionsp = $disbSpecPossessionsp;
        return $this;
    }

    /**
     * Gets the Disbursement for Subsidence Fund
     *
     * @return float
     */
    public function getDisbSubsidenceFund()
    {
        return $this->disbSubsidenceFund;
    }

    /**
     * Sets the Disbursement for Subsidence Fund
     *
     * @param float $disbSubsidenceFund
     * @return $this
     */
    public function setDisbSubsidenceFund($disbSubsidenceFund)
    {
        $this->disbSubsidenceFund = $disbSubsidenceFund;
        return $this;
    }

    /**
     * Gets the Disno
     *
     * @return int
     */
    public function getDisno()
    {
        return $this->disno;
    }

    /**
     * Sets the Disno
     *
     * @param int $disno
     * @return $this
     */
    public function setDisno($disno)
    {
        $this->disno = $disno;
        return $this;
    }

    /**
     * Gets the End Date
     *
     * @return string
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Sets the End Date
     *
     * @param string $endDate
     * @return $this
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * Gets the From Rate Set Id
     *
     * @return int
     */
    public function getFromRateSetId()
    {
        return $this->fromRateSetId;
    }

    /**
     * Sets the From Rate Set Id
     *
     * @param int $fromRateSetId
     * @return $this
     */
    public function setFromRateSetId($fromRateSetId)
    {
        $this->fromRateSetId = $fromRateSetId;
        return $this;
    }

    /**
     * Gets the Legal Ipt
     *
     * @return float
     */
    public function getLegalIpt()
    {
        return $this->legalIpt;
    }

    /**
     * Sets the Legal Ipt
     *
     * @param float $legalIpt
     * @return $this
     */
    public function setLegalIpt($legalIpt)
    {
        $this->legalIpt = $legalIpt;
        return $this;
    }

    /**
     * Gets the Rrpi Credit Check 12m
     *
     * @return string
     */
    public function getRrpiCreditCheck12m()
    {
        return $this->rrpiCreditCheck12m;
    }

    /**
     * Sets the Rrpi Credit Check 12m
     *
     * @param string $rrpiCreditCheck12m
     * @return $this
     */
    public function setRrpiCreditCheck12m($rrpiCreditCheck12m)
    {
        $this->rrpiCreditCheck12m = $rrpiCreditCheck12m;
        return $this;
    }

    /**
     * Gets the Rrpi Credit Check 6m
     *
     * @return string
     */
    public function getRrpiCreditCheck6m()
    {
        return $this->rrpiCreditCheck6m;
    }

    /**
     * Sets the Rrpi Credit Check 6m
     *
     * @param string $rrpiCreditCheck6m
     * @return $this
     */
    public function setRrpiCreditCheck6m($rrpiCreditCheck6m)
    {
        $this->rrpiCreditCheck6m = $rrpiCreditCheck6m;
        return $this;
    }

    /**
     * Gets the Rrpi Full Ref 12m
     *
     * @return string
     */
    public function getRrpiFullRef12m()
    {
        return $this->rrpiFullRef12m;
    }

    /**
     * Sets the Rrpi Full Ref 12m
     *
     * @param string $rrpiFullRef12m
     * @return $this
     */
    public function setRrpiFullRef12m($rrpiFullRef12m)
    {
        $this->rrpiFullRef12m = $rrpiFullRef12m;
        return $this;
    }

    /**
     * Gets the Rrpi Full Ref 12m 0xs
     *
     * @return string
     */
    public function getRrpiFullRef12m0xs()
    {
        return $this->rrpiFullRef12m0xs;
    }

    /**
     * Sets the Rrpi Full Ref 12m 0xs
     *
     * @param string $rrpiFullRef12m0xs
     * @return $this
     */
    public function setRrpiFullRef12m0xs($rrpiFullRef12m0xs)
    {
        $this->rrpiFullRef12m0xs = $rrpiFullRef12m0xs;
        return $this;
    }

    /**
     * Gets the Rrpi Full Ref 6m
     *
     * @return string
     */
    public function getRrpiFullRef6m()
    {
        return $this->rrpiFullRef6m;
    }

    /**
     * Sets the Rrpi Full Ref 6m
     *
     * @param string $rrpiFullRef6m
     * @return $this
     */
    public function setRrpiFullRef6m($rrpiFullRef6m)
    {
        $this->rrpiFullRef6m = $rrpiFullRef6m;
        return $this;
    }

    /**
     * Gets the Rrpi Full Ref 6m 0xs
     *
     * @return string
     */
    public function getRrpiFullRef6m0xs()
    {
        return $this->rrpiFullRef6m0xs;
    }

    /**
     * Sets the Rrpi Full Ref 6m 0xs
     *
     * @param string $rrpiFullRef6m0xs
     * @return $this
     */
    public function setRrpiFullRef6m0xs($rrpiFullRef6m0xs)
    {
        $this->rrpiFullRef6m0xs = $rrpiFullRef6m0xs;
        return $this;
    }

    /**
     * Gets the Start Date
     *
     * @return string
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Sets the Start Date
     *
     * @param string $startDate
     * @return $this
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * Gets the To Rate Ret Id
     *
     * @return int
     */
    public function getToRateSetId()
    {
        return $this->toRateSetId;
    }

    /**
     * Sets the To Rate Ret Id
     *
     * @param int $toRateSetId
     * @return $this
     */
    public function setToRateSetId($toRateSetId)
    {
        $this->toRateSetId = $toRateSetId;
        return $this;
    }

    /**
     * Gets the WhiteLabelID
     *
     * @return string
     */
    public function getWhiteLabelID()
    {
        return $this->whiteLabelID;
    }

    /**
     * Sets the WhiteLabelID
     *
     * @param string $whiteLabelID
     * @return $this
     */
    public function setWhiteLabelID($whiteLabelID)
    {
        $this->whiteLabelID = $whiteLabelID;
        return $this;
    }

    /**
     * Extract the band disbursement from the composite string
     *
     * @param string $compositeDisbursement
     * @param int $band use the BAND_* class constants
     * @return null|int
     */
    public static function extractBandDisbursement($compositeDisbursement, $band)
    {
        $bandDisbursements = explode(self::COMPOSITE_DELIMITER, $compositeDisbursement);
        if ($bandDisbursements) {
            if ($band <= count($bandDisbursements)) {
                return (int)$bandDisbursements[$band-1];
            }
        }
        return null;
    }
}
