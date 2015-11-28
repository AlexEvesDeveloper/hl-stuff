<?php
/**
* Portfolio Insurance
* @author John Burrin
* @since 1.3 (arcane coyote)
*
*/
class Json_PortfolioInsuranceQuoteController extends Zend_Controller_Action
{

	public function init() {
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		header('Content-type: application/json');
	}

	public function validatePageAction() {

		$return = array();

		$pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');

		$postData = $this->getRequest()->getParams();
		$page = $postData['page'];

		switch($page) {
			case '1':
				$ajaxForm = new Form_PortfolioInsuranceQuote_Step1();

				break;
			case '2':
					$ajaxForm = new Form_PortfolioInsuranceQuote_Step2();
				break;
			case 'add':
					$ajaxForm = new Form_PortfolioInsuranceQuote_insuredAddress();

				break;
				
			case '3':
				$ajaxForm = new Form_PortfolioInsuranceQuote_Step3();

				break;

			case '4':
				$ajaxForm = new Form_PortfolioInsuranceQuote_Step4();

				break;

			case '5':
				$ajaxForm = new Form_PortfolioInsuranceQuote_Step5();

				break;
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
			// don't push errors into the parent page
			if($page != 2){
				$return['errorHtml'] =  $this->view->partial('portfolio-insurance-quote/partials/error-list.phtml', array('errors' => $errorMessages));
			}
		} else {
			$return['errorJs'] = '';
			$return['errorCount'] = '';
			$return['errorHtml'] = '';
		}


		echo Zend_Json::encode($return);
	}


	/**
	* @param None
	* @return JSON Object
	* @author John Burrin
	* @since 1.3
	*/
    public function addPropertyAction(){
	$pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
	$customerReferenceNumber = $pageSession->CustomerRefNo;

        $return = array();
		$ajaxForm = new Form_PortfolioInsuranceQuote_insuredAddress();

		$request = $this->getRequest();
		$postdata = $request->getPost();

		$return['success'] = false;

		$ajaxForm->isValid($postdata);
		$return['errorObject'] = $ajaxForm->getMessages();
		$return['action'] = $postdata['action'];
		if($ajaxForm->isValid($postdata)){

			// Create a new property manager and a new property data object
			$property = new Manager_Insurance_Portfolio_Property();
			$propertyObject = new Model_Insurance_Portfolio_Property();

			$cleanData = $ajaxForm->getValues(); // According to the Zend manual these *should* be the clean values
			$propertyObject->building = $cleanData['ins_house_number_name'];

			$propertyObject->buildingsAccidentalDamage = $cleanData['buildings_accidental_damage'];
			$propertyObject->buildingsNilExcess = $cleanData['buildings_nil_excess'];
			$propertyObject->buildingsSumInsured = $cleanData['buildings_cover'];

			$propertyObject->contentsAccidentalDamage = $cleanData['contents_accidental_damage'];
			$propertyObject->contentsNilExcess = $cleanData['contents_nil_excess'];
			$propertyObject->contentsSumInsured = $cleanData['contents_cover'];
			// $propertyObject->id
			$propertyObject->limitedContents = $cleanData['limited_contents'];

			$address = new Manager_Core_Postcode();
			$return['address'] = $address->getPropertyByID($cleanData['ins_address']);

			$propertyObject->houseNumber = $return['address']['houseNumber'];
			$propertyObject->building = $return['address']['buildingName'];
			$propertyObject->address1 = $return['address']['address1'];
			$propertyObject->address2 = $return['address']['address2'];
			$propertyObject->address3 = $return['address']['address3'];
			$propertyObject->address4 = $return['address']['address4'];
			$propertyObject->address5 = $return['address']['address5'];
			$propertyObject->postcode = $property->formatPostcode($cleanData['ins_postcode']);
			$propertyObject->refno = $pageSession->CustomerRefNo;
			$propertyObject->tenantOccupation = $cleanData['employment_status'];

			if($lastId = $property->save($propertyObject)){
				$propertyArray = array();
				$propertyManager = new Manager_Insurance_Portfolio_Property();
				$propertyArray = $propertyManager->fetchAllProperties($customerReferenceNumber);
				$this->view->stepNum = 2;
				$return['html'] = $this->view->partialLoop('portfolio-insurance-quote/partials/property-list.phtml', $propertyArray);
				$return['success'] = true;
				$return['propNumb'] = count($propertyArray->toArray());
				$return['errorHtml'] = "";
			}
		}
		echo Zend_Json::encode($return);

    }

	/**
	* TODO: Document this
	* @param
	* @return
	* @author John Burrin
	* @since
	*/
	public function removePropertyAction(){
		$pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
		$customerReferenceNumber = $pageSession->CustomerRefNo;
		$ajaxForm = new Form_PortfolioInsuranceQuote_removePropertyDialog();
        $return = array();
		$request = $this->getRequest();
		$postdata = $request->getPost();

		$return['success'] = false;

		if($ajaxForm->isValid($postdata)){
			$propertyArray = array();
			$propertyManager = new Manager_Insurance_Portfolio_Property();
			$propertyManager->deleteById($postdata['propertyid']);
			$propertyArray = $propertyManager->fetchAllProperties($customerReferenceNumber);
			// need to tell the view we are on step 2
			$this->view->stepNum = 2;
			$return['html'] = $this->view->partialLoop('portfolio-insurance-quote/partials/property-list.phtml', $propertyArray);
			$return['success'] = true;
			$return['propNumb'] = count($propertyArray->toArray());
		}
		echo Zend_Json::encode($return);
	}

	/**
	* TODO: Document this
	* @param
	* @return
	* @author John Burrin
	* @since
	*/
	public function updatePropertyAction(){
		$pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
		$customerReferenceNumber = $pageSession->CustomerRefNo;
		$ajaxForm = new Form_PortfolioInsuranceQuote_editPropertyDialog();
		$return = array();
		$request = $this->getRequest();
		$postdata = $request->getPost();

		$return['success'] = false;

		$ajaxForm->isValid($postdata);
		$return['errorObject'] = $ajaxForm->getMessages();
		if($ajaxForm->isValid($postdata)){
			$cleanData = $ajaxForm->getValues(); // According to the Zend manual these *should* be the clean values
			$propertyArray = array();
			$propertyManager = new Manager_Insurance_Portfolio_Property();
			$dataObject = new Model_Insurance_Portfolio_Property();

			if(isset($postdata['propertyid'])) $dataObject->id = $cleanData['propertyid'];
            if(isset($customerReferenceNumber))$dataObject->refno = $customerReferenceNumber;
            if(isset($postdata['ins_house_number_name']))$dataObject->building = $cleanData['ins_house_number_name'];
            if(isset($postdata['ins_postcode']))$dataObject->postcode = $propertyManager->formatPostcode($cleanData['ins_postcode']);
            if(isset($postdata['employment_status']))$dataObject->tenantOccupation = $cleanData['employment_status'];
            if(isset($postdata['buildings_cover']))$dataObject->buildingsSumInsured = $cleanData['buildings_cover'];
            if(isset($postdata['buildings_accidental_damage']))$dataObject->buildingsAccidentalDamage = $cleanData['buildings_accidental_damage'];
            if(isset($postdata['buildings_nil_excess']))$dataObject->buildingsNilExcess = $cleanData['buildings_nil_excess'];
            if(isset($postdata['contents_cover']))$dataObject->contentsSumInsured = $cleanData['contents_cover'];
            if(isset($postdata['contents_accidental_damage']))$dataObject->contentsAccidentalDamage = $cleanData['contents_accidental_damage'];
            if(isset($postdata['contents_nil_excess']))$dataObject->contentsNilExcess = $cleanData['contents_nil_excess'];
            if(isset($postdata['limited_contents']))$dataObject->limitedContents = $cleanData['limited_contents'];

			$address = new Manager_Core_Postcode();
			$return['address'] = $address->getPropertyByID($cleanData['ins_address']);

			$dataObject->houseNumber = $return['address']['houseNumber'];
			$dataObject->building = $return['address']['buildingName'];
			$dataObject->address1 = $return['address']['address1'];
			$dataObject->address2 = $return['address']['address2'];
			$dataObject->address3 = $return['address']['address3'];
			$dataObject->address4 = $return['address']['address4'];
			$dataObject->address5 = $return['address']['address5'];
			$dataObject->postcode = $property->formatPostcode($cleanData['ins_postcode']);
			$dataObject->refno = $pageSession->CustomerRefNo;
			$dataObject->tenantOccupation = $dataObject->tenantOccupation ;

			// Do the update stuffs
			$propertyManager->save($dataObject);


			// need to tell the view we are on step 2
			$this->view->stepNum = 2;
			// Get the properties to shove back into the page
			$propertyArray = $propertyManager->fetchAllProperties($customerReferenceNumber);
			$return['html'] = $this->view->partialLoop('portfolio-insurance-quote/partials/property-list.phtml', $propertyArray);
			$return['success'] = true;
			$return['propNumb'] = count($propertyArray->toArray());
		}
		echo Zend_Json::encode($return);
	}

	/**
	* TODO: Document this
	* @param
	* @return
	* @author John Burrin
	* @since 1.3
	* http://homelet.centos5.dev/json/portfolio-portfolio/add-bank-interest
	*/
	public function addBankInterestAction(){

		$pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
		$customerReferenceNumber = $pageSession->CustomerRefNo;
		$ajaxForm = new Form_PortfolioInsuranceQuote_bankInterestDialog();
		$return = array();
		$request = $this->getRequest();
		$postdata = $request->getPost();

		$return['success'] = false;
		if($ajaxForm->isValid($postdata)){
			$dataObject = new Model_Insurance_BankInterest();
			$manager = new Manager_Insurance_Portfolio_BankInterest();
		//interestID
			$dataObject->refno = $customerReferenceNumber;
		//policynumber =
			$dataObject->bankname = $postdata['bank_name'];
			$dataObject->bankaddress1 = $postdata['bank_address_line1'];
			$dataObject->bankaddress2 = $postdata['bank_address_line2'];
			$dataObject->bankaddress3 = $postdata['bank_address_line3'];
			$dataObject->bankaddress4 = $postdata['bank_address_line4'];
			$dataObject->bankpostcode = $postdata['bank_postcode'];
			$dataObject->accountnumber = $postdata['bank_account_number'];
			$dataObject->propertyId = $postdata['bank_property'];
			#$dataObject->bank_property =

			// Do the update stuffs
			$manager->save($dataObject);
						// Get the properties to shove back into the page
			$interestsArray = $manager->fetchAllInterests($customerReferenceNumber);
			$return['html'] = $this->view->partialLoop('portfolio-insurance-quote/partials/bank-interest.phtml', $interestsArray);
			$return['success'] = true;

		}else{
				foreach ($ajaxForm->getMessages() as $error){
				$return['errors'] = $error;
			}

		}
		echo Zend_Json::encode($return);
	}

	/**
	* add Previous calims add previous claims to the previous claims table (?)
	* @param POST vars
	* @return JSON Encoded html
	* @author John Burrin
	* @since 1.3
	* http://homelet.centos5.dev/json/portfolio-portfolio/add-previous-claim
	*/
	public function addPreviousClaimAction(){
		$pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
		$customerReferenceNumber = $pageSession->CustomerRefNo;
		$ajaxForm = new Form_PortfolioInsuranceQuoteJson_Claims();
		$return = array();
		$request = $this->getRequest();
		$postdata = $request->getPost();

		$return['success'] = false;
		if($ajaxForm->isValid($postdata)){
			$claimsManager = new Manager_Insurance_Portfolio_PreviousClaims();
			$dataObject = new Model_Insurance_Portfolio_PreviousClaims();
			$dataObject->refno = $customerReferenceNumber;
			$dataObject->claimtype = "";
			$dataObject->claimmonth = $postdata['claim_month'];
			$dataObject->claimyear = $postdata['claim_year'];
			$dataObject->claimvalue = $postdata['claim_value'];
			$dataObject->propertyId = $postdata['claim_property'];
			$dataObject->claimTypeID = $postdata['claim_type'];
			$dataObject->claimDetail = $postdata['claim_detail'];

			// Do the update stuffs
			$claimsManager->save($dataObject);
			// Get the properties to shove back into the page
			$claimsArray = $claimsManager->fetchAllClaims($customerReferenceNumber);
			$return['html'] = $this->view->partialLoop('portfolio-insurance-quote/partials/claims-list.phtml', $claimsArray);
			$return['success'] = true;


		}else{
			foreach ($ajaxForm->getMessages() as $error){
				$return['errors'] = $error;
			}

		}

		echo Zend_Json::encode($return);
	}

	public function addAdditionalAction(){

		$pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
		$customerReferenceNumber = $pageSession->CustomerRefNo;
		$ajaxForm = new Form_PortfolioInsuranceQuote_additionalDialog();
		$return = array();
		$request = $this->getRequest();
		$postdata = $request->getPost();

		$return['success'] = false;
		if($ajaxForm->isValid($postdata)){
			$dataObject = new Model_Insurance_Portfolio_AdditionalInformation();
			$manager = new Manager_Insurance_Portfolio_AdditionalInformation();
			$dataObject->refNo = $customerReferenceNumber;
			$dataObject->propertyId = $postdata['property'];
			$dataObject->questionId = $postdata['questionId'];
			$dataObject->information  = $postdata['information'];

			// Do the update stuffs
			$manager->save($dataObject);
			// Get the properties to shove back into the page
			$additionalArray = $manager->fetchAllByRefNo($customerReferenceNumber,$postdata['questionId']);
			#Zend_Debug::dump($additionalArray);
			$return['html'] = $this->view->partialLoop('portfolio-insurance-quote/partials/additional-list.phtml', $additionalArray);
			$return['success'] = true;

		}else{
				foreach ($ajaxForm->getMessages() as $error){
				$return['errors'] = $error;
			}

		}
		echo Zend_Json::encode($return);
	}

	/**
	* remove Previous claims add previous claims to the previous claims table (?)
	* @param POST vars
	* @return JSON Encoded html
	* @author John Burrin
	* @since 1.3
	* http://homelet.centos5.dev/json/portfolio-portfolio/remove-previous-claim
	*/
	public function removePreviousClaimAction(){
		$pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
		$customerReferenceNumber = $pageSession->CustomerRefNo;
		$ajaxForm = new Form_PortfolioInsuranceQuote_claimsDialog();
		$return = array();
		$request = $this->getRequest();
		$postdata = $request->getPost();

		$return['success'] = false;
		if($ajaxForm->isValid($postdata)){
			$claimsManager = new Manager_Insurance_Portfolio_PreviousClaims();
			$claimsManager->removeClaim($postdata['id']);
			// Do the update stuffs
			// Get the properties to shove back into the page
			$claimsArray = $claimsManager->fetchAllClaims($customerReferenceNumber);
            if ($claimsArray->count() > 0) {
                $return['html'] = $this->view->partialLoop('portfolio-insurance-quote/partials/claims-list.phtml', $claimsArray);
            } else {
                $return['html'] = '<em>None</em>';
            }
			$return['success'] = true;


		}else{
			// TODO: This shouldn't do this should it?
			Zend_Debug::dump($ajaxForm->getMessages() );
		}
		echo Zend_Json::encode($return);
	}

		/**
	* remove bank interest
	* @param POST vars
	* @return JSON Encoded html
	* @author John Burrin
	* @since 1.3
	* http://homelet.centos5.dev/json/portfolio-portfolio/remove-bank-interest
	*/
	public function removeBankInterestAction(){
		$pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
		$customerReferenceNumber = $pageSession->CustomerRefNo;
		$ajaxForm = new Form_PortfolioInsuranceQuote_claimsDialog();
		$return = array();
		$request = $this->getRequest();
		$postdata = $request->getPost();

		$return['success'] = false;
		if($ajaxForm->isValid($postdata)){
			$interestManager = new Manager_Insurance_Portfolio_BankInterest();
			$interestManager->removeInterest($postdata['id']);
			// Do the update stuffs
			// Get the properties to shove back into the page
			$interestArray = $interestManager->fetchAllInterests($customerReferenceNumber);
            if ($interestArray->count() > 0) {
                $return['html'] = $this->view->partialLoop('portfolio-insurance-quote/partials/bank-interest.phtml', $interestArray);
            } else {
                $return['html'] = '<em>None</em>';
            }
			$return['success'] = true;


		}else{
			// TODO: This shouldn't do this should it?
			Zend_Debug::dump($ajaxForm->getMessages() );
		}
		echo Zend_Json::encode($return);
	}

	public function removeAdditionalAction(){
		$pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
		$customerReferenceNumber = $pageSession->CustomerRefNo;
		$return = array();
		$request = $this->getRequest();
		$postdata = $request->getPost();

		$return['success'] = false;
		$interestManager = new Manager_Insurance_Portfolio_AdditionalInformation();
		$interestManager->removeAdditional($postdata['id']);
		// Do the update stuffs
		// Get the properties to shove back into the page
		$interestArray = $interestManager->fetchAllByRefNo($customerReferenceNumber);
		$return['html'] = $this->view->partialLoop('portfolio-insurance-quote/partials/additional-list.phtml', $interestArray);
		$return['success'] = true;
		echo Zend_Json::encode($return);
	}
}
?>
