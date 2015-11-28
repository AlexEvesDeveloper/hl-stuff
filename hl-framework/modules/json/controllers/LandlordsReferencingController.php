<?php

// Can't be unit tested/code coverage'd as it's all designed to work over ajax
// @codeCoverageIgnoreStart

class Json_LandlordsReferencingController extends Zend_Controller_Action
{
	public function init() {
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		header('Content-type: application/json');
	}
    
    /**
     * Validate a form page's values via AJAX
     *
     * @return void
     */
    public function validatePageAction() {
		$return = array();
		
        $session = new Zend_Session_Namespace('referencing_global');
		
        $postData = $this->getRequest()->getParams();
		$page = $postData['page'];
		
        switch($page) {
			
			case 'register':
                $ajaxForm = new LandlordsReferencing_Form_Register();
                break;
			
			case 'login':
                $ajaxForm = new LandlordsReferencing_Form_Login();	
                break;
			
			case 'property-lease':
                $ajaxForm = new LandlordsReferencing_Form_PropertyLease();
                break;
			
			case 'product-selection':
                $ajaxForm = new LandlordsReferencing_Form_ProductSelection();
                break;
			
            case 'prospective-landlord':
                $ajaxForm = new LandlordsReferencing_Form_ProspectiveLandlord();
                break;
			
            case 'reference-subject':
                $ajaxForm = new LandlordsReferencing_Form_ReferenceSubject();
                break;
			
			case 'current-landlord':
                $ajaxForm = new LandlordsReferencing_Form_CurrentLandlord ();	
                break;
			
            case 'current-occupation':
			case 'future-occupation':
                $ajaxForm = new LandlordsReferencing_Form_Occupation();	
                break;
			
            case 'first-residence':
            case 'second-residence':
            case 'third-residence':
                $ajaxForm = new LandlordsReferencing_Form_Residence();
                break;
			
			case 'terms':
                $ajaxForm = new LandlordsReferencing_Form_Terms();	
                break;
			
            default:
                return;
        }
        $valid = $ajaxForm->isValid($postData);
		
		if (!$valid) {
			$errorMessages = $ajaxForm->getMessagesFlattened();

			// We need to strip out some complex messages that the end user won't care about
			if (isset($errorMessages['email_address'])) {
				if (isset($errorMessages['email_address']['hostnameUnknownTld'])) {
					unset($errorMessages['email_address']['hostnameUnknownTld']);
				};
				if (isset($errorMessages['email_address']['hostnameLocalNameNotAllowed'])) {
					unset($errorMessages['email_address']['hostnameLocalNameNotAllowed']);
				};
			}
			
			$return['errorJs'] = $errorMessages;
			$return['errorCount'] = count($errorMessages);
			$return['errorHtml'] = $this->view->partial('partials/error-list.phtml', array('errors' => $errorMessages));
			$return['postData'] = $postData;
		} else {
			$return['errorJs'] = '';
			$return['errorCount'] = '';
			$return['errorHtml'] = '';
			$return['postData'] = $postData;
		}

		echo Zend_Json::encode($return);
    }
}

// @codeCoverageIgnoreEnd
?>