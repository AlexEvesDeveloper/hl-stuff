<?php
require_once('ConnectAbstractController.php');
class Connect_RentguaranteeclaimsJsonController extends ConnectAbstractController {

    protected $_claimReferenceNumber;

    public function init() {
        $this->context = 'json';
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        // Should be of MIME type application/json but IE is CRAP and tries to
        //   download the server response!
        header('Content-type: text/plain');

        // Load session data into private variables
        $pageSession = new Zend_Session_Namespace('online_claims');
        if(isset($pageSession->ClaimReferenceNumber)) {
             $this->_claimReferenceNumber = $pageSession->ClaimReferenceNumber;
        }
        parent::init();
    }

    /**
     * Validate a form page's values via AJAX
     *
     * @return void
     */
    public function validatePageAction() {

        $return = array();
        $postData = $this->getRequest()->getParams();
        $page = $postData['page'];

        switch($page) {
            case '1':
                $ajaxForm = new Connect_Form_RentGuaranteeClaims_Step1();
                break;
            case '2':
                $ajaxForm = new Connect_Form_RentGuaranteeClaims_Step2();
                break;
            case '3':
                $ajaxForm = new Connect_Form_RentGuaranteeClaims_Step3();
                break;
            case '4':
                $ajaxForm = new Connect_Form_RentGuaranteeClaims_Step4();
                break;
            default:
                return;
        }

        $valid = $ajaxForm->isValid($postData);
        if (!$valid) {
            $errorMessages = $this->_getMessagesFlattened($ajaxForm, true);
            $return['errorJs'] = $this->_getMessagesFlattened($ajaxForm, false);
            $return['errorCount'] = count($errorMessages);
            $return['errorHtml'] = $this->view->partial(
                'partials/rent-guarantee-claim-error-list.phtml',
                'connect',
                array(
                    'errors' => $errorMessages
                )
            );
        } else {
            $return['errorJs'] = '';
            $return['errorCount'] = '';
            $return['errorHtml'] = '';
        }

        echo Zend_Json::encode($return);
    }

    /**
    *   To check the existence of claim number in keyhouse database
    */
    public function claimDetailsAction() {
       	if ($this->_request->isPost()) {
			$filters = array('*' => array('StringTrim','HtmlEntities','StripTags'));
            $validators = array('*' => array('allowEmpty' => true));
            $input['claimRefNo'] = $this->_request->getParam('claimNumber');
            $validate = new Zend_Filter_Input($filters, $validators, $input);
           
            $claimRefNo = $validate->getEscaped->claimRefNo;
            $keyHouseManager = new Manager_Insurance_KeyHouse_Claim();
            $claimDetails = $keyHouseManager->getClaim(
                $claimRefNo,
                $this->_agentSchemeNumber
            );
            $claimDetailsResponse = empty($claimDetails) ? 0 : 1;
            echo $claimDetailsResponse;
        }
    }

    /**
     * Returns either an array of messages with error codes or a flat array of
     * error message strings.
     *
     * @param boolean $fullyFlatten Set to false for array of errors grouped by
     * error codes or true for a fully flattened array of messages (keys are
     * lost in the process).
     *
     * @return array
     */
    private function _getMessagesFlattened($form, $fullyFlatten = false) {
        if (!$fullyFlatten) {
            $messages = $form->getMessages();

            $validationErrors = array();

            foreach($messages as $key => $val) {
                if (substr($key, 0, 8) == 'subform_') {
                    foreach($val as $subkey => $subval) {
                        $validationErrors[$subkey] = $subval;
                    }
                } else {
                    $validationErrors[$key] = $val;
                }
            }

            return $validationErrors;
        } else {
            return $this->_flattenArrayToValues($form->getMessages());
        }
    }

    /**
     * Return a true flattened list of error messages only.
     *
     * @param mixed $data Anything, but typically n-depth nested arrays of
     * mixed-type values.
     *
     * @return array|mixed Flattened array of values, or the original thing
     * passed in if not an array.
     */
    private function _flattenArrayToValues($data) {
        if (is_array($data)) {
            $newData = array();
            foreach($data as $key => $val) {
                if (is_array($val)) {
                    $newData = array_merge($newData, $this->_flattenArrayToValues($val));
                } else {
                    $newData[] = $val;
                }
            }
            return $newData;
        } else {
            return $data;
        }
    }
}
