<?php

require_once __DIR__ . '/ConnectAbstractController.php';

/**
 * Class RRPConnectAbstractController
 *
 * @author April Portus <april.portus@barbon.com>
 */
abstract class RRPConnectAbstractController extends ConnectAbstractController
{
    /**
     * @var Datasource_Insurance_RentRecoveryPlus_Rates
     */
    protected $rateDatasource;

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

        // Embed JS and jQuery plugins
        $this->view->headScript()->appendFile('/assets/vendor/jquery-date/js/date.js');
        $this->view->headScript()->appendFile('/assets/vendor/jquery-datepicker/js/jquery.datePicker.js');

        $this->rateDatasource = new Datasource_Insurance_RentRecoveryPlus_Rates();
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
     * @return \RRP\Utility\Validation\FormValidationErrorBinderInterface
     */
    protected function getFormValidationErrorBinder()
    {
        return $this->getContainer()->get('rrp.utility.validation.form_validation_error_binder');
    }

    /**
     * Get RRP service container
     *
     * @return \RRP\DependencyInjection\RRPContainer
     */
    protected function getContainer()
    {
        return \Zend_Registry::get('rrp_container');
    }
}