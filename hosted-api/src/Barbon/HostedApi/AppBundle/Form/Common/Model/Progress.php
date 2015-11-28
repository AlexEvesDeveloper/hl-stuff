<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Model;

use Barbon\IrisRestClient\Annotation as Iris;

/**
 * @Iris\Entity\Progress
 */
class Progress
{
    /**
     * @Iris\Field
     * @var bool
     */
    private $chargeAgreed;

    /**
     * @Iris\Field
     * @var bool
     */
    private $propertyDetailsCompleted;

    /**
     * @Iris\Field
     * @var bool
     */
    private $tenantDetailsCompleted;

    /**
     * @Iris\Field
     * @var bool
     */
    private $declarationReceivedViaDocuSign;

    /**
     * @Iris\Field
     * @var bool
     */
    private $creditHistoryChecked;

    /**
     * @Iris\Field
     * @var bool
     */
    private $rentalIndexChecked;

    /**
     * @Iris\Field
     * @var bool
     */
    private $interimReportCompleted;

    /**
     * @Iris\Field
     * @var bool
     */
    private $defaultTenantChecked;

    /**
     * @Iris\Field
     * @var bool
     */
    private $financialRefereeValidated;

    /**
     * @Iris\Field
     * @var bool
     */
    private $landlordRefereeDetailsCompleted;

    /**
     * @Iris\Field
     * @var bool
     */
    private $financialRefereeDetailsCompleted;

    /**
     * @Iris\Field
     * @var bool
     */
    private $finalReportCompleted;

    /**
     * Get charge agreed
     *
     * @return boolean
     */
    public function isChargeAgreed()
    {
        return $this->chargeAgreed;
    }

    /**
     * Set charge agreed
     *
     * @param boolean $chargeAgreed
     */
    public function setChargeAgreed($chargeAgreed)
    {
        $this->chargeAgreed = $chargeAgreed;
    }

    /**
     * Get property details completed
     *
     * @return boolean
     */
    public function isPropertyDetailsCompleted()
    {
        return $this->propertyDetailsCompleted;
    }

    /**
     * Set property details completed
     *
     * @param boolean $propertyDetailsCompleted
     */
    public function setPropertyDetailsCompleted($propertyDetailsCompleted)
    {
        $this->propertyDetailsCompleted = $propertyDetailsCompleted;
    }

    /**
     * Get tenant details completed
     *
     * @return boolean
     */
    public function isTenantDetailsCompleted()
    {
        return $this->tenantDetailsCompleted;
    }

    /**
     * Set tenant details completed
     *
     * @param boolean $tenantDetailsCompleted
     */
    public function setTenantDetailsCompleted($tenantDetailsCompleted)
    {
        $this->tenantDetailsCompleted = $tenantDetailsCompleted;
    }

    /**
     * Get declaration received via docusign
     *
     * @return boolean
     */
    public function isDeclarationReceivedViaDocuSign()
    {
        return $this->declarationReceivedViaDocuSign;
    }

    /**
     * Set declaration received via docusign
     *
     * @param boolean $declarationReceivedViaDocuSign
     */
    public function setDeclarationReceivedViaDocuSign($declarationReceivedViaDocuSign)
    {
        $this->declarationReceivedViaDocuSign = $declarationReceivedViaDocuSign;
    }

    /**
     * Get credit history checked
     *
     * @return boolean
     */
    public function isCreditHistoryChecked()
    {
        return $this->creditHistoryChecked;
    }

    /**
     * Set credit history checked
     *
     * @param boolean $creditHistoryChecked
     */
    public function setCreditHistoryChecked($creditHistoryChecked)
    {
        $this->creditHistoryChecked = $creditHistoryChecked;
    }

    /**
     * Get rental index checked
     *
     * @return boolean
     */
    public function isRentalIndexChecked()
    {
        return $this->rentalIndexChecked;
    }

    /**
     * Set rental index checked
     *
     * @param boolean $rentalIndexChecked
     */
    public function setRentalIndexChecked($rentalIndexChecked)
    {
        $this->rentalIndexChecked = $rentalIndexChecked;
    }

    /**
     * Get interim report completed
     *
     * @return boolean
     */
    public function isInterimReportCompleted()
    {
        return $this->interimReportCompleted;
    }

    /**
     * Set interim report completed
     *
     * @param boolean $interimReportCompleted
     */
    public function setInterimReportCompleted($interimReportCompleted)
    {
        $this->interimReportCompleted = $interimReportCompleted;
    }

    /**
     * Get default tenant checked
     *
     * @return boolean
     */
    public function isDefaultTenantChecked()
    {
        return $this->defaultTenantChecked;
    }

    /**
     * Set default tenant checked
     *
     * @param boolean $defaultTenantChecked
     */
    public function setDefaultTenantChecked($defaultTenantChecked)
    {
        $this->defaultTenantChecked = $defaultTenantChecked;
    }

    /**
     * Get financial referee validated
     *
     * @return boolean
     */
    public function isFinancialRefereeValidated()
    {
        return $this->financialRefereeValidated;
    }

    /**
     * Set financial referee validated
     *
     * @param boolean $financialRefereeValidated
     */
    public function setFinancialRefereeValidated($financialRefereeValidated)
    {
        $this->financialRefereeValidated = $financialRefereeValidated;
    }

    /**
     * Get landlord referee details completed
     *
     * @return boolean
     */
    public function isLandlordRefereeDetailsCompleted()
    {
        return $this->landlordRefereeDetailsCompleted;
    }

    /**
     * Set landlord referee details completed
     *
     * @param boolean $landlordRefereeDetailsCompleted
     */
    public function setLandlordRefereeDetailsCompleted($landlordRefereeDetailsCompleted)
    {
        $this->landlordRefereeDetailsCompleted = $landlordRefereeDetailsCompleted;
    }

    /**
     * Get financial referee details completed
     *
     * @return boolean
     */
    public function isFinancialRefereeDetailsCompleted()
    {
        return $this->financialRefereeDetailsCompleted;
    }

    /**
     * Set financial referee details completed
     *
     * @param boolean $financialRefereeDetailsCompleted
     */
    public function setFinancialRefereeDetailsCompleted($financialRefereeDetailsCompleted)
    {
        $this->financialRefereeDetailsCompleted = $financialRefereeDetailsCompleted;
    }

    /**
     *
     * Get final report completed
     * @return boolean
     */
    public function isFinalReportCompleted()
    {
        return $this->finalReportCompleted;
    }

    /**
     * Set final report completed
     *
     * @param boolean $finalReportCompleted
     */
    public function setFinalReportCompleted($finalReportCompleted)
    {
        $this->finalReportCompleted = $finalReportCompleted;
    }
}
