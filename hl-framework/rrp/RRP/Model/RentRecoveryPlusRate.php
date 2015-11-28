<?php

namespace RRP\Model;

/**
 * Class RentRecoveryPlusRate
 *
 * @package RRP\Model
 * @author April Portus <april.portus@barbon.com>
 */
class RentRecoveryPlusRate extends AbstractResponseModel
{
    /**
     * @var int
     */
    private $rateSetId;

    /**
     * @var float
     */
    private $rentGuaranteeRrpFullRef6mBandA;

    /**
     * @var float
     */
    private $rentGuaranteeRrpFullRef6mBandB;

    /**
     * @var float
     */
    private $rentGuaranteeRrpFullRef12mBandA;

    /**
     * @var float
     */
    private $rentGuaranteeRrpFullRef12mBandB;

    /**
     * @var float
     */
    private $rentGuaranteeRrpFullRef6mNilExcessBandA;

    /**
     * @var float
     */
    private $rentGuaranteeRrpFullRef6mNilExcessBandB;

    /**
     * @var float
     */
    private $rentGuaranteeRrpFullRef12mNilExcessBandA;

    /**
     * @var float
     */
    private $rentGuaranteeRrpFullRef12mNilExcessBandB;

    /**
     * @var float
     */
    private $rentGuaranteeRrpCreditCheck6m;

    /**
     * @var float
     */
    private $rentGuaranteeRrpCreditCheck12m;

    /**
     * @return int
     */
    public function getRateSetId()
    {
        return $this->rateSetId;
    }

    /**
     * @param int $rateSetId
     */
    public function setRateSetID($rateSetId)
    {
        $this->rateSetId = $rateSetId;
    }

    /**
     * @return float
     */
    public function getRentGuaranteeRrpCreditCheck12m()
    {
        return $this->rentGuaranteeRrpCreditCheck12m;
    }

    /**
     * @param float $rentGuaranteeRrpCreditCheck12m
     */
    public function setRentGuaranteeRrpCreditCheck12m($rentGuaranteeRrpCreditCheck12m)
    {
        $this->rentGuaranteeRrpCreditCheck12m = $rentGuaranteeRrpCreditCheck12m;
    }

    /**
     * @return float
     */
    public function getRentGuaranteeRrpCreditCheck6m()
    {
        return $this->rentGuaranteeRrpCreditCheck6m;
    }

    /**
     * @param float $rentGuaranteeRrpCreditCheck6m
     */
    public function setRentGuaranteeRrpCreditCheck6m($rentGuaranteeRrpCreditCheck6m)
    {
        $this->rentGuaranteeRrpCreditCheck6m = $rentGuaranteeRrpCreditCheck6m;
    }

    /**
     * @return float
     */
    public function getRentGuaranteeRrpFullRef12mNilExcessBandA()
    {
        return $this->rentGuaranteeRrpFullRef12mNilExcessBandA;
    }

    /**
     * @param float $rentGuaranteeRrpFullRef12mNilExcessBandA
     */
    public function setRentGuaranteeRrpFullRef12mNilExcessBandA($rentGuaranteeRrpFullRef12mNilExcessBandA)
    {
        $this->rentGuaranteeRrpFullRef12mNilExcessBandA = $rentGuaranteeRrpFullRef12mNilExcessBandA;
    }

    /**
     * @return float
     */
    public function getRentGuaranteeRrpFullRef12mNilExcessBandB()
    {
        return $this->rentGuaranteeRrpFullRef12mNilExcessBandB;
    }

    /**
     * @param float $rentGuaranteeRrpFullRef12mNilExcessBandB
     */
    public function setRentGuaranteeRrpFullRef12mNilExcessBandB($rentGuaranteeRrpFullRef12mNilExcessBandB)
    {
        $this->rentGuaranteeRrpFullRef12mNilExcessBandB = $rentGuaranteeRrpFullRef12mNilExcessBandB;
    }

    /**
     * @return float
     */
    public function getRentGuaranteeRrpFullRef12mBandA()
    {
        return $this->rentGuaranteeRrpFullRef12mBandA;
    }

    /**
     * @param float $rentGuaranteeRrpFullRef12mBandA
     */
    public function setRentGuaranteeRrpFullRef12mBandA($rentGuaranteeRrpFullRef12mBandA)
    {
        $this->rentGuaranteeRrpFullRef12mBandA = $rentGuaranteeRrpFullRef12mBandA;
    }

    /**
     * @return float
     */
    public function getRentGuaranteeRrpFullRef12mBandB()
    {
        return $this->rentGuaranteeRrpFullRef12mBandB;
    }

    /**
     * @param float $rentGuaranteeRrpFullRef12mBandB
     */
    public function setRentGuaranteeRrpFullRef12mBandB($rentGuaranteeRrpFullRef12mBandB)
    {
        $this->rentGuaranteeRrpFullRef12mBandB = $rentGuaranteeRrpFullRef12mBandB;
    }

    /**
     * @return float
     */
    public function getRentGuaranteeRrpFullRef6mNilExcessBandA()
    {
        return $this->rentGuaranteeRrpFullRef6mNilExcessBandA;
    }

    /**
     * @param float $rentGuaranteeRrpFullRef6mNilExcessBandA
     */
    public function setRentGuaranteeRrpFullRef6mNilExcessBandA($rentGuaranteeRrpFullRef6mNilExcessBandA)
    {
        $this->rentGuaranteeRrpFullRef6mNilExcessBandA = $rentGuaranteeRrpFullRef6mNilExcessBandA;
    }

    /**
     * @return float
     */
    public function getRentGuaranteeRrpFullRef6mNilExcessBandB()
    {
        return $this->rentGuaranteeRrpFullRef6mNilExcessBandB;
    }

    /**
     * @param float $rentGuaranteeRrpFullRef6mNilExcessBandB
     */
    public function setRentGuaranteeRrpFullRef6mNilExcessBandB($rentGuaranteeRrpFullRef6mNilExcessBandB)
    {
        $this->rentGuaranteeRrpFullRef6mNilExcessBandB = $rentGuaranteeRrpFullRef6mNilExcessBandB;
    }

    /**
     * @return float
     */
    public function getRentGuaranteeRrpFullRef6mBandA()
    {
        return $this->rentGuaranteeRrpFullRef6mBandA;
    }

    /**
     * @param float $rentGuaranteeRrpFullRef6mBandA
     */
    public function setRentGuaranteeRrpFullRef6mBandA($rentGuaranteeRrpFullRef6mBandA)
    {
        $this->rentGuaranteeRrpFullRef6mBandA = $rentGuaranteeRrpFullRef6mBandA;
    }

    /**
     * @return float
     */
    public function getRentGuaranteeRrpFullRef6mBandB()
    {
        return $this->rentGuaranteeRrpFullRef6mBandB;
    }

    /**
     * @param float $rentGuaranteeRrpFullRef6mBandB
     */
    public function setRentGuaranteeRrpFullRef6mBandB($rentGuaranteeRrpFullRef6mBandB)
    {
        $this->rentGuaranteeRrpFullRef6mBandB = $rentGuaranteeRrpFullRef6mBandB;
    }

    /**
     * Hydrate a single application
     *
     * @param array $data
     * @return object
     */
    public static function hydrate($data)
    {
        return self::hydrateModelProperties(
            new self(),
            $data,
            array(
                'rateSetID' => 'rateSetId',
                'rentguaranteerrp_full_6m_band_a' => 'rentGuaranteeRrpFullRef6mBandA',
                'rentguaranteerrp_full_6m_band_b' => 'rentGuaranteeRrpFullRef6mBandB',
                'rentguaranteerrp_full_12m_band_a' => 'rentGuaranteeRrpFullRef12mBandA',
                'rentguaranteerrp_full_12m_band_b' => 'rentGuaranteeRrpFullRef12mBandB',
                'rentguaranteerrp_full_6m_nilexcess_band_a' => 'rentGuaranteeRrpFullRef6mNilExcessBandA',
                'rentguaranteerrp_full_6m_nilexcess_band_b' => 'rentGuaranteeRrpFullRef6mNilExcessBandB',
                'rentguaranteerrp_full_12m_nilexcess_band_a' => 'rentGuaranteeRrpFullRef12mNilExcessBandA',
                'rentguaranteerrp_full_12m_nilexcess_band_b' => 'rentGuaranteeRrpFullRef12mNilExcessBandB',
                'rentguaranteerrp_credit_6m_band_a' => 'rentGuaranteeRrpCreditCheck6m',
                'rentguaranteerrp_credit_12m_band_a' => 'rentGuaranteeRrpCreditCheck12m'
            )
        );
    }
}

