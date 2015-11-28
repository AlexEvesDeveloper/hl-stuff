<?php

namespace Barbondev\IRISSDK\Utility\RentAffordability\Model;

use Barbondev\IRISSDK\Common\Model\AbstractResponseModel;
use Guzzle\Service\Command\OperationCommand;

/**
 * Class RentAffordability
 *
 * @package Barbondev\IRISSDK\Utility\RentAffordability\Model
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class RentAffordability extends AbstractResponseModel
{
    /**
     * @var float
     */
    private $monthlyRent;

    /**
     * @var float
     */
    private $tenantAnnualIncome;

    /**
     * @var float
     */
    private $guarantorAnnualIncome;

    /**
     * {@inheritdoc}
     */
    public static function fromCommand(OperationCommand $command)
    {
        return self::hydrateModelProperties(
            new self(),
            $command->getResponse()->json()
        );
    }

    /**
     * Set guarantor annual income
     *
     * @param float $guarantorAnnualIncome
     * @return $this
     */
    public function setGuarantorAnnualIncome($guarantorAnnualIncome)
    {
        $this->guarantorAnnualIncome = $guarantorAnnualIncome;

        return $this;
    }

    /**
     * Get guarantor annual income
     *
     * @return float
     */
    public function getGuarantorAnnualIncome()
    {
        return $this->guarantorAnnualIncome;
    }

    /**
     * Set monthly rent
     *
     * @param float $monthlyRent
     * @return $this
     */
    public function setMonthlyRent($monthlyRent)
    {
        $this->monthlyRent = $monthlyRent;

        return $this;
    }

    /**
     * Get monthly rent
     *
     * @return float
     */
    public function getMonthlyRent()
    {
        return $this->monthlyRent;
    }

    /**
     * Set tenant annual income
     *
     * @param float $tenantAnnualIncome
     * @return $this
     */
    public function setTenantAnnualIncome($tenantAnnualIncome)
    {
        $this->tenantAnnualIncome = $tenantAnnualIncome;

        return $this;
    }

    /**
     * Get tenant annual income
     *
     * @return float
     */
    public function getTenantAnnualIncome()
    {
        return $this->tenantAnnualIncome;
    }
}