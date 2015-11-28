<?php

namespace Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model;

use Barbondev\IRISSDK\Common\Model\AbstractResponseModel;
use Guzzle\Service\Command\OperationCommand;

/**
 * Class Progress
 *
 * @package Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class Progress extends AbstractResponseModel
{
    /**
     * @var bool
     */
    private $chargeAgreed;

    /**
     * @var bool
     */
    private $propertyDetailsCompleted;

    /**
     * @var bool
     */
    private $tenantDetailsCompleted;

    /**
     * @var bool
     */
    private $declarationReceivedViaDocuSign;

    /**
     * @var bool
     */
    private $creditHistoryChecked;

    /**
     * @var bool
     */
    private $rentalIndexChecked;

    /**
     * @var bool
     */
    private $interimReportCompleted;

    /**
     * @var bool
     */
    private $defaultTenantChecked;

    /**
     * @var bool
     */
    private $financialRefereeValidated;

    /**
     * @var bool
     */
    private $landlordRefereeDetailsCompleted;

    /**
     * @var bool
     */
    private $financialRefereeDetailsCompleted;

    /**
     * @var bool
     */
    private $finalReportCompleted;

    /**
     * {@inheritdoc}
     */
    public static function fromCommand(OperationCommand $command)
    {
        $data = $command->getResponse()->json();

        return self::hydrateModelProperties(
            new self(),
            $data
        );
    }

    /**
     * Set chargeAgreed
     *
     * @param boolean $chargeAgreed
     * @return $this
     */
    public function setChargeAgreed($chargeAgreed)
    {
        $this->chargeAgreed = $chargeAgreed;
        return $this;
    }

    /**
     * Get chargeAgreed
     *
     * @return boolean
     */
    public function getChargeAgreed()
    {
        return $this->chargeAgreed;
    }

    /**
     * Set creditHistoryChecked
     *
     * @param boolean $creditHistoryChecked
     * @return $this
     */
    public function setCreditHistoryChecked($creditHistoryChecked)
    {
        $this->creditHistoryChecked = $creditHistoryChecked;
        return $this;
    }

    /**
     * Get creditHistoryChecked
     *
     * @return boolean
     */
    public function getCreditHistoryChecked()
    {
        return $this->creditHistoryChecked;
    }

    /**
     * Set declarationReceivedViaDocuSign
     *
     * @param boolean $declarationReceivedViaDocuSign
     * @return $this
     */
    public function setDeclarationReceivedViaDocuSign($declarationReceivedViaDocuSign)
    {
        $this->declarationReceivedViaDocuSign = $declarationReceivedViaDocuSign;
        return $this;
    }

    /**
     * Get declarationReceivedViaDocuSign
     *
     * @return boolean
     */
    public function getDeclarationReceivedViaDocuSign()
    {
        return $this->declarationReceivedViaDocuSign;
    }

    /**
     * Set defaultTenantChecked
     *
     * @param boolean $defaultTenantChecked
     * @return $this
     */
    public function setDefaultTenantChecked($defaultTenantChecked)
    {
        $this->defaultTenantChecked = $defaultTenantChecked;
        return $this;
    }

    /**
     * Get defaultTenantChecked
     *
     * @return boolean
     */
    public function getDefaultTenantChecked()
    {
        return $this->defaultTenantChecked;
    }

    /**
     * Set finalReportCompleted
     *
     * @param boolean $finalReportCompleted
     * @return $this
     */
    public function setFinalReportCompleted($finalReportCompleted)
    {
        $this->finalReportCompleted = $finalReportCompleted;
        return $this;
    }

    /**
     * Get finalReportCompleted
     *
     * @return boolean
     */
    public function getFinalReportCompleted()
    {
        return $this->finalReportCompleted;
    }

    /**
     * Set financialRefereeDetailsCompleted
     *
     * @param boolean $financialRefereeDetailsCompleted
     * @return $this
     */
    public function setFinancialRefereeDetailsCompleted($financialRefereeDetailsCompleted)
    {
        $this->financialRefereeDetailsCompleted = $financialRefereeDetailsCompleted;
        return $this;
    }

    /**
     * Get financialRefereeDetailsCompleted
     *
     * @return boolean
     */
    public function getFinancialRefereeDetailsCompleted()
    {
        return $this->financialRefereeDetailsCompleted;
    }

    /**
     * Set financialRefereeValidated
     *
     * @param boolean $financialRefereeValidated
     * @return $this
     */
    public function setFinancialRefereeValidated($financialRefereeValidated)
    {
        $this->financialRefereeValidated = $financialRefereeValidated;
        return $this;
    }

    /**
     * Get financialRefereeValidated
     *
     * @return boolean
     */
    public function getFinancialRefereeValidated()
    {
        return $this->financialRefereeValidated;
    }

    /**
     * Set interimReportCompleted
     *
     * @param boolean $interimReportCompleted
     * @return $this
     */
    public function setInterimReportCompleted($interimReportCompleted)
    {
        $this->interimReportCompleted = $interimReportCompleted;
        return $this;
    }

    /**
     * Get interimReportCompleted
     *
     * @return boolean
     */
    public function getInterimReportCompleted()
    {
        return $this->interimReportCompleted;
    }

    /**
     * Set landlordRefereeDetailsCompleted
     *
     * @param boolean $landlordRefereeDetailsCompleted
     * @return $this
     */
    public function setLandlordRefereeDetailsCompleted($landlordRefereeDetailsCompleted)
    {
        $this->landlordRefereeDetailsCompleted = $landlordRefereeDetailsCompleted;
        return $this;
    }

    /**
     * Get landlordRefereeDetailsCompleted
     *
     * @return boolean
     */
    public function getLandlordRefereeDetailsCompleted()
    {
        return $this->landlordRefereeDetailsCompleted;
    }

    /**
     * Set propertyDetailsCompleted
     *
     * @param boolean $propertyDetailsCompleted
     * @return $this
     */
    public function setPropertyDetailsCompleted($propertyDetailsCompleted)
    {
        $this->propertyDetailsCompleted = $propertyDetailsCompleted;
        return $this;
    }

    /**
     * Get propertyDetailsCompleted
     *
     * @return boolean
     */
    public function getPropertyDetailsCompleted()
    {
        return $this->propertyDetailsCompleted;
    }

    /**
     * Set rentalIndexChecked
     *
     * @param boolean $rentalIndexChecked
     * @return $this
     */
    public function setRentalIndexChecked($rentalIndexChecked)
    {
        $this->rentalIndexChecked = $rentalIndexChecked;
        return $this;
    }

    /**
     * Get rentalIndexChecked
     *
     * @return boolean
     */
    public function getRentalIndexChecked()
    {
        return $this->rentalIndexChecked;
    }

    /**
     * Set tenantDetailsCompleted
     *
     * @param boolean $tenantDetailsCompleted
     * @return $this
     */
    public function setTenantDetailsCompleted($tenantDetailsCompleted)
    {
        $this->tenantDetailsCompleted = $tenantDetailsCompleted;
        return $this;
    }

    /**
     * Get tenantDetailsCompleted
     *
     * @return boolean
     */
    public function getTenantDetailsCompleted()
    {
        return $this->tenantDetailsCompleted;
    }
}