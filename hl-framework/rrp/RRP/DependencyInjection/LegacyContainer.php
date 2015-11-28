<?php

namespace RRP\DependencyInjection;
use RRP\Rate\RateDecorators\RentRecoveryPlus;

/**
 * Class LegacyContainer
 *
 * @package RRP\DependencyInjection
 * @author April Portus <april.portus@barbon.com>
 */
class LegacyContainer extends \Pimple implements ContainerInterface
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->bootParameters();
        $this->bootServices();
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        if ( ! isset($this[$id])) {
            throw new \InvalidArgumentException(sprintf('Unable to find Legacy service or parameter with the id %s', $id));
        }

        // Mailer class must have a new instance every time
        if ('rrp.legacy.mailer' == $id) {
            return new $this['rrp.legacy.mailer.class'];
        }

        return $this[$id];
    }

    /**
     * Boots parameters
     *
     * @return void
     */
    protected function bootParameters()
    {
        // Quote
        $this['rrp.legacy.datasource.quote.class'] = 'Datasource_Insurance_LegacyQuotes';

        // Policy
        $this['rrp.legacy.datasource.policy.class'] = 'Datasource_Insurance_LegacyPolicies';

        // Insight Policies
        $this['rrp.legacy.datasource.insight_policies.class'] = 'Datasource_Insurance_RentRecoveryPlus_InsightPolicies';

        // MTA
        $this['rrp.legacy.datasource.mta.class'] = 'Datasource_Insurance_MTA';

        // Rent Recovery Plus Mta
        $this['rrp.legacy.datasource.rrp_mta.class'] = 'Datasource_Insurance_RentRecoveryPlus_RentRecoveryPlusMta';

        // Rent Recovery Plus
        $this['rrp.legacy.datasource.rent_recovery_plus.class'] = 'Datasource_Insurance_RentRecoveryPlus_RentRecoveryPlus';

        // Landlord Interest
        $this['rrp.legacy.datasource.landlord_interest.class'] = 'Datasource_Insurance_RentRecoveryPlus_LandlordInterest';

        // RRP Tenant Reference
        $this['rrp.legacy.datasource.rrp_tenant_reference.class'] = 'Datasource_Insurance_RentRecoveryPlus_RrpTenantReferences';

        // Rent Recovery Plus Rates
        $this['rrp.legacy.datasource.rrp_rates.class'] = 'Datasource_Insurance_RentRecoveryPlus_Rates';

        // Policy Notes
        $this['rrp.legacy.datasource.policy_notes.class'] = 'Datasource_Insurance_PolicyNotes';

        // Policy Term
        $this['rrp.legacy.datasource.policy_term.class'] = 'Datasource_Insurance_Policy_Term';

        // Policy Cover
        $this['rrp.legacy.datasource.policy_cover.class'] = 'Datasource_Insurance_Policy_Cover';

        // Policy Options
        $this['rrp.legacy.datasource.policy_options.class'] = 'Datasource_Insurance_Policy_Options';

        // Underwriting Answers
        $this['rrp.legacy.datasource.underwriting_answers.class'] = 'Datasource_Insurance_Answers';

        // Underwriting Questions
        $this['rrp.legacy.datasource.underwriting_questions.class'] = 'Datasource_Insurance_Questions';

        // Agent
        $this['rrp.legacy.datasource.agent.class'] = 'Datasource_Core_Agents';

        // Search
        $this['rrp.legacy.datasource.search.class'] = 'Datasource_Insurance_RentRecoveryPlus_Search';

        // Disbursement
        $this['rrp.legacy.datasource.disbursement.class'] = 'Datasource_Insurance_Disbursement';

        // Transaction
        $this['rrp.legacy.datasource.transaction.class'] = 'Datasource_Core_Transaction';

        // TransactionSupport
        $this['rrp.legacy.datasource.transaction_support.class'] = 'Datasource_Core_TransactionSupport';

        // Insurance Product
        $this['rrp.legacy.datasource.product.class'] = 'Datasource_ReferencingLegacy_Product';

        // Quote
        $this['rrp.legacy.quote.class'] = 'Model_Insurance_RentRecoveryPlus_LegacyQuote';

        // Policy
        $this['rrp.legacy.policy.class'] = 'Model_Insurance_RentRecoveryPlus_LegacyPolicy';

        // Insight Policies
        $this['rrp.legacy.insight_policies.class'] = 'Model_Insurance_RentRecoveryPlus_InsightRrpPolicy';

        // Rent Recovery Plus
        $this['rrp.legacy.rent_recovery_plus.class'] = 'Model_Insurance_RentRecoveryPlus_RentRecoveryPlus';

        // Landlord Interest
        $this['rrp.legacy.landlord_interest.class'] = 'Model_Insurance_RentRecoveryPlus_LandlordInterest';

        // RRP Tenant Reference
        $this['rrp.legacy.rrp_tenant_reference.class'] = 'Model_Insurance_RentRecoveryPlus_RrpTenantReference';

        // Rent Recovery Plus Mta
        $this['rrp.legacy.rrp_mta.class'] = 'Model_Insurance_RentRecoveryPlus_RentRecoveryPlusMta';

        // Underwriting Answers
        $this['rrp.legacy.underwriting_answers.class'] = 'Model_Insurance_Answer';

        // Transaction
        $this['rrp.legacy.transaction.class'] = 'Model_Core_Transaction';

        // TransactionSupport
        $this['rrp.legacy.transaction_support.class'] = 'Model_Core_TransactionSupport';

        // MTA
        $this['rrp.legacy.mta.class'] = 'Model_Insurance_MTA';

        // PolicyNumber manager
        $this['rrp.legacy.manager.policy_number.class'] = 'Manager_Core_PolicyNumber';

        // Customer manager
        $this['rrp.legacy.manager.customer.class'] = 'Manager_Core_Customer';

        // Mailer
        $this['rrp.legacy.mailer.class'] = 'Application_Core_Mail';

        // Postcode validator
        $this['rrp.legacy.postcode_validator.class'] = 'Application_Core_Postcode';

        // Quote Manager
        $this['rrp.legacy.manager.quote'] = 'Manager_Insurance_Quote';

        // Constants
        $this['rrp.legacy.const.quote_policy_number'] = \Manager_Insurance_TenantsContentsPlus_Quote::POLICY_NUMBER;
        $this['rrp.legacy.const.customer_agent'] = \Model_Core_Customer::AGENT;
        $this['rrp.legacy.const.customer_address1'] = \Model_Core_Customer::ADDRESSLINE1;
        $this['rrp.legacy.const.customer_address2'] = \Model_Core_Customer::ADDRESSLINE2;
        $this['rrp.legacy.const.customer_address3'] = \Model_Core_Customer::ADDRESSLINE3;
        $this['rrp.legacy.const.customer_legacy_identifier'] = \Model_Core_Customer::LEGACY_IDENTIFIER;
        $this['rrp.legacy.const.status_quote'] = \Model_Insurance_RentRecoveryPlus_LegacyPolicy::STATUS_QUOTE;
        $this['rrp.legacy.const.status_policy'] = \Model_Insurance_RentRecoveryPlus_LegacyPolicy::STATUS_POLICY;
        $this['rrp.legacy.const.pay_status_referred'] = \Model_Insurance_RentRecoveryPlus_LegacyPolicy::PAY_STATUS_REFERRED;
        $this['rrp.legacy.const.pay_status_up-to-date'] = \Model_Insurance_RentRecoveryPlus_LegacyPolicy::PAY_STATUS_UP_TO_DATE;
        $this['rrp.legacy.const.pay_status_policy'] = \Model_Insurance_RentRecoveryPlus_LegacyPolicy::PAY_STATUS_CANCELLED;
        $this['rrp.legacy.const.policy_option_rrp'] = \Model_Insurance_RentRecoveryPlus_LegacyPolicy::POLICY_OPTION_RRP;
        $this['rrp.legacy.const.policy_option_rrp-nilexcess'] = \Model_Insurance_RentRecoveryPlus_LegacyPolicy::POLICY_OPTION_RRP_NIL_EXCESS;
        $this['rrp.legacy.const.insight_status_ias'] = \Model_Insurance_RentRecoveryPlus_RentRecoveryPlus::INSIGHT_STATUS_IAS;
        $this['rrp.legacy.const.insight_status_insight'] = \Model_Insurance_RentRecoveryPlus_RentRecoveryPlus::INSIGHT_STATUS_INSIGHT;
        $this['rrp.legacy.const.insight_status_exception'] = \Model_Insurance_RentRecoveryPlus_RentRecoveryPlus::INSIGHT_STATUS_EXCEPTION;
        $this['rrp.legacy.const.pay_by_monthly'] = \Model_Insurance_RentRecoveryPlus_LegacyQuote::PAYBY_MONTHLY;
        $this['rrp.legacy.const.pay_by_annually'] = \Model_Insurance_RentRecoveryPlus_LegacyQuote::PAYBY_ANNUALLY;
        $this['rrp.legacy.const.transaction_status_live'] = \Model_Core_Transaction::STATUS_LIVE;
        $this['rrp.legacy.const.transaction_status_cancelled'] = \Model_Core_Transaction::STATUS_CANCELLED;
        $this['rrp.legacy.const.mta_status_live'] = \Model_Insurance_MTA::STATUS_LIVE;

        // Config Params
        $zendRrpParams = $this->getZendParams()->connect->settings->rentRecoveryPlus;
        $this['rrp.config.risk_area']                    = $zendRrpParams->riskArea;
        $this['rrp.config.underwriting_question_set_id'] = $zendRrpParams->underwritingQuestionSetID;
        $this['rrp.config.ipt_percent']                  = $zendRrpParams->iptPercent;
        $this['rrp.config.system_csu_id']                = $zendRrpParams->systemCsuID;
        $this['rrp.config.renewal_invite_period']        = $zendRrpParams->renewalInvitePeriod;
        $this['rrp.config.cancellation_period']          = $zendRrpParams->cancellationPeriod;
    }

    /**
     * Boots services
     *
     * @return void
     */
    protected function bootServices()
    {
        // Quote
        $this['rrp.legacy.datasource.quote'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.datasource.quote.class']();
        };

        // Policy
        $this['rrp.legacy.datasource.policy'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.datasource.policy.class']();
        };

        // Insight Policies
        $this['rrp.legacy.datasource.insight_policies'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.datasource.insight_policies.class']();
        };

        // MTA
        $this['rrp.legacy.datasource.mta'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.datasource.mta.class']();
        };

        // Rent Recovery Plus Mta
        $this['rrp.legacy.datasource.rrp_mta'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.datasource.rrp_mta.class']();
        };

        // Rent Recovery Plus
        $this['rrp.legacy.datasource.rent_recovery_plus'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.datasource.rent_recovery_plus.class']();
        };

        // Landlord Interest
        $this['rrp.legacy.datasource.landlord_interest'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.datasource.landlord_interest.class']();
        };

        // RRP Tenant References
        $this['rrp.legacy.datasource.rrp_tenant_reference'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.datasource.rrp_tenant_reference.class']();
        };

        // Rent Recovery Plus Rates
        $this['rrp.legacy.datasource.rrp_rates'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.datasource.rrp_rates.class']();
        };

        // Policy Notes
        $this['rrp.legacy.datasource.policy_notes'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.datasource.policy_notes.class']();
        };

        // Policy Term
        $this['rrp.legacy.datasource.policy_term'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.datasource.policy_term.class']();
        };

        // Policy Cover
        $this['rrp.legacy.datasource.policy_cover'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.datasource.policy_cover.class']();
        };

        // Policy Options
        $this['rrp.legacy.datasource.policy_options'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.datasource.policy_options.class']();
        };

        // Underwriting Answers
        $this['rrp.legacy.datasource.underwriting_answers'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.datasource.underwriting_answers.class']();
        };

        // Underwriting Questions
        $this['rrp.legacy.datasource.underwriting_questions'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.datasource.underwriting_questions.class']();
        };

        // Agent
        $this['rrp.legacy.datasource.agent'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.datasource.agent.class']();
        };

        // Search
        $this['rrp.legacy.datasource.search'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.datasource.search.class']();
        };

        // Disbursement
        $this['rrp.legacy.datasource.disbursement'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.datasource.disbursement.class']();
        };

        // Transaction
        $this['rrp.legacy.datasource.transaction'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.datasource.transaction.class']();
        };

        // TransactionSupport
        $this['rrp.legacy.datasource.transaction_support'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.datasource.transaction_support.class']();
        };

        // Insurance Product
        $this['rrp.legacy.datasource.product'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.datasource.product.class']();
        };

        // Quote
        $this['rrp.legacy.quote'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.quote.class']();
        };

        // Policy
        $this['rrp.legacy.policy'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.policy.class']();
        };

        // Insight Policies
        $this['rrp.legacy.insight_policies'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.insight_policies.class']();
        };

        // Rent Recovery Plus
        $this['rrp.legacy.rent_recovery_plus'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.rent_recovery_plus.class']();
        };

        // Landlord Interest
        $this['rrp.legacy.landlord_interest'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.landlord_interest.class']();
        };

        // RRP Tenant Reference
        $this['rrp.legacy.rrp_tenant_reference'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.rrp_tenant_reference.class']();
        };

        // Rent Recovery Plus Mta
        $this['rrp.legacy.rrp_mta'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.rrp_mta.class']();
        };

        // Underwriting Answers
        $this['rrp.legacy.underwriting_answers'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.underwriting_answers.class']();
        };

        // Transaction
        $this['rrp.legacy.transaction'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.transaction.class']();
        };

        // TransactionSupport
        $this['rrp.legacy.transaction_support'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.transaction_support.class']();
        };

        // MTA
        $this['rrp.legacy.mta'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.mta.class']();
        };

        // PolicyNumber manager
        $this['rrp.legacy.manager.policy_number'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.manager.policy_number.class']();
        };

        // Customer manager
        $this['rrp.legacy.manager.customer'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.manager.customer.class']();
        };

        // Mailer
        $this['rrp.legacy.mailer'] = function (LegacyContainer $c) {
            return new $c['rrp.legacy.mailer.class']();
        };
    }

    /**
     * Get Zend parameters
     *
     * @return object
     */
    private function getZendParams()
    {
        return \Zend_Registry::get('params');
    }
}


