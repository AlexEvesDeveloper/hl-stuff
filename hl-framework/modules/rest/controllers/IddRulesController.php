<?php

/**
 * JSON over REST IDD rules controller.
 */
require_once('RestAbstractController.php');
class Rest_IddRulesController extends RestAbstractController
{
    /**
     * Initialise the RGCS service accessor.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        if (
            is_null($this->_accessor) ||
            get_class($this->_accessor) != 'Service_Core_IddRulesAccessor'
        ) {
            $this->_accessor = new Service_Core_IddRulesAccessor();
        }
    }

    /**
     * Action to handle the INDEX HTTP verb.
     *
     * @return void
     */
    public function indexAction()
    {

    }

    /**
     * Action to handle the GET HTTP verb.
     *
     * @return void
     */
    public function getAction()
    {
        $responseBody = '';

        // Cleanse incoming parameters
        foreach ($this->_requestParameters as $key => $val) {
            $this->_requestParameters[$key] = strtolower(trim(preg_replace('/\W/', '', $val)));
        }

        $fsaStatus = (isset($this->_requestParameters[0])) ? $this->_requestParameters[0] : '';
        $quote = (isset($this->_requestParameters[1])) ? $this->_requestParameters[1] : '';
        $buy = (isset($this->_requestParameters[2])) ? $this->_requestParameters[2] : '';
        $precondition = (isset($this->_requestParameters[3])) ? $this->_requestParameters[3] : '';

        switch($this->_restAction) {

            case 'fetch':
                $result = $this->_accessor->fetchIddType($fsaStatus, $quote, $buy, $precondition);
                $responseBody = Zend_Json::encode($result);
                break;

        }

        $this->getResponse()
            ->appendBody($responseBody);
    }

    /**
     * Action to handle the POST HTTP verb.
     *
     * @return void
     */
    public function postAction()
    {

    }

    /**
     * Action to handle the PUT HTTP verb.
     *
     * @return void
     */
    public function putAction()
    {

    }

    /**
     * Action to handle the DELETE HTTP verb.
     *
     * @return void
     */
    public function deleteAction()
    {

    }
}