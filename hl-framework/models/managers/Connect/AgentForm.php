<?php

/**
 * Manager class responsible for performing content injection into pre-defined
 * agent PDFs.  Provides equivalent functionality to agentForms.php in legacy
 * PHP4 Connect.
 *
 * @category   Manager
 * @package    Manager_Connect
 * @subpackage AgentForm
 */
class Manager_Connect_AgentForm {

    private $_params;
    private $_pdfName;
    private $_pdfMerge;

    /**
     * Constructor
     *
     * @param string $formName Optional form name.
     *
     * return void
     */
    public function __construct($formName = null) {

        $this->_params = Zend_Registry::get('params');

        if (!is_null($formName)) {
            $this->setForm($formName);
        }

    }

    /**
     * Opens a PDF from local storage, populates it with agent details (if
     * needed) and outputs it to either browser or by e-mail.
     *
     * @param string $formName The name of the PDF form, or 'all' for all by e-mail.
     * @param mixed $asn Agent scheme number of agent whose details are to be inserted.
     * @param int $agentUserId Optional user ID - needed for e-mailing forms.
     * @param string $destination Optional output mechanism, if set should be 'browser' or not 'browser'.
     * @param mixed $refno Optional reference number, for a special case PDF that requires applicant data injection.
     */
    public function populateAndOuput($formName, $asn, $agentUserId = null, $destination = 'browser', $refno = null) {

        $attachmentList = array();

        switch($formName) {

            // Forms that require agent details to be injected
            case 'Agent-Company':
            case 'Agent-Guarantor':
            case 'Agent-Individual':
            case 'Agent-Student-guarantor':
            case 'Agent-Unemployed-guarantor':

                // Instantiate agent manager and fetch agent details
                $agentManager = new Manager_Core_Agent();
                $agent = $agentManager->getAgent($asn);

                // Shove agent details through form
                $this->setForm($formName);
                $this->agentPopulate($agent);
                // For "Print Guarantor Form" from ref summary screen:
                if (!is_null($refno)) {
                    // Fetch reference by refno using the Referencing MUNT Manager class
                    $refMuntManager = new Manager_ReferencingLegacy_Munt();
                    $reference = $refMuntManager->getReference($refno);
                    // For safety, ensure reference belongs to this ASN before injecting applicant details
                    if ($reference->customer->customerId == $asn) {
                        $this->applicantPopulate($reference);
                    }
                }
                if ($destination == 'browser') {
                    $this->output('browser');
                } else {
                    $attachmentList[$formName] = $this->output('file');
                }
                break;

            // Forms that are a pass-through
            case 'Tenant-Declaration':
            case 'Guarantor-Declaration':

                $this->setForm($formName);
                if ($destination == 'browser') {
                    $this->output('browser');
                } else {
                    $attachmentList[$formName] = $this->output('file');
                }
                break;

            // Send all forms - by e-mail only
            case 'all':

                // Instantiate agent manager and fetch agent details
                $agentManager = new Manager_Core_Agent();
                $agent = $agentManager->getAgent($asn);

                // Generate those needing agent data merged in
                foreach (array(
                    'Agent-Company',
                    'Agent-Guarantor',
                    'Agent-Individual',
                    'Agent-Student-guarantor',
                    'Agent-Unemployed-guarantor'
                ) as $thisFormName) {
                    $this->setForm($thisFormName);
                    $this->agentPopulate($agent);
                    $attachmentList[$thisFormName] = $this->output('file');
                }

                // Generate straight throughs
                foreach (array(
                    'Tenant-Declaration',
                    'Guarantor-Declaration'
                ) as $thisFormName) {
                    $this->setForm($thisFormName);
                    $attachmentList[$thisFormName] = $this->output('file');
                }
                break;
        }

        // If there are attachments, this is/these are to be sent by e-mail
        if (count($attachmentList) > 0) {

            // Instantiate agent user manager to get name and e-mail address
            $agentUserManager = new Manager_Core_Agent_User();
            $agentUser = $agentUserManager->getUser($agentUserId);

            // Generate e-mail
            $mailer = new Application_Core_Mail();
            $mailer->setTo($agentUser->email->emailAddress, $agentUser->name);
            // TODO: Parameterise:
            $mailer->setFrom('faxref@ref.homelet.com', 'HomeLet Referencing');
            $mailer->setSubject('HomeLet Referencing Application Form');
            $mailer->setBodyText('Please find your HomeLet referencing application forms attached.');
            foreach ($attachmentList as $name => $location) {
                $mailer->addAttachment($location, "{$name}.pdf");
            }
            $mailer->send();

            // Garbage collection
            $this->garbageCollect($attachmentList);
        }
    }

    public function setForm($formName) {
        $this->_pdfName = $formName;

        $this->_pdfMerge = new Application_Core_PdfMerge(
            substr($this->_params->connect->basePublicPath, 0, -1) . $this->_params->connect->refAppPdfPublicPath,
            "{$formName}.pdf"
        );
    }



    /**
     * Insert an agent's details into a PDF.
     *
     * @param Model_Core_Agent $agent An object representing the agent whose
     * data is to be inserted.
     *
     * @return void
     */
    public function agentPopulate(Model_Core_Agent $agent)
    {

        // Extract Referencing and General e-mail addresses from agent object, if present
        $emailAddresses = array();
        if (isset($agent->email) && count($agent->email) > 0) {
            foreach ($agent->email as $emailObj) {
                if ($emailObj->category == Model_Core_Agent_EmailMapCategory::REFERENCING) {
                    $emailAddresses['Referencing'] = $emailObj->emailAddress->emailAddress;
                } elseif ($emailObj->category == Model_Core_Agent_EmailMapCategory::GENERAL) {
                    $emailAddresses['General'] = $emailObj->emailAddress->emailAddress;
                }
            }
        }

        $fontSize = 10; // points
        $this->_pdfMerge->pdfPageStyle->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), $fontSize);

        $address = '';
        /*
        // Format address as a newline-separated block
        $address .= ($agent->contact[0]->address->flatNumber != '') ? "{$agent->contact[0]->address->flatNumber}, " : '';
        $address .= ($agent->contact[0]->address->houseName != '') ? "{$agent->contact[0]->address->houseName}\n" : '';
        $address .= ($agent->contact[0]->address->houseNumber != '') ? "{$agent->contact[0]->address->houseNumber}\n" : '';
        $address .= ($agent->contact[0]->address->addressLine1 != '') ? "{$agent->contact[0]->address->addressLine1}\n" : '';
        $address .= ($agent->contact[0]->address->addressLine2 != '') ? "{$agent->contact[0]->address->addressLine2}\n" : '';
        $address .= ($agent->contact[0]->address->town != '') ? "{$agent->contact[0]->address->town}\n" : '';
        $address .= ($agent->contact[0]->address->county != '') ? "{$agent->contact[0]->address->county}\n" : '';
        $address .= ($agent->contact[0]->address->postCode != '') ? "{$agent->contact[0]->address->postCode}\n" : '';
        if ($agent->contact[0]->address->isOverseasAddress) {
            $address .= ($agent->contact[0]->address->country != '') ? "{$agent->contact[0]->address->country}\n" : '';
        }
        */

        if (!isset($emailAddresses['Referencing']) && !isset($emailAddresses['General'])) {
            // Format address as a comma-separated single line
            $address .= ($agent->contact[0]->address->flatNumber != '') ? "{$agent->contact[0]->address->flatNumber}, " : '';
            $address .= ($agent->contact[0]->address->houseName != '') ? "{$agent->contact[0]->address->houseName}, " : '';
            $address .= ($agent->contact[0]->address->houseNumber != '') ? "{$agent->contact[0]->address->houseNumber}, " : '';
            $address .= ($agent->contact[0]->address->addressLine1 != '') ? "{$agent->contact[0]->address->addressLine1}, " : '';
            $address .= ($agent->contact[0]->address->addressLine2 != '') ? "{$agent->contact[0]->address->addressLine2}, " : '';
            $address .= ($agent->contact[0]->address->town != '') ? "{$agent->contact[0]->address->town}, " : '';
            $address .= ($agent->contact[0]->address->county != '') ? "{$agent->contact[0]->address->county}, " : '';
            $address .= ($agent->contact[0]->address->postCode != '') ? "{$agent->contact[0]->address->postCode}, " : '';
            if ($agent->contact[0]->address->isOverseasAddress) {
                $address .= ($agent->contact[0]->address->country != '') ? "{$agent->contact[0]->address->country}, " : '';
            }
            $address = substr($address, 0, -2);
        } else {
            // e-mail address, referencing one used preferentially
            if (isset($emailAddresses['Referencing'])) {
                $address = $emailAddresses['Referencing'];
            } else {
                $address = $emailAddresses['General'];
            }
        }


        $textPlacements = array();

        // Page 0 has special placement requirements

        // Agent name
        $textPlacements[] = new Model_Core_Pdf_Element(
            0,              // page
            $agent->name,   // text
            42,             // x
            93,             // y
            null,           // x spacing
            null,           // y spacing
            true,           // wrap
            245             // maxWidth
        );

        // Agent telephone
        $textPlacements[] = new Model_Core_Pdf_Element(
            0,
            $agent->contact[0]->phoneNumbers->telephone1,
            360,
            119
        );

        // Agent address
        $textPlacements[] = new Model_Core_Pdf_Element(
            0,
            $address,
            309,
            93,
            null,
            null,
            true,
            245
        );

        // Agent scheme number
        $textPlacements[] = new Model_Core_Pdf_Element(
            0,
            $agent->agentSchemeNumber,
            104,
            119
        );

        // Add ASN and telephone number into subsequent pages
        if (count($this->_pdfMerge->pdfObj->pages) > 1) {

            // Fudge offsets to account for sausage inconsistencies in PDF
            // source files
            $xFudge = $yFudge = 0;
            switch($this->_pdfName) {
                case 'Agent-Guarantor':
                case 'Agent-Guarantor-BL':
                    $yFudge = 0;
                    break;
                case 'Agent-Unemployed-guarantor':
                case 'Agent-Unemployed-guarantor-BL':
                    $yFudge = 0;
            }

            for ($i = 1; $i < count($this->_pdfMerge->pdfObj->pages); $i++) {
                /*
                $textPlacements[] = new Model_Core_Pdf_Element(
                    $i,
                    $agent->name,
                    195 + $xFudge,
                    90 + $yFudge,
                    null,
                    null,
                    true,
                    188
                );
                */

                $textPlacements[] = new Model_Core_Pdf_Element(
                    $i,
                    $agent->agentSchemeNumber,
                    104 + $xFudge,
                    93 + $yFudge
                );

                $textPlacements[] = new Model_Core_Pdf_Element(
                    $i,
                    $agent->contact[0]->phoneNumbers->telephone1,
                    360 + $xFudge,
                    93 + $yFudge
                );
            }
        }

        $this->_pdfMerge->merge($textPlacements);
    }

    /**
     * Insert an applicant's details into a PDF.
     *
     * @param Model_Referencing_Reference $reference Reference object
     * @return void
     */
    public function applicantPopulate(Model_Referencing_Reference $reference) {

        // Overflow and trim address as necessary
        $address = $reference->propertyLease->address;
        $addressLine1 = trim("{$address->flatNumber} {$address->houseName} {$address->houseNumber} {$address->addressLine1} {$address->town} {$address->county}");
        $postCode = $address->postCode;

        $fontSize = 10; // points
        $this->_pdfMerge->pdfPageStyle->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), $fontSize);

        $textPlacements = array();

        // Page 0 has special placement requirements
        // Reference number
        $textPlacements[] = new Model_Core_Pdf_Element(
            0,                      // page
            $reference->internalId, // text
            120,                     // x
            262                    // y
        );
        // Surname
        $textPlacements[] = new Model_Core_Pdf_Element(
            0,
            strtoupper($reference->referenceSubject->name->lastName),
            367,
            262
        );
        // Address line 1
        $textPlacements[] = new Model_Core_Pdf_Element(
            0,
            strtoupper($addressLine1),
            135,
            392
        );
        // Postcode
        $textPlacements[] = new Model_Core_Pdf_Element(
            0,
            strtoupper($postCode),
            135,
            413
        );
        // Total rent for the property
        $textPlacements[] = new Model_Core_Pdf_Element(
            0,
            $reference->propertyLease->rentPerMonth->getValue(),
            330,
            413
        );
        // Share of rent for the property
        $textPlacements[] = new Model_Core_Pdf_Element(
            0,
            $reference->referenceSubject->shareOfRent->getValue(),
            170,
            462
        );
        // Duration of tenancy
        $textPlacements[] = new Model_Core_Pdf_Element(
            0,
            sprintf('%02d', $reference->propertyLease->tenancyTerm),
            170,
            609
        );
        // Expected tenancy start date (day)
        $textPlacements[] = new Model_Core_Pdf_Element(
            0,
            $reference->propertyLease->tenancyStartDate->toString('dd'),
            286,
            609
        );
        // Expected tenancy start date (month)
        $textPlacements[] = new Model_Core_Pdf_Element(
            0,
            $reference->propertyLease->tenancyStartDate->toString('MM'),
            304,
            609
        );
        // Expected tenancy start date (year)
        $textPlacements[] = new Model_Core_Pdf_Element(
            0,
            $reference->propertyLease->tenancyStartDate->toString('YY'),
            321,
            609
        );

        $this->_pdfMerge->merge($textPlacements);
    }

    public function output($destination = 'browser') {

        switch (strtolower($destination)) {
            case 'browser':
                // Direct to browser, dump it out and quit
                $this->_pdfMerge->output();
                break;
            case 'file':
                // Store to disc and return the full path to it
                $tempDir = $this->_params->connect->tempPrivatePath;
                $fullPath = "{$tempDir}agentForm_{$this->_pdfName}_" . md5($this->_pdfMerge->output('raw')) . '.pdf';
                $this->_pdfMerge->output('file', $fullPath);
                return $fullPath;
                break;
        }

    }

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
                if (substr($file, 0, 10) == 'agentForm_' && substr($file, -4, 4) == '.pdf') {
                    // Check its age vs now, more than 24 hours?
                    $fileModTime = filemtime("{$tempDir}{$file}");
                    if ($timeNow - $fileModTime > 24 * 60 * 60) {
                        @unlink("{$tempDir}{$file}");
                    }
                }
            }
        }
    }

}
