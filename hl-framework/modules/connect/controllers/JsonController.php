<?php

// Can't be unit tested/code coverage'd as it's all designed to work over AJAX
// @codeCoverageIgnoreStart

/**
 * Note: this is only for JSON calls that return data.  For calls that return
 * HTML, use the AJAX controller.
 */
require_once('ConnectAbstractController.php');
class Connect_JsonController extends ConnectAbstractController {

    public function init() {
        $this->context = 'json';
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->getHelper('layout')->disableLayout();
        // Should be of MIME type application/json but IE is CRAP and tries to
        //   download the server response!
        header('Content-type: text/plain');

        parent::init();
    }

    public function canOfferRentGuananteeAction(){
    	$fsa_status = $this->getRequest()->getParam('fsa_status');
    	$letType = $this->getRequest()->getParam('letType');
    	$howRgOffered = $this->getRequest()->getParam('howRgOffered');
    	$manager = new Manager_Core_RGProductOffered();
    	$productJson = $manager->canOfferRentGuanantee($fsa_status, $letType, $howRgOffered);
    	echo (json_encode($productJson));
    }
    
    public function fetchRgProductsAction(){
    	$fsa_status = $this->getRequest()->getParam('fsa_status');
    	$letType = $this->getRequest()->getParam('letType');
    	$howRgOffered = $this->getRequest()->getParam('howRgOffered');
    	$isCompanyApplication = $this->getRequest()->getParam('isCompanyApplication');
    	
		$manager = new Manager_Core_RGProductOffered();
    	$productJson = $manager->fetchProducts($fsa_status, $letType, $howRgOffered);
		
		//Ensure that set of possible products includes only Enhance, Extra and Advantage.
		$returnArray = array();
		foreach($productJson as $currentProduct) {
			
			if(preg_match("/^9$|^10$|^21$/", $currentProduct['value'])) {
				
				$returnArray[] = array('value' => $currentProduct['value'], 'name' => $currentProduct['name']);
			}
		}
    	echo (json_encode($returnArray));
    }
      
    public function fetchExternalNewsAction() {
        // Check user is logged in
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('hl_connect'));
        if ($auth->hasIdentity()) {
            // Fetch news according to this user's prefs
            $userId = $auth->getStorage()->read()->agentid;
            $userManager = new Manager_Core_Agent_User($userId);

            // Check cache contents
            $params = Zend_Registry::get('params');

            // Initialise the user items cache
            $frontendOptions = array(
                'lifetime' => $params->cms->extnews->fetchUserFilteredItemsCacheLifetime, // cache lifetime of x minutes
                'automatic_serialization' => true
            );
            $backendOptions = array(
                'cache_dir' => $params->cms->extnews->cachePath // Directory where to put the cache files
            );

            $cache = Zend_Cache::factory('Core',
                                         'File',
                                         $frontendOptions,
                                         $backendOptions);

            if (($newsJson = $cache->load('externalNews_user_' . $userId)) === false) {

                // Cache miss, get new results

                // Check user's news category preferences
                $newsCategoryFilter = array();
                list(, $newsPrefs) = $userManager->getUserExternalNewsPreferences();
                foreach ($newsPrefs as $id => $obj) {
                    $newsCategoryFilter[] = $id;
                }
                $extNewsManager = new Manager_Cms_ExternalNews();
                $externalNews = $extNewsManager->fetchNews($newsCategoryFilter);

                // Create array ready for JSON output
                $newsArray = array('news' => array());
                $count = 0;
                foreach ($externalNews as $newsItem) {
                    $newsArray['news']["item{$count}"] = array(
                        'title'         => $newsItem->title,
                        'summary'       => $newsItem->summary,
                        'link'          => $newsItem->linkUrl,
                        'attribution'   => "{$newsItem->sourceName}: {$newsItem->categoryName}"
                    );
                    $count++;
                }

                // Generate JSON
                $newsJson = Zend_Json::encode($newsArray);

                // Save in secondary cache for this user
                $cache->save($newsJson, 'externalNews_user_' . $userId);
            }

            echo $newsJson;
        }
    }

    public function sendPdfAction() {

        // Check user is logged in to get ASN from
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('hl_connect'));
        if ($auth->hasIdentity()) {

            // Fetch ASN and agent user ID
            $asn = $auth->getStorage()->read()->agentschemeno;
            $userId = $auth->getStorage()->read()->agentid;

            $request = $this->getRequest();
            if (!is_null($request->getParam('filename'))) {

                $filename = $request->getParam('filename');

                // Is this a special agent application form that requires content injection and is sent to a specific agent user?
                if (preg_match('/agent-form\?form=([\w\-]+)$/i', $filename, $matches) > 0) {

                    // Yes, requires agent content injection and sending
                    $formName = $matches[1];
                    $agentFormManager = new Manager_Connect_AgentForm();
                    $agentFormManager->populateAndOuput($formName, $asn, $userId, 'email');

                    echo "{\"successMessage\":\"Email sent\"}\n";
                    exit();

                } else {

                    // Standard PDF, load and send as-is
               	
                	$filters = array('*' => array('StringTrim','HtmlEntities','StripTags'));
                	
                	// Check e-mail present and valid
                    $formInput['to'] = htmlentities($request->getParam('to'));
					$formInput['message'] =  htmlentities($request->getParam('message'));
					$formInput['filename'] = htmlentities($request->getParam('filename'));
                	               
                    $emailValidator = new Zend_Validate_EmailAddress();
                    $emailValidator->setMessages(
                        array(
                            Zend_Validate_EmailAddress::INVALID_HOSTNAME    => 'Domain name invalid in email address',
                            Zend_Validate_EmailAddress::INVALID_FORMAT      => 'Invalid email address'
                        )
                    );
                    $validators = array(
                    	'*' => array('allowEmpty' => true),
                        'email' => $emailValidator
                    );
                    $validate = new Zend_Filter_Input($filters, $validators, $formInput);

                    if ($validate->isValid()) {

                        // Security - ensure PDF can only be requested from public webspace
                        $params = Zend_Registry::get('params');
                        $realpath = realpath($params->connect->basePublicPath . $validate->filename);
                        if (
                            strpos($realpath, $params->connect->safePublicRealPathContains) !== false &&
                            strtolower(substr($realpath, -4, 4)) == '.pdf'
                        ) {

                            // Generate e-mail
                            $mailer = new Application_Core_Mail();
                            $mailer->setTo($validate->to, $validate->to);
                            // TODO: Parameterise:
                            $mailer->setFrom('noreply@homelet.co.uk', 'HomeLet');
                            $mailer->setSubject("{$validate->filename} sent by HomeLet");
                            $mailer->setBodyText($validate->message);
                            $mailer->addAttachment($realpath, $validate->filename);
                            $mailer->send();
                            echo "{\"successMessage\":\"Email sent\"}\n";
                            exit();
                        }

                    } else {
                        echo "{\"errorMessage\":\"Invalid e-mail address\"}\n";
                        exit();
                    }
                }
            } else {
                echo "{\"errorMessage\":\"No PDF specified\"}\n";
                exit();
            }
        }
        echo "{\"errorMessage\":\"There was an error, please try again later\"}\n";
    }

    /**
     * Log an agent user's access request to the Deed of Guarantee doc, and
     * serve up secure link.
     */
    public function doglogAction() {

        // Identify user
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('hl_connect'));
        if ($auth->hasIdentity()) {
            // Fetch user ID
            $userId = $auth->getStorage()->read()->agentid;

            // Add doglog entry
            $doglogManager = new Manager_Connect_Doglog();
            $doglogManager->logPush($userId);

            // Send limited-use DoG link to dogsend action
            $dogSendHash = substr(md5($userId . date('Y-m-d') . 'd0gl0g secret string...'), 0, 8);
            echo "{\"dogLocation\":\"/json/dogsend?hash={$dogSendHash}\"}\n";

        } else {

            echo "{\"error\":\"true\",\"errorMessage\":\"Not authorised to access this resource\"}\n";
        }
    }

    /**
     * Send Deed of Guarantee doc, but only if user logged into Connect and hash
     * is valid.  Seems a bit strong to have such security on a simple doc but
     * it helps enforce each explicit request for the doc is logged.
     */
    public function dogsendAction() {

        // Identify user
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('hl_connect'));
        if ($auth->hasIdentity()) {
            // Fetch user ID
            $userId = $auth->getStorage()->read()->agentid;

            if ($_GET['hash'] == substr(md5($userId . date('Y-m-d') . 'd0gl0g secret string...'), 0, 8)) {
                $params = Zend_Registry::get('params');
                $dogPath = "{$params->connect->basePrivatePath}assets/connect/doc/";

                header('Pragma: '); // Remove pragma
                header('Cache-Control: '); // Remove cache control
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=Deed_Of_Guarantee.doc');
                readfile("{$dogPath}Deed_Of_Guarantee.doc");
            }
        }
    }

     /**
     * Validate and return address list for use via AJAX
     * TODO: This is a duplicate of the json Action in the equivalent
     * json module. Refactor when the json problem is resolved.
     *
     * @return void
     */
    public function getpropertiesAction() {
        $output = array();

        // Filter input
        $inputPostcode = trim(preg_replace('/[^0-9a-z\ ]/i', '', $_POST['postcode']));

        if ($inputPostcode != '') {
            $postcode = new Manager_Core_Postcode();
            $addresses = $postcode->getPropertiesByPostcode($inputPostcode);

            $returnArray = array();
            foreach($addresses as $address) {
                $returnArray[] = array(
                    'addressId' => $address['id'],
                    'addressLine' => $address['singleLineWithoutPostcode']
                );
            }

            if (isset($returnArray[0]['addressId']) && $returnArray[0]['addressId'] != null && $returnArray[0]['addressId'] != '') {
				if (preg_match('/^IM|^GY|^JE/i', $_POST['postcode']) && preg_match('/ins_postcode|property_postcode/', $_POST['inputId'])) {
                        $output['data'] = array();
                        $output['error'] = "Unfortunately we're unable to offer you a policy, as we're unable to provide cover in the Channel Islands or the Isle of Man. If the property you're looking to insure isn't in the Channel Islands or the Isle of Man then please double check the post code you have entered. If you're still experiencing problems, or if you have any further queries or questions, please call us on 0845 117 6000.";
                        $output['restriction'] = 1;                        
                } else {
                    $output['data'] = $returnArray;
                    $output['error'] = '';
                }
            } else {
                $output['data'] = array();
                $output['error'] = 'Can\'t find address';
            }
        } else {
            $output['data'] = array();
            $output['error'] = 'Please enter a valid postcode';
        }

        echo Zend_Json::encode($output);
    }

    public function rgClaimsFileUploaderAction() {

        // Load session data
        $claimReferenceNumber = null;
        $pageSession = new Zend_Session_Namespace('online_claims');
        if (isset($pageSession->ClaimReferenceNumber)) {
             $claimReferenceNumber = $pageSession->ClaimReferenceNumber;

            $upload_handler = new Manager_Insurance_RentGuaranteeClaim_SupportingDocument(
                $claimReferenceNumber,
                $this->_agentSchemeNumber
            );
            header('Pragma: no-cache');
            header('Cache-Control: private, no-cache');
            header('Content-Disposition: inline; filename="files.json"');
            header('X-Content-Type-Options: nosniff');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT, DELETE');
            header('Access-Control-Allow-Headers: X-File-Name, X-File-Type, X-File-Size');
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'OPTIONS':
                    break;
                case 'HEAD':
                case 'GET':
                    echo Zend_Json::encode($upload_handler->getSupportingDocumentList());
                    break;
                case 'POST':
                    echo Zend_Json::encode($upload_handler->saveSupportingDocument());
                    break;
                case 'DELETE':
                    $upload_handler->deleteSupportingDocument();
                    break;
                default:
                    header('HTTP/1.1 405 Method Not Allowed');
            }

        }
    }

    /**
     * Return all entries from referencing.autocomplete_job_titles database
     *
     * @return array (json_encoded)
     */
    public function getAutoCompleteJobTitlesAction()
    {
        // get the data from db
        $em = new Manager_Referencing_AutoCompleteJobTitles();
        $data = $em->findAll();

        $results = array();
        // $data is an array of Model_Referencing_JobTitle objects, delve into each and grab the 'title' property
        foreach ($data as $jobtitle) {
                $results[]['title'] = $jobtitle->getTitle();
                $results[]['fast_track'] = $jobtitle->getFastTrack();
        }

        $this->_helper->json($results);
    }

    /**
     * Return entries from referencing.autocomplete_job_titles database, filtered by 'fast_track' column = true
     *
     * @return array (json_encoded)
     */
    public function getFastTrackAutoCompleteJobTitlesAction()
    {
        // get the data from the db
        $em = new Manager_Referencing_AutoCompleteJobTitles();
        $data = $em->findByFastTrackStatus(true);

        $results = array();
        // $data is an array of Model_Referencing_JobTitle objects, delve into each and grab the 'title' property
        foreach ($data as $jobtitle) {
                $results[] = $jobtitle->getTitle();
        }

        $this->_helper->json($results);   
    }
}