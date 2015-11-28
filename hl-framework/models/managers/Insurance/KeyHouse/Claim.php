<?php
/**
 *  Manager class responsible for claims
 *
 */
class Manager_Insurance_KeyHouse_Claim {

    protected $_claimModel;

    public function __construct() {
        $this->_claimModel = new Datasource_Insurance_KeyHouse_Claim();
    }

    /**
     * This is horrible. Reverses the split to get the last (first) three elements
     * of the path.
     *
     * @param str $filePath
     */
    private function _pathConvert($filePath) {
        $params = Zend_Registry::get('params');
        $reversed = array_reverse(explode('/', $filePath));
        return sprintf($params->connect->sqlserverFilePath, $reversed[2], $reversed[1], $reversed[0]);
    }

     /**
     * Move all rent guarantee claim details to keyHouse database, but does not
     * complete the claim
     *
     * @param int $referenceNum
     *
     * @return void
     */
    public function submitClaim($referenceNum) {
        // Get Claim Information
        $dsClaim = new Datasource_Insurance_RentGuaranteeClaim_Claim();
        $claimInfo = $dsClaim->getClaimByReferenceNumber($referenceNum);

        // Get Guarantors Information
        $dsGuarantors = new Datasource_Insurance_RentGuaranteeClaim_Guarantor();
        $guarantorsInfo = $dsGuarantors->getGuarantors($referenceNum);

        // Get Rent Payments Information
        $dsRentPayments = new Datasource_Insurance_RentGuaranteeClaim_RentalPayment();
        $rentPaymentsInfo = $dsRentPayments->getRentPaymentsByReferenceNumber($referenceNum);

        // Get Tenants Information
        $dsTenants = new Datasource_Insurance_RentGuaranteeClaim_Tenant();
        $tenantsInfo = $dsTenants->getTenants($referenceNum);

        // Get Supporting Documents Information
        $supportingDocuments = new Datasource_Insurance_RentGuaranteeClaim_SupportingDocuments();
        $documentsInfo = $supportingDocuments->getByReferenceNumber($referenceNum);

        // Submit all the data to keyhouse DB
        $dsKHClaim = new Datasource_Insurance_KeyHouse_Claim();
        $validData = array();

        if($dsKHClaim->save($claimInfo)) {
            $dsKHGuarantors = new Datasource_Insurance_KeyHouse_Guarantors();
            if(count($guarantorsInfo)>0) {
                foreach($guarantorsInfo as $guarantor) {
                    $dsKHGuarantors->save($guarantor);
                }
            }

            $dsKHRentPayments = new Datasource_Insurance_KeyHouse_RentPayments();
            if(count($rentPaymentsInfo)>0) {
                foreach($rentPaymentsInfo as $rentalPayment) {
                    $dsKHRentPayments->save($rentalPayment);
                }
            }
            $dsKHTenants = new Datasource_Insurance_KeyHouse_Tenants();
            if(count($tenantsInfo)>0) {
                foreach($tenantsInfo as $tenant) {
                    $dsKHTenants->save($tenant);
                }
            }
            $dsKHSupportingDocuments = new Datasource_Insurance_KeyHouse_SupportingDocuments();

            if(count($documentsInfo) > 0) {
                foreach($documentsInfo as $document) {
                    // Convert from StdClass to array as expected.
                    $doc = array();
                    $doc['reference_number'] = $referenceNum;
                    $doc['supporting_document_name'] = $document->type;
                    $doc['attachment_filename'] = $this->_pathConvert($document->fullPath);
                    $dsKHSupportingDocuments->save($doc);
                }
            }
        }
    }

    /**
     * Get Claim Summary for the given Agent Scheme Number
     *
     * @param int $agentSchemeNumber
     *
     * @return Array
     */
    public function getOpenClaims($agentSchemeNumber) {
        return $this->_claimModel->getOpenClaims($agentSchemeNumber);
    }

     /**
     * Returns claim details for the given claim reference number.
     *
     * @param mixed $claimRefNo Claim reference number
     * @param mixed $agentSchemeNo Agent scheme number
     *
     * This method will retrieve claim information stored in the keyhouse database
     *
     * @return Manager_Insurance_KeyHouse_Claim
     * Returns this object populated with relevant information, or null if no
     * relevant information has been stored.
     */
    public function getClaim($claimRefNo, $agentSchemeNo) {
        return $this->_claimModel->getClaim($claimRefNo, $agentSchemeNo);
    }

    /**
     * Insert a claim details into a PDF.
     *
     * @param int $claimRefNo
     *
     * @return void
     */
    public function populateAndOuputClaimStatusReport($claimRefNo, $agentSchemeNumber) {

        $claimDataSource = new Datasource_Insurance_KeyHouse_Claim();
        $claimData = $claimDataSource->getClaim($claimRefNo,$agentSchemeNumber);

        $pdf = new Zend_Pdf_WrapText();
        // create A4 page
        $page = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);

        // Add HomeLet logo
        $xcoord = 15;
        $ycoord = 780;
        $image = Zend_Pdf_Image::imageWithPath(APPLICATION_PATH . '/../public/assets/common/images/logo-mid.png');
        $page->drawImage($image, $xcoord, $ycoord, $xcoord + $image->getPixelWidth(), $ycoord + $image->getPixelHeight());

        // define a style
        $claimHeaderFont = new Zend_Pdf_Style();
        $claimHeaderFont->setFillColor(Zend_Pdf_Color_Html::color('#FF6F1C'));
        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
        $claimHeaderFont->setFont($font, 14);

        // define another style
        $claimContentTitleFont = new Zend_Pdf_Style();
        $claimContentTitleFont->setFillColor(Zend_Pdf_Color_Html::color('#0C2F6B'));
        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
        $claimContentTitleFont->setFont($font, 10);

        // define another style
        $claimContentFont = new Zend_Pdf_Style();
        $claimContentFont->setFillColor(Zend_Pdf_Color_Html::color('#0C2F6B'));
        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
        $claimContentFont->setFont($font, 10);

        // write title text to page
        $page->setStyle($claimHeaderFont)->drawText('Claim Status Report', 250, 810);

        // write content text to page
        $page->setStyle($claimContentTitleFont)->drawText('Claim Number', 15, 700);
        $page->setStyle($claimContentFont)->drawText($claimData[0]['ClaimNo'], 200, 700);
        $page->setStyle($claimContentTitleFont)->drawText('Claim Handler', 15, 680);
        $page->setStyle($claimContentFont)->drawText($claimData[0]['ClaimsHandler'], 200, 680);
        $page->setStyle($claimContentTitleFont)->drawText('Reference Number', 15, 660);
        $page->setStyle($claimContentFont)->drawText($claimData[0]['ClaimNo'], 200, 660); $page->setStyle($claimContentTitleFont)->drawText('Start Date', 15, 640);
        $page->setStyle($claimContentFont)->drawText($claimData[0]['ClaimDate'], 200, 640);

        $page->setStyle($claimContentTitleFont)->drawText('Date', 35, 590);
        $page->setStyle($claimContentTitleFont)->drawText('Action', 235, 590);
        $page->setStyle($claimContentTitleFont)->drawText('Status', 435, 590);

        // wrap text to avoid overlapping
        $zendWrapText = new Zend_Pdf_WrapText();
        $sectionHeight = 0;
        $y = 570;
        for($i=0;$i<count($claimData);$i++) {

            $page->setStyle($claimContentFont)->drawText($claimData[$i]['ClaimDate'], 35, $y);
            $sectionHeight = $zendWrapText->drawWrappedText($page, 235, $y, $claimData[$i]['Activity'], 150, $claimContentFont);
            //$page->setStyle($claimContentFont)->drawTextBlock($claimData[$i]['Activitiy'], 235, 570, 200, 200, Zend_Pdf_Page::ALIGN_LEFT);
            $page->setStyle($claimContentFont)->drawText($claimData[$i]['OpenOrClosed'], 435, $y);
        	$y -= ($sectionHeight);
        }

        // add page to document
        $pdf->pages[] = $page;
        $filename = "claimstatus_".md5($claimRefNo);
        // send to browser as download
        return $pdf->render();
    }
}
?>
