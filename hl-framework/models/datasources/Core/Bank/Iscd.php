 <?php
 
/**
 * Model definition for the bank ISCD (Industry Sorting Code Directory) datasource.
 */
class Datasource_Core_Bank_Iscd extends Zend_Db_Table_Multidb {
    
    protected $_multidb = 'db_homelet_insurance_com';    
    protected $_name = 'ISCD';
    protected $_primary = 'ISCDid';
    
    
    /**
     * Populates and returns a Model_Core_Bank_Iscd from the datasource.
     *
     * @param mixed $sortCode
     * Identifies the corresponding ISCD object to retrieve. Must be formatted so
     * that it contains only numerics. Can be a string or integer.
     * 
     * @return mixed 
     * A Model_Core_Bank_Iscd object representing the first ISCD found in the datasource,
     * or null if no matches are found.
     */
    public function getIscd($sortCode) {
        
        $select = $this->select()
            ->from($this->_name)
            ->where('sortcode = ?', $sortCode);
        $row = $this->fetchRow($select);
        
        if(!empty($row)) {
            
            $iscd = new Model_Core_Bank_Iscd();
            $iscd->ISCDid = $row->ISCDid;
            $iscd->sortCode = $row->sortCode;
            $iscd->BICBank = $row->BICBank;
            $iscd->BICBranch = $row->BICBranch;
            $iscd->subBranchSuffix = $row->subBranchSuffix;
            $iscd->shortBranchTitle = $row->shortBranchTitle;
            $iscd->shortNameOfOwningBank = $row->shortNameOfOwningBank;
            $iscd->fullOwningBankName1 = $row->fullOwningBankName1;
            $iscd->fullOwningBankName2 = $row->fullOwningBankName2;
            $iscd->owningBankCode = $row->owningBankCode;
            $iscd->nationalCentralBankCountryCode = $row->nationalCentralBankCountryCode;
            $iscd->supervisoryBody = $row->supervisoryBody;
            $iscd->deletedDate = $row->deletedDate;
            $iscd->dateLastChange = $row->dateLastChange;
            $iscd->printIndicator = $row->printIndicator;
            $iscd->BACSStatus = $row->BACSStatus;
            $iscd->BACSDateLastChange = $row->BACSDateLastChange;
            $iscd->dateClosedBACSClearing = $row->dateClosedBACSClearing;
            $iscd->redirectionFromFlag = $row->redirectionFromFlag;
            $iscd->redirectedToSortCode = $row->redirectedToSortCode;
            $iscd->BACSSettlementBank = $row->BACSSettlementBank;
            $iscd->settlementSection = $row->settlementSection;
            $iscd->settlementSubSection = $row->settlementSubSection;
            $iscd->handlingBank = $row->handlingBank;
            $iscd->handlingBankStream = $row->handlingBankStream;
            $iscd->accountNumberedFlag = $row->accountNumberedFlag;
            $iscd->DDIVoucherFlag = $row->DDIVoucherFlag;
            $iscd->disallowDR = $row->disallowDR;
            $iscd->disallowCR = $row->disallowCR;
            $iscd->disallowCU = $row->disallowCU;
            $iscd->disallowPR = $row->disallowPR;
            $iscd->disallowBS = $row->disallowBS;
            $iscd->disallowDV = $row->disallowDV;
            $iscd->disallowAU = $row->disallowAU;
            $iscd->disallowX = $row->disallowX;
            $iscd->disallowY = $row->disallowY;
            $iscd->disallowZ = $row->disallowZ ;
            $iscd->spareField = $row->spareField;
            $iscd->CHAPSSterlingReturnIndicator = $row->CHAPSSterlingReturnIndicator;
            $iscd->CHAPSSterlingStatus = $row->CHAPSSterlingStatus;
            $iscd->CHAPSSterlingEffectiveDateLastChange = $row->CHAPSSterlingEffectiveDateLastChange;
            $iscd->CHAPSSterlingDateClosedInClearing = $row->CHAPSSterlingDateClosedInClearing;
            $iscd->CHAPSSterlingSettlementMember = $row->CHAPSSterlingSettlementMember;
            $iscd->CHAPSSterlingRoutingBICBank = $row->CHAPSSterlingRoutingBICBank;
            $iscd->CHAPSSterlingRoutingBICBranch = $row->CHAPSSterlingRoutingBICBranch;
            $iscd->CHAPSEuroStatus = $row->CHAPSEuroStatus;
            $iscd->CHAPSEuroEffectiveDateLastChange = $row->CHAPSEuroEffectiveDateLastChange;
            $iscd->CHAPSEuroDateClosedInClearing = $row->CHAPSEuroDateClosedInClearing;
            $iscd->CHAPSEuroRoutingBICBank = $row->CHAPSEuroRoutingBICBank;
            $iscd->CHAPSEuroRoutingBICBranch = $row->CHAPSEuroRoutingBICBranch;
            $iscd->CHAPSEuroSettlementMember = $row->CHAPSEuroSettlementMember;
            $iscd->CHAPSEuroReturnIndicator = $row->CHAPSEuroReturnIndicator;
            $iscd->CHAPSEuroSWIFTData = $row->CHAPSEuroSWIFTData;
            $iscd->CHAPSEuroSpareField = $row->CHAPSEuroSpareField;
            $iscd->CandCCCStatus = $row->CandCCCStatus;
            $iscd->CandCCCEffectiveDateLastChange = $row->CandCCCEffectiveDateLastChange;
            $iscd->CandCCCDateClosedInClearing = $row->CandCCCDateClosedInClearing;
            $iscd->CandCCCSettlementBank = $row->CandCCCSettlementBank;
            $iscd->CandCCCDebitAgencySortCode = $row->CandCCCDebitAgencySortCode;
            $iscd->CandCCCReturnIndicator = $row->CandCCCReturnIndicator;
            $iscd->CandCCCGB_NI_Indicator               = $row->CandCCCGB_NI_Indicator;
            $iscd->PRINTBranchTypeIndicator = $row->PRINTBranchTypeIndicator;
            $iscd->PRINTSortCodeMainBranch = $row->PRINTSortCodeMainBranch;
            $iscd->PRINTMajorLocationName = $row->PRINTMajorLocationName;
            $iscd->PRINTMinorLocationName = $row->PRINTMinorLocationName;
            $iscd->PRINTBranchNamePlace = $row->PRINTBranchNamePlace;
            $iscd->PRINTSecondEntryIndicator = $row->PRINTSecondEntryIndicator;
            $iscd->PRINTBranchNameForSecondEntry = $row->PRINTBranchNameForSecondEntry;
            $iscd->PRINTBranchTitle1 = $row->PRINTBranchTitle1;
            $iscd->PRINTBranchTitle2 = $row->PRINTBranchTitle2;
            $iscd->PRINTBranchTitle3 = $row->PRINTBranchTitle3;
            $iscd->PRINTAddress1 = $row->PRINTAddress1;
            $iscd->PRINTAddress2 = $row->PRINTAddress2;
            $iscd->PRINTAddress3 = $row->PRINTAddress3;
            $iscd->PRINTAddress4 = $row->PRINTAddress4;
            $iscd->PRINTTown = $row->PRINTTown;
            $iscd->PRINTCounty = $row->PRINTCounty;
            $iscd->PRINTPostcodeMajorPart = $row->PRINTPostcodeMajorPart;
            $iscd->PRINTPostcodeMinorPart = $row->PRINTPostcodeMinorPart;
            $iscd->PRINTPhoneArea = $row->PRINTPhoneArea;
            $iscd->PRINTSubscriberNo = $row->PRINTSubscriberNo;
            $iscd->reservedFAXAreaCode = $row->reservedFAXAreaCode;
            $iscd->reservedFAXSubscriberNo = $row->reservedFAXSubscriberNo;
        }
        
        if(empty($iscd)) {
            $returnVal = null;
        }
        else {
            $returnVal = $iscd;
        }
        
        return $returnVal;
    }
}

?>