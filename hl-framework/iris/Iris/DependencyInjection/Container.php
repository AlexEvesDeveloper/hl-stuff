<?php

namespace Iris\DependencyInjection;
use Twig_Extension_Debug;

/**
 * Class Container
 *
 * @package Iris\DependencyInjection
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class Container extends \Pimple implements ContainerInterface
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
        if (!isset($this[$id])) {
            throw new \InvalidArgumentException(sprintf('Unable to find service or parameter with the id %s', $id));
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
        // Application search
        $this['iris_search_cachePath'] = __DIR__ . '/../../../../private/cache';
        $this['iris_search_cacheTagPrefix'] = 'applicationSearch';
        $this['iris_search_cacheLifetime'] = 300;
        $this['iris.reference_search.class'] = 'Iris\IndividualApplication\Search\IndividualApplicationSearch';

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
        $this['twig.loader.class'] = 'Twig_Loader_Filesystem';
        $this['twig.class'] = 'Twig_Environment';
        $this['twig.form_layouts'] = array(
            'form_div_layout.html.twig',
            'widgets.html.twig',
        );

        $this['twig.renderer_engine.class'] = 'Symfony\Bridge\Twig\Form\TwigRendererEngine';
        $this['twig.renderer.class'] = 'Symfony\Bridge\Twig\Form\TwigRenderer';
        $this['twig.form_extension.class'] = 'Symfony\Bridge\Twig\Extension\FormExtension';

        // Translation
        $this['translator.locale'] = 'en';
        $this['translator.class'] = 'Symfony\Component\Translation\Translator';
        $this['translation_extension.class'] = 'Symfony\Bridge\Twig\Extension\TranslationExtension';

        // Twig Extensions
        $this['iris.twig.extension.lookup_extension.class'] = 'Iris\Twig\Extension\LookupExtension';
        $this['iris.twig.extension.duration_extension.class'] = 'Iris\Twig\Extension\DurationExtension';
        $this['iris.twig.extension.cmsPanel_extension.class'] = 'Iris\Twig\Extension\CmsPanelExtension';

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

        // Form Validation Error Binder
        $this['iris.utility.validation.form_validation_error_binder.class'] = 'Iris\Utility\Validation\FormValidationErrorBinder';

        // Zend Auth
        $this['zend_auth.class'] = 'Zend_Auth';
        $this['zend_auth_storage_session.class'] = 'Zend_Auth_Storage_Session';

        // Address Finder
        $this['iris.address_finder.class'] = 'Iris\Utility\AddressFinder\AddressFinder';

        // Zend Log
        $this['zend_log_writer.class'] = 'Zend_Log_Writer_Stream';
        $this['zend_log.class'] = 'Zend_Log';

        // Session
        $this['symfony_session.class'] = 'Symfony\Component\HttpFoundation\Session\Session';

        // Iris progressive store
        $this['iris.referencing.form_set.progressive_store.agent_progressive_store.class'] = 'Iris\Referencing\FormSet\ProgressiveStore\AgentProgressiveStore';
        $this['iris.referencing.form_set.progressive_store.agent_guarantor_progressive_store.class'] = 'Iris\Referencing\FormSet\ProgressiveStore\AgentGuarantorProgressiveStore';
        $this['iris.referencing.form_set.progressive_store.landlord_progressive_store.class'] = 'Iris\Referencing\FormSet\ProgressiveStore\LandlordProgressiveStore';
        $this['iris.referencing.form_set.progressive_store.system_progressive_store.class'] = 'Iris\Referencing\FormSet\ProgressiveStore\SystemProgressiveStore';

        // Iris form flows
        $this['iris.referencing.form_flow.connect_new_reference_form_flow.class'] = 'Iris\Referencing\FormFlow\ConnectNewReferenceFormFlow';
        $this['iris.referencing.form_flow.connect_add_tenant_form_flow.class'] = 'Iris\Referencing\FormFlow\ConnectAddTenantReferenceFormFlow';
        $this['iris.referencing.form_flow.connect_add_guarantor_form_flow.class'] = 'Iris\Referencing\FormFlow\ConnectAddGuarantorReferenceFormFlow';
        $this['iris.referencing.form_flow.applicant_continue_reference_form_flow.class'] = 'Iris\Referencing\FormFlow\ContinueReferenceFormFlow';
        $this['iris.referencing.form_flow.applicant_complete_reference_form_flow.class'] = 'Iris\Referencing\FormFlow\CompleteReferenceFormFlow';


        // Iris report finders
        $this['iris.referencing.report.report_finder.class'] = 'Iris\Referencing\Report\ReportFinder';

        // Iris report filename builder
        $this['iris.referencing.report.report_filename_builder.class'] = 'Iris\Referencing\Report\ReportFilenameBuilder';

        // Iris application counter
        $this['iris.referencing.application.application_counter.class'] = 'Iris\Referencing\Application\ApplicationCounter';

        // Iris submitters
        $this['iris.referencing.application.submission.case_submitter.class'] = 'Iris\Referencing\Submission\CaseSubmitter';
        $this['iris.referencing.application.submission.application_submitter.class'] = 'Iris\Referencing\Submission\ApplicationSubmitter';

        // Iris Lookup Cache
        $this['iris.lookup_cache.dir'] = __DIR__ . '/../../../../private/cache/_lookup';
        $this['iris.lookup_cache.ttl'] = 120; // 2 Minute TTL
        $this['iris.lookup_cache.adapter.class'] = 'Desarrolla2\Cache\Adapter\File';
        $this['iris.lookup_cache.class'] = 'Desarrolla2\Cache\Cache';

        // Iris Product Cache
        $this['iris.product_cache.dir'] = __DIR__ . '/../../../../private/cache/_product';
        $this['iris.product_cache.ttl'] = 600; // 10 Minute TTL
        $this['iris.product_cache.adapter.class'] = 'Desarrolla2\Cache\Adapter\File';
        $this['iris.product_cache.class'] = 'Desarrolla2\Cache\Cache';

        // Iris Notifications Cache
        $this['iris.notifications_cache.dir'] = __DIR__ . '/../../../../private/cache/_notifications';
        $this['iris.notifications_cache.ttl'] = 300; // 5 Minute TTL
        $this['iris.notifications_cache.adapter.class'] = 'Desarrolla2\Cache\Adapter\File';
        $this['iris.notifications_cache.class'] = 'Desarrolla2\Cache\Cache';

        // Iris product services
        $this['iris.product.class'] = 'Iris\Utility\Product\Product';
        $this['iris.product_price.class'] = 'Iris\Utility\Product\ProductPrice';

        // Current form flow records
        $this['iris.referencing.application.current_form_flow_records.class'] = 'Iris\Referencing\Application\CurrentFormFlowRecords';

        // Slugify
        $this['slugifier.class'] = 'AshleyDawson\Slugify\Slugifier';

        // Failure reporting
        $this['iris.referencing.submission.submission_failure_message_resolver.template_dir'] = __DIR__ . '/../../../modules/connect/views/scripts/iris-referencing/submission-failure-messages/';
        $this['iris.referencing.submission.submission_failure_message_resolver.class'] = 'Iris\Referencing\Submission\SubmissionFailureMessageResolver';

        // Additional information note handling
        $this['iris.individual_application.additional_information_note.note_handler.class'] = 'Iris\IndividualApplication\AdditionalInformationNote\NoteHandler';
    }

    /**
     * Boots services
     *
     * @return void
     */
    protected function bootServices()
    {
        // Application search
        $this['iris.reference_search'] = function (Container $c) {
            return new $c['iris.reference_search.class'](
                $c['iris_search_cachePath'],
                $c['iris_search_cacheTagPrefix'],
                $c['iris_search_cacheLifetime']
            );
        };

        // HTTP Foundation
        $this['request'] = function (Container $c) {
            return call_user_func(array($c['request.class'], 'createFromGlobals'));
        };

        $this['http_foundation_extension'] = function (Container $c) {
            return new $c['http_foundation_extension.class']();
        };

        // Validation
        $this['validation'] = function (Container $c) {
            return call_user_func(array($c['validation.class'], 'createValidator'));
        };

        $this['validator_extension'] = function (Container $c) {
            return new $c['validator_extension.class']($c['validation']);
        };

        // Forms
        $this['form_factory_builder'] = function (Container $c) {
            return call_user_func(array($c['form_factory_builder.class'], 'createFormFactoryBuilder'));
        };

        $this['form_factory'] = function (Container $c) {
            return $c['form_factory_builder']
                ->addExtension($c['http_foundation_extension'])
                ->addExtension($c['validator_extension'])
                ->getFormFactory()
            ;
        };

        // Twig
        $this['twig.loader'] = function (Container $c) {
            return new $c['twig.loader.class'](array(
                $c['twig.bridge_form_layout_dir'],
                $c['twig.connect_layout_dir'],

                // todo: Not sure why there are duplicate widget views? I'm disabling for now
//                $c['twig.referee_layout_dir'],
//                $c['twig.tenant-application-tracker_layout_dir'],
            ));
        };

        $this['twig'] = function (Container $c) {
            return new $c['twig.class']($c['twig.loader'], array(
                'auto_reload' => $c['twig.auto_reload'],
                'cache' => $c['twig.cache_dir'],
                'debug' => $c['twig.debug'],
            ));
        };

        $this['twig.renderer_engine'] = function (Container $c) {
            return new $c['twig.renderer_engine.class']($c['twig.form_layouts']);
        };

        $this['twig.renderer'] = function (Container $c) {
            return new $c['twig.renderer.class']($c['twig.renderer_engine']);
        };

        $this['twig.form_extension'] = function (Container $c) {
            return new $c['twig.form_extension.class']($c['twig.renderer']);
        };

        // Translation
        $this['translator'] = function (Container $c) {
            return new $c['translator.class']($c['translator.locale']);
        };

        $this['translation_extension'] = function (Container $c) {
            return new $c['translation_extension.class']($c['translator']);
        };

        // Twig Extensions
        $this['iris.twig.extension.lookup_extension'] = function (Container $c) {
            return new $c['iris.twig.extension.lookup_extension.class']();
        };

        $this['iris.twig.extension.duration_extension'] = function (Container $c) {
            return new $c['iris.twig.extension.duration_extension.class']();
        };

        $this['iris.twig.extension.cmsPanel_extension'] = function (Container $c) {
            return new $c['iris.twig.extension.cmsPanel_extension.class']();
        };

        // Zend Auth
        $this['zend_auth'] = function (Container $c) {
            return call_user_func(array($c['zend_auth.class'], 'getInstance'));
        };

        $this['zend_auth_storage_session_iris'] = function (Container $c) {
            return new $c['zend_auth_storage_session.class']('iris');
        };

        // Authentication & Authorization
        $this['iris.authentication.agent_authenticator'] = function (Container $c) {
            return new $c['iris.authentication.agent_authenticator.class']();
        };

        $this->extend('iris.authentication.agent_authenticator', function ($authenticator, Container $c) {
            $authenticator->setIrisClientRegistry($c['iris_sdk_client_registry']);
            return $authenticator;
        });

        $this['iris.authentication.landlord_authenticator'] = function (Container $c) {
            return new $c['iris.authentication.landlord_authenticator.class']();
        };

        $this->extend('iris.authentication.landlord_authenticator', function ($authenticator, Container $c) {
            $authenticator->setIrisClientRegistry($c['iris_sdk_client_registry']);
            return $authenticator;
        });

        $this['iris.authentication.agent_authorization_token'] = function (Container $c) {
            return new $c['iris.authentication.agent_authorization_token.class']();
        };

        $this['iris.authentication.landlord_authorization_token'] = function (Container $c) {
            return new $c['iris.authentication.landlord_authorization_token.class']();
        };

        $this['iris.authentication'] = function (Container $c) {
            return new $c['iris.authentication.authentication.class'](
                $c['iris.authentication.agent_authenticator'],
                $c['iris.authentication.landlord_authenticator'],
                $c['iris_sdk_base_url'],
                $c['iris_sdk_version'],
                $c['zend_log']
            );
        };

        // IRIS SDK
        $this['iris_sdk_client_registry'] = function (Container $c) {
            return new $c['iris_sdk_client_registry.class']();
        };

        $this['iris_sdk_client_registry.system_context'] = function (Container $c) {
            return new $c['iris_sdk_client_registry.system_context.class'](array(
                'base_url' => $c['iris_sdk_base_url'],
                'version' => $c['iris_sdk_version'],
                'consumer_key' => $c['iris_sdk_system_key'],
                'consumer_secret' => $c['iris_sdk_system_secret'],
            ));
        };

        $this['iris_sdk_client_registry.agent_context'] = function (Container $c) {
            return new $c['iris_sdk_client_registry.agent_context.class'](
                $c['iris.authentication.agent_authorization_token']->getContextParameters()
            );
        };

        $this['iris_sdk_client_registry.landlord_context'] = function (Container $c) {
            return new $c['iris_sdk_client_registry.landlord_context.class'](
                $c['iris.authentication.landlord_authorization_token']->getContextParameters()
            );
        };

        $this->extend('iris_sdk_client_registry', function ($registry, Container $c) {

            // Add parameterised contexts to registry
            $registry
                ->addContext($c['iris_sdk_client_registry.system_context'])
                ->addContext($c['iris_sdk_client_registry.agent_context'])
                ->addContext($c['iris_sdk_client_registry.landlord_context'])
            ;

            return $registry;
        });

        $this->extend('twig', function ($twig, Container $c) {

            // Add extensions
            $twig->addExtension($c['twig.form_extension']);
            $twig->addExtension($c['iris.twig.extension.lookup_extension']);
            $twig->addExtension($c['iris.twig.extension.duration_extension']);
            $twig->addExtension($c['iris.twig.extension.cmsPanel_extension']);
            $twig->addExtension($c['translation_extension']);
            if ($c['twig.debug']) {
                $twig->addExtension(new Twig_Extension_Debug());
            }

            // Set renderer engine environment
            $c['twig.renderer_engine']->setEnvironment($twig);

            // Set globals
            // This 'if' is to enable sharing the same container outside Connect with a different authentication context
            if (is_object($c['zend_auth']->getStorage()->read())) {
                $twig->addGlobal('_isCurrentAgentInIris', (bool) $c['zend_auth']->getStorage()->read()->isInIris);
            }
            $twig->addGlobal('_isXmlHttpRequest', $c['request']->isXmlHttpRequest());
            $twig->addGlobal('_request', $c['request']);

            return $twig;
        });

        // Form Validation Error Binder
        $this['iris.utility.validation.form_validation_error_binder'] = function (Container $c) {
            return new $c['iris.utility.validation.form_validation_error_binder.class']();
        };

        // Address Finder
        $this['iris.address_finder'] = function (Container $c) {
            return new $c['iris.address_finder.class']($c['iris_sdk_client_registry.system_context']);
        };

        // Zend log
        $this['zend_log_writer'] = function(Container $c) {
            $writer = new $c['zend_log_writer.class']('php://stderr');
            $writer->addFilter(\Zend_Log::WARN);
            return $writer;
        };

        $this['zend_log'] = function (Container $c) {
            return new $c['zend_log.class']($c['zend_log_writer']);
        };

        // Symfony session
        $this['symfony_session'] = function (Container $c) {
            return new $c['symfony_session.class']();
        };

        // Progressive store
        $this['iris.referencing.form_set.progressive_store.agent_progressive_store'] = function (Container $c) {
            return new $c['iris.referencing.form_set.progressive_store.agent_progressive_store.class']();
        };

        $this['iris.referencing.form_set.progressive_store.agent_guarantor_progressive_store'] = function (Container $c) {
            return new $c['iris.referencing.form_set.progressive_store.agent_guarantor_progressive_store.class']();
        };

        $this['iris.referencing.form_set.progressive_store.landlord_progressive_store'] = function (Container $c) {
            return new $c['iris.referencing.form_set.progressive_store.landlord_progressive_store.class']();
        };

        $this['iris.referencing.form_set.progressive_store.system_progressive_store'] = function (Container $c) {
            return new $c['iris.referencing.form_set.progressive_store.system_progressive_store.class']();
        };

        $this->extend('iris.referencing.form_set.progressive_store.agent_progressive_store', function ($store, Container $c) {
            $store->setIrisSdkContext($c['iris_sdk_client_registry.agent_context']);
            return $store;
        });

        $this->extend('iris.referencing.form_set.progressive_store.agent_guarantor_progressive_store', function ($store, Container $c) {
            $store->setIrisSdkContext($c['iris_sdk_client_registry.agent_context']);
            return $store;
        });

        $this->extend('iris.referencing.form_set.progressive_store.landlord_progressive_store', function ($store, Container $c) {
            $store->setIrisSdkContext($c['iris_sdk_client_registry.landlord_context']);
            return $store;
        });

        $this->extend('iris.referencing.form_set.progressive_store.system_progressive_store', function ($store, Container $c) {
            $store->setIrisSdkContext($c['iris_sdk_client_registry.system_context']);
            return $store;
        });

        $this['iris.referencing.form_flow.connect_new_reference_form_flow'] = function (Container $c) {
            return new $c['iris.referencing.form_flow.connect_new_reference_form_flow.class'](
                $c['request'],
                $c['iris.referencing.form_set.progressive_store.agent_progressive_store']
            );
        };

        $this['iris.referencing.form_flow.connect_add_tenant_form_flow'] = function (Container $c) {
            return new $c['iris.referencing.form_flow.connect_add_tenant_form_flow.class'](
                $c['request'],
                $c['iris.referencing.form_set.progressive_store.agent_progressive_store']
            );
        };

        $this['iris.referencing.form_flow.connect_add_guarantor_form_flow'] = function (Container $c) {
            return new $c['iris.referencing.form_flow.connect_add_guarantor_form_flow.class'](
                $c['request'],
                $c['iris.referencing.form_set.progressive_store.agent_guarantor_progressive_store']
            );
        };

        $this['iris.referencing.form_flow.applicant_continue_reference_form_flow'] = function (Container $c) {
            return new $c['iris.referencing.form_flow.applicant_continue_reference_form_flow.class'](
                $c['request'],
                $c['iris.referencing.form_set.progressive_store.system_progressive_store']
            );
        };

        $this['iris.referencing.form_flow.applicant_complete_reference_form_flow'] = function (Container $c) {
            return new $c['iris.referencing.form_flow.applicant_complete_reference_form_flow.class'](
                $c['request'],
                $c['iris.referencing.form_set.progressive_store.system_progressive_store']
            );
        };

        // Iris report finders
        $this['iris.referencing.report.agent_report_finder'] = function (Container $c) {
            return new $c['iris.referencing.report.report_finder.class']($c['iris_sdk_client_registry.agent_context']);
        };

        $this['iris.referencing.report.landlord_report_finder'] = function (Container $c) {
            return new $c['iris.referencing.report.report_finder.class']($c['iris_sdk_client_registry.landlord_context']);
        };

        // Iris filename builder
        $this['iris.referencing.report.report_filename_builder'] = function (Container $c) {
            return new $c['iris.referencing.report.report_filename_builder.class']();
        };

        // Iris application counter
        $this['iris.referencing.application.application_counter'] = function (Container $c) {
            return new $c['iris.referencing.application.application_counter.class']($c['iris_sdk_client_registry.agent_context']);
        };

        // Iris submitters
        $this['iris.referencing.application.submission.case_submitter'] = function (Container $c) {
            return new $c['iris.referencing.application.submission.case_submitter.class']($c['iris_sdk_client_registry.agent_context']);
        };

        $this['iris.referencing.application.submission.application_submitter'] = function (Container $c) {
            return new $c['iris.referencing.application.submission.application_submitter.class']($c['iris_sdk_client_registry.agent_context']);
        };

        // Iris lookup cache
        $this['iris.lookup_cache.adapter'] = function (Container $c) {
            return new $c['iris.lookup_cache.adapter.class']($c['iris.lookup_cache.dir']);
        };

        $this['iris.lookup_cache'] = function (Container $c) {
            return new $c['iris.lookup_cache.class']($c['iris.lookup_cache.adapter']);
        };

        $this->extend('iris.lookup_cache', function ($cache, Container $c) {
            $cache->setOption('ttl', $c['iris.lookup_cache.ttl']);
            return $cache;
        });

        // Iris product cache
        $this['iris.product_cache.adapter'] = function (Container $c) {
            return new $c['iris.product_cache.adapter.class']($c['iris.product_cache.dir']);
        };

        $this['iris.product_cache'] = function (Container $c) {
            return new $c['iris.product_cache.class']($c['iris.product_cache.adapter']);
        };

        $this->extend('iris.product_cache', function ($cache, Container $c) {
            $cache->setOption('ttl', $c['iris.product_cache.ttl']);
            return $cache;
        });

        // Iris notifications cache
        $this['iris.notifications_cache.adapter'] = function (Container $c) {
            return new $c['iris.notifications_cache.adapter.class']($c['iris.notifications_cache.dir']);
        };

        $this['iris.notifications_cache'] = function (Container $c) {
            return new $c['iris.notifications_cache.class']($c['iris.notifications_cache.adapter']);
        };

        $this->extend('iris.notifications_cache', function ($cache, Container $c) {
            $cache->setOption('ttl', $c['iris.notifications_cache.ttl']);
            return $cache;
        });

        // Iris product services
        $this['iris.product'] = function (Container $c) {
            return new $c['iris.product.class']($c['iris_sdk_client_registry.agent_context'], $c['iris.product_cache']);
        };

        $this['iris.product_price'] = function (Container $c) {
            return new $c['iris.product_price.class']($c['iris_sdk_client_registry.agent_context'], $c['iris.product_cache']);
        };

        // Current form flow records
        $this['iris.referencing.application.current_form_flow_records'] = function (Container $c) {
            return new $c['iris.referencing.application.current_form_flow_records.class']($c);
        };

        // Slugify
        $this['slugifier'] = function (Container $c) {
            return new $c['slugifier.class'];
        };

        // Failure reporting
        $this['iris.referencing.submission.submission_failure_message_resolver'] = function (Container $c) {
            $c['twig']->getLoader()->addPath($c['iris.referencing.submission.submission_failure_message_resolver.template_dir']);
            return new $c['iris.referencing.submission.submission_failure_message_resolver.class']($c['twig']);
        };

        // Additional information note handling
        $this['iris.additional_information_note_handler.agent_tenant'] = function (Container $c) {
            return new $c['iris.individual_application.additional_information_note.note_handler.class'](
                $c['iris_sdk_client_registry.agent_context']->getNoteClient(),
                $c['iris.referencing.form_set.progressive_store.agent_progressive_store']
            );
        };

        $this['iris.additional_information_note_handler.agent_guarantor'] = function (Container $c) {
            return new $c['iris.individual_application.additional_information_note.note_handler.class'](
                $c['iris_sdk_client_registry.agent_context']->getNoteClient(),
                $c['iris.referencing.form_set.progressive_store.agent_guarantor_progressive_store']
            );
        };

        $this['iris.additional_information_note_handler.system'] = function (Container $c) {
            return new $c['iris.individual_application.additional_information_note.note_handler.class'](
                $c['iris_sdk_client_registry.system_context']->getSystemApplicationClient(),
                $c['iris.referencing.form_set.progressive_store.system_progressive_store']
            );
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
