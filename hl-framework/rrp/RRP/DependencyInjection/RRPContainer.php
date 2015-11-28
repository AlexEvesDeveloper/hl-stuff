<?php

namespace RRP\DependencyInjection;

use Twig_Extension_Debug;

/**
 * Class RRPContainer
 *
 * @package RRP\DependencyInjection
 * @author April Portus <april.portus@barbon.com>
 */
class RRPContainer extends \Pimple implements ContainerInterface
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
            throw new \InvalidArgumentException(sprintf('Unable to find RRP service or parameter with the id %s', $id));
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
        // Search
        $this['rrp_search_cachePath'] = __DIR__ . '/../../../../private/cache';
        $this['rrp_search_cacheTagPrefix'] = 'search';
        $this['rrp_search_cacheLifetime'] = 300;
        $this['rrp.search.class'] = 'RRP\Search\RentRecoveryPlusSearch';

        // Application Form
        $this['rrp.form.application.class'] = 'RRP\Form\RentRecoveryPlusApplicationType';

        // Search Form
        $this['rrp.form.search.class'] = 'RRP\Form\RentRecoveryPlusSearchType';

        // Cancellation Form
        $this['rrp.form.cancellation.class'] = 'RRP\Form\RentRecoveryPlusCancellationType';

        // MTA Form
        $this['rrp.form.mta.class'] = 'RRP\Form\RentRecoveryPlusMtaType';

        // Search Criteria Model
        $this['rrp.model.search_criteria.class'] = 'RRP\Model\RentRecoveryPlusSearchCriteria';

        // RentRecoveryPlus Cancellation
        $this['rrp.model.cancellation.class'] = '\RRP\Model\RentRecoveryPlusCancellation';

        // RentRecoveryPlus Mta
        $this['rrp.model.mta.class'] = 'RRP\Model\RentRecoveryPlusMta';

        // RentRecoveryPlus Summary
        $this['rrp.model.summary.class'] = 'RRP\Model\RentRecoveryPlusSummary';

        // PolicyOptionsManager
        $this['rrp.utility.policy_options_manager.class'] = 'RRP\Utility\PolicyOptionsManager';

        // Referral required exception
        $this['rrp.rate.referralException.class'] = 'RRP\Rate\Exception\ReferralRequiredException';

        // Rate Decorator Factory
        $this['rrp.rate.decorator.class'] = 'RRP\Rate\RateDecoratorFactory';

        // Application Decorator Factory
        $this['rrp.application.decorator.class'] = 'RRP\Application\ApplicationDecoratorFactory';

        // Mta Decorator Factory
        $this['rrp.mta.decorator.class'] = 'RRP\Mta\MtaDecoratorFactory';

        // Underwriting Decorator Factory
        $this['rrp.underwriting.decorator.class'] = 'RRP\Underwriting\UnderwritingDecoratorFactory';

        // RentRecoveryPlus Referral
        $this['rrp.referral.class'] = 'RRP\Referral\RentRecoveryPlusReferral';

        // Application Session Store
        //$this['rrp.application.store.class'] = 'RRP\SessionStore\ApplicationSessionStore';

        $this['rrp.utility.pro_rata_calcs.class'] = 'RRP\Utility\ProRataCalculations';

        // Common classes
        $this['rrp.reference_types.class'] = 'RRP\Common\ReferenceTypes';
        $this['rrp.property_let_types.class'] = 'RRP\Common\PropertyLetTypes';

        //Paginator
        $this['rrp.paginator.class'] = 'AshleyDawson\SimplePagination\Paginator';

        // Composer
        $this['composer_vendor_dir'] = __DIR__ . '/../../../../vendor';

        // HTTP Foundation
        $this['request.class'] = 'Symfony\Component\HttpFoundation\Request';
        $this['http_foundation_extension.class'] = 'Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension';

        // Validation
        $this['validation.class'] = 'Symfony\Component\Validator\Validation';
        $this['validator_extension.class'] = 'Symfony\Component\Form\Extension\Validator\ValidatorExtension';

        // Forms
        $this['form_factory_builder.class'] = 'Symfony\Component\Form\Forms';

        // Twig
        $this['twig.debug'] = false;
        $this['twig.auto_reload'] = true;
        $this['twig.cache_dir'] = __DIR__ . '/../../../../private/cache/_twig';
        $this['twig.bridge_dir'] = $this['composer_vendor_dir'] . '/symfony/twig-bridge/Symfony/Bridge/Twig';
        $this['twig.bridge_form_layout_dir'] = $this['twig.bridge_dir'] . '/Resources/views/Form';

        $this['twig.connect_layout_dir'] = __DIR__ . '/../../../modules/connect/layouts/form';
        $this['twig.connect_mail_layout_dir'] = __DIR__ . '/../../../modules/connect/layouts/mail';


        $this['twig.loader.class'] = 'Twig_Loader_Filesystem';
        $this['twig.class'] = 'Twig_Environment';
        $this['twig.form_layouts'] = array(
            'form_div_layout.html.twig',
            'rrpi-widgets.html.twig',
        );

        // IRIS SDK
        $this['iris_sdk_base_url'] = $this->getZendParams()->iris->api->base_url;
        $this['iris_sdk_version'] = $this->getZendParams()->iris->api->version;
        $this['iris_sdk_system_key'] = $this->getZendParams()->iris->api->system_key;
        $this['iris_sdk_system_secret'] = $this->getZendParams()->iris->api->system_secret;
        $this['iris_sdk_client_registry.class'] = 'Barbondev\IRISSDK\Common\ClientRegistry\ClientRegistry';
        $this['iris_sdk_client_registry.system_context.class'] = 'Barbondev\IRISSDK\Common\ClientRegistry\Context\SystemContext';
        $this['iris_sdk_client_registry.agent_context.class'] = 'Barbondev\IRISSDK\Common\ClientRegistry\Context\AgentContext';
        $this['iris_sdk_client_registry.landlord_context.class'] = 'Barbondev\IRISSDK\Common\ClientRegistry\Context\LandlordContext';

        // Authentication & Authorization
        $this['iris.authentication.agent_authenticator.class'] = 'Iris\Authentication\AgentAuthenticator';
        $this['iris.authentication.landlord_authenticator.class'] = 'Iris\Authentication\LandlordAuthenticator';
        $this['iris.authentication.authentication.class'] = 'Iris\Authentication\Authentication';
        $this['iris.authentication.agent_authorization_token.class'] = 'Iris\Authentication\AgentAuthorizationToken';
        $this['iris.authentication.landlord_authorization_token.class'] = 'Iris\Authentication\LandlordAuthorizationToken';

        $this['twig.renderer_engine.class'] = 'Symfony\Bridge\Twig\Form\TwigRendererEngine';
        $this['twig.renderer.class'] = 'Symfony\Bridge\Twig\Form\TwigRenderer';
        $this['twig.form_extension.class'] = 'Symfony\Bridge\Twig\Extension\FormExtension';

        // Translation
        $this['translator.locale'] = 'en';
        $this['translator.class'] = 'Symfony\Component\Translation\Translator';
        $this['translation_extension.class'] = 'Symfony\Bridge\Twig\Extension\TranslationExtension';

        // Form Validation Error Binder
        $this['rrp.utility.validation.form_validation_error_binder.class'] = 'RRP\Utility\Validation\FormValidationErrorBinder';

        $zendConnectSettings = $this->getZendParams()->connect->settings;

        // SFTP Client
        $this['rrp.sftp_client.class']    = 'Net_SFTP';
        $this['rrp.sftp_client.path']     = $zendConnectSettings->logoUpload->document->sftpPath;
        $this['rrp.sftp_client.port']     = $zendConnectSettings->logoUpload->document->sftpPort;
        $this['rrp.sftp_client.timeout']  = $zendConnectSettings->logoUpload->document->sftpTimeoutSec;
        $this['rrp.sftp_client.username'] = $zendConnectSettings->logoUpload->document->sftpUsername;
        $this['rrp.sftp_client.password'] = $zendConnectSettings->logoUpload->document->sftpPassword;

        // SFTP path config
        $this['rrp.config.sftp_logo_path'] = $zendConnectSettings->logoUpload->path->public;

        // Survey config
        $this['rrp.config.survey.username']   = $zendConnectSettings->survey->username;
        $this['rrp.config.survey.password']   = $zendConnectSettings->survey->password;
        $this['rrp.config.survey.userAgent']  = $zendConnectSettings->survey->userAgent;
        $this['rrp.config.survey.cookieFile'] = $zendConnectSettings->survey->cookieFile;
        $this['rrp.config.survey.loginUrl']   = $zendConnectSettings->survey->loginUrl;
        $this['rrp.config.survey.loginQuery'] = $zendConnectSettings->survey->loginQuery;
        $this['rrp.config.survey.reportUrl']  = $zendConnectSettings->survey->reportUrl;
        $this['rrp.config.survey.csvFileUrl'] = $zendConnectSettings->survey->csvFileUrl;
        $this['rrp.config.survey.sleepTime']  = $zendConnectSettings->survey->sleepSecs;
        $this['rrp.config.survey.csvFile']    = $zendConnectSettings->survey->tmpFile;
        $this['rrp.config.survey.dateFormat'] = $zendConnectSettings->survey->dateFormat;

        $this['rrp.config.survey.inception.survey_id']            = $zendConnectSettings->survey->inception->surveyID;
        $this['rrp.config.survey.inception.report_data']          = $zendConnectSettings->survey->inception->reportData;
        $this['rrp.config.survey.inception.report_match']         = $zendConnectSettings->survey->inception->reportMatch;
        $this['rrp.config.survey.inception.csv_file_data']        = $zendConnectSettings->survey->inception->csvFileData;
        $this['rrp.config.survey.inception.csv_file_match']       = $zendConnectSettings->survey->inception->csvFileMatch;
        $this['rrp.config.survey.inception.delimiter']            = $zendConnectSettings->survey->inception->csvDelimiter;
        $this['rrp.config.survey.inception.policy_column_name']   = $zendConnectSettings->survey->inception->csvPolicyColumn;
        $this['rrp.config.survey.inception.complete_column_name'] = $zendConnectSettings->survey->inception->csvCompleteColumn;
        $this['rrp.config.survey.inception.email_duration1']      = $zendConnectSettings->survey->inception->emailReminder->durationDays1;
        $this['rrp.config.survey.inception.email_duration2']      = $zendConnectSettings->survey->inception->emailReminder->durationDays2;
        $this['rrp.config.survey.inception.email_from']           = $zendConnectSettings->survey->inception->emailReminder->from;
        $this['rrp.config.survey.inception.email_subject']        = $zendConnectSettings->survey->inception->emailReminder->subject;
        $this['rrp.config.survey.inception.email_surveyLink']     = $zendConnectSettings->survey->inception->emailReminder->surveyLink;

        $this['rrp.config.survey.renewal.survey_id']            = $zendConnectSettings->survey->renewal->surveyID;
        $this['rrp.config.survey.renewal.report_data']          = $zendConnectSettings->survey->renewal->reportData;
        $this['rrp.config.survey.renewal.report_match']         = $zendConnectSettings->survey->renewal->reportMatch;
        $this['rrp.config.survey.renewal.csv_file_data']        = $zendConnectSettings->survey->renewal->csvFileData;
        $this['rrp.config.survey.renewal.csv_file_match']       = $zendConnectSettings->survey->renewal->csvFileMatch;
        $this['rrp.config.survey.renewal.delimiter']            = $zendConnectSettings->survey->renewal->csvDelimiter;
        $this['rrp.config.survey.renewal.policy_column_name']   = $zendConnectSettings->survey->renewal->csvPolicyColumn;
        $this['rrp.config.survey.renewal.complete_column_name'] = $zendConnectSettings->survey->renewal->csvCompleteColumn;
        $this['rrp.config.survey.renewal.email_duration1']      = $zendConnectSettings->survey->renewal->emailReminder->durationDays1;
        $this['rrp.config.survey.renewal.email_duration2']      = $zendConnectSettings->survey->renewal->emailReminder->durationDays2;
        $this['rrp.config.survey.renewal.email_from']           = $zendConnectSettings->survey->renewal->emailReminder->from;
        $this['rrp.config.survey.renewal.email_subject']        = $zendConnectSettings->survey->renewal->emailReminder->subject;
        $this['rrp.config.survey.renewal.email_surveyLink']     = $zendConnectSettings->survey->renewal->emailReminder->surveyLink;

        // Referral Config
        $this['rrp.config.referral.email_subject']      = $zendConnectSettings->rentRecoveryPlus->referral->emailSubject;
        $this['rrp.config.referral.email_to_address']   = $zendConnectSettings->rentRecoveryPlus->referral->emailToAddress;
        $this['rrp.config.referral.email_to_name']      = $zendConnectSettings->rentRecoveryPlus->referral->emailToName;
        $this['rrp.config.referral.email_from_address'] = $zendConnectSettings->rentRecoveryPlus->referral->emailFromAddress;
        $this['rrp.config.referral.email_from_name']    = $zendConnectSettings->rentRecoveryPlus->referral->emailFromName;

        // Migration Report Config
        $this['rrp.config.migration_report.email_subject']      = $zendConnectSettings->rentRecoveryPlus->migrationReport->emailSubject;
        $this['rrp.config.migration_report.email_to_address']   = $zendConnectSettings->rentRecoveryPlus->migrationReport->emailToAddress;
        $this['rrp.config.migration_report.email_to_name']      = $zendConnectSettings->rentRecoveryPlus->migrationReport->emailToName;
        $this['rrp.config.migration_report.email_from_address'] = $zendConnectSettings->rentRecoveryPlus->migrationReport->emailFromAddress;
        $this['rrp.config.migration_report.email_from_name']    = $zendConnectSettings->rentRecoveryPlus->migrationReport->emailFromName;

        $this['rrp.config.risk_area']                    = $zendConnectSettings->rentRecoveryPlus->riskArea;
        $this['rrp.config.underwriting_question_set_id'] = $zendConnectSettings->rentRecoveryPlus->underwritingQuestionSetID;
        $this['rrp.config.system_csu_id']                = $zendConnectSettings->rentRecoveryPlus->systemCsuID;

        // Zend Auth
        $this['zend_auth.class'] = 'Zend_Auth';
        $this['zend_auth_storage_session.class'] = 'Zend_Auth_Storage_Session';

        // Zend Log
        $this['zend_log_writer.class'] = 'Zend_Log_Writer_Stream';
        $this['zend_log.class'] = 'Zend_Log';

        // Session
        $this['symfony_session.class'] = 'Symfony\Component\HttpFoundation\Session\Session';

        // IRIS
        $this['iris_search_cachePath'] = __DIR__ . '/../../../../private/cache';
        $this['iris_search_cacheTagPrefix'] = 'applicationSearch';
        $this['iris_search_cacheLifetime'] = 300;
        $this['iris.reference_search.class'] = 'Iris\IndividualApplication\Search\IndividualApplicationSearch';
        $this['iris.search_individual_application_criteria.class'] = 'Iris\IndividualApplication\Model\SearchIndividualApplicationsCriteria';

        // GuarantorCreator
        $this['rrp.utility.rrp_guarantor_reference_creator.class'] = 'RRP\Utility\RrpGuarantorReferenceCreator';

        // DecisionDetailsRetriever
        $this['rrp.utility.decision_details_retriever.class'] = 'RRP\Utility\DecisionDetailsRetriever';

        // Subscribers
        $this['rrp.form.subscriber.verify_reference_subscriber.class'] = 'RRP\Form\Subscriber\VerifyReferenceSubscriber';

        // Constraints
        $this['rrp.constraint.reference_belongs_to_agent_constraint.class'] = 'RRP\Constraint\ReferenceBelongsToAgentConstraint';
        $this['rrp.constraint.reference_status_constraint.class'] = 'RRP\Constraint\ReferenceStatusConstraint';
        $this['rrp.constraint.reference_expiry_constraint.class'] = 'RRP\Constraint\ReferenceExpiryConstraint';

        // Transformers
        $this['rrp.form.data_transformer.reference_number_transformer.class'] = 'RRP\Form\DataTransformer\ReferenceNumberToReferenceObjectTransformer';
        $this['rrp.form.data_transformer.reference_type_transformer.class'] = 'RRP\Form\DataTransformer\ReferenceToProductTypeTransformer';

        // Utility
        $this['rrp.utility.session_reference_holder.class'] = 'RRP\Utility\SessionReferenceHolder';

        // Zend Session
        $this['zend_session_namespace.class'] = 'Zend_Session_Namespace';

        // RentRecoveryPlusReference
        $this['rrp.model.rent_recovery_plus_reference.class'] = 'RRP\Model\RentRecoveryPlusReference';
    }

    /**
     * Boots services
     *
     * @return void
     */
    protected function bootServices()
    {
        // Search
        $this['rrp.search'] = function (RRPContainer $c) {
            return new $c['rrp.search.class'](
                $c['rrp_search_cachePath'],
                $c['rrp_search_cacheTagPrefix'],
                $c['rrp_search_cacheLifetime']
            );
        };

        // Application search
        $this['iris.reference_search'] = function (RRPContainer $c) {
            return new $c['iris.reference_search.class'](
                $c['iris_search_cachePath'],
                $c['iris_search_cacheTagPrefix'],
                $c['iris_search_cacheLifetime']
            );
        };

        $this['iris.search_individual_application_criteria'] = function (RRPContainer $c) {
            return new $c['iris.search_individual_application_criteria.class']();
        };

        // Application Type
        $this['rrp.form.application'] = function (RRPContainer $c) {
            return new $c['rrp.form.application.class'](
                $c['rrp.form.subscriber.verify_reference_subscriber'],
                $c['rrp.form.data_transformer.reference_number_transformer'],
                $c['rrp.form.data_transformer.reference_type_transformer']
            );
        };

        // Search Type
        $this['rrp.form.search'] = function (RRPContainer $c) {
            return new $c['rrp.form.search.class']();
        };

        // Cancellation Type
        $this['rrp.form.cancellation'] = function (RRPContainer $c) {
            return new $c['rrp.form.cancellation.class']();
        };

        // Mta
        $this['rrp.form.mta'] = function (RRPContainer $c) {
            return new $c['rrp.form.mta.class']();
        };

        // Cancellation
        $this['rrp.model.cancellation'] = function (RRPContainer $c) {
            return new $c['rrp.model.cancellation.class']();
        };

        // RentRecoveryPlus Mta
        $this['rrp.model.mta'] = function (RRPContainer $c) {
            return new $c['rrp.model.mta.class']();
        };

        // RentRecoveryPlus Summary
        $this['rrp.model.summary'] = function (RRPContainer $c) {
            return new $c['rrp.model.summary.class']();
        };

        // Search Criteria
        $this['rrp.model.search_criteria'] = function (RRPContainer $c) {
            return new $c['rrp.model.search_criteria.class']();
        };

        // Paginator
        $this['rrp.paginator'] = function (RRPContainer $c) {
            return new $c['rrp.paginator.class']();
        };

        // RentRecoveryPlus Referral
        $this['rrp.referral'] = function (RRPContainer $c) {
            return new $c['rrp.referral.class']();
        };

        // Application Session Store
        //$this['rrp.application.store'] = function (RRPContainer $c) {
        //    return new $c['rrp.application.store.class']();
        //};

        // HTTP Foundation
        $this['request'] = function (RRPContainer $c) {
            return call_user_func(array($c['request.class'], 'createFromGlobals'));
        };

        $this['http_foundation_extension'] = function (RRPContainer $c) {
            return new $c['http_foundation_extension.class']();
        };

        // Validation
        $this['validation'] = function (RRPContainer $c) {
            return call_user_func(array($c['validation.class'], 'createValidator'));
        };

        $this['validator_extension'] = function (RRPContainer $c) {
            return new $c['validator_extension.class']($c['validation']);
        };

        // Forms
        $this['form_factory_builder'] = function (RRPContainer $c) {
            return call_user_func(array($c['form_factory_builder.class'], 'createFormFactoryBuilder'));
        };

        $this['form_factory'] = function (RRPContainer $c) {
            return $c['form_factory_builder']
                ->addExtension($c['http_foundation_extension'])
                ->addExtension($c['validator_extension'])
                ->getFormFactory();
        };

        // Twig
        $this['twig.loader'] = function (RRPContainer $c) {
            return new $c['twig.loader.class'](array(
                $c['twig.bridge_form_layout_dir'],
                $c['twig.connect_layout_dir'],
                $c['twig.connect_mail_layout_dir']
            ));
        };

        $this['twig'] = function (RRPContainer $c) {
            return new $c['twig.class']($c['twig.loader'], array(
                'auto_reload' => $c['twig.auto_reload'],
                'cache' => $c['twig.cache_dir'],
                'debug' => $c['twig.debug'],
            ));
        };

        $this['twig.renderer_engine'] = function (RRPContainer $c) {
            return new $c['twig.renderer_engine.class']($c['twig.form_layouts']);
        };

        $this['twig.renderer'] = function (RRPContainer $c) {
            return new $c['twig.renderer.class']($c['twig.renderer_engine']);
        };

        $this['twig.form_extension'] = function (RRPContainer $c) {
            return new $c['twig.form_extension.class']($c['twig.renderer']);
        };

        // Translation
        $this['translator'] = function (RRPContainer $c) {
            return new $c['translator.class']($c['translator.locale']);
        };

        $this['translation_extension'] = function (RRPContainer $c) {
            return new $c['translation_extension.class']($c['translator']);
        };

        // Zend Auth
        $this['zend_auth'] = function (RRPContainer $c) {
            return call_user_func(array($c['zend_auth.class'], 'getInstance'));
        };

        $this['zend_auth_storage_session_rrp'] = function (RRPContainer $c) {
            return new $c['zend_auth_storage_session.class']('iris');
        };

        $this->extend('twig', function ($twig, RRPContainer $c) {

            // Add extensions
            $twig->addExtension($c['twig.form_extension']);
            $twig->addExtension($c['translation_extension']);
            if ($c['twig.debug']) {
                $twig->addExtension(new Twig_Extension_Debug());
            }

            // Set renderer engine environment
            $c['twig.renderer_engine']->setEnvironment($twig);

            // Set globals
            $twig->addGlobal('_isXmlHttpRequest', $c['request']->isXmlHttpRequest());
            $twig->addGlobal('_request', $c['request']);

            return $twig;
        });

        // Form Validation Error Binder
        $this['rrp.utility.validation.form_validation_error_binder'] = function (RRPContainer $c) {
            return new $c['rrp.utility.validation.form_validation_error_binder.class']();
        };

        // SFTP Client with login
        $this['rrp.sftp_client'] = function (RRPContainer $c) {
            $client = new $c['rrp.sftp_client.class'](
                $c['rrp.sftp_client.path'],
                $c['rrp.sftp_client.port'],
                $c['rrp.sftp_client.timeout']
            );
            if ($client->login(
                $c['rrp.sftp_client.username'],
                $c['rrp.sftp_client.password']
            )) {
                return $client;
            }
            return null;
        };


        // Zend log
        $this['zend_log_writer'] = function (RRPContainer $c) {
            $writer = new $c['zend_log_writer.class']('php://stderr');
            $writer->addFilter(\Zend_Log::WARN);
            return $writer;
        };

        $this['zend_log'] = function (RRPContainer $c) {
            return new $c['zend_log.class']($c['zend_log_writer']);
        };

        // Symfony session
        $this['symfony_session'] = function (RRPContainer $c) {
            return new $c['symfony_session.class']();
        };

        // Authentication & Authorization
        $this['iris.authentication.agent_authenticator'] = function (RRPContainer $c) {
            return new $c['iris.authentication.agent_authenticator.class']();
        };

        $this->extend('iris.authentication.agent_authenticator', function ($authenticator, RRPContainer $c) {
            $authenticator->setIrisClientRegistry($c['iris_sdk_client_registry']);
            return $authenticator;
        });

        $this['iris.authentication.landlord_authenticator'] = function (RRPContainer $c) {
            return new $c['iris.authentication.landlord_authenticator.class']();
        };

        $this->extend('iris.authentication.landlord_authenticator', function ($authenticator, RRPContainer $c) {
            $authenticator->setIrisClientRegistry($c['iris_sdk_client_registry']);
            return $authenticator;
        });

        $this['iris.authentication.agent_authorization_token'] = function (RRPContainer $c) {
            return new $c['iris.authentication.agent_authorization_token.class']();
        };

        $this['iris.authentication.landlord_authorization_token'] = function (RRPContainer $c) {
            return new $c['iris.authentication.landlord_authorization_token.class']();
        };

        $this['iris.authentication'] = function (RRPContainer $c) {
            return new $c['iris.authentication.authentication.class'](
                $c['iris.authentication.agent_authenticator'],
                $c['iris.authentication.landlord_authenticator'],
                $c['iris_sdk_base_url'],
                $c['iris_sdk_version'],
                $c['zend_log']
            );
        };

        // IRIS SDK
        $this['iris_sdk_client_registry'] = function (RRPContainer $c) {
            return new $c['iris_sdk_client_registry.class']();
        };

        $this['iris_sdk_client_registry.system_context'] = function (RRPContainer $c) {
            return new $c['iris_sdk_client_registry.system_context.class'](array(
                'base_url' => $c['iris_sdk_base_url'],
                'version' => $c['iris_sdk_version'],
                'consumer_key' => $c['iris_sdk_system_key'],
                'consumer_secret' => $c['iris_sdk_system_secret'],
            ));
        };

        $this['iris_sdk_client_registry.agent_context'] = function (RRPContainer $c) {
            return new $c['iris_sdk_client_registry.agent_context.class'](
                $c['iris.authentication.agent_authorization_token']->getContextParameters()
            );
        };

        $this['iris_sdk_client_registry.landlord_context'] = function (RRPContainer $c) {
            return new $c['iris_sdk_client_registry.landlord_context.class'](
                $c['iris.authentication.landlord_authorization_token']->getContextParameters()
            );
        };

        $this->extend('iris_sdk_client_registry', function ($registry, RRPContainer $c) {

            // Add parameterised contexts to registry
            $registry
                ->addContext($c['iris_sdk_client_registry.system_context'])
                ->addContext($c['iris_sdk_client_registry.agent_context'])
                ->addContext($c['iris_sdk_client_registry.landlord_context'])
            ;

            return $registry;
        });
        
        // DecisionDetailsRetriever
        $this['rrp.utility.decision_details_retriever'] = function (RRPContainer $c) {
            return new $c['rrp.utility.decision_details_retriever.class'](
                $c['iris_sdk_client_registry']
            );
        };

        // GuarantorCreator
        $this['rrp.utility.rrp_guarantor_reference_creator'] = function (RRPContainer $c) {
            return new $c['rrp.utility.rrp_guarantor_reference_creator.class'](
                $c['iris_sdk_client_registry'],
                $c['rrp.utility.decision_details_retriever']
            );
        };

        // Subscribers
        $this['rrp.form.subscriber.verify_reference_subscriber'] = function (RRPContainer $c) {
            $service = new $c['rrp.form.subscriber.verify_reference_subscriber.class'] ();
            $service->addPreSubmitConstraint($c['rrp.constraint.reference_belongs_to_agent_constraint']);
            $service->addPreSubmitConstraint($c['rrp.constraint.reference_status_constraint']);
            $service->addPreSubmitConstraint($c['rrp.constraint.reference_expiry_constraint']);

            return $service;
        };

        // Constraints
        $this['rrp.constraint.reference_belongs_to_agent_constraint'] = function (RRPContainer $c) {
            return new $c['rrp.constraint.reference_belongs_to_agent_constraint.class'] (
                $c['iris_sdk_client_registry'],
                $c['iris.reference_search'],
                $c['iris.search_individual_application_criteria'],
                $c['rrp.utility.session_reference_holder']
            );
        };

        $this['rrp.constraint.reference_status_constraint'] = function (RRPContainer $c) {
            return new $c['rrp.constraint.reference_status_constraint.class'] (
                $c['rrp.utility.session_reference_holder']
            );
        };

        $this['rrp.constraint.reference_expiry_constraint'] = function (RRPContainer $c) {
            return new $c['rrp.constraint.reference_expiry_constraint.class'] (
                $c['rrp.utility.session_reference_holder']
            );
        };

        // Transformers
        $this['rrp.form.data_transformer.reference_number_transformer'] = function (RRPContainer $c) {
            return new $c['rrp.form.data_transformer.reference_number_transformer.class'] (
                $c['rrp.utility.decision_details_retriever'],
                $c['rrp.utility.session_reference_holder'],
                $c['rrp.model.rent_recovery_plus_reference']
            );
        };

        $this['rrp.form.data_transformer.reference_type_transformer'] = function (RRPContainer $c) {
            return new $c['rrp.form.data_transformer.reference_type_transformer.class'] (
                $c['rrp.utility.session_reference_holder']
            );
        };

        // Utility
        $this['rrp.utility.session_reference_holder'] = function (RRPContainer $c) {
            return new $c['rrp.utility.session_reference_holder.class'] (
                $c['rrp_policy_application.zend_session_namespace']
            );
        };

        $this['rrp_policy_application.zend_session_namespace'] = function (RRPContainer $c) {
            return new $c['zend_session_namespace.class'] ('rrp_policy_application');
        };

        // RentRecoveryPlusReference
        $this['rrp.model.rent_recovery_plus_reference'] = function (RRPContainer $c) {
            return new $c['rrp.model.rent_recovery_plus_reference.class'] ();
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