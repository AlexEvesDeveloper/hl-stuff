<?php
/**
 * Business rules class which provides rent guarantee claim services.
 */
class Manager_Insurance_RentGuaranteeClaim_Claim {

    protected $_onlineclaimModel;
    private $_params;

    /**
     * Returns partial claim.
     *
     * This method will retrieve partial claim information stored in the database
     *
     * @param int $agentschemeno
     *
     * @return Datasource_Insurance_RentGuaranteeClaim_Claim
     *
     * Returns this object populated with relevant information, or empty array
     * if no relevant information has been stored.
     */

    public function fetchPartialClaim($agentschemeno) {

        if(empty($this->_onlineclaimModel)) {

            $this->_onlineclaimModel = new Datasource_Insurance_RentGuaranteeClaim_Claim();
        }

        return $this->_onlineclaimModel->getPartialClaim($agentschemeno);
    }

    /**
     * Fetches all untransferred but data complete claims
     *
     * @param void
     * @return array Datasource_Insurance_RentGuaranteeClaim_Claim || array()
     *
     */
    public function fetchDataCompleteClaimsIds()
    {
        if(empty($this->_onlineclaimModel)) {

            $this->_onlineclaimModel = new Datasource_Insurance_RentGuaranteeClaim_Claim();
        }

        return $this->_onlineclaimModel->getDataCompleteClaimsIds();
    }

    /**
     * Inserts a new claim.
     *
     * @param int $agentID, int $agentSchemeNumber
     *
     * @return Model_Insurance_OnlineClaims_Claim
     */

    public function createNewClaim($agentId, $agentSchemeNumber) {

        if(empty($this->_onlineclaimModel)) {

            $this->_onlineclaimModel = new Datasource_Insurance_RentGuaranteeClaim_Claim();
        }

        //Encapsulate claim And return Object
        $referenceNumber = $this->_onlineclaimModel->insertClaim($agentId, $agentSchemeNumber);
        $claim = new Model_Insurance_RentGuaranteeClaim_Claim();
        $claim->setReferenceNumber($referenceNumber);
        return $claim;
    }

    /**
     * Updates a claim.
     *
     * @param $claim Array
     *
     * @return void
     */

    public function updateClaim($claim){

        if(empty($this->_onlineclaimModel)) {

            $this->_onlineclaimModel = new Datasource_Insurance_RentGuaranteeClaim_Claim();
        }
        return $this->_onlineclaimModel->updateClaim($claim);
    }

    /**
     * Retrieves the specified claim record from the database.
     *
     * @return param $referenceNumber
     * Identifies the claim record in the online_claims table.
     * @param int $agentSchemeNumber
     * Identifies the agent to prevent horizontal privledge escalation
     * 
     * @return Model_Insurance_RentGuaranteeClaim_Claim
     * The claim details encapsulated in a Claim object.
     *
     */

    public function getClaim($referenceNumber,$agentSchemeNumber){

        if(empty($this->_onlineclaimModel)) {

            $this->_onlineclaimModel = new Datasource_Insurance_RentGuaranteeClaim_Claim();
        }
        return $this->_onlineclaimModel->getClaim($referenceNumber,$agentSchemeNumber);
    }

    
    public function getKHClaim($referenceNumber){

        if(empty($this->_onlineclaimModel)) {

            $this->_onlineclaimModel = new Datasource_Insurance_RentGuaranteeClaim_Claim();
        }
        return $this->_onlineclaimModel->getClaim($referenceNumber);
    }
    
    
    /**
     * Get keyhouse unique claim number
     *
     * @param int $referenceNumber
     *
     * @return claim number
     */
    public function getKHClaimNumber($referenceNumber) {
        $_khValidationModel = new Datasource_Insurance_RentGuaranteeClaim_KeyhouseValidation();
        return $_khValidationModel->getKHClaimNumber($referenceNumber);
    }

    /**
     * Gets all the guarantors for the given Claim Reference Number
     *
     * @param int $referenceNumber
     *
     * @return Array
     */
    public function getGuarantorsByReferenceNumber($referenceNumber){
        $dsGuarantors = new Datasource_Insurance_RentGuaranteeClaim_Guarantor();
        $guarantors = $dsGuarantors->getGuarantors($referenceNumber);
        return $guarantors;
    }

    /**
     * Gets all the Tenants for the given Claim Reference Number
     *
     * @param int $referenceNumber
     *
     * @return array
     */
    public function getTenantsByReferenceNumber($referenceNumber){
        $dsTenants = new Datasource_Insurance_RentGuaranteeClaim_Tenant();
        $tenants = $dsTenants->getTenants($referenceNumber);
        return $tenants;
    }

    /**
     * Gets all the RentPayments for the given Claim Reference Number
     *
     * @param int $referenceNumber
     *
     * @return array
     */
    public function getRentPaymentsByReferenceNumber($referenceNumber){
        $dsRentPayments = new Datasource_Insurance_RentGuaranteeClaim_RentalPayment();
        $rentPayments = $dsRentPayments->getRentPaymentsByReferenceNumber($referenceNumber);
        return $rentPayments;
    }

    /**
     * Gets all the SupportingDocuments for the given Claim Reference Number
     *
     * @param int $referenceNumber
     *
     * @return array
     *
     * Array of SupportingDocuments
     */
    public function getSupportingDocumentsByReferenceNumber($referenceNumber) {
        try {
            $supportingDocumentModel = new Datasource_Insurance_RentGuaranteeClaim_SupportingDocuments();
            $supportingDocuments = $supportingDocumentModel->getByReferenceNumber($referenceNumber);
            return $supportingDocuments;
        } catch (Zend_Exception $e) {
            throw new Zend_Exception('Couldn\'t getSupportingDocumentsByReferenceNumber(): ' . $e->getMessage());
        }
    }

    /**
     *
     * Delete claim details for the given Claim Reference Number
     *
     * @param int $referenceNumber
     *
     * @param int $agentSchemeNum
     *
     * @return void
     */
    public function deleteClaim($referenceNumber, $agentSchemeNum) {

        $dsGuarantors = new Datasource_Insurance_RentGuaranteeClaim_Guarantor();
        $dsRentPayments = new Datasource_Insurance_RentGuaranteeClaim_RentalPayment();
        $dsTenants = new Datasource_Insurance_RentGuaranteeClaim_Tenant();
        $dsSupportingDocuments = new Datasource_Insurance_RentGuaranteeClaim_SupportingDocuments();
        $dsKeyHouseValidation = new Datasource_Insurance_RentGuaranteeClaim_KeyhouseValidation();

        $dsGuarantors->removeGuarantors($referenceNumber);
        $dsRentPayments->deleteByReferenceNumber($referenceNumber);
        $dsTenants->removeTenants($referenceNumber);
        $dsSupportingDocuments->deleteByReferenceNumber($referenceNumber);
        $dsKeyHouseValidation->deleteByReferenceNumber($referenceNumber);

        if(empty($this->_onlineclaimModel)) {
            $this->_onlineclaimModel = new Datasource_Insurance_RentGuaranteeClaim_Claim();
        }
        $this->_onlineclaimModel->deleteClaim($referenceNumber);
        //remove the supporting documents files
        $supportDocManager = new Manager_Insurance_RentGuaranteeClaim_SupportingDocument(
            $referenceNumber,
            $agentSchemeNum
        );
        $docPath = $supportDocManager->getPath();//."/".$agentSchemeNum."/".$referenceNumber;
        if(file_exists($docPath)){
            rmdir($docPath);
        }
    }

    /**
    * Set the address for specified postcode
    *
    * @param array $data, String $type
    *
    * @return array of housename, street, city, town
    *
    */
    public function getPropertyAddress($data, $type) {

        $coreAddressManager = new Manager_Core_Postcode();
        $returnArrayValue = array();
        // If there is an address id and a list of properties from post code,
        // we go and get a property by its ID
        if(isset($data[$type.'_address']) && $data[$type.'_address'] != '' && $data[$type.'_address'] != '-') {
            $getAddress = $coreAddressManager->getPropertyByID($data[$type.'_address']);
            $streetLine = '';
            if ($getAddress['buildingName']) {
                $streetLine .= ucwords(strtolower($getAddress['buildingName'])) . ', ';
            }
            if ($getAddress['address1']) {
                $streetLine .= ucwords(strtolower($getAddress['address1'])) . ', ';
            }
            if ($getAddress['address2']) {
                $streetLine .= ucwords(strtolower($getAddress['address2'])) . ', ';
            }
            if ($getAddress['address3']) {
                $streetLine .= ucwords(strtolower($getAddress['address3'])) . ', ';
            }
            if ($getAddress['address4']) {
                    //$streetLine .= ucwords(strtolower($getAddress['address4'])) . ', ';
            }
            if ($getAddress['address5']) {
                    //$streetLine .= ucwords(strtolower($getAddress['address5'])) . ', ';
            }
            $returnArrayValue = array(
                $type.'_housename' => $getAddress['houseNumber'],
                $type.'_street' => $streetLine,
                $type.'_town' => $getAddress['address4'],
                $type.'_city' => $getAddress['address5'],
                $type.'_postcode' => $getAddress['postcode']
            );
        } else {
            $returnArrayValue = array(
                $type.'_housename'  => $data[$type.'_housename'],
                $type.'_street'  => $data[$type.'_street'],
                $type.'_town' => $data[$type.'_town'],
                $type.'_city' => $data[$type.'_city'],
                $type.'_postcode' => $data[$type.'_postcode']
            );
        }
        return $returnArrayValue;
    }

    /**
     *
     * @param unknown_type $input
     */
    public function concatDetails($input) {
        return implode(', ', array_filter($input));
    }

     /**
     * Uploads all attachments provided.
     *
     * @param int $claimRefNo
     * @param int $agentSchemeNumber
     *
     * The unique claim identifier
     *
     * @return boolean
     * Returns true on successful upload, false otherwise.
     */
    public function addAttachments($claimRefNo,$agentSchemeNumber) {

        $temporaryAttachmentFolder = APPLICATION_PATH."/../private/uploads/rentguaranteeclaims/";
        if(!file_exists($temporaryAttachmentFolder)) {
            mkdir($temporaryAttachmentFolder, 0777);
        }
        if(!file_exists($temporaryAttachmentFolder."emailhandler/")) {
            mkdir($temporaryAttachmentFolder."emailhandler/", 0777);
        }
        $temporaryAttachmentFolder = $temporaryAttachmentFolder."emailhandler/";
        if(!file_exists($temporaryAttachmentFolder.$agentSchemeNumber."/")) {
            mkdir($temporaryAttachmentFolder.$agentSchemeNumber."/", 0777);
        }
        $temporaryAttachmentFolder = $temporaryAttachmentFolder.$agentSchemeNumber."/";
        if(!file_exists($temporaryAttachmentFolder.$claimRefNo."/")) {
            mkdir($temporaryAttachmentFolder.$claimRefNo."/", 0777);
        }
        $temporaryAttachmentFolder = $temporaryAttachmentFolder.$claimRefNo."/";
        if(!file_exists($temporaryAttachmentFolder)) {
            mkdir($temporaryAttachmentFolder, 0777);
        }

        //Upload the attachment(s).
        $upload = new Zend_File_Transfer_Adapter_Http();
        $upload->setDestination($temporaryAttachmentFolder);
        $upload->addValidator('Size', false, 4194304);

        //Call isUploaded(), which will return true if the user has uploaded an attachment.
        $returnVal = false;
        if($upload->isUploaded()) {
            if ($upload->receive()) {
                //The attachment has been uploaded succesfully.
                $returnVal = true;
            }
        }
        return $returnVal;
    }

     /**
     * Gives basic details of all attachments provided.
     *
     * @param int $claimRefNo The unique claim number.
     * @param int $agentSchemeNumber
     *
     * @return array List of filename => filesize (in bytes) pairs
     */
    public function detailAttachments($claimRefNo,$agentSchemeNumber) {
        $returnVal = array();
        //Locate the temporary folder for this claim, if one exists. Note that
        //the temporary folder incorporates the ASN.
        $temporaryAttachmentFolder = APPLICATION_PATH."/../private/uploads/rentguaranteeclaims/emailhandler/".$agentSchemeNumber."/".$claimRefNo."/";
        if(!file_exists($temporaryAttachmentFolder)) {
            mkdir($temporaryAttachmentFolder, 0777);
        }
         if (file_exists($temporaryAttachmentFolder)) {
            // Read file names and sizes
            if ($handle = opendir($temporaryAttachmentFolder)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != '.' && $file != '..') {
                        $returnVal["{$temporaryAttachmentFolder}/{$file}"] = filesize("{$temporaryAttachmentFolder}/{$file}");
                    }
                }
                closedir($handle);
            }
        }
        return $returnVal;
    }


     /**
     * Sends an email to 'Claim Handler' with attachments
     *
     * @param int $claimRefNo
     * @param String $content
     * @param int $agentSchemeNumber
     * @param int $attachment
     * @param String $agentEmail
     * Provides support for attachments, automatically including any uploaded by the
     * reference subject.
     *
     * @return boolean
     * Returns true on successful send, false otherwise.
     */
    public function notifyEmailHandlerWithAttachments($claimRefNo,$content,$agentSchemeNumber,$attachment,$agentEmail) {

        // Fetch params from registry for default From address
        $this->_params = Zend_Registry::get('params');

        $emailer = new Application_Core_Mail();
        $keyHouseClaimManager   =   new Datasource_Insurance_KeyHouse_Claim();
        $keyHouseClaimManager   =   $keyHouseClaimManager->getClaim($claimRefNo, $agentSchemeNumber);
        $getClaimHandler    =   $keyHouseClaimManager[0]['ClaimsHandlerEmail'];

        $emailer->setTo($getClaimHandler, 'KHDBView');
        $emailer->setFrom($this->_params->homelet->defaultEmailAddress, 'Online Claims');
        $emailer->setSubject('ASN:'.$agentSchemeNumber.';'.'Claim Ref:'.$claimRefNo);
        $setBodyContent ='';
        $setBodyContent .= "ASN: {$agentSchemeNumber} \r\n\r\n";
        $setBodyContent .= "Claim Ref No: {$claimRefNo} \r\n\r\n";
        $setBodyContent .= "Contact e-mail: {$agentEmail}\r\n\r\n";
        $setBodyContent .= $content;
        $emailer->setBodyText($setBodyContent);
        $claimDir   =   explode('/',$claimRefNo);
        if($attachment ==1) {
            // Attach all files from detailAttachments
            foreach (array_keys($this->detailAttachments($claimDir[1],$agentSchemeNumber)) as $filename) {
                $emailer->addAttachment($filename, substr($filename, strrpos($filename, '/') + 1));
            }
        }

        // Send and set returnval
        $success = $emailer->send();
        if($success) {
              $returnVal = true;
        }
        else {
            $returnVal = false;
        }
        return $returnVal;
    }

     /**
     * Deletes all attachments previously provided, by deleting
     * the relevant temporary attachment folder.
     *
     * @param mixed $claimRefNo
     * @param int $agentSchemeNumber
     *
     * @return boolean
     * Returns true on successful deletion, false otherwise.
     */
    public function deleteAttachments($claimRefNo,$agentSchemeNumber) {

        //Locate the temporary folder for this Enquiry, if one exists. Note that
        //the temporary folder incorporates the IRN in its name.
        $temporaryAttachmentFolder = APPLICATION_PATH."/../private/uploads/rentguaranteeclaims/";
        $temporaryAttachmentEmailHandlerFolder  =   $temporaryAttachmentFolder."emailhandler/";
        $temporaryAttachmentASNFolder   =  $temporaryAttachmentEmailHandlerFolder.$agentSchemeNumber."/";
        $temporaryAttachmentClaimRefFolder  =   $temporaryAttachmentASNFolder.$claimRefNo."/";
        if (file_exists($temporaryAttachmentClaimRefFolder)) {
            // Empty contents of directory
            if ($handle = opendir($temporaryAttachmentClaimRefFolder)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != '.' && $file != '..') {
                        unlink("{$temporaryAttachmentClaimRefFolder}/{$file}");
                    }
                }
                closedir($handle);
            }
            // Delete directory
            $returnVal = rmdir($temporaryAttachmentClaimRefFolder);
            $returnVal = rmdir($temporaryAttachmentASNFolder);
        }
        else {

            //No files to delete. As there are no attachments
            //the server, return true to put the client code at ease.
            $returnVal = true;
        }

        return $returnVal;
    }

    /**
     * Insert an claim details into a PDF.
     *
     * @param int $refNum
     *
     * @return void
     */
    public function populateAndOutputClaimFaxHeader($refNum)
    {
        $this->_params = Zend_Registry::get('params');
        $claimPDFMerge = new Application_Core_PdfMerge(
            substr($this->_params->connect->basePublicPath, 0, -1)
            . $this->_params->connect->rentGuaranteeFaxheaderPdfPublicPath,
            "FaxHeader.pdf"
        );
        $claimPDFMerge->pdfPageStyle->setFont(
            Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA),
            10 // <--- Font size in points
        );
        $claimData = $this->getClaim($refNum);
        $agentName = $claimData->getAgentName();
        $agentContactName = $claimData->getAgentContactName();
        $agentTelephone = $claimData->getAgentTelephone();

        // Page 0 has special placement requirements
        $textPlacements = array();
        // Claim Number
        /*$textPlacements[] = new Model_Core_Pdf_Element(
	        0,
	        $refNum,
	        85,
	        105,
	        null,
	        null,
	        true,
	        178
        );*/
        // Agent name
        $textPlacements[] = new Model_Core_Pdf_Element(
            0,
            $agentName,
            135,
            199,
            null,
            null,
            true,
            178
        );
        // Agent contact name
        $textPlacements[] = new Model_Core_Pdf_Element(
            0,
            $agentContactName,
            135,
            241,
            null,
            null,
            true,
            178
        );
        // Agent telephone
        $textPlacements[] = new Model_Core_Pdf_Element(
            0,
            $agentTelephone,
            135,
            284
        );
        $claimPDFMerge->merge($textPlacements);
        $claimPDFMerge->output('browser');
    }

     /**
     * To remove temporary stored PDF files
     *
     * @param array $fileArray
     *
     * @return void
     */
    public function garbageCollect($fileArray = array()) {

        // Remove generated and sent files
        foreach ($fileArray as $file) {
            @unlink($file);
        }
        // Random garbage collection of other (> 24 hours old) files 5% of the time
        if (mt_rand(1, 100) <= 5) {
            $timeNow = time();
            clearstatcache();
            $tempDir = $this->_params->connect->tempPrivatePath;
            $dh = @opendir($tempDir);
            // Loop through the directory
            while (false !== ($file = readdir($dh))) {
                // Only look for files this class will have created
                if (substr($file, 0, 10) == 'FaxHeader_' && substr($file, -4, 4) == '.pdf') {
                    // Check its age vs now, more than 24 hours?
                    $fileModTime = filemtime("{$tempDir}{$file}");
                    if ($timeNow - $fileModTime > 24 * 60 * 60) {
                        @unlink("{$tempDir}{$file}");
                    }
                }
            }
        }
    }

    /**
    *   To send a confirmation email after the completion of step4
    *
    *   @return void
    */
    public function sendConfirmationEmail($refNumber, $khClaimNumber) {

        // get agent name and claim number to send an confirmation email
        $claimData = $this->getClaim($refNumber);

        $emailer = new Application_Core_Mail();
        $emailer->setTo($claimData->getAgentEmail(), $claimData->getAgentName());
        $emailer->setFrom('claim@homelet.co.uk', 'Homelet Claim Submission');
        $emailer->setSubject('ASN: '.$claimData->getAgentSchemeNumber().'; Claim Ref: '.$khClaimNumber);
        $metaData = array(
            'agentname'         => $claimData->getAgentName(),
            'propertyAddress'   => $claimData->getTenancyAddress(),
            'claimNumber'       => $khClaimNumber
        );
        $emailer->applyTemplate('connect_claimSubmitConfirmation', $metaData, false, '/email-branding/homelet/generic-with-signature-footer.phtml');
        return $emailer->send();
    }

}

?>
