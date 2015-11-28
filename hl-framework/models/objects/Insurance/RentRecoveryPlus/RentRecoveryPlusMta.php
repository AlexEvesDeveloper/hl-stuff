<?php

/**
 * Class Model_Insurance_RentRecoveryPlus_RentRecoveryPlus
 *
 * @author April Portus <april.portus@barbon.com>
 */
class Model_Insurance_RentRecoveryPlus_RentRecoveryPlusMta extends Model_Insurance_RentRecoveryPlus_AbstractResponseModel
{
    /**
     * @var int
     */

    private $mtaId;

    /**
     * @var string
     */
    private $policyNumber;

    /**
     * @var string
     */
    private $claimInfo;

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
            array(),
            array()
        );
    }

    /**
     * Gets the claim info
     *
     * @return string
     */
    public function getClaimInfo()
    {
        return $this->claimInfo;
    }

    /**
     * Sets the claim info
     *
     * @param string $claimInfo
     * @return $this
     */
    public function setClaimInfo($claimInfo)
    {
        $this->claimInfo = $claimInfo;
        return $this;
    }

    /**
     * Gets the MTA Id
     *
     * @return int
     */
    public function getMtaId()
    {
        return $this->mtaId;
    }

    /**
     * Sets the MTA Id
     *
     * @param mixed $mtaId
     * @return $this
     */
    public function setMtaId($mtaId)
    {
        $this->mtaId = $this->transformDate($mtaId);
        return $this;
    }

    /**
     * Gets the policy number
     *
     * @return string
     */
    public function getPolicyNumber()
    {
        return $this->policyNumber;
    }

    /**
     * Sets the policy number
     *
     * @param string $policyNumber
     * @return $this
     */
    public function setPolicyNumber($policyNumber)
    {
        $this->policyNumber = $policyNumber;
        return $this;
    }

}