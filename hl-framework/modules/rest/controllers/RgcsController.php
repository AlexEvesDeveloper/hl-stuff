<?php

/**
 * JSON over REST RGCS controller.
 */
require_once('RestAbstractController.php');
class Rest_RgcsController extends RestAbstractController
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
            get_class($this->_accessor) != 'Service_Connect_RGCSAccessor'
        ) {
            $this->_accessor = new Service_Connect_RGCSAccessor();
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
     * Action to handle the GET HTTP verb.  In this case implements fetching the
     * files for a given reference number, or listing the reference numbers for
     * the completed but not transferred claims.
     *
     * @return void
     */
    public function getAction()
    {
        $responseBody = '';

        $refno = (isset($this->_requestParameters[0])) ? $this->_requestParameters[0] : '';
        $refno = preg_replace('/\D/', '', $refno);

        switch($this->_restAction) {

            case 'getfile':
                if ($refno != '') {
                    $result = $this->_accessor->getFileByRefNo($refno);
                    $responseBody = Zend_Json::encode($result);
                }
                break;

            case 'listcomplete':
                $result = $this->_accessor->listDataCompleteRefIds();
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
     * Action to handle the PUT HTTP verb.  In this case implements the marking
     * of a claim as complete by its reference number.
     *
     * @return void
     */
    public function putAction()
    {
        $responseBody = '';

        $refno = (isset($this->_requestParameters[0])) ? $this->_requestParameters[0] : '';
        $refno = preg_replace('/\D/', '', $refno);

        switch($this->_restAction) {

            case 'completeclaim':
                if ($refno != '') {
                    $result = $this->_accessor->completeClaimByRefNo($refno);
                    $responseBody = Zend_Json::encode($result);
                }
                break;

        }

        $this->getResponse()
            ->appendBody($responseBody);
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