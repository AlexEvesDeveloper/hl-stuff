<?php

/**
 * Class Model_Insurance_Claims
 *
 * @author April Portus <april.portus@barbon.com>
 */
class Model_Insurance_Claims extends Model_Abstract
{
    /**
     * Claim status for an active claim
     */
    const CLAIM_STATUS_ACTIVE = 'active';

    /**
     * @var int
     */
    protected $claimNumber;

    /**
     * @var string
     */
    protected $policyNumber;

    /**
     * @var string
     */
    protected $claimdatetime;

    /**
     * @var string
     */
    protected $incidentdatetime;

    /**
     * @var string
     */
    protected $incidentdescription;

    /**
     * @var bool
     */
    protected $beingprocessed;

    /**
     * @var bool
     */
    protected $processed;

    /**
     * @var string
     */
    protected $claimstatus;

    /**
     * Hydrates the properties from an array
     *
     * @param array $row
     * @return Model_Insurance_Claims
     */
    public static function hydrate(array $row)
    {
        $claims = new self();
        foreach($row as $key => $value) {
            $setter = 'set' . ucfirst($key);
            if (method_exists($claims, $setter)) {
                $claims->{$setter}($value);
            }
        }
        return $claims;
    }

    /**
     * Gets the being processed flag
     *
     * @return bool
     */
    public function getBeingprocessed()
    {
        return $this->beingprocessed;
    }

    /**
     * Sets the being processed flag
     *
     * @param bool $beingprocessed
     * @return $this
     */
    public function setBeingprocessed($beingprocessed)
    {
        $this->beingprocessed = $beingprocessed;
        return $this;
    }

    /**
     * Gets the claim number
     *
     * @return int
     */
    public function getClaimNumber()
    {
        return $this->claimNumber;
    }

    /**
     * Sets the claim number
     *
     * @param int $claimNumber
     * @return $this
     */
    public function setClaimNumber($claimNumber)
    {
        $this->claimNumber = $claimNumber;
        return $this;
    }

    /**
     * Gets the claim datetime string
     *
     * @return string
     */
    public function getClaimdatetime()
    {
        return $this->claimdatetime;
    }

    /**
     * Sets the claim datetime string
     *
     * @param string $claimdatetime
     * @return $this
     */
    public function setClaimdatetime($claimdatetime)
    {
        $this->claimdatetime = $claimdatetime;
        return $this;
    }

    /**
     * Gets the claim status
     *
     * @return string
     */
    public function getClaimstatus()
    {
        return $this->claimstatus;
    }

    /**
     * Sets the claim status
     *
     * @param string $claimstatus
     * @return $this
     */
    public function setClaimstatus($claimstatus)
    {
        $this->claimstatus = $claimstatus;
        return $this;
    }

    /**
     * Gets the incident datetime string
     *
     * @return string
     */
    public function getIncidentdatetime()
    {
        return $this->incidentdatetime;
    }

    /**
     * Sets the incident datetime string
     *
     * @param string $incidentdatetime
     * @return $this
     */
    public function setIncidentdatetime($incidentdatetime)
    {
        $this->incidentdatetime = $incidentdatetime;
        return $this;
    }

    /**
     * Gets the incident description
     *
     * @return string
     */
    public function getIncidentdescription()
    {
        return $this->incidentdescription;
    }

    /**
     * Sets the incident description
     *
     * @param string $incidentdescription
     * @return $this
     */
    public function setIncidentdescription($incidentdescription)
    {
        $this->incidentdescription = $incidentdescription;
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

    /**
     * Gets the processed flag
     *
     * @return bool
     */
    public function getProcessed()
    {
        return $this->processed;
    }

    /**
     * Sets the processed flag
     *
     * @param bool $processed
     * @return $this
     */
    public function setProcessed($processed)
    {
        $this->processed = $processed;
        return $this;
    }

}