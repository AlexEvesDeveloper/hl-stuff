<?php

namespace RRP\Application\Decorators;

use RRP\Application\AbstractApplicationDecorator;
use RRP\Application\ApplicationDecoratorInterface;
use RRP\Common\ReferenceTypes;
use RRP\Rate\RateDecorators\RentRecoveryPlus;
use RRP\Transaction\TransactionDecoratorFactory;
use RRP\Underwriting\UnderwritingDecoratorFactory;
use RRP\Utility\PolicyOptionsManager;

/**
 * Class RentRecoveryPlusQuote
 *
 * @package RRP\Application\Decorator
 * @author April Portus <april.portus@barbon.com>
 */
class RentRecoveryPlusQuote extends AbstractApplicationDecorator implements ApplicationDecoratorInterface
{
    /**
     * @var \Datasource_Insurance_PolicyNotes
     */
    private $policyNotes;

    /**
     * @var \Datasource_Insurance_Disbursement
     */
    private $disbursement;

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct('rrp.legacy.datasource.quote');
        $this->policyNotes = $this->container->get('rrp.legacy.datasource.policy_notes');
        $this->disbursement = $this->container->get('rrp.legacy.datasource.disbursement');
    }

    /**
     * Accept the quote and change it to a policy
     *
     * @param string $quoteNumber
     * @return null|string
     * @throws \RuntimeException
     */
    public function acceptQuote($quoteNumber)
    {
        /** @var \Manager_Core_PolicyNumber $policyNumberManager */
        $policyNumberManager = $this->getContainer()->get('rrp.legacy.manager.policy_number');

        if ($policyNumberManager->isRentRecoveryPlusQuote($quoteNumber)) {
            $this->isNewApplication = false;

            $policyNumber = $policyNumberManager::convertQuoteToPolicyNumber($quoteNumber);

            if ($this->populateByPolicyNumber($quoteNumber)) {
                //Set the issueDate and timecompleted fields (which oddly record the same value but
                // in different formats).
                $timeCompleted = time();
                $issueDate = new \DateTime();
                $issueDate->setTimestamp($timeCompleted);

                $this
                    ->setPolicyNumber($policyNumber)
                    ->appData
                        ->setStatus($this->getContainer()->get('rrp.legacy.const.status_policy'))
                        ->setPayStatus($this->getContainer()->get('rrp.legacy.const.pay_status_up-to-date'))
                        ->setIssueDate($issueDate)
                        ->setTimeCompleted($timeCompleted);

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
                    $this->appData->getAmountscovered()
                );

                if ($propertyRental < RentRecoveryPlus::BAND_A_LIMIT) {
                    $band = $disbData::BAND_A;
                    $transBand = RentRecoveryPlus::BAND_A;
                }
                else {
                    $band = $disbData::BAND_B;
                    $transBand = RentRecoveryPlus::BAND_B;
                }
                $insuranceNetAmount = $disbData->extractBandDisbursement($disbursement, $band);

                $termId = $this->policyTerm->insertPolicyTerm($this->appData, $insuranceNetAmount);
                if ($termId) {

                    $policy = $this->container->get('rrp.legacy.datasource.policy');
                    if ($policy->save($this->appData)) {

                        //Delete the legacy quote.
                        $policyConstType = $this->container->get('rrp.legacy.const.quote_policy_number');
                        $this->application->remove(array($policyConstType => $quoteNumber));

                        // Now move the RRP / LL Interest parts

                        if ($this->rrp->accept($quoteNumber, $policyNumber)) {
                            if ($this->rrpTenantReference->accept($quoteNumber, $policyNumber, $termId)) {
                                if ($this->llInterest->accept($quoteNumber, $policyNumber)) {

                                    $this->policyNotes->changeQuoteToPolicy($quoteNumber, $policyNumber);

                                    $transaction = TransactionDecoratorFactory::getDecorator(
                                        TransactionDecoratorFactory::TRANSACTION_ADD,
                                        $this->getAppData()->getPremium(),
                                        $insuranceNetAmount,
                                        $termId,
                                        0,
                                        $transBand,
                                        $this->appData->getStartDate(),
                                        $this->appData
                                    );
                                    $transaction->processAllTransactions();

                                    $underwriting = UnderwritingDecoratorFactory::getDecorator(
                                        UnderwritingDecoratorFactory::UNDERWRITING_RENT_RECOVERY_PLUS,
                                        $this->container->get('rrp.config.underwriting_question_set_id'),
                                        $this->appData->getIssueDate(),
                                        $quoteNumber
                                    );
                                    $underwriting->changeQuoteToPolicy();

                                    /** @var \Datasource_Insurance_Policy_Cover $policyCover */
                                    $policyCover = $this->container->get('rrp.legacy.datasource.policy_cover');
                                    $policyCover->changeQuoteToPolicy($quoteNumber, $policyNumber);

                                    return $policyNumber;
                                }
                            }
                        }
                    }
                }
            }
        }
        return null;
    }

}