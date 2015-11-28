<?php

namespace RRP\Application\Decorators;

use RRP\Application\AbstractApplicationDecorator;
use RRP\Application\ApplicationDecoratorInterface;
use RRP\Common\ReferenceTypes;
use RRP\Common\Titles;
use RRP\Rate\RateDecorators\RentRecoveryPlus;
use RRP\Transaction\TransactionDecoratorFactory;
use RRP\Underwriting\UnderwritingDecoratorFactory;
use RRP\Underwriting\Decorators\RentRecoveryPlusAnswers;
use RRP\Utility\PolicyOptionsManager;

/**
 * Class RentRecoveryPlusInsight
 *
 * @package RRP\Application\Decorator
 * @author April Portus <april.portus@barbon.com>
 */
class RentRecoveryPlusInsight extends AbstractApplicationDecorator implements ApplicationDecoratorInterface
{
    /**
     * @var \Manager_Core_PolicyNumber
     */
    private $policyNumberManager;

    /**
     * @var \Datasource_Insurance_Disbursement
     */
    private $disbursement;

    /**
     * @var \Datasource_Insurance_Policy_Cover
     */
    private $policyCover;

    /**
     * @var \Datasource_Insurance_Policy_Options
     */
    private $policyOptions;

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct('rrp.legacy.datasource.insight_policies');
        $this->policyNumberManager = $this->container->get('rrp.legacy.manager.policy_number');
        $this->disbursement = $this->container->get('rrp.legacy.datasource.disbursement');
        $this->policyOptions = $this->container->get('rrp.legacy.datasource.policy_options');
        $this->policyCover = $this->container->get('rrp.legacy.datasource.policy_cover');
    }

    /**
     * Renew a policy from the 'temporary' insight_rrp_policy to the policy table
     *
     * @param string $rrpPolicyNumber
     * @param string $newPolicyNumber
     * @param string $termNumber
     * @return bool
     * @throws \RuntimeException
     */
    public function migratePolicy($rrpPolicyNumber, $newPolicyNumber, $termNumber)
    {
        if ($this->policyNumberManager->isRentRecoveryPlusInsightPolicy($rrpPolicyNumber)) {

            $this->setPolicyNumber($newPolicyNumber);

            $disbData = $this->disbursement->getDisbursement(
                $this->appData->getWhiteLabelID(),
                $this->appData->getRateSetID()
            );
            if ( ! $disbData) {
                $message = sprintf(
                    'Disbursement data not found (WhiteLabelId = %s, RateSetID = %d)',
                    $this->appData->getWhiteLabelID(),
                    $this->appData->getRateSetID()
                );
                throw new \RuntimeException($message);
            }
            $getter = sprintf(
                'getRrpi%s%dm',
                (ReferenceTypes::isFullReference($this->rrpData->getReferenceType()) ? 'FullRef' : 'CreditCheck'),
                $this->appData->getPolicyLength()
            );
            if (PolicyOptionsManager::isOptionSet(
                $this->appData->getPolicyOptions(),
                $this->container->get('rrp.legacy.const.policy_option_rrp-nilexcess'),
                $this->appData->getAmountsCovered()
            )) {
                $getter .= '0xs';
            }
            if ( ! method_exists($disbData, $getter)) {
                $message = sprintf('Unknown disbursement type (%s)', $getter);
                throw new \RuntimeException($message);
            }
            $disbursement = $disbData->{$getter}();
            if (empty($disbursement)) {
                $message = sprintf(
                    'Disbursement data not set (WhiteLabelId = %s, RateSetID = %d, %s)',
                    $this->appData->getWhiteLabelID(),
                    $this->appData->getRateSetID(),
                    $getter
                );
                throw new \RuntimeException($message);
            }

            $propertyRental = PolicyOptionsManager::getOption(
                $this->appData->getPolicyOptions(),
                $this->getContainer()->get('rrp.legacy.const.policy_option_rrp'),
                $this->appData->getAmountsCovered()
            );
            if ($propertyRental < RentRecoveryPlus::BAND_A_LIMIT) {
                $band = $disbData::BAND_A;
            }
            else {
                $band = $disbData::BAND_B;
            }
            $insuranceNetAmount = $disbData->extractBandDisbursement($disbursement, $band);

            // Policy Term
            $termId = $this->policyTerm->updatePolicyTerm(
                $this->appData,
                $termNumber,
                array(
                    'netPremium' => $insuranceNetAmount,
                    'collectedPremium' => $this->appData->premium
                )
            );

            if ($termId) {
                // Write the policy to the datasource
                $this
                    ->getAppData()
                        ->setStatus($this->container->get('rrp.legacy.const.status_policy'))
                        ->setTermId($termId);
                $legacyPolicy = $this->container->get('rrp.legacy.datasource.policy');;
                $legacyPolicy->save($this->appData);

                // Delete the legacy insight record.
                $policyConstType = $this->container->get('rrp.legacy.const.quote_policy_number');
                $this->application->remove(array($policyConstType => $rrpPolicyNumber));

                $insightStatusIas = $this->container->get('rrp.legacy.const.insight_status_ias');

                // Now move the RRP / LL Interest parts
                if ($this->rrp->accept($rrpPolicyNumber, $newPolicyNumber, $insightStatusIas)) {
                    if ($this->llInterest->accept($rrpPolicyNumber, $newPolicyNumber)) {

                        /** @var RentRecoveryPlusAnswers $underwriting */
                        $underwriting = UnderwritingDecoratorFactory::getDecorator(
                            UnderwritingDecoratorFactory::UNDERWRITING_RENT_RECOVERY_PLUS,
                            $this->container->get('rrp.config.underwriting_question_set_id'),
                            $this->appData->getIssueDate(),
                            $rrpPolicyNumber
                        );
                        $answers = $underwriting->getAllAnswers();
                        $answers[RentRecoveryPlusAnswers::QUESTION_ID_RENT_IN_ADVANCE] = true;
                        $answers[RentRecoveryPlusAnswers::QUESTION_ID_PRIOR_CLAIMS] = false;
                        $answers[RentRecoveryPlusAnswers::QUESTION_ID_DEPOSIT_SUFFICIENT] = true;
                        $underwriting
                            ->setAnswers($answers)
                            ->saveAnswers()
                            ->changeQuoteToPolicy($newPolicyNumber);

                        $coverOptions = array();
                        foreach ($this->appData->getValidPolicyOptionNames() as $optionName) {
                            if (PolicyOptionsManager::isOptionSet(
                                $this->appData->getPolicyOptions(),
                                $optionName,
                                $this->appData->getAmountsCovered()
                            )) {
                                $optionId = $this->policyOptions->fetchOptionsByName($optionName);

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
                                    'policyNumber' => $newPolicyNumber,
                                    'policyOptionID' => $optionId,
                                    'sumInsured' => $sumInsured,
                                    'premium' => $premium
                                );
                            }
                        }
                        $this->policyCover->setCover($newPolicyNumber, $coverOptions);

                        return $newPolicyNumber;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Split the name if required
     *
     * @return $this;
     */
    public function splitName()
    {
        $lastName = $this->lliData->getLastName();
        if ($lastName == '') {
            $fullName = $this->lliData->getFirstName();

            $titleList = Titles::getTitles();
            $keys = array_map('strlen', array_keys($titleList));
            array_multisort($keys, SORT_DESC, $titleList);
            foreach (array_keys($titleList) as $title) {
                if (substr($fullName, 0, strlen($title)+1) == $title . ' ') {
                    preg_match("/(.*) ([A-Za-z-']+)/", substr($fullName, strlen($title)+1), $matches);
	                $firstName = $matches[1];
                    $lastName = $matches[2];
                    if (count(explode(' ', $firstName)) <= 3) {
                        $this->lliData
                            ->setTitle($title)
                            ->setFirstName($firstName)
                            ->setLastName($lastName);
                        break;
                    }
                }
            }
        }
        return $this;
    }
}