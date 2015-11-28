<?php
class CmsAdmin_ConnectController extends Zend_Controller_Action {
    public function init() {
        // Start the zend layout engine and load the cms admin layout
        Zend_Layout::startMvc();
        $this->_helper->layout->setLayout('default');
    }
    
	/**
     * Does nothing except set the current page.
     */
    public function indexAction() {
        
        $this->view->currentPage = 'connect';
    }
    
    
    /**
     * Controls the MOTD summary page.
     */
    public function motdAction() {
        
        $this->view->currentPage = 'connectMotd';
        
        $motds = new Datasource_Cms_Connect_Motd();
        $motdsArray = $motds->getAll();
        
        
        //Separate the active motd from the inactive.
        $activeMotd = array();
        $inactiveMotds = array();
    
        foreach($motdsArray as $currentMotd) {
            
            if($currentMotd['active'] == 1) {
                
                $activeMotd[] = $currentMotd;
                }
            else {
                
                $inactiveMotds[] = $currentMotd;
            }
        }
        
        
        $this->view->connectMotdActive =
            $this->view->partialLoop('partials/connect-motd-row.phtml', $activeMotd);
        $this->view->connectMotdInactiveList =
            $this->view->partialLoop('partials/connect-motd-row.phtml', $inactiveMotds);
            
        
        $passThrough = $this->_helper->getHelper('FlashMessenger')->getMessages();
        if (count($passThrough)>0) {
            
            if (isset($passThrough[0]['saved'])) {
                
                if ($passThrough[0]['saved'] == true) $this->view->saved=true;
            }
            if (isset($passThrough[0]['deleted'])) {
                
                if ($passThrough[0]['deleted'] == true) $this->view->deleted=true;
            }
            if (isset($passThrough[0]['activated'])) {
                
                if ($passThrough[0]['activated'] == true) $this->view->activated=true;
            }
            if (isset($passThrough[0]['deactivated'])) {
                
                if ($passThrough[0]['deactivated'] == true) $this->view->deactivated=true;
            }
            if (isset($passThrough[0]['errorMessage'])) {
                
                $this->view->errorMessage = $passThrough[0]['errorMessage'];
            }
        }
    }
    
    /**
     * Just call the add function
     *
     * @munt 8 This isn't nice but it avoids routing
     */
    public function upsertMotdAction() {
    	$this->addMotdAction();
    }
    
    /**
     * Responsible for managing the addition and modification of Connect MOTDs.
     */
    public function addMotdAction() {
        
        $this->view->currentPage = 'connect';
        $motdID = $this->getRequest()->getParam('id');
        
        
        if ($this->getRequest()->isPost()) {
            
            //Validate and possibly save form
            if($this->_saveMotd()) {
                
                //Changes saved - so go to MOTD home and provide success message
                $this->_helper->getHelper('FlashMessenger')->addMessage(array('saved' => true));
                $this->_helper->getHelper('Redirector')->goToUrl('/cms-admin/connect/motd?id=' . $motdID);
                return;
        }
            else {
                
                //Display the error message.
                $this->view->errorMessage = 'Data missing or incorrect';
                
                
                //Re-layout from the form
                $upsertType = $this->getRequest()->getParam('upsertType');
                $displayFrom = $this->getRequest()->getParam('displayFrom');
                $displayTo = $this->getRequest()->getParam('displayTo');
                
                
                $agentTypes = array();
                $standard = $this->getRequest()->getParam('standard');
                $premier = $this->getRequest()->getParam('premier');
                if(!empty($standard)) {
                    
                    array_push($agentTypes, $standard);
    }
                if(!empty($premier)) {
                    
                    array_push($agentTypes, $premier);
                }
                //Convert the array to a comma separated string.
                $agentTypes = join(',', $agentTypes);
                
                
                $agentUserTypes = array();
                $basic = $this->getRequest()->getParam('basic');
                $master = $this->getRequest()->getParam('master');
                if(!empty($basic)) {
                    
                    array_push($agentUserTypes, $basic);
                }
                if(!empty($master)) {
                    
                    array_push($agentUserTypes, $master);
                }
                //Convert the array to a comma separated string.
                $agentUserTypes = join(',', $agentUserTypes);
                
                
                $motdTitle = $this->getRequest()->getParam('motdTitle');
                $message = $this->getRequest()->getParam('message');
                $active = $this->getRequest()->getParam('active');
                if(empty($active)) {
                    
                    $active = 0;
                }
                
                $displayWidth = $this->getRequest()->getParam('displayWidth');
            }
        }
        else {
            
            //Load form
            if(empty($motdID)) {
                
                //Layout new
                $upsertType = 'Add';  
                $displayFrom = '';
                $displayTo = '';
                $agentTypes = '';
                $agentUserTypes = '';
                $motdTitle = '';
                $message = '';
                $active = 0;
                
                $params = Zend_Registry::get('params');
                $displayWidth = $params->cms->motdPopupDefaultWidth;
            }
            else {           
                
                //Layout from database
                $upsertType = 'Edit';
                
                $motds = new Datasource_Cms_Connect_Motd();
                $motd = $motds->getByID($motdID);
                
                //Convert the date format.
                $zendDate = new Zend_Date($this->_toUnixTimestamp($motd['displayFrom']));
                $displayFrom = $zendDate->toString('dd/MM/YYYY');
                
                $zendDate = new Zend_Date($this->_toUnixTimestamp($motd['displayTo']));
                $displayTo = $zendDate->toString('dd/MM/YYYY');
                
                $agentTypes = $motd['agentTypes'];
                $agentUserTypes = $motd['agentUserTypes'];
                $motdTitle = $motd['motdTitle'];
                $message = $motd['message'];
                $active = $motd['active'];
                $displayWidth = $motd['displayWidth'];
            }
        }
        
        
        $this->view->upsertType = $upsertType;    
        $this->view->id = $motdID;
        $this->view->displayFrom = $displayFrom;
        $this->view->displayTo = $displayTo;
        $this->view->agentTypes = $agentTypes;
        $this->view->agentUserTypes = $agentUserTypes;
        $this->view->motdTitle = $motdTitle;
        $this->view->message = $message;
        $this->view->active = $active;
        $this->view->displayWidth = $displayWidth;        
        $this->render('upsert-motd');
    }    
    
    
    /**
     * Controls the activation and de-activation of Connect MOTDs. Only one MOTD
     * can be active at any one time. The active MOTD is intended for pop-up
     * display on Connect, but that is another story...
     */
    public function setMotdStatusAction() {
        
        $this->view->currentPage = 'connect';
        $motds = new Datasource_Cms_Connect_Motd();
        
        $motdID = $this->getRequest()->getParam('id');
        $motdStatus = $this->getRequest()->getParam('setMotdStatus');
        if($motdStatus == 1) {
        
            $motds->setActiveStatus($motdID, true);
        }
        else {
            
            $motds->setActiveStatus($motdID, false);
        }
        
        
        //Changes saved - so send them back with a nice success message
        if($motdStatus == 1) {
            
            $this->_helper->getHelper('FlashMessenger')->addMessage(array('activated' => true));
        }
        else {
            
            $this->_helper->getHelper('FlashMessenger')->addMessage(array('deactivated' => true));
        }
        $this->_helper->getHelper('Redirector')->goToUrl('/cms-admin/connect/motd');
    }
    
    
    /**
     * Archives MOTDs. MOTDs are not visible in either the CMS or Connect, but
     * remain in the datastore.
     */
    public function archiveMotdAction() {
        
        $this->view->currentPage = 'connect';
        $motds = new Datasource_Cms_Connect_Motd();
        
        $motdID = $this->getRequest()->getParam('id');
        $motds->setArchivedStatus($motdID, true);
        
        
        //Changes saved - so send them back with a nice success message
        $this->_helper->getHelper('FlashMessenger')->addMessage(array('deleted' => true));
        $this->_helper->getHelper('Redirector')->goToUrl('/cms-admin/connect/motd');
            }
    
    
    /**
     * Responsible for validating and either saving or rejecting new and modified Connect
     * MOTDs.
     */
    protected function _saveMotd() {

        //Prepare the validators.
        $requiredTextValidator = new Zend_Validate_NotEmpty();
        
        $nonEmptyGroupValidator = new Zend_Validate_NotEmptyGroup();
        
        $params = Zend_Registry::get('params');
        $minWidth = $params->cms->motdPopupMinWidth;
        $maxWidth = $params->cms->motdPopupMaxWidth;
        
        $popupWidthValidator = new Zend_Validate();
        $popupWidthValidator->addValidator(new Zend_Validate_NotEmpty());
        $popupWidthValidator->addValidator(new Zend_Validate_Between(array('min' => $minWidth, 'max' => $maxWidth)));
        
        
        //Now filter the $_POST with the validators.
        $validators = array(
            'id'            =>  array('allowEmpty' => true),
            'displayFrom'   =>  $requiredTextValidator,
            'displayTo'     =>  $requiredTextValidator,
            'agentTypes'     =>  array($nonEmptyGroupValidator, 'fields' => array('standard', 'premier')),
            'agentUserTypes' =>  array($nonEmptyGroupValidator, 'fields' => array('basic', 'master')),
            'motdTitle'     =>  $requiredTextValidator,
            'message'       =>  $requiredTextValidator,
            'active'        => $requiredTextValidator,
            'displayWidth'  => $popupWidthValidator
        );
        
        $input = new Zend_Filter_Input(null, $validators, $_POST);
        if ($input->isValid()) {
            
            //Data is all valid, formatted and sanitized so we can save it in the database.
            //Prepare the checkbox data.
            $agentTypes = array();
            if(!empty($input->standard)) {
                
                array_push($agentTypes, $input->standard);
}
            if(!empty($input->premier)) {
                
                array_push($agentTypes, $input->premier);
            }
            
            
            $agentUserTypes = array();
            if(!empty($input->basic)) {
                
                array_push($agentUserTypes, $input->basic);
            }
            if(!empty($input->master)) {
                
                array_push($agentUserTypes, $input->master);
            }
            
            
            $motds = new Datasource_Cms_Connect_Motd();
            if (!$input->id) {
                
                // This is a new user message so we need to create a new ID
                $motdID = $motds->addNew(
                    new Zend_Date(),
                    new Zend_Date($this->_toUnixTimestamp($input->displayFrom)),
                    new Zend_Date($this->_toUnixTimestamp($input->displayTo)),
                    $agentTypes,
                    $agentUserTypes,
                    $input->motdTitle,
                    $input->message,
                    $input->active,
                    $input->displayWidth,
					false
                );
            }
            else {
                
                // This is an existing article so we can just update the data
				$existingMotd = $motds->getByID($input->id);
				
                $motdID = $input->id;
                $motds->saveChanges(
                    $input->id,
                    new Zend_Date(),
                    new Zend_Date($this->_toUnixTimestamp($input->displayFrom)),
                    new Zend_Date($this->_toUnixTimestamp($input->displayTo)),
                    $agentTypes,
                    $agentUserTypes,
                    $input->motdTitle,
                    $input->message,
                    $input->active,
                    $input->displayWidth,
					($existingMotd['isArchived'] == 1) ? true : false
                );
            }
            $returnVal = true;
        }
        else {
            
            // Invalid data in form
            $returnVal = false;
        }
        
        return $returnVal;
    }
    
    
    /**
     * Controls the Connect blog summary page.
     */
    public function blogAction() {
        
        $this->view->currentPage = 'connect';
        
        $blogEntries = new Datasource_Cms_Connect_BlogEntries();
        $blogEntriesArray = $blogEntries->getAll();
        

        $mainBlogEntry = array();
        $summaryBlogEntries = array();
        $poolBlogEntries = array();
        
        foreach($blogEntriesArray as $currentBlogEntry) {
            
            if($currentBlogEntry['status'] == 1) {
                
                $mainBlogEntry[] = $currentBlogEntry;
            }
            else if($currentBlogEntry['status'] == 2) {
                
                $summaryBlogEntries[] = $currentBlogEntry;
            }
            else {
                
                $poolBlogEntries[] = $currentBlogEntry;
            }
        }
        
        
        $this->view->connectBlogMain =
            $this->view->partialLoop('partials/connect-blog-entry-row.phtml', $mainBlogEntry);
            
        $this->view->connectBlogSummary =
            $this->view->partialLoop('partials/connect-blog-entry-row.phtml', $summaryBlogEntries);
            
        $this->view->connectBlogPool =
            $this->view->partialLoop('partials/connect-blog-entry-row.phtml', $poolBlogEntries);
            
        
        $passThrough = $this->_helper->getHelper('FlashMessenger')->getMessages();
        if (count($passThrough)>0) {
            
            if (isset($passThrough[0]['saved'])) {
                
                if ($passThrough[0]['saved'] == true) $this->view->saved=true;
            }
            if (isset($passThrough[0]['deleted'])) {
                
                if ($passThrough[0]['deleted'] == true) $this->view->deleted=true;
            }
            if (isset($passThrough[0]['statusChanged'])) {
                
                if ($passThrough[0]['statusChanged'] == true) $this->view->statusChanged=true;
            }
            if (isset($passThrough[0]['errorMessage'])) {
                
                $this->view->errorMessage = $passThrough[0]['errorMessage'];
            }
        }
    }
    
    
    /**
     * Controls the status of Connect blog entries. Blog entries can be 'main', 'summary'
     * or 'pool'.
     */
    public function setBlogEntryStatusAction() {
        
        $this->view->currentPage = 'connect';
        $blogEntries = new Datasource_Cms_Connect_BlogEntries();
        
        $blogEntryID = $this->getRequest()->getParam('id');
        $blogEntryStatus = $this->getRequest()->getParam('setBlogEntryStatus');
        $blogEntries->setStatus($blogEntryID, $blogEntryStatus);
        
        
        //Changes saved - so send them back with a nice success message            
        $this->_helper->getHelper('FlashMessenger')->addMessage(array('statusChanged' => true));
        $this->_helper->getHelper('Redirector')->goToUrl('/cms-admin/connect/blog');
    }
    
    
    /**
     * Archives blog entries. Blog entries are not visible in either the CMS
     * or Connect, but remain in the datastore.
     */
    public function archiveBlogEntryAction() {
        
        $this->view->currentPage = 'connect';
        $blogEntries = new Datasource_Cms_Connect_BlogEntries();
        
        $blogEntryID = $this->getRequest()->getParam('id');
        $blogEntries->setArchivedStatus($blogEntryID, true);
        
            // Changes saved - so send them back with a nice success message
        $this->_helper->getHelper('FlashMessenger')->addMessage(array('deleted' => true));
        $this->_helper->getHelper('Redirector')->goToUrl('/cms-admin/connect/blog');
    }
    
    /**
     * Just call the upsert function
     *
     * @munt 8 This isn't nice but it avoids routing
     */
    public function addBlogEntryAction() {
    	$this->upsertBlogEntryAction();
    	$this->render('upsert-blog-entry');
    }
    
    /**
     * Responsible for managing the addition and modification of Connect blog entries.
     */
    public function upsertBlogEntryAction() {

        $this->view->currentPage = 'connect';
        $blogEntryID = $this->getRequest()->getParam('id');
        
        $blogEntries = new Datasource_Cms_Connect_BlogEntries();
        if ($this->getRequest()->isPost()) {
            
            //Validate and possibly save form
            if($this->_saveBlogEntry()) {
                
                //Changes saved - redirect to the main BLOG page.
            $this->_helper->getHelper('FlashMessenger')->addMessage(array('saved' => true));
                $this->_helper->getHelper('Redirector')->goToUrl('/cms-admin/connect/blog');
                return;
            }
            else {
                
                //Display the error message.
                $this->view->errorMessage = 'Data missing or incorrect';
                
                
                //Re-layout from the form
                $upsertType = $this->getRequest()->getParam('upsertType');
                $title = $this->getRequest()->getParam('title');
                $summary = $this->getRequest()->getParam('summary');
                $article = $this->getRequest()->getParam('article');
                $status = $this->getRequest()->getParam('status');
                
                $imageName = $this->getRequest()->getParam('imageName');
                if(!empty($imageName)) {
                    
                    $params = Zend_Registry::get('params');
                    $imageToDisplay = $params->cms->imageDisplayPath . $imageName;
                }
                else {
                    
                    $imageToDisplay = $this->getRequest()->getParam('articleIcon');
                }
                
                $tagString = $this->getRequest()->getParam('tagString');
            }
        }
        else {
        
            //Load form
            if(empty($blogEntryID)) {
                
                //Layout new
                $upsertType = 'Add';
                $title = '';
                $summary = '';
                $article = '';
                $status = 3; //Pool blog entry
                $imageToDisplay = '';
                $imageName = '';
                $tagString = '';
            }
            else {
                
                //Layout from database
                $upsertType = 'Edit';
    
                $blogEntry = $blogEntries->getByID($blogEntryID);
            
                $title = $blogEntry['title'];
                $summary = $blogEntry['summary'];
                $article = $blogEntry['article'];
                $status = $blogEntry['status'];
                
                if(!empty($blogEntry['imageName'])) {
                    
                    $params = Zend_Registry::get('params');
                    $imageToDisplay = $params->cms->imageDisplayPath . $blogEntry['imageName'];
                    $imageName = $blogEntry['imageName'];
                }
                else {
                    
                    $imageToDisplay = '';
                    $imageName = '';
                }
                $tagString = $blogEntry['tagString'];
            }
        }
        
        //Load the possible blog tags from the database in a comma-separated string.
        $this->view->blogTags = $blogEntries->getPossibleTags();
        
        $this->view->upsertType = $upsertType;
        $this->view->id = $blogEntryID;
        $this->view->title = $title;
        $this->view->summary = $summary;
        $this->view->article = $article;
        $this->view->status = $status;
        $this->view->imageToDisplay = $imageToDisplay;
        $this->view->imageName = $imageName;
        $this->view->tagString = $tagString;
    }
    
    
    /**
     * Responsible for validating and either saving or rejecting new and modified Connect
     * blog entries.
     */
    protected function _saveBlogEntry() {
              
        //First perform the image upload validation.
        $params = Zend_Registry::get('params');
        $upload = new Zend_File_Transfer_Adapter_Http();
        $upload->setDestination($params->cms->imageUploadPath);
        $upload->addValidator('Extension', false, $params->cms->imageAllowedTypes);
        $upload->addValidator('Count', false, $params->cms->imageNumberAllowed);
        $upload->addValidator('Size', false, $params->cms->imageMaxUploadSize);
        
        /*
        $upload->addValidator('ImageSize', false, array(
            'minwidth' => $params->cms->imageMinWidth,
            'maxwidth' => $params->cms->imageMaxWidth,
            'minheight' => $params->cms->imageMinHeight,
            'maxheight' => $params->cms->imageMaxHeight
        ));
        */
        
        
        //Call isUploaded(), which will return true if the user has uploaded an image.
        if($upload->isUploaded()) {
            
            if ($upload->receive()) {
                
                //The file has been uploaded succesfully. Make a note of its uploaded location
                //so that we can write this to the database.
                $this->view->uploadSuccess=true;
                $imageName = $upload->getFileName(null, false);
            }
            else {
                
                //Communicate image upload failure
                return false;
            }
        }
        
        
        //Validate the rest of the inputs.
        $requiredText = new Zend_Validate();
        $requiredText->addValidator(new Zend_Validate_NotEmpty());
        
        $validators = array(
            'id' => array('allowEmpty' => true),
            'title' => $requiredText,
            'summary' => $requiredText,
            'article' => $requiredText,
            'status' => $requiredText,
            'imageName' => array('allowEmpty' => true),
            'tagString' => array('allowEmpty' => true)
        );
        
        $input = new Zend_Filter_Input(null, $validators, $_POST);
        if ($input->isValid()) {
            
            //Determine if we are using the newly uploaded image (if any), or if we are
            //preserving a previously uploaded iimage (if any).
            $imageNameToSave = null;
            if(!empty($imageName)) {
                
                //Newly uploaded
                $imageNameToSave = $imageName;
            }
            else if(!empty($input->imageName)) {
                
                //Previously uploaded
                $imageNameToSave = $input->imageName;
            }
            
            
            //Prepare the tags.
            if(empty($input->tagString)) {
                
                $blogEntryTags = null;
            }
            else {
                
                $blogEntryTags = explode(',', $input->tagString);
                
                //Ensure no 'empty string' tags are added to the datasource.
                $cleanedTags = array();
                foreach($blogEntryTags as $currentTag) {
                    
                    if(empty($currentTag)) {
                        
                        continue;
                    }
                    if(preg_match("/^\s+$/", $currentTag)) {
                        
                        continue;
                    }
                    $cleanedTags[] = trim($currentTag);
                }
                $blogEntryTags = $cleanedTags;
            }
            
            
            //Now save to the database
            $blogEntries = new Datasource_Cms_Connect_BlogEntries();
            if (!$input->id) {
                
                // This is a new user message so we need to create a new ID
                $blogEntryID = $blogEntries->addNew(
                    new Zend_Date(),
                    $input->title,
                    $input->summary,
                    $input->article,
                    $input->status,
                    $imageNameToSave,
                    $blogEntryTags
                );
            }
            else {
                
                // This is an existing article so we can just update the data
                $blogEntryID = $input->id;
                $blogEntries->saveChanges(
                    $input->id,
                    new Zend_Date(),
                    $input->title,
                    $input->summary,
                    $input->article,
                    $input->status,
                    $imageNameToSave,
                    $blogEntryTags
                );
            }
            
            $returnVal = true;
        }
        else {
            
            // Invalid data in form
            $returnVal = false;
        }
        
        return $returnVal;
    }

	/**
     * Utility function which converts date strings to UNIX timestamps.
     *
     * Built to avoid the regex error that occurs with the perl-compatible
     * regexs used by Zend_Date. Delete this function once these regex errors
     * have been resolved.
     *
     * @param string $dateString
     * Currently supported formats are: 'yyyy-mm-dd', or 'dd/mm/yyyy'
     *
     * @return int
     * Returns the Unix timestamp derived from the date string passed in.
     *
     * @throws Exception
     * Throws an Exception if the $dateString is an unrecognised format.
     */
    protected function _toUnixTimestamp($dateString)
    {
        if(preg_match("/-/", $dateString)) {
            
            $dateArray = explode('-', $dateString);
            $date = date("U", mktime(0, 0, 0, $dateArray[1], $dateArray[2], $dateArray[0]));
}
        else if(preg_match("/\//", $dateString)) {
            
            $dateArray = explode('/', $dateString);
            $date = date("U", mktime(0, 0, 0, $dateArray[1], $dateArray[0], $dateArray[2]));            
        }
        else {
            
            throw new Zend_Exception("Unsupported date format.");
            }
        
        return $date;
    }
}
?>
