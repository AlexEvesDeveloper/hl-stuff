<?php

use Barbondev\IRISSDK\Common\Exception\AuthenticationException;
use Barbondev\IRISSDK\Common\Exception\NotFoundException;
use Barbondev\IRISSDK\SystemApplication\Tat\Model\TatStatus;

/**
 * Class IrisTatAbstractController
 *
 * @author Paul Swift <paul.swift@barbon.com>
 */
abstract class IrisTatAbstractController extends Zend_Controller_Action {

    /**
     * Base URL
     */
    const BASE_URL = '/tenant-application-tracker/';

    /**
     * Login URL (relative to base URL)
     */
    const LOGIN_URL = 'login';

    /**
     * @var Zend_Config
     */
    protected $params;

    /**
     * @var Zend_Session_Namespace
     */
    protected $authSession;

    /**
     * @var string
     */
    protected $linkRef;

    /**
     * @var int
     */
    protected $agentSchemeNumber;

    /**
     * @var string
     */
    protected $applicationReferenceNumber;

    /**
     * @var string
     */
    protected $applicantBirthDate;

    /**
     * Initialise controller
     *
     * @throws Exception
     * @return void
     */
    public function init()
    {
        $this->params = Zend_Registry::get('params');

        // todo: Use the Zend_Auth mechanism instead
        $this->authSession = new Zend_Session_Namespace('homelet_tat');

        // Get and store link reference from GET parameters if there is one
        // Get case-insensitive link reference if there is one
        // Get stored auth details back if the user has previously been authenticated
        if (
            isset($this->authSession->hasAuth) && $this->authSession->hasAuth &&
            isset($this->authSession->agentSchemeNumber) &&
            isset($this->authSession->applicationReferenceNumber) &&
            isset($this->authSession->applicantBirthDate)
        ) {

            // Set class properties from auth details held in session
            $this->agentSchemeNumber = $this->authSession->agentSchemeNumber;
            $this->applicationReferenceNumber = $this->authSession->applicationReferenceNumber;
            $this->applicantBirthDate = $this->authSession->applicantBirthDate;

        }
        else {

            // Send non-logged in user to login page
            $thisUrl = rtrim($this->getRequest()->getRequestUri(), '/');
            $loginUrl = IrisTatAbstractController::BASE_URL . IrisTatAbstractController::LOGIN_URL;

            if ($thisUrl != $loginUrl) {
                $this->_helper->redirector->gotoUrl($loginUrl);
                return;
            }

        }

        Zend_Layout::startMvc();
        $this->_helper->layout->setLayout('default');

        // Pass params to view
        $this->view->params = $this->params;

        // Set template directory paths
        $this->getTemplating()->getLoader()->addPath(__DIR__ . '/../views/scripts');
        $this->getTemplating()->getLoader()->addPath(__DIR__ . '/../layouts');

        // As some templates are borrowed from Connect, look there too
        $this->getTemplating()->getLoader()->addPath(__DIR__ . '/../../connect/views/scripts');
        $this->getTemplating()->getLoader()->addPath(__DIR__ . '/../../connect/layouts');

        // If this is an XHR request, turn off the layout
        if ($this->getSymfonyRequest()->isXmlHttpRequest()) {
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->layout->disableLayout();
        }
    }

    /**
     * Render a twig view
     *
     * @param string $name
     * @param array $context
     * @return string
     */
    protected function renderTwigView($name, array $context = array())
    {
        // Switch off default Zend renderer
        $this->_helper->viewRenderer->setNoRender(true);

        // Render content
        $content = $this->getTemplating()->render($name, $context);

        // Append to response
        $this->getResponse()->appendBody($content);

        return $content;
    }

    /**
     * Get form factory
     *
     * @return \Symfony\Component\Form\FormFactoryInterface
     */
    protected function getFormFactory()
    {
        return $this->getContainer()->get('form_factory');
    }

    /**
     * Get symfony request
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    protected function getSymfonyRequest()
    {
        return $this->getContainer()->get('request');
    }

    /**
     * Get twig templating
     *
     * @return Twig_Environment
     */
    protected function getTemplating()
    {
        return $this->getContainer()->get('twig');
    }

    /**
     * Get form validation error binder
     *
     * @return \Iris\Utility\Validation\FormValidationErrorBinderInterface
     */
    protected function getFormValidationErrorBinder()
    {
        return $this->getContainer()->get('iris.utility.validation.form_validation_error_binder');
    }

    /**
     * Get IRIS SDK client registry
     *
     * @return \Barbondev\IRISSDK\Common\ClientRegistry\ClientRegistry
     */
    protected function getIrisClientRegistry()
    {
        return $this->getContainer()->get('iris_sdk_client_registry');
    }

    /**
     * Get IRIS SDK system registry context
     *
     * @return \Barbondev\IRISSDK\Common\ClientRegistry\Context\SystemContext
     */
    protected function getIrisSystemContext()
    {
        return $this->getIrisClientRegistry()->getSystemContext();
    }

    /**
     * Get IRIS SDK agent registry context
     *
     * @return \Barbondev\IRISSDK\Common\ClientRegistry\Context\AgentContext
     */
    protected function getIrisAgentContext()
    {
        return $this->getIrisClientRegistry()->getAgentContext();
    }

    /**
     * Get IRIS service container
     *
     * @return \Iris\DependencyInjection\Container
     */
    protected function getContainer()
    {
        return \Zend_Registry::get('iris_container');
    }
}