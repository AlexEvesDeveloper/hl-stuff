<?php

use Barbondev\IRISSDK\Common\Exception\AuthenticationException;
use Barbondev\IRISSDK\Common\Exception\NotFoundException;

/**
 * Class IrisRefereeAbstractController
 *
 * @author Paul Swift <paul.swift@barbon.com>
 */
abstract class IrisRefereeAbstractController extends Zend_Controller_Action {

    /**
     * @var Zend_Config
     */
    protected $params;

    /**
     * @var Zend_Session_Namespace
     */
    private $authSession;

    /**
     * @var string
     */
    protected $linkRef;

    /**
     * @var bool
     */
    protected $hasAuth = false;

    /**
     * Initialise controller
     *
     * @throws Exception
     * @return void
     */
    public function init()
    {
        $this->params = Zend_Registry::get('params');
        $request = $this->getRequest();
        $actionName = $request->getActionName();

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

        // Don't perform any authentication for displaying the generic completed page
        if ('completed' != $actionName) {

            // todo: instead use the Zend_Auth mechanism commented out below
            $this->authSession = new Zend_Session_Namespace('homelet_referee');

            // Get case-insensitive link reference if there is one
            $query = array_change_key_case($this->getSymfonyRequest()->query->all());
            $linkRef = (isset($query['linkref'])) ? $query['linkref'] : null;

            // Fetch back the link ref either from GET var (takes precedence) or session
            if ($linkRef) {

                // From GET var
                $this->linkRef = $linkRef;

            }
            elseif (isset($this->authSession->linkRef)) {

                // From session
                $this->linkRef = $this->authSession->linkRef;

            }

            // Check auth via the incoming URL's {linkRef}; if the user didn't have a valid link then end here
//            $this->_auth = Zend_Auth::getInstance();
//            $this->_auth->setStorage(new Zend_Auth_Storage_Session('hl_referee'));

            // Check link ref is valid - note this is intentionally performed for every request (with the side effect
            // it'll be twice for PUTs and POSTs as the endpoints that service actions themselves also make the check)
            $exception = false;
            $exceptionMessage = '';
            try {
                $response = $this->getIrisSystemContext()->getSystemApplicationClient()->validateLink(
                    array('linkRef' => $this->linkRef)
                );
            }
            catch (NotFoundException $e) {
                //unset($this->authSession->linkRef);
                //throw new AuthenticationException('Not Found Exception: ' . $e->getMessage());
                $exception = true;
                $exceptionMessage = 'Not found: ' . $e->getMessage();
            }
            catch (\Exception $e) {
                //unset($this->authSession->linkRef);
                //throw new AuthenticationException('Exception: ' . $e->getMessage());
                $exception = true;
                $exceptionMessage = 'Exception: ' . $e->getMessage();
            }

            // All should be good at this point, ensure it is and store auth details
            if (!$exception && 200 == $response->getStatusCode()) {

                // Store success in auth
                $this->authSession->linkRef = $this->linkRef;
                $this->hasAuth = true;

            } else {

                // Something else has gone awry
                unset($this->authSession->linkRef);

                // If it's not an exception from testing the linkRef then it's a bad status code
                if (!$exception) {
                    //throw new AuthenticationException('Unexpected response status code.');
                    $exceptionMessage = 'Unexpected response status code.';
                }

                // Don't execute originally requested action
                $this->getRequest()->setActionName('null');

                // Show error template
                $this->renderTwigView('/iris-referee/error.html.twig', array(
                    'bodyTitle' => 'Error',
                    'errorMessage' => $exceptionMessage,
                ));
            }

        }

        // Embed date picker style
        $this->view->headLink()->appendStylesheet('/assets/vendor/jquery-datepicker/css/datePicker.css');

        // Embed JS and jQuery plugins (address finder borrowed from Connect)
        $this->view->headScript()->appendFile('/assets/connect/js/addressfinder.iris.plugin.jquery.js');
        $this->view->headScript()->appendFile('/assets/vendor/jquery-date/js/date.js');
        $this->view->headScript()->appendFile('/assets/vendor/jquery-datepicker/js/jquery.datePicker.js');

    }

    /**
     * Null action - used when an error is displayed from the init() method
     *
     * @return void
     */
    public function nullAction()
    {
    }

    /**
     * Format a datetime to push to IRIS API
     *
     * @param mixed|\DateTime $dateTime
     * @return null|string
     */
    protected function transformDateTime($dateTime)
    {
        if ($dateTime instanceof \DateTime) {
            return $dateTime->format('Y-m-d');
        }

        return null;
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