<?php

require_once __DIR__ . '/ConnectAbstractController.php';

/**
 * Class IrisConnectAbstractController
 *
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
abstract class IrisConnectAbstractController extends ConnectAbstractController
{
    /**
     * Agent returning from notification session namespace
     */
    const AGENT_RETURNING_FROM_NOTIFICATION_NAMESPACE = 'arfn';

    /**
     * Initialise controller
     *
     * @throws Exception
     * @return void
     */
    public function init()
    {
        parent::init();

        // Set template directory paths
        $this->getTemplating()->getLoader()->addPath(__DIR__ . '/../views/scripts');
        $this->getTemplating()->getLoader()->addPath(__DIR__ . '/../layouts');

        // If this is an XHR request, turn off the layout
        if ($this->getSymfonyRequest()->isXmlHttpRequest()) {
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->layout->disableLayout();
        }

        // If current agent is not in IRIS then throw exception
        if (!$this->_isAgentInIris) {
            throw new \Exception('Agent is currently not in IRIS referencing system');
        }

        // Embed IRIS-specific styles and date picker style
        $this->view->headLink()->appendStylesheet('/assets/connect/css/iris.css', 'all');
        $this->view->headLink()->appendStylesheet('/assets/vendor/jquery-datepicker/css/datePicker.css');

        // Embed JS and jQuery plugins
        $this->view->headScript()->appendFile('/assets/connect/js/addressfinder.iris.plugin.jquery.js');
        $this->view->headScript()->appendFile('/assets/vendor/jquery-date/js/date.js');
        $this->view->headScript()->appendFile('/assets/vendor/jquery-datepicker/js/jquery.datePicker.js');
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

    /**
     * Set is agent returning from notification flag
     *
     * @param string $applicationUuId
     * @param boolean $isAgentReturningFromNotification
     * @return $this
     */
    protected function setIsAgentReturningFromNotification($applicationUuId, $isAgentReturningFromNotification)
    {
        $_SESSION[self::buildAgentReturningFromNotificationNamespace($applicationUuId)]
            = $isAgentReturningFromNotification;

        return $this;
    }

    /**
     * Get the is agent returning from notification flag
     *
     * @param string $applicationUuId
     * @return bool
     */
    protected function getIsAgentReturningFromNotification($applicationUuId)
    {
        if (!isset($_SESSION[self::buildAgentReturningFromNotificationNamespace($applicationUuId)])) {
            return false;
        }

        return $_SESSION[self::buildAgentReturningFromNotificationNamespace($applicationUuId)];
    }

    /**
     * Build agent returning from notification flag namespace
     *
     * @param string $applicationUuId
     * @return string
     */
    protected static function buildAgentReturningFromNotificationNamespace($applicationUuId)
    {
        return self::AGENT_RETURNING_FROM_NOTIFICATION_NAMESPACE . md5($applicationUuId);
    }

    /**
     * Try to get the referencing model from the form data
     *
     * @param array $data
     * @return null|object
     */
    protected function getReferencingModelFromFormData(array $data)
    {
        return isset($data['step']) ? $data['step'] : null;
    }

    /**
     * Can the agent complete the prospective landlord section?
     *
     * @return bool
     */
    protected function canAgentCompleteProspectiveLandlord()
    {
        return in_array($this->_fsastatusabbr, array(
            'Direct',
            'DIR',
            'AR',
        ));
    }
}
