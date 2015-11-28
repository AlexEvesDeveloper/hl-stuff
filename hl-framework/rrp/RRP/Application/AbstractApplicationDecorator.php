<?php

namespace RRP\Application;

use RRP\Application\Exception\InvalidUnderwritingAnswerNumberException;
use RRP\Application\Exception\InvalidTenantReferenceNumberException;
use RRP\Common\Countries;
use RRP\Common\ReferenceTypes;
use RRP\Common\TenancyAgreementTypes;
use RRP\Common\PropertyLetTypes;
use RRP\Common\Titles;
use RRP\DependencyInjection\LegacyContainer;
use RRP\Model\RentRecoveryPlusApplication;
use RRP\Underwriting\UnderwritingDecoratorFactory;
use RRP\Underwriting\Decorators\RentRecoveryPlusAnswers;
use RRP\Utility\PolicyOptionsManager;

/**
 * Class AbstractApplicationDecorator
 *
 * @package RRP\Application
 * @author April Portus <april.portus@barbon.com>
 */
abstract class AbstractApplicationDecorator
{
    /**
     * Containerised identifier for the Rent Recovery Plus data-source
     */
    const LEGACY_RENT_RECOVERY_PLUS_DATASOURCE = 'rrp.legacy.datasource.rent_recovery_plus';

    /**
     * Containerised identifier for the Landlord Interest data-source
     */
    const LEGACY_LANDLORD_INTEREST_DATASOURCE = 'rrp.legacy.datasource.landlord_interest';

    /**
     * Containerised identifier for the RRP Tenant Reference data-source
     */
    const LEGACY_RRP_TENANT_REFERENCE_DATASOURCE = 'rrp.legacy.datasource.rrp_tenant_reference';

    /**
     * Containerised identifier for the RRP Tenant Reference data-source
     */
    const LEGACY_POLICY_TERM_DATASOURCE = 'rrp.legacy.datasource.policy_term';

    /**
     * Containerised identifier for the policy options data-source
     */
    const LEGACY_POLICY_OPTION_DATASOURCE = 'rrp.legacy.datasource.policy_options';

    /**
     * Containerised identifier for the policy cover data-source
     */
    const LEGACY_POLICY_COVER_DATASOURCE = 'rrp.legacy.datasource.policy_cover';

    /**
     * @var \RRP\DependencyInjection\LegacyContainer
     */
    protected $container;

    /**
     * @var object
     */
    protected $application;
    
    /**
     * @var object
     */
    protected $llInterest;

    /**
     * @var object
     */
    protected $rrp;

    /**
     * @var \Datasource_Insurance_RentRecoveryPlus_RrpTenantReferences but decoupled via dependancy injection
     */
    protected $rrpTenantReference;

    /**
     * @var \Datasource_Insurance_Policy_Term but decoupled via dependancy injection
     */
    protected $policyTerm;

    /**
     * @var \Model_Insurance_RentRecoveryPlus_LegacyQuote but decoupled via dependancy injection
     */
    protected $appData;

    /**
     * @var \Model_Insurance_RentRecoveryPlus_LandlordInterest but decoupled via dependancy injection
     */
    protected $lliData;

    /**
     * @var \Model_Insurance_RentRecoveryPlus_RentRecoveryPlus but decoupled via dependancy injection
     */
    protected $rrpData;

    /**
     * @var array of \Model_Insurance_RentRecoveryPlus_RrpTenantReference but decoupled via dependancy injection
     */
    protected $rrpTenantReferenceData;

    /**
     * @var array
     */
    protected $underwritingAnswers;

    /**
     * @var bool
     */
    protected $isNewApplication;

    /**
     * @inheritdoc
     */
    public function __construct($applicationDatasourceIdentifier)
    {
        $this->container = new LegacyContainer();
        $this->application = $this->container->get($applicationDatasourceIdentifier);
        $this->rrp = $this->container->get(self::LEGACY_RENT_RECOVERY_PLUS_DATASOURCE);
        $this->llInterest = $this->container->get(self::LEGACY_LANDLORD_INTEREST_DATASOURCE);
        $this->rrpTenantReference = $this->container->get(self::LEGACY_RRP_TENANT_REFERENCE_DATASOURCE);
        $this->policyTerm = $this->container->get(self::LEGACY_POLICY_TERM_DATASOURCE);

        $this->appData =
            $this->container->get(str_replace('.datasource.', '.', $applicationDatasourceIdentifier));
        $this->rrpData =
            $this->container->get(str_replace('.datasource.', '.', self::LEGACY_RENT_RECOVERY_PLUS_DATASOURCE));
        $this->lliData =
            $this->container->get(str_replace('.datasource.', '.', self::LEGACY_LANDLORD_INTEREST_DATASOURCE));
        $this->rrpTenantReferenceData = array();
        $this->underwritingAnswers = array();

        $this->isNewApplication = true;
    }

    /**
     * Gets the Legacy Container
     *
     * @return LegacyContainer
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @inheritdoc
     */
    public function save()
    {
        if ($this->application->save($this->appData)) {
            if ($this->rrp->save($this->rrpData)) {
                if ($this->llInterest->save($this->lliData)) {
                    $isSuccess = true;
                    if ($this->isNewApplication) {
                        // Check we have reference data as it may have been a reference type of other provider
                        if (count($this->rrpTenantReferenceData) > 0) {
                            /** @var \Model_Insurance_RentRecoveryPlus_RrpTenantReference $rrpTenantReference */
                            $rrpTenantReference = $this->rrpTenantReferenceData[0];
                            $this->rrpTenantReference->removeAllForPolicy(
                                $rrpTenantReference->getPolicyNumber(),
                                $rrpTenantReference->getDateCreatedAt(),
                                $rrpTenantReference->getTermId(),
                                $rrpTenantReference->getMtaId()
                            );
                            foreach ($this->rrpTenantReferenceData as &$rrpTenantReference) {
                                if (!$this->rrpTenantReference->save($rrpTenantReference)) {
                                    $isSuccess = false;
                                }
                            }
                        }

                        /** @var \Datasource_Insurance_Policy_Cover $policyCover */
                        $policyCover = $this->container->get(self::LEGACY_POLICY_COVER_DATASOURCE);

                        /** @var \Datasource_Insurance_Policy_Options $policyOptions */
                        $policyOptions = $this->container->get(self::LEGACY_POLICY_OPTION_DATASOURCE);

                        $coverOptions = array();
                        foreach ($this->appData->getValidPolicyOptionNames() as $optionName) {
                            if (PolicyOptionsManager::isOptionSet(
                                $this->appData->getPolicyOptions(),
                                $optionName,
                                $this->appData->getAmountsCovered()
                            )) {
                                $optionId = $policyOptions->fetchOptionsByName($optionName);

                                $sumInsured = PolicyOptionsManager::getOption(
                                    $this->appData->getPolicyOptions(),
                                    $optionName,
                                    $this->appData->getAmountsCovered()
                                );

                                $premium = PolicyOptionsManager::getOption(
                                    $this->appData->getPolicyOptions(),
                                    $optionName,
                                    $this->appData->getOptionPremiums()
                                );

                                $coverOptions[] = array(
                                    'policyNumber' => $this->appData->getPolicyNumber(),
                                    'policyOptionID' => $optionId,
                                    'sumInsured' => $sumInsured,
                                    'premium' => $premium
                                );
                            }
                        }
                        $policyCover->setCover($this->appData->getPolicyNumber(), $coverOptions);

                        $underwriting = UnderwritingDecoratorFactory::getDecorator(
                            UnderwritingDecoratorFactory::UNDERWRITING_RENT_RECOVERY_PLUS,
                            $this->container->get('rrp.config.underwriting_question_set_id'),
                            $this->appData->getIssueDate(),
                            $this->appData->getPolicyNumber()
                        );
                        /** @var \RRP\Underwriting\Decorators\RentRecoveryPlusAnswers $underwriting */
                        $underwriting
                            ->setAnswers($this->underwritingAnswers)
                            ->saveAnswers();
                    }
                    return $isSuccess;
                }
            }
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getAppData()
    {
        return $this->appData;
    }

    /**
     * @inheritdoc
     */
    public function getRrpData()
    {
        return $this->rrpData;
    }

    /**
     * @inheritdoc
     */
    public function getLliData()
    {
        return $this->lliData;
    }

    /**
     * @inheritdoc
     */
    public function getRrpTenantReferenceCount()
    {
        return count($this->rrpTenantReferenceData);
    }

    /**
     * @inheritdoc
     */
    public function getRrpTenantReferenceRecord($recordNumber)
    {
        if (
            $recordNumber < 0 ||
            $recordNumber > $this->getRrpTenantReferenceCount()
        ) {
            throw new InvalidTenantReferenceNumberException();
        }
        return $this->rrpTenantReferenceData[$recordNumber];
    }

    /**
     * @inheritdoc
     */
    public function addRrpTenantReferenceRecord($rrpTenantReference)
    {
        $this->rrpTenantReferenceData[] = $rrpTenantReference;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function clearAllRrpTenantReferences()
    {
        $this->rrpTenantReferenceData = array();
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUnderwritingAnswerCount()
    {
        return count($this->underwritingAnswers);
    }

    /**
     * @inheritdoc
     */
    public function getUnderwritingAnswer($answerNumber)
    {
        if (
            $answerNumber < 0 ||
            $answerNumber > $this->getUnderwritingAnswerCount()
        ) {
            throw new InvalidUnderwritingAnswerNumberException;
        }
        return $this->underwritingAnswers[$answerNumber];
    }

    /**
     * @inheritdoc
     */
    public function getAllUnderwritingAnswers()
    {
        return $this->underwritingAnswers;
    }

    /**
     * @inheritdoc
     */
    public function setPolicyNumber($policyNumber)
    {
        $this->appData->setPolicyNumber($policyNumber);
        $this->lliData->setPolicyNumber($policyNumber);
        $this->rrpData->setPolicyNumber($policyNumber);
        /** @var \Model_Insurance_RentRecoveryPlus_RrpTenantReference $rrpTenantReference */
        foreach ($this->rrpTenantReferenceData as &$rrpTenantReference) {
            $rrpTenantReference->setPolicyNumber($policyNumber);
        }
        return $this;
    }

    /**
     * Sets the defaults for a new quote
     *
     * @param string $policyNumber
     * @param string $referenceType
     * @param bool $isContinuationOfExistingPolicy
     * @param string $propertyLetType
     * @param float $propertyDeposit
     * @param string $status
     * @return $this
     */
    public function setDefaults(
        $policyNumber, $referenceType, $isContinuationOfExistingPolicy, $propertyLetType, $propertyDeposit, $status
    ) {
        $this
            ->setPolicyNumber($policyNumber)
            ->getAppData()
                ->setPolicyName('rentrecoveryp')
                ->setIssueDate(date('d-m-Y'))
                ->setCancelledDate('00-00-0000')
                ->setPayStatus('')
                ->setPayMethod('CreditAccount')
                ->setPaidNet('no')
                ->setPolicyType('A')
                ->setStatus($status)
                ->setDateOfLastPayment('')
                ->setTimeCompleted('')
                ->setWhiteLabelID('HL')
                ->setOrigin(0)
                ->setExcessID(0)
                ->setStartTime('00:00:00');
        if ( ! ReferenceTypes::isProviderRequired($referenceType)) {
            $this->getRrpData()->setOtherProvider('');
        }
        else {
            $this->clearAllRrpTenantReferences();
        }
        if ($isContinuationOfExistingPolicy) {
            $this->getRrpData()->setIsExistingPolicyToBeCancelled(false);
        }
        if ( ! PropertyLetTypes::isLandlordPermissionRequired($propertyLetType)) {
            $this->getRrpData()->setHasLandlordPermission(false);
        }
        if ($propertyDeposit > 0.0) {
            $this->getRrpData()->setHasNilDepositInsurance(false);
        }
        return $this;
    }

    /**
     * Set the policy options
     *
     * @param float $sumInsured
     * @param float $premium
     * @param float $nilExcessOption
     * @return $this
     */
    public function setPolicyOptions($sumInsured, $premium, $nilExcessOption)
    {
        $policyOptions = PolicyOptionsManager::addPolicyOption(
            $this->container->get('rrp.legacy.const.policy_option_rrp')
        );
        $amountsCovered = PolicyOptionsManager::addPolicyOption($sumInsured);
        $optionPremiums = PolicyOptionsManager::addPolicyOption($premium - $nilExcessOption);
        if (0 != $nilExcessOption) {
            $policyOptions = PolicyOptionsManager::addPolicyOption(
                $this->container->get('rrp.legacy.const.policy_option_rrp-nilexcess'),
                $policyOptions);
            $amountsCovered = PolicyOptionsManager::addPolicyOption($sumInsured, $amountsCovered);
            $optionPremiums = PolicyOptionsManager::addPolicyOption($nilExcessOption, $optionPremiums);
        }
        $this->appData
            ->setPolicyOptions($policyOptions)
            ->setAmountsCovered($amountsCovered)
            ->setOptionPremiums($optionPremiums);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function populateByPolicyNumber($policyNumber)
    {
        $this->isNewApplication = false;
        $legacyApplication = $this->application->getByPolicyNumber($policyNumber);
        if ($legacyApplication) {
            // Body retrieved. now retrieve RRP specifics

            $this->appData->setFromLegacy($legacyApplication);
            $this->lliData = $this->llInterest->getLandlordInterest($legacyApplication->policyNumber);
            $this->rrpData = $this->rrp->getRentRecoveryPlus($legacyApplication->policyNumber);
            $this->rrpTenantReferenceData = $this->rrpTenantReference->getRrpTenantReferencesForPolicy($policyNumber);

            /** @var RentRecoveryPlusAnswers $underwriting */
            $underwriting = UnderwritingDecoratorFactory::getDecorator(
                UnderwritingDecoratorFactory::UNDERWRITING_RENT_RECOVERY_PLUS,
                $this->container->get('rrp.config.underwriting_question_set_id'),
                $this->appData->getIssueDate(),
                $this->appData->getPolicyNumber()
            );
            $this->underwritingAnswers = $underwriting->getAllAnswers();
            return true;
        }

        return false;
    }

    /**
     * Gets the application data using the policy number
     *
     * @param $policyNumber
     * @return RentRecoveryPlusApplication|null
     */
    public function getApplicationData($policyNumber)
    {
        if ( ! $this->populateByPolicyNumber($policyNumber)) {
            return null;
        }
        else if ($this->appData->getPayStatus() == $this->container->get('rrp.legacy.const.pay_status_referred')) {
            return null;
        }

        $referenceNumbers = array();
        /** @var \Model_Insurance_RentRecoveryPlus_RrpTenantReference $rrpTenantReference */
        foreach ($this->rrpTenantReferenceData as $rrpTenantReference) {
            $referenceNumbers[] = $rrpTenantReference->getReferenceNumber();
        }
        $tenancyStartAt = \DateTime::createFromFormat('Y-m-d', $this->rrpData->getTenancyStartAt());

        $applicationData = new RentRecoveryPlusApplication();
        $applicationData
            ->setPolicyNumber($policyNumber)
            ->setPolicyLength($this->appData->getPolicyLength())
            ->setReferenceType($this->rrpData->getReferenceType())
            ->setOtherProvider($this->rrpData->getOtherProvider())
            ->setReferenceNumber(implode("\n", $referenceNumbers))
            ->setIsNilExcess(PolicyOptionsManager::isOptionSet(
                $this->appData->getPolicyOptions(),
                $this->container->get('rrp.legacy.const.policy_option_rrp-nilexcess'),
                $this->appData->getAmountsCovered()
            ))
            ->setIsContinuationOfExistingPolicy(
                $this->underwritingAnswers[RentRecoveryPlusAnswers::QUESTION_ID_CONTINUATION]
            )
            ->setExistingPolicyRef($this->rrpData->getExistingPolicyRef())
            ->setIsExistingPolicyToBeCancelled($this->rrpData->getIsExistingPolicyToBeCancelled())
            ->setCancellationObjections($this->rrpData->getCancellationObjections())
            ->setPropertyLetType($this->rrpData->getPropertyLetType())
            ->setHasLandlordPermission($this->rrpData->getHasLandlordPermission())
            ->setPropertyAddress1($this->combineAddress($this->appData->getPropertyAddress1(), $this->appData->getPropertyAddress2()))
            ->setPropertyAddress2($this->appData->getPropertyAddress3())
            ->setPropertyPostcode($this->appData->getPropertyPostcode())
            ->setPropertyDeposit($this->rrpData->getPropertyDeposit())
            ->setPropertyRental(PolicyOptionsManager::getOption(
                $this->appData->getPolicyOptions(),
                $this->container->get('rrp.legacy.const.policy_option_rrp'),
                $this->appData->getAmountsCovered()
            ))
            ->setHasPaidRentInAdvance(
                $this->underwritingAnswers[RentRecoveryPlusAnswers::QUESTION_ID_RENT_IN_ADVANCE]
            )
            ->setTenancyStartAt($tenancyStartAt)
            ->setPolicyStartAt(\DateTime::createFromFormat('Y-m-d', $this->appData->getStartDate()))
            ->setLandlordTitle($this->lliData->getTitle())
            ->setLandlordFirstName($this->lliData->getFirstName())
            ->setLandlordLastName($this->lliData->getLastName())
            ->setLandlordEmailAddress($this->lliData->getEmailAddress())
            ->setLandlordPhoneNumber($this->lliData->getPhoneNumber())
            ->setLandlordAddress1($this->combineAddress($this->lliData->getAddress1(), $this->lliData->getAddress2()))
            ->setLandlordAddress2($this->lliData->getAddress3())
            ->setLandlordPostcode($this->lliData->getPostcode())
            ->setLandlordCountry($this->lliData->getCountry())
            ->setHasPossibleClaimCircumstances(
                $this->underwritingAnswers[RentRecoveryPlusAnswers::QUESTION_ID_CLAIM_CIRCUMSTANCES]
            )
            ->setClaimInfo($this->rrpData->getClaimInfo())
            ->setHasPermittedOccupiersOnly(
                $this->underwritingAnswers[RentRecoveryPlusAnswers::QUESTION_ID_PERMITTED_OCCUPIERS]
            )
            ->setHasTenancyDisputes(
                $this->underwritingAnswers[RentRecoveryPlusAnswers::QUESTION_ID_TENANCY_DISPUTES]
            )
            ->setTenancyAgreementType(TenancyAgreementTypes::getIsAssuredShortholdTenancy(
                $this->underwritingAnswers[RentRecoveryPlusAnswers::QUESTION_ID_TENANCY_AST]
            ))
            ->setHasPriorClaims(
                $this->underwritingAnswers[RentRecoveryPlusAnswers::QUESTION_ID_PRIOR_CLAIMS]
            )
            ->setIsDepositSufficient(
                $this->underwritingAnswers[RentRecoveryPlusAnswers::QUESTION_ID_DEPOSIT_SUFFICIENT]
            )
            ->setIsPayMonthly($this->appData->getPayBy() == $this->container->get('rrp.legacy.const.pay_by_monthly'))
        ;
        $this->isNewApplication = false;
        return $applicationData;
    }

    /**
     * @inheritdoc
     */
    public function getPolicyOptionMatch($policyOption, $optionName, $matchField)
    {
        return $this->application->getPolicyOptionMatch($policyOption, $optionName, $matchField);
    }

    /**
     * Sets the data from the application
     *
     * @param RentRecoveryPlusApplication $application
     * @return $this;
     */
    public function setFromApplication(RentRecoveryPlusApplication $application)
    {
        $this->rrpData
            ->setPolicyNumber($application->getPolicyNumber())
            ->setReferenceType($application->getReferenceType())
            ->setOtherProvider($application->getOtherProvider())
            ->setExistingPolicyRef($application->getExistingPolicyRef())
            ->setIsExistingPolicyToBeCancelled($application->getIsExistingPolicyToBeCancelled())
            ->setCancellationObjections($application->getCancellationObjections())
            ->setPropertyLetType($application->getPropertyLetType())
            ->setHasLandlordPermission($application->getHasLandlordPermission())
            ->setPropertyDeposit($application->getPropertyDeposit())
            ->setHasNilDepositInsurance($application->getHasNilDepositInsurance())
            ->setTenancyStartAt($application->getTenancyStartAt())
            ->setClaimInfo($application->getClaimInfo())
        ;
        $this->underwritingAnswers[RentRecoveryPlusAnswers::QUESTION_ID_CONTINUATION] =
            $application->getIsContinuationOfExistingPolicy();
        $this->underwritingAnswers[RentRecoveryPlusAnswers::QUESTION_ID_RENT_IN_ADVANCE] =
            $application->getHasPaidRentInAdvance();
        $this->underwritingAnswers[RentRecoveryPlusAnswers::QUESTION_ID_CLAIM_CIRCUMSTANCES] =
            $application->getHasPossibleClaimCircumstances();
        $this->underwritingAnswers[RentRecoveryPlusAnswers::QUESTION_ID_PERMITTED_OCCUPIERS] =
            $application->getHasPermittedOccupiersOnly();
        $this->underwritingAnswers[RentRecoveryPlusAnswers::QUESTION_ID_TENANCY_DISPUTES] =
            $application->getHasTenancyDisputes();
        $this->underwritingAnswers[RentRecoveryPlusAnswers::QUESTION_ID_TENANCY_AST] =
            TenancyAgreementTypes::isAssuredShortholdTenancy($application->getTenancyAgreementType());
        $this->underwritingAnswers[RentRecoveryPlusAnswers::QUESTION_ID_PRIOR_CLAIMS] =
            $application->getHasPriorClaims();
        $this->underwritingAnswers[RentRecoveryPlusAnswers::QUESTION_ID_DEPOSIT_SUFFICIENT] =
            $application->getIsDepositSufficient();

        $now = new \DateTime();
        $referenceNumberList = preg_split('/[\n,]/', $application->getReferenceNumber());
        $this->rrpTenantReferenceData = array();
        $rrpTenantReferenceData =
            $this->container->get(str_replace('.datasource.', '.', self::LEGACY_RRP_TENANT_REFERENCE_DATASOURCE));
        foreach ($referenceNumberList as $referenceNumber) {
            $referenceNumber = trim($referenceNumber);
            if ( ! empty($referenceNumber)) {
                $rrpTenantReferenceData
                    ->setId(0)
                    ->setPolicyNumber($application->getPolicyNumber())
                    ->setReferenceNumber($referenceNumber)
                    ->setTermId(0)
                    ->setMtaId(0)
                    ->setDateCreatedAt($now);
                $this->rrpTenantReferenceData[] = clone $rrpTenantReferenceData;
            }
        }

        list($address1, $address2, $address3) = $this->splitAddress(
            $application->getPropertyAddress1(),
            $application->getPropertyAddress2()
        );
        $this->appData
            ->setPolicyLength($application->getPolicyLength())
            ->setStartDate($application->getPolicyStartAt())
            ->setEndDate($application->getPolicyEndAt())
            ->setPropertyAddress1($address1)
            ->setPropertyAddress2($address2)
            ->setPropertyAddress3($address3)
            ->setPropertyPostcode($application->getPropertyPostcode())
        ;
        if ($application->getIsPayMonthly()) {
            $this->appData->setPayBy($this->container->get('rrp.legacy.const.pay_by_monthly'));
        }
        else {
            $this->appData->setPayBy($this->container->get('rrp.legacy.const.pay_by_annually'));
        }

        list($address1, $address2, $address3) = $this->splitAddress(
            $application->getLandlordAddress1(),
            $application->getLandlordAddress2()
        );
        $this->lliData
            ->setTitle($application->getLandlordTitle())
            ->setFirstName($application->getLandlordFirstName())
            ->setLastName($application->getLandlordLastName())
            ->setEmailAddress($application->getLandlordEmailAddress())
            ->setPhoneNumber($application->getLandlordPhoneNumber())
            ->setAddress1($address1)
            ->setAddress2($address2)
            ->setAddress3($address3)
            ->setPostcode($application->getLandlordPostcode())
            ->setCountry(Countries::getCountryName($application->getLandlordCountry()))
            ->setIsForeignAddress($application->getLandlordCountry() != Countries::GB)
        ;
        if (Titles::isOther($application->getLandlordTitle())) {
            $this->lliData->setTitle($application->getLandlordOtherTitle());
        }
        return $this;
    }

    /**
     * Function to split 2 address fields into 3
     *
     * @param string $inputAddress1
     * @param string $inputAddress2
     * @return array
     */
    private function splitAddress($inputAddress1, $inputAddress2)
    {
        $outputAddress3 = $inputAddress2;
        $list = explode(',', $inputAddress1);
        if (count($list) == 1) {
            $outputAddress1 = array_pop($list);
            $outputAddress2 = '';
        }
        else {
            $outputAddress2 = trim($list[count($list)-1]);
            unset($list[count($list)-1]);
            $outputAddress1 = implode(',', $list);
        }
        return array($outputAddress1, $outputAddress2, $outputAddress3);
    }

    /**
     * Function to combine 2 address fields together
     *
     * @param string $inputAddress1
     * @param string $inputAddress2
     * @return string
     */
    private function combineAddress($inputAddress1, $inputAddress2)
    {
        if ($inputAddress2 == '') {
            return $inputAddress1;
        }
        else if ($inputAddress1 == '') {
            return $inputAddress2;
        }
        return $inputAddress1 . ', ' . $inputAddress2;
    }
}